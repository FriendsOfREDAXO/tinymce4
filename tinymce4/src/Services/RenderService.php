<?php
namespace Tinymce4\Services;

class RenderService
{
    public $container;
    public $pathes;

    public function __construct($container)
    {
        $this->container = $container;
        $this->pathes = $container->getParameter('render_pathes');
    }

    public function render($__view, $__params = array()){
        foreach($this->pathes as $__path){
            if(file_exists($__path.'/'.$__view)){
                ob_start();
                extract($__params);
                include $__path.'/'.$__view;
                return ob_get_clean();
            }
        }
        return $__view.' not found';
        
    }
    
}

