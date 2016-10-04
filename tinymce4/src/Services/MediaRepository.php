<?php
namespace Tinymce4\Services;
use Tinymce4\Classes\Repository;

class MediaRepository extends Repository
{
    public $container;
    public $table = 'rex_media';
    public $primary = 'id';
    public $model = '\Tinymce4\Models\Media';
    public $multilang = false;

    
}

