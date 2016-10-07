<?php
namespace Tinymce4\Controller;

class ConfigController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function editAction() {
        $model = new \Tinymce4\Models\Config();
        $model->hydrate();
        $errors = array();
        if (isset($_POST['model'])) {
            $model->setFormData($_POST['model'], $this->container);
            $errors = $model->validate($this->container);
            if (0 == count($errors)) {
                $model->save();
                $url = $this->container->get('UrlService')->getUrl('/profile/index'); 
                header("Location: $url");
                die();
            }
        }

        return $this->container->get('RenderService')->render(
            'backend/config_edit.php', array(
                'model' => $model,
                'form' => $this->container->get('FormService'),
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'errors' => $errors,
            ));
    }
     
}


