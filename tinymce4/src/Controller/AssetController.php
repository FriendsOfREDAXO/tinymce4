<?php
namespace Tinymce4\Controller;

class AssetController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function jsAction($file) {
        $regex = '/^tinymce4_init\.([a-z_]+)\.js$/';
        if (!preg_match($regex, $file, $matches)){
            return 'not found';
        }
        $lang_pack = $matches[1];

        header('Content-Type: application/javascript');
        
        return $this->container->get('RenderService')->render(
            'frontend/tinymce4_init.php', array(
                'lang_pack' => $lang_pack,
                'profiles' => $this->container->get('ProfileRepository')->findAll(),
            ));
    }
    public function cssAction($file) {
        die('tst');
        header('Content-Type: text/css');
        return $this->container->get('RenderService')->render(
            'frontend/tinymce4_css.php', array(
            ));
    }
    
}


