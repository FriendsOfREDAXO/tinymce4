<?php
namespace Tinymce4\Services;

class UrlService
{
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getUrl($path, $parameter = array()){
        if (\rex::isBackend()) {
            return 'index.php?page=tinymce4&r='.$path;
        }
    }

   
}

