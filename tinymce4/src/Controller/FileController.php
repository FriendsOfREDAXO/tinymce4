<?php
namespace Tinymce4\Controller;

use Tinymce4\Services\ProfileRepository;

class FileController
{
    public $container;
    protected $tables;

    public function __construct($container) {
        $this->container = $container;
    }
    public function indexAction() {
        $Repository = new ProfileRepository();
        $filter = $this->container->get('FilterService');
        $type = isset($_GET['type']) ? $_GET['type'] : 'link';
        $category_id = rex_get('category_id', 'int', 0);
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();
        $this->profile = $Repository->find(rex_get('mce_profile', 'string'));
        $search = isset($_GET['search']) ? trim($filter->filterString($_GET['search'])) : '';

        if ('table' == $type) {
            $category_choices = [];

            if ($this->profile) {
                $profile_data = $this->profile->decode();

                if (strlen($profile_data['tables'])) {
                    $this->tables = [];
                    preg_match_all('@(\w+)(\[(\w+:[\s\w+=><!\d]+,?)+\]),?@i', trim(trim(strtr($profile_data['tables'], [
                        '{{CLANG_CURRENT_ID}}' => \rex_clang::getCurrentId(),
                    ]), "'"), '"'), $matches);

                    foreach ($matches[1] as $index => $tablename) {
                        $ytable = \rex_yform_manager_table::get($tablename);
                        $category_choices[$index] = $ytable ? $ytable->getName() : $tablename;

                        $data = ['tablename' => $tablename];
                        $_data = explode(',', trim(trim($matches[2][$index], ']'), '['));

                        foreach ($_data as $row) {
                            list($key, $values) = explode(':', $row);
                            $data[$key] = array_filter(explode('+', $values));
                        }
                        $this->tables[$index] = array_filter($data);
                    }
                }
            }
        }
        else if ('link' == $type) {
            $category_choices = $this->container->get('ArticleRepository')
                ->getCategoryChoices($clang_id);
        } else {
            $category_choices = $this->container->get('MediaCategoryRepository')
                ->getCategoryChoices();
        }

        $list_content = $this->listAction();

        return $this->container->get('RenderService')->render(
            'frontend/file_index.php', array(
                'list_content' => $list_content,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'form' => $this->container->get('FormService'),
                'category_id' => $category_id,
                'type' => $type,
                'category_choices' => $category_choices,
                'language_choices' => $this->container->get('LanguageService')->getLanguageChoices(),
                'clang_id' => $clang_id,
                'profile' => $this->profile,
                'search' => $search,
            ));

    }

    public function listAction() {
        $filter = $this->container->get('FilterService');
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit  = isset($_GET['limit ']) ? intval($_GET['limit']) : 100;
        $type = isset($_GET['type']) ? $_GET['type'] : 'link';
        $category_id = rex_get('category_id', 'int', 0);
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
        } elseif ('table' == $type) {
            if (count($this->tables)) {
                $table = $this->tables[$category_id] ?: $this->tables[0];
                $where = array_merge([1], (array) $table['filter']);
                $order = strlen($table['order'][0]) ? $table['order'][0] : $table['fields'][0] .' ASC';

                if (strlen($search)) {
                    $where[] = implode(" LIKE '%{$search}%' OR ", $table['fields']) ." LIKE '%{$search}%'";
                }

                $sql = \rex_sql::factory();
                $query = "
                    SELECT 
                        id, 
                        CONCAT('table://{$table['tablename']}-', id, '-{$clang_id}') AS url, 
                        CONCAT(". implode('," | ",', $table['fields']) .") AS name 
                    FROM {$table['tablename']}
                    WHERE ". implode(' AND ', $where) ."
                    ORDER BY {$order}
                ";
                echo "<!-- Table-Data: ". print_r($table, true) ." -->";
                echo "<!-- Table-Query: {$query} -->";
                $link_list = $sql->getArray($query);
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

}


