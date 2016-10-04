<?php
namespace Tinymce4\Models;

class Media
{
    public $id;
    public $category_id;
    public $attributes = '';
    public $filetype = '';
    public $filename = '';
    public $originalname = '';
    public $filesize = '';
    public $width = '';
    public $height = 0;
    public $title = '';

    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        
    }

    public function validate($container) 
    {
        $errors = array();
       

        return $errors;
    }

}
