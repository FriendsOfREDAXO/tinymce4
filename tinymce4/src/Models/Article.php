<?php
namespace Tinymce4\Models;

class Article
{
    public $pid;
    public $id;
    public $parent_id;
    public $name = '';
    public $catname = '';
    public $filename = '';
    public $clang_id = '';

    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        
    }

    public function validate($container) 
    {
        $errors = array();
       

        return $errors;
    }

}
