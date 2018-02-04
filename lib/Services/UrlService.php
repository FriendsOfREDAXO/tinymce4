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
            $url= 'index.php?page=tinymce4&r='.$path;
            foreach($parameter as $key => $val) {
                $url.= '&'.$key.'='.urlencode($val);
            }
            return $url;
        }
    }
    public function getAjaxUrl($path, $parameter = array()) {
        $url= 'index.php?tinymce4_call='.$path;
        foreach($parameter as $key => $val) {
            $url.= '&'.$key.'='.urlencode($val);
        }
        return $url;
    }

   
}

