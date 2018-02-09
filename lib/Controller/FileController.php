<?php
namespace Tinymce4\Controller;

use Tinymce4\Services\ProfileRepository;

class FileController
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }
    public function testAction(){
    }
    public function indexAction() {
        $Repository = new ProfileRepository($this->container);
        $this->profile = $Repository->find(rex_get('mce_profile', 'string'));
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();

        $tables = [];
        $db = \rex_sql::factory();
        $sql = "SELECT pid,id,parent_id,
            `name`,catname,catpriority,startarticle,
            priority,status,clang_id 
            FROM ".\rex::getTable('article')." 
            WHERE clang_id=$clang_id";
        $all_arts = $db->getArray($sql);
        $sql =  "SELECT id,category_id,filename,originalname,title
            FROM ".\rex::getTable('media')." 
            WHERE 1";
        $all_files = $db->getArray($sql);
        $sql =  "SELECT id,`name`,parent_id 
            FROM ".\rex::getTable('media_category')." 
            WHERE 1";
        $all_media_categories = $db->getArray($sql);


        if ($this->profile) {
            $profile_data = $this->profile->decode();

            if (strlen($profile_data['tables'])) {
                /*
                    Example profile config:
                    tables: 'rex_table_1[fields:field_1+field_2,filter:field_status=1,order:field_1],rex_table_2[fields:field_1]'
                */
                preg_match_all('@(\w+)(\[(\w+:[\s\w+=><!\d]+,?)+\]),?@i', trim(trim(strtr($profile_data['tables'], [
                    '{{CLANG_CURRENT_ID}}' => \rex_clang::getCurrentId(),
                ]), "'"), '"'), $matches);

                foreach ($matches[1] as $index => $tablename) {
                    $ytable = \rex_addon::get('yform')->isAvailable() ? \rex_yform_manager_table::get($tablename) : null;
                    $data = ['table' => $tablename, 'tablename' => $ytable ? $ytable->getName() : $tablename];
                    $_data = explode(',', trim(trim($matches[2][$index], ']'), '['));
                    foreach ($_data as $row) {
                        list($key, $values) = explode(':', $row);
                        $data[$key] = array_filter(explode('+', $values));
                    }
                    $table_data = array_filter($data);

                    $where = array_merge([1], (array) $table_data['filter']);
                    $order = strlen($table_data['order'][0]) ? $table_data['order'][0] : $table_data['fields'][0] .' ASC';

                    $sql = \rex_sql::factory();
                    $query = "
                        SELECT 
                            id, 
                            CONCAT('table://{$table_data['table']}-', id, '-{$clang_id}') AS url, 
                            CONCAT(". implode('," | ",', $table_data['fields']) .") AS name 
                        FROM {$table_data['table']}
                        WHERE ". implode(' AND ', $where) ."
                        ORDER BY {$order}
                    ";
                    $table_data['query'] = $query;
                    $table_data['data'] = $sql->getArray($query);

                    $tables[$index] = $table_data;
                }
            }
        }

        return $this->container->get('RenderService')->render(
            'frontend/file_index.php', array(
                'all_tables' => $tables,
                'all_arts' => $all_arts,
                'all_files' => $all_files,
                'all_media_categories' => $all_media_categories,
                'media_format' => \rex_config::get('tinymce4', 'media_format'),
            ));

    }

    /*
    public function listAction() {
        $filter = $this->container->get('FilterService');
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit  = isset($_GET['limit ']) ? intval($_GET['limit']) : 100;
        $type = isset($_GET['type']) ? $_GET['type'] : 'link';
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();
        $search = isset($_GET['search']) ? trim($filter->filterString($_GET['search'])) : '';
        $media_format = \rex_config::get('tinymce4', 'media_format');
        if ('link' == $type) {
            if (-1 == $category_id) {
                // Alle
                $sql = " clang_id=?";
                $binds = array($clang_id);
            }
            elseif (0 == $category_id) {
                $sql = "parent_id=? AND startarticle=? and clang_id=?";
                $binds = array($category_id, 0, $clang_id);

            } 
            else {
                $sql = "(
                       ( id=? AND startarticle=1)
                       OR
                       ( parent_id=? AND startarticle=0)
                   )
                    AND clang_id=?
                    ";
                $binds = array($category_id, $category_id, $clang_id);
            }
            $page_list = $this->container->get('ArticleRepository')
                ->findWhere($sql, $binds, array('priority' => 'ASC'));
            $total = $this->container->get('ArticleRepository')
                ->countWhere($sql, $binds);
            $link_list = array();
            foreach ($page_list as $p) {
                $link_list[] = array(
                    'url' => 'redaxo://'. $p->id.'-'.$clang_id,
                    'name' => $p->name,
                );
            }
            $category_choices = $this->container->get('ArticleRepository')
                ->getCategoryChoices($clang_id);
        } elseif ('media' == $type) {
            $binds = array();
            $sql = "1";
            foreach (explode(' ', $search) as $s) {
                if ('' == $s) continue;
                $sql.= " AND (
                    `filename` LIKE ? 
                    OR `originalname` LIKE ?
                    OR `title` LIKE ?
                    )
                    ";
                $binds[] = '%'.$s.'%';
                $binds[] = '%'.$s.'%';
                $binds[] = '%'.$s.'%';
            }
            if (0 < $category_id) {
                $sql = "category_id=?";
                $binds[] = $category_id;
            }
            $media = $this->container->get('MediaRepository')
                ->findWhere($sql, $binds, array('originalname'=>'ASC'));
            $total = $this->container->get('MediaRepository')
                ->countWhere($sql, $binds);
            $link_list = array();
            foreach ($media as $m) {
                $name = $m->originalname;
                if ('' != $m->title) {
                    $name.= ' | '.$m->title;
                }
                $link_list[] = array(
                    'url' => 'default'==$media_format ? '/media/'.urlencode($m->filename) : str_replace('{filename}',urlencode($m->filename),$media_format),
                    'name' => $name,
                );
            }

        }
        
        return $this->container->get('RenderService')->render(
            'frontend/file_list.php', array(
                'link_list' => $link_list,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'form' => $this->container->get('FormService'),
                'category_id' => $category_id,
                'type' => $type,
                'clang_id' => $clang_id,
                'search' => $search,
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit,
                
            ));
    }
     */
    
}


