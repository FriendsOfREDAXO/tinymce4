<?php
namespace FormInModule\Services;
use FormInModule\Classes\Repository;

class FormRepository extends Repository
{
    public $container;
    public $table = 'rex_form_in_module';
    public $primary = 'form_id';
    public $model = '\FormInModule\Models\Form';
    public $multilang = false;
    
    public function getViewChoices(){
        $choices = array();
        foreach ($this->container->getParameter('render_pathes') as $k => $dir) {
            $dir = $dir.'/frontend';
            if (!is_dir($dir)) continue;
            $a = scandir($dir);
            foreach (scandir($dir) as $f) {
                if (in_array($f, ['.','..'])) continue;
                $choices[$f] = $f;
            }
        }
        return $choices;
    }
}

