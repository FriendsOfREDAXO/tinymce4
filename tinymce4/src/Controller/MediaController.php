<?php
namespace Tinymce4\Controller;

class ImageController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function indexAction($category_id) {
        $sql = "filetype IN ('image/png', 'image/jpeg', 'image/gif')";
        if (0 == $category) {
            $media = $this->container->get('MediaRepository')
                ->findWhere($sql, array(), array('originalname'=>'ASC'));
        } else {
            $sql.=" AND category_id=?";
            $media = $this->container->get('MediaRepository')
                ->findWhere($sql, array(intval($category_id)), array('originalname'=>'ASC'));
        }
        return $this->container->get('RenderService')->render(
            'frontend/image_index.php', array(
                'media_list' => $media,
                'form' => $this->container->get('FormService'),
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'category_choices' => $this->container->get('MediaCategoryRepository')->getCategoryChoices(),
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


