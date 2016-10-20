<?php
namespace Tinymce4\Models;

class Config
{
    public $content_css = 'default';
    public $image_format = 'default';
    public $media_format = 'default';
    
    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        if (isset($data['content_css'])){
            $this->content_css = trim($fs->filterString($data['content_css']));
            if ('' == trim($this->content_css)){
                $this->content_css = 'default';
            }
        }
        if (isset($data['image_format'])){
            $this->image_format = trim($fs->filterString($data['image_format']));
            if ('' == trim($this->image_format)){
                $this->image_format = 'default';
            }
        }
        if (isset($data['media_format'])){
            $this->media_format = trim($fs->filterString($data['media_format']));
            if ('' == trim($this->media_format)){
                $this->media_format = 'default';
            }
        }
    }
    public function validate($container) {
        $errors = array();
        
        
        return $errors;
    }

    // Gibt die Daten mit dem Label zurück
    public function hydrate() {
        $this->content_css = \rex_config::get('tinymce4', 'content_css');
        $this->image_format = \rex_config::get('tinymce4', 'image_format');
        $this->media_format = \rex_config::get('tinymce4', 'media_format');
    }

    public function save() {
        \rex_config::set('tinymce4', 'content_css', $this->content_css);
        \rex_config::set('tinymce4', 'image_format', $this->image_format);
        \rex_config::set('tinymce4', 'media_format', $this->media_format);
    }
    
}
