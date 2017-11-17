<?php
namespace Tinymce4\Models;

class MediaCategory
{
    public $id;
    public $name;
    public $parent_id = '';
    public $path = '';
    public $attributes = '';

    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        
    }

    public function validate($container) 
    {
        $errors = array();
       

        return $errors;
    }

}
