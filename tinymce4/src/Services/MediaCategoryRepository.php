<?php
namespace Tinymce4\Services;
use Tinymce4\Classes\Repository;

class MediaCategoryRepository extends Repository
{
    public $container;
    public $table = 'rex_media_category';
    public $primary = 'id';
    public $model = '\Tinymce4\Models\MediaCategory';

    
    public function getCategoryChoices() {
        $list = $this->findAll();
        $a = array();
        $a[0] = 'Alle';
        foreach ($list as $cat) {
            $a[$cat->id] = $cat->name;
        }
        return $a;
    }
}

