<?php
namespace Tinymce4\Services;

class TranslatorService
{
    public $pathes = array(); 
    public $loaded = array();
    public $container;

    public function __construct($container){
        $this->container = $container;
        $this->pathes = $container->getParameter('translator_pathes');
    }
    
    public function trans($string, $group = 'messages', $locale = null){
        if(null === $locale){
            $locale = $this->container->get('LanguageService')->getLocale();
        }
        if(!isset($this->loaded[$group][$locale])){
            $a = array();
            foreach($this->pathes as $path){
                $file = $path.'/'.$group.'.'.$locale.'.php';
                if(file_exists($file)){
                    $a[] = include($file);
                }
            }
            $this->loaded[$group][$locale] = array();
            foreach($a as $s){
                $this->loaded[$group][$locale] = array_merge($this->loaded[$group][$locale], $s);
            }
        }
        if(isset($this->loaded[$group][$locale][$string])){
            return $this->loaded[$group][$locale][$string];
        }
        return $string;
    }
}

