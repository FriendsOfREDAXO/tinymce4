<?php
namespace Tinymce4\Models;

class Profile
{
    public $id = 0;
    public $name = '';
    public $json = '';

    public function __construct(){
        $content_css = \rex_url::addonAssets('tinymce4', 'bootstrap/css/bootstrap.min.css');
        $this->json = "{
            selector: 'textarea.tinyMCEEditor',
            file_browser_callback: redaxo5FileBrowser,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste code',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            convert_urls: false,
            content_css: '$content_css',
            }";
    }
    
    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        if (isset($data['name'])){
            $this->name = trim($fs->filterString($data['name']));
        }
        if (isset($data['json'])){
            $this->json = trim($fs->filterText($data['json']));
        }
    }
    public function validate($container) {
        $errors = array();
        if ('' == trim($this->name)) {
            $errors['name'] = 'Input required';
        }
        if ('' == $this->json) {
            $errors['json'] = 'Input required';
        }
        // kann so nicht gehen, weil das json javascript-Funktionen enthalten kann
        // Muss bei der Eingabe geprüft werden
        /*
        else{
            $decoded = json_decode($this->json);
            if (null === $decoded) {
                $errors['json'] = 'Json not valid';
            } 
        }
         */
        
        return $errors;
    }

    // Gibt die Daten mit dem Label zurück
    public function hydrate($data, $container) {
        if (isset($data['id'])){
            $this->id = intval($data['id']);
        }
        if (isset($data['name'])){
            $this->name = $data['name'];
        }
        if (isset($data['json'])){
            $this->json = $data['json'];
        }
    }
    
}

