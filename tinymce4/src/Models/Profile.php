<?php
namespace Tinymce4\Models;

class Profile
{
    public $id = 0;
    public $selector = '';
    public $plugins = '';
    public $toolbar = ''; 
    
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
    }
    public function validate($container, $form, $field_list) {
        $errors = array();
        if ('' == trim($this->selector)) {
            $errors['selector'] = 'Input required';
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
    }
    
}
