<?php
namespace Tinymce4\Services;

class ProfileRepository
{
    public $container;

    public function findAll() {
        $v = \rex_config::get('tinymce4', 'profiles');
        $a = unserialize($v);
        if (!is_array($a)) {
            $a = array();
        }
        $r = array();
        foreach ($a as $data) {
            $mod  = $this->getModel($data);
            if (null!== $mod) {
                $r[] = $mod;
            }
        }
        return $r;

    }
    public function find($id) {
        foreach ($this->findAll() as $mod){
            if ($id == $mod->id) {
                return $mod;
                break;
            }
        }
        return null;
    }

    public function insert($model){
        $v = \rex_config::get('tinymce4', 'profiles');
        $a = unserialize($v);
        if (!is_array($a)) {
            $a = array();
        }
        $a[] = get_object_vars($model);
        \rex_config::set('tinymce4', 'profiles',serialize($a));
    }
    public function update($model) {
        $v = \rex_config::get('tinymce4', 'profiles');
        $a = unserialize($v);
        if (!is_array($a)) {
            return;
        }
        foreach ($a as $key=>$mod) {
            if (intval($mod['id']) == intval($model->id)) {
                $a[$key] = get_object_vars($model);
                break;
            }
        }
        \rex_config::set('tinymce4', 'profiles',serialize($a));
    }
    public function remove($model) {
        $v = \rex_config::get('tinymce4', 'profiles');
        $a = unserialize($v);
        if (!is_array($a)) {
            return;
        }
        foreach ($a as $key=>$mod) {
            if (intval($mod['id']) == intval($model->id)) {
                unset($a[$key]);
                break;
            }
        }
        \rex_config::set('tinymce4', 'profiles',serialize($a));
    }

    public function getModel($data) {
        if (!is_array($data) ) {
            return null;
        }
        $obj = new \Tinymce4\Models\Profile();
        $obj->hydrate($data, $this->container);
        return $obj;
    }
}

