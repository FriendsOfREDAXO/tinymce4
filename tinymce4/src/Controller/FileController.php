<?php
namespace Tinymce4\Controller;

class FileController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function indexAction() {
        $type = isset($_GET['type']) ? $_GET['type'] : 'link';
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $clang_id = isset($_GET['clang_id']) ? intval($_GET['clang_id']) : \rex_clang::getStartId();
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
            if (0 == $category_id) {
                $media = $this->container->get('MediaRepository')
                    ->findAll(array('originalname'=>'ASC'));
            } else {
                $sql = "category_id=?";
                $media = $this->container->get('MediaRepository')
                    ->findWhere($sql, array($category_id), array('originalname'=>'ASC'));
            }
            $link_list = array();
            foreach ($media as $m) {
                $link_list[] = array(
                    'url' => \rex_url::media($m->filename),
                    'name' => $m->originalname,
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
            ));
    }
    public function detailAction($form_id_file) {
        list($form_id, $file) = explode('_', $form_id_file, 2);
        $form = $this->container->get('FormRepository')->find($form_id);
        $dir = $this->container->getParameter('data_dir').'/submissions/f'.$form_id;
        $data = unserialize(file_get_contents($dir.'/'.$file));
        return $this->container->get('RenderService')->render(
            'backend/data_detail.php', array(
                'form' => $form,
                'data' => $data,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
            ));

    }
    public function removeAction($form_id_file) {
        list($form_id, $file) = explode('_', $form_id_file, 2);
        $form = $this->container->get('FormRepository')->find($form_id);
        $dir = $this->container->getParameter('data_dir').'/submissions/f'.$form_id;
        unlink($dir.'/'.$file);
        header("Location: ". $this->container->get('UrlService')->getUrl('/data/list/'.$form_id));
        die();

    }
    public function removeAllAction($form_id) {
        $dir = $this->container->getParameter('data_dir').'/submissions/f'.$form_id;
        foreach (scandir($dir) as $f) {
            if (in_array($f, ['.','..'])) continue;
            unlink($dir.'/'.$f);
        }
        header("Location: ". $this->container->get('UrlService')->getUrl('/data/list/'.$form_id));
        die();

    }
}


