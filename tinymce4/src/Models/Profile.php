<?php
namespace Tinymce4\Models;

class Profile
{
    public $id = 0;
    public $selector = '';
    public $plugins = '';
    public $toolbar = ''; 
    public $init = ''; 
    
    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        if (isset($data['selector'])){
            $this->selector = trim($fs->filterString($data['selector']));
        }
        if (isset($data['plugins'])){
            $this->plugins = trim($fs->filterString($data['plugins']));
        }
        if (isset($data['toolbar'])){
            $this->toolbar = trim($fs->filterString($data['toolbar']));
        }
        if (isset($data['initparams'])){
            $this->initparams = trim($fs->filterText($data['initparams']));
        }
    }
    public function validate($container, $form, $field_list) {
        $errors = array();
        if ('' == trim($this->selector)) {
            $errors['selector'] = 'Input required';
        }
        if ('' != $this->initparams) {
            if (',' == substr($this->initparams, -1)) {
                $errors['initparams'] = 'Komma am Ende weglassen';
            }
            if (',' == substr($this->initparams, 0, 1)) {
                $errors['initparams'] = 'Komma am Anfang weglassen';
            }
        }
        
        return $errors;
    }

    // Gibt die Daten mit dem Label zurück
    public function hydrate($data, $container) {
        if (isset($data['id'])){
            $this->id = intval($data['id']);
        }
        if (isset($data['selector'])){
            $this->selector = $data['selector'];
        }
        if (isset($data['plugins'])){
            $this->plugins = $data['plugins'];
        }
        if (isset($data['toolbar'])){
            $this->toolbar = $data['toolbar'];
        }
        if (isset($data['initparams'])){
            $this->initparams = $data['initparams'];
        }
    }
    
}
