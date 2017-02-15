<?php
namespace Tinymce4\Controller;

class ProfileController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function indexAction() {
        $profile_list = $this->container->get('ProfileRepository')
            ->findAll();

        return $this->container->get('RenderService')->render(
            'backend/profile_index.php', array(
                'profile_list' => $profile_list,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'form' => $this->container->get('FormService'),
                'clang_id' => $clang_id,
            ));
    }
    public function editAction($id) {
        $model = $this->container->get('ProfileRepository')->find($id);
        if (null === $model) {
            $id = 0;
            $model = new \Tinymce4\Models\Profile();
        } 

        if (isset($_POST['model'])) {
            $model->setFormData($_POST['model'], $this->container);
            $errors = $model->validate($this->container);
            if (0 == count($errors)) {
                if (0 == $id) {
                    $model->id = time();
                    $this->container->get('ProfileRepository')->insert($model);
                } else {
                    $this->container->get('ProfileRepository')->update($model);
                }
                $this->container->get('ProfileRepository')->rebuildInitScripts();

                $url = $this->container->get('UrlService')->getUrl('/profile/index');
                header("Location: $url");
                die();
            }
        }
        return $this->container->get('RenderService')->render(
            'backend/profile_edit.php', array(
                'model' => $model,
                'id' => $id,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'form' => $this->container->get('FormService'),
                'errors' => $errors,
            ));
        
    }
    
    public function removeAction($id){
        $model = $this->container->get('ProfileRepository')->find($id);
        if (null !== $model) {
            $this->container->get('ProfileRepository')->remove($model);
        }
        $url = $this->container->get('UrlService')->getUrl('/profile/index');
        header("Location: $url");
        die();

    }
    
}


