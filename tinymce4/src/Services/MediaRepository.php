<?php
namespace Tinymce4\Services;
use Tinymce4\Classes\Repository;

class MediaRepository extends Repository
{
    public $container;
    public $table = '';
    public $primary = 'id';
    public $model = '\Tinymce4\Models\Media';
    public $multilang = false;

    public function __construct($container) {
        parent::__construct($container);
        $this->table = \rex::getTablePrefix().'media';
    }
    
}

