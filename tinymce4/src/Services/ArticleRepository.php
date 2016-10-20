<?php
namespace Tinymce4\Services;
use Tinymce4\Classes\Repository;

class ArticleRepository extends Repository
{
    public $container;
    public $table = 'rex_article';
    public $primary = 'pid';
    public $model = '\Tinymce4\Models\Article';

    public function getCategoryChoices($clang_id) {
        
        $list = $this->findBy(array(
            'startarticle' => 1,
            'clang_id' => $clang_id,
        ), array('path' => 'ASC', 'priority' => 'ASC'));
        $cat_idx = array();
        foreach ($list as $cat) {
            $cat_idx[$cat->parent_id][] = $cat;
        }
        $tree = array(
            -1 => 'Alle',
            0 => 'Homepage',
        );

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
        $tree[$cat->id] = str_repeat('-', $count-1).$cat->catname;
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

