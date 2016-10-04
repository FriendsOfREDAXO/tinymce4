<?php
namespace FormInModule\Controller;

class FormFieldController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function editAction($form_id_field_id) {
        $errors = array();
        list($form_id, $field_id) = explode('_', $form_id_field_id);
        $form = $this->container->get('FormRepository')->find($form_id);
        $model = $this->container->get('FormFieldRepository')
            ->find($field_id);

        if(null === $model){
            $field_id = 0;
            $model = new \FormInModule\Models\FormField();
            $model->form_id = $form_id;
        }
        if(isset($_POST['model'])){
            $model->setFormData($_POST['model'], $this->container);
            $errors = $model->validate($this->container);
            if(0 == count($errors)){
                if(0 == $field_id){
                    $err = $this->container->get('FormFieldRepository')->insert($model);
                }else{
                    $err = $this->container->get('FormFieldRepository')->update($model);
                }
                header("Location: ". $this->container->get('UrlService')->getUrl('/form/fields/'.$model->form_id));
                die();
            }
        }
        
        return $this->container->get('RenderService')
            ->render('backend/field_edit.php', array(
                'field_id' => $field_id,
                'form_id' => $form_id,
                'model' => $model,
                'UrlService' => $this->container->get('UrlService'),
                'form' => $this->container->get('FormService'),
                'errors' => $errors,
                'language_choices' => $this->container->get('LanguageService')->getLanguageChoices(),
                'Translator' => $this->container->get('TranslatorService'),
                'type_choices' => array(
                    'submit' => 'Sendebutton',
                    'html' => 'HTML',
                    'text' => 'Text',
                    'textarea' => 'Text, mehrzeilig',
                    'email' => 'Email',
                    'password' => 'Password',
                    'hidden' => 'Hidden',
                    'select' => 'Select',
                    'selectMultiple' => 'Multiple Select',
                    'checkbox' => 'Checkbox',
                    'radio' => 'Radio',
                    'checkboxMultiple' => 'Multiple Checkbox',
                    'file' => 'Datei',
                ),
        ));
    }
    public function moveAction($field_id) {
        $model = $this->container->get('FormFieldRepository')
            ->find($field_id);
        if (isset($_GET['direction']) && 'up' == $_GET['direction']) {
            $this->container->get('FormFieldRepository')->moveUp($model);
        } elseif (isset($_GET['direction']) && 'down' == $_GET['direction']) {
            $this->container->get('FormFieldRepository')->moveDown($model);
        }
        $url = $this->container->get('UrlService')
            ->getUrl('/form/fields/'.$model->form_id);
        header("Location: $url");
        die();
    }

    public function removeAction($field_id) {
        $model = $this->container->get('FormFieldRepository')
            ->find($field_id);
        if (null !== $model) {
            $this->container->get('FormFieldRepository')->delete($model);
        }
        $url = $this->container->get('UrlService')
            ->getUrl('/form/fields/'.$model->form_id);
        header("Location: $url");
        die();
    }

}

