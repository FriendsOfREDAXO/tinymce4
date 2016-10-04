<?php
namespace Tinymce4\Services;

class LanguageService
{
    public $container;

    public function __construct($container){
        $this->container = $container;
    }
    
    public function getLocale() {

        $clang = \rex_clang::getCurrent();
        return $clang->getCode();
    }
    public function getLanguageId() {
        return \rex_clang::getCurrentId();
    }
    public function getLanguageChoices() {
        $language_choices = array();
        foreach (\rex_clang::getAll() as $clang) {
            $language_choices[$clang->getId()] = $clang->getValue('name');
        }
        return $language_choices;
    }
    
}

