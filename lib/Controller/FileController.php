<?php
namespace Tinymce4\Controller;

class FileController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }
    public function testAction(){
    }
    public function indexAction() {
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();
        $db = \rex_sql::factory();
        $sql = "SELECT * FROM ".\rex::getTable('article')." WHERE clang_id=$clang_id";
        $all_arts = $db->getArray($sql);
        $sql =  "SELECT * FROM ".\rex::getTable('media')." WHERE 1";
        $all_files = $db->getArray($sql);
        $sql =  "SELECT * FROM ".\rex::getTable('media_category')." WHERE 1";
        $all_media_categories = $db->getArray($sql);
        return $this->container->get('RenderService')->render(
            'frontend/file_index.php', array(
                'all_arts' => $all_arts,
                'all_files' => $all_files,
                'all_media_categories' => $all_media_categories,
                'media_format' => \rex_config::get('tinymce4', 'media_format'),
            ));

    }

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
    
}


