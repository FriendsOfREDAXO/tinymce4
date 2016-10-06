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
        if ('link' == $type) {
            if (0 == $category_id) {
                $page_list = $this->container->get('ArticleRepository')
                    ->findBy(array(
                        'parent_id' =>$category_id,
                        'startarticle' => 0,
                        'clang_id' => $clang_id,
                    ), array('priority' => 'ASC'));
            } else {
                $page_list = $this->container->get('ArticleRepository')
                    ->findWhere("(
                       ( id=? AND startarticle=1)
                       OR
                       ( parent_id=? AND startarticle=0)
                   )
                    AND clang_id=?
                    ", array($category_id, $category_id, $clang_id));
            }
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
            $category_choices = $this->container->get('MediaCategoryRepository')
                ->getCategoryChoices();
        }
        
        return $this->container->get('RenderService')->render(
            'frontend/file_index.php', array(
                'link_list' => $link_list,
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
    
}


