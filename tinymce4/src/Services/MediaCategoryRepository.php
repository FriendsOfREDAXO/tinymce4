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
        $list = $this->findAll(array('path' => 'ASC', 'name' => 'ASC'));
        $cat_idx = array();
        foreach ($list as $cat) {
            $cat_idx[$cat->parent_id][] = $cat;
        }
        $tree = array();
        $tree[0] = 'Alle';

        foreach ($list as $cat) {
            if (0 != $cat->parent_id) continue;
            $elements = $this->getSubTree($cat_idx, $cat);
            foreach ($elements as $key=>$name) {
                $tree[$key] = $name;
            }

        }
        return $tree;
    }

    public function getSubTree($cat_idx, $cat) {
        $tree = array();
        $count = substr_count($cat->path, '|');
        $tree[$cat->id] = str_repeat('-', $count-1).$cat->name;
        if (isset($cat_idx[$cat->id])) {
            foreach ( $cat_idx[$cat->id] as $subcat) {
                $elements = $this->getSubTree($cat_idx, $subcat);
                foreach ($elements as $key=>$name) {
                    $tree[$key] = $name;
                }
            }
        }
        return $tree;
    }
}

