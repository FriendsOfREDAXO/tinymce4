<?php
namespace Tinymce4\Controller;

class MediaController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function indexAction() {
        $filter = $this->container->get('FilterService');
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $search = isset($_GET['search']) ? trim($filter->filterString($_GET['search'])) : '';
        $sql = "filetype LIKE 'video%'";
        $binds = array();
        foreach (explode(' ', $search) as $s) {
            if ('' == $s) continue;
            $sql.= " AND (
                `filename` LIKE ? 
                OR `originalname` LIKE ?
                OR `title` LIKE ?
                )
                ";
            $binds[] = '%'.$s.'%';
            $binds[] = '%'.$s.'%';
            $binds[] = '%'.$s.'%';
        }
        if (0 < $category_id) {
            $sql.=" AND category_id=?";
            $binds[] = $category_id;
        }
        $media = $this->container->get('MediaRepository')
            ->findWhere($sql, $binds, array('originalname'=>'ASC'));

        return $this->container->get('RenderService')->render(
            'frontend/media_index.php', array(
                'media_list' => $media,
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'category_choices' => $this->container->get('MediaCategoryRepository')->getCategoryChoices(),
                'form' => $this->container->get('FormService'),
                'category_id' => $category_id,
                'search' => $search,
            ));
    }
    
}


