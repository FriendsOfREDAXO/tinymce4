<?php
namespace Tinymce4\Controller;

class FileController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }
    public function indexAction() {
        $filter = $this->container->get('FilterService');
        $type = isset($_GET['type']) ? $_GET['type'] : 'link';
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();
        $search = isset($_GET['search']) ? trim($filter->filterString($_GET['search'])) : '';
        $list_content = $this->listAction();
        if ('link' == $type) {
            $category_choices = $this->container->get('ArticleRepository')
                    ->getCategoryChoices($clang_id);
        } else {
            $category_choices = $this->container->get('MediaCategoryRepository')
                ->getCategoryChoices();
        }
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
                'search' => $search,
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
        if ('link' == $type) {
            if (0 == $category_id) {
                $sql = "parent_id=? AND startarticle=? and clang_id=?";
                $binds = array($category_id, 0, $clang_id);

            } else {
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
                    'url' => \rex_url::media($m->filename),
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


