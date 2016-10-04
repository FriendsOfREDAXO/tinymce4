<?php
namespace FormInModule\Controller;

class FormController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function indexAction() {
        $data = $this->container->get('FormRepository')->findAll();
        return $this->container->get('RenderService')->render(
            'backend/form_index.php', array(
                'data' => $data,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
            ));
    }
    public function editAction($form_id) {
        $errors = array();
        $model = $this->container->get('FormRepository')
            ->find($form_id);

        if(null === $model){
            $form_id = 0;
            $model = new \FormInModule\Models\Form();
            
        }
        if(isset($_POST['model'])){
            $model->setFormData($_POST['model'], $this->container);
            $errors = $model->validate($this->container);
            if(0 == count($errors)){
                if(0 == $form_id){
                    $err = $this->container->get('FormRepository')->insert($model);
                }else{
                    $err = $this->container->get('FormRepository')->update($model);
                }
                header("Location: ". $this->container->get('UrlService')->getUrl('/form/index'));
                die();
            }
        }
        
        return $this->container->get('RenderService')
            ->render('backend/form_edit.php', array(
                'form_id' => $form_id,
                'model' => $model,
                'UrlService' => $this->container->get('UrlService'),
                'form' => $this->container->get('FormService'),
                'errors' => $errors,
                'language_choices' => $this->container->get('LanguageService')->getLanguageChoices(),
                'view_choices' => $this->container->get('FormRepository')->getViewChoices(),
                'confirmto_choices' => $this->container->get('FormFieldRepository')->getConfirmtoChoices($form_id),
                'Translator' => $this->container->get('TranslatorService'),
        ));
    }

    public function fieldsAction($form_id) {
        $form = $this->container->get('FormRepository')->find($form_id);
        $field_list = $this->container->get('FormFieldRepository')->findBy(
            array('form_id' => $form_id), array('pos' => 'ASC'));
        $language_keys = array_keys($this->container->get('LanguageService')->getLanguageChoices());
        $default_language = array_shift($language_keys);

        return $this->container->get('RenderService')->render(
            'backend/form_fields.php', array(
                'form' => $form,
                'form_id' => $form_id,
                'field_list' => $field_list,
                'UrlService' => $this->container->get('UrlService'),
                'default_language' => $default_language,
                'Translator' => $this->container->get('TranslatorService'),
            ));

    }
    
    public function dataListAction($form_id) {
        $form = $this->container->get('FormRepository')->find($form_id);
        $dir = $this->container->getParameter('data_dir').'/f'.$form->form_id;
        $data_list = is_dir($dir) ? scandir($dir) : array();
        return $this->container->get('RenderService')->render(
            'backend/data_list.php', array(
                'form' => $form,
                'data_list' => $form_id,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
            ));

    }

    public function outputAction($form_id) {
        $submission = $this->container->get('Submission');
        $form = $this->container->get('FormRepository')->find($form_id);
        if (null === $form) {
            return '';
        }
        $submission->form = $form;
        $model = new \FormInModule\Models\Data();
        $submission->model = $model;
        $field_list = $submission->getFieldList();
        $complete = false;
        $errors = array();
        $has_file = false;
        foreach ($field_list as $field) {
            if ('html' == $field->type) continue;
            $fid = 'fid'.$field->field_id;
            if ($field->id = '') {
                $field->id = $fid;
            }
            if (in_array($field->type, ['text','textarea', 'password', 'hidden', 'email','select','checkbox', 'radio'])) {
                $model->{$fid} = $field->getDefaultvalue();
            } elseif (in_array($field->type, ['selectMultiple', 'checkboxMultiple'])) {
                $model->{$fid} = $field->getDefaultValue();
            } else {
                $model->{$fid} = '';
            }
            if ('file' == $field->type) {
                $has_file = true;
            }
        }
        if (isset($_POST['model'])) {
            $model->setFormData($_POST['model'], $this->container, $form, $field_list);
            if ($has_file && isset($_FILES['model'])) {
                $model->setFormFile($_FILES['model'], $this->container, $form, $field_list);
            }
            $errors = $model->validate($this->container, $form, $field_list);
            if (0 == count($errors)) {
                // Daten Speichern
                $submission->saveData();
                if ('' != $form->callback) {
                    call_user_func_array($form->callback, array($submission));
                }
                // send data email
                $submission->sendDataEmail();
                // send confirm email
                $submission->sendConfirmEmail();

                $complete = true;
            }
        }
        return $this->container->get('RenderService')->render(
            'frontend/'.$form->view, array(
                'form_id' => $form_id,
                'form' => $form,
                'model' => $model,
                'field_list' => $field_list,
                'FormService' => $this->container->get('FormService'),
                'UrlService' => $this->container->get('UrlService'),
                'errors' => $errors,
                'complete' => $complete,
                'Translator' => $this->container->get('TranslatorService'),
                'has_file' => $has_file,
            ));

    }
    public function copyAction($form_id) {
        $repo = $this->container->get('FormRepository');
        $form = $repo->find($form_id);
        if (null !== $form) {
            $copy = clone $form;
            $copy->form_id = null;
            $copy->title = $form->title.' (copy)';
            $repo->insert($copy);
            $fields = $this->container->get('FormFieldRepository')
                ->findBy(array('form_id' => $form_id));
            foreach ($fields as $field) {
                $fcopy = clone($field);
                $fcopy->form_id = $copy->form_id;
                $fcopy->field_id = null;
                $this->container->get('FormFieldRepository')->insert($fcopy);
            }
        }
        header("Location: ". $this->container->get('UrlService')->getUrl('/form/index'));
        die();
    }   
    public function removeAction($form_id) {
        $fields = $this->container->get('FormFieldRepository')
            ->findBy(array('form_id' => $form_id));
        foreach ($fields as $field) {
            $this->container->get('FormFieldRepository')->delete($field);
        }
        $model = $this->container->get('FormRepository')
            ->find($form_id);
        if (null !== $model) {
            $this->container->get('FormRepository')->delete($model);
        }
        header("Location: ". $this->container->get('UrlService')->getUrl('/form/index'));
        die();
    }
}

