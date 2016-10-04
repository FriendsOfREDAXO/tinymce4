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
        $choices = array(
            0 => 'Root',
        );
        $list = $this->findBy(array(
            'startarticle' => 1,
            'clang_id' => $clang_id,
        ), array('priority' => 'ASC', 'path' => 'ASC'));
        foreach ($list as $cat) {
            $choices[$cat->id] = $cat->catname;
        }
        return $choices;

    }
    
}

