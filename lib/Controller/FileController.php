<?php

namespace Tinymce4\Controller;

use Tinymce4\Services\ProfileRepository;


class FileController
{
    public    $container;
    protected $tables;
    protected $total;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function testAction()
    {
    }

    public function indexAction()
    {
        $Repository    = new ProfileRepository($this->container);
        $filter        = $this->container->get('FilterService');
        $call          = rex_get('tinymce4_call', 'string', '');
        $type          = rex_get('type', 'string', $call == '/image/index' ? 'media' : 'link');
        $category_id   = rex_get('category_id', 'int', 0);
        $clang_id      = rex_get('clang_id', 'int', \rex_clang::getStartId());
        $onlyFileList  = rex_get('ofl', 'int', 0);
        $search        = trim($filter->filterString(rex_get('search', 'string')));
        $this->profile = $Repository->find(rex_get('mce_profile', 'string'));

        if ('table' == $type) {
            $category_choices = [];

            if ($this->profile) {
                $profile_data = $this->profile->decode();

                if (strlen($profile_data['tables'])) {
                    $this->tables = [];
                    preg_match_all('@(\w+)(\[(\w+:[\s\w+=><!\d]+,?)+\]),?@i', trim(trim(strtr($profile_data['tables'], [
                        '{{CLANG_CURRENT_ID}}' => \rex_clang::getCurrentId(),
                    ]), "'"), '"'), $matches);

                    foreach ($matches[1] as $index => $tablename) {
                        $ytable                   = \rex_yform_manager_table::get($tablename);
                        $category_choices[$index] = $ytable ? $ytable->getName() : $tablename;

                        $data  = ['tablename' => $tablename];
                        $_data = explode(',', trim(trim($matches[2][$index], ']'), '['));

                        foreach ($_data as $row) {
                            list($key, $values) = explode(':', $row);
                            $data[$key] = array_filter(explode('+', $values));
                        }
                        $this->tables[$index] = array_filter($data);
                    }
                }
            }
        } else if ('link' == $type) {
            $category_choices = $this->container->get('ArticleRepository')
                ->getCategoryChoices($clang_id);
        } else {
            $category_choices = $this->container->get('MediaCategoryRepository')
                ->getCategoryChoices();
        }

        $list_content = $this->listAction();

        if ($onlyFileList) {
            return $list_content;
        } else {
            return $this->container->get('RenderService')
                ->render('frontend/file_index.php', [
                    'list_content'     => $list_content,
                    'total'            => $this->total,
                    'UrlService'       => $this->container->get('UrlService'),
                    'Translator'       => $this->container->get('TranslatorService'),
                    'form'             => $this->container->get('FormService'),
                    'category_id'      => $category_id,
                    'type'             => $type,
                    'category_choices' => $category_choices,
                    'language_choices' => $this->container->get('LanguageService')
                        ->getLanguageChoices(),
                    'clang_id'         => $clang_id,
                    'profile'          => $this->profile,
                    'search'           => $search,
                ]);
        }
    }

    public function listAction()
    {
        $offset      = 0;
        $limit       = 25;
        $page        = rex_get('page', 'int', 0);
        $call        = rex_get('tinymce4_call', 'string', '');
        $type        = rex_get('type', 'string', $call == '/image/index' ? 'media' : 'link');
        $category_id = rex_get('category_id', 'int', 0);
        $clang_id    = rex_get('clang_id', 'int', \rex_clang::getStartId());
        $filter      = $this->container->get('FilterService');
        $search      = trim($filter->filterString(rex_get('search', 'string')));

        if ($page) {
            $offset = $page * $limit;
        }


        if ('link' == $type) {
            if (-1 == $category_id) {
                // Alle
                $sql   = " clang_id=?";
                $binds = [$clang_id];
            } else if (0 == $category_id) {
                $sql   = "parent_id=? AND startarticle=? and clang_id=?";
                $binds = [$category_id, 0, $clang_id];
            } else {
                $sql   = "(
                       ( id=? AND startarticle=1)
                       OR
                       ( parent_id=? AND startarticle=0)
                   )
                    AND clang_id=?
                    ";
                $binds = [$category_id, $category_id, $clang_id];
            }
            $page_list = $this->container->get('ArticleRepository')
                ->findWhere($sql, $binds, ['priority' => 'ASC']);
            $total     = $this->container->get('ArticleRepository')
                ->countWhere($sql, $binds);
            $link_list = [];
            foreach ($page_list as $p) {
                $link_list[] = [
                    'url'  => 'redaxo://' . $p->id . '-' . $clang_id,
                    'name' => $p->name,
                ];
            }
            $category_choices = $this->container->get('ArticleRepository')
                ->getCategoryChoices($clang_id);
        } else if ('media' == $type) {
            $where  = [1];
            $params = [];

            if ($category_id) {
                $where[]         = "category_id = :catId";
                $params['catId'] = $category_id;
            }
            if (strlen($search)) {
                $where[] = "(
                    filename LIKE :searchTerm
                    OR originalname LIKE :searchTerm
                    OR title LIKE :searchTerm
                )";

                $params['searchTerm'] = "%{$search}%";
                $params['searchTerm'] = "%{$search}%";
                $params['searchTerm'] = "%{$search}%";
            }

            $sql  = \rex_sql::factory();
            $from = "
                FROM ".\rex::getTable('media')."
                WHERE " . implode(' AND ', $where) . "
                ORDER BY createdate DESC
            ";

            $_total      = $sql->getArray("SELECT COUNT(id) AS cnt {$from}", $params);
            $this->total = $_total[0]['cnt'];

            $query = "
                SELECT 
                    id, 
                    filetype,
                    filename,
                    CONCAT('/media/', filename) AS url, 
                    CONCAT(
                        IF(
                            filetype = 'image/jpeg',
                            CONCAT(
                                '<img class=\"thumbnail\" style=\"float:left;margin:0 10px 0 0;\" src=\"index.php?rex_media_type=rex_mediapool_preview&rex_media_file=',
                                filename,
                                '&buster=',
                                updatedate,
                                '\">'
                            ),
                            IF (
                                filetype = 'image/png',
                                CONCAT(
                                    '<img class=\"thumbnail\" style=\"float:left;margin:0 10px 0 0;\" src=\"index.php?rex_media_type=rex_mediapool_preview&rex_media_file=',
                                    filename,
                                    '&buster=',
                                    updatedate,
                                    '\">'
                                ),
                                IF (
                                    filetype = 'image/gif',
                                    CONCAT(
                                        '<img class=\"thumbnail\" style=\"float:left;margin:0 10px 0 0;\" src=\"index.php?rex_media_type=rex_mediapool_preview&rex_media_file=',
                                        filename,
                                        '&buster=',
                                        updatedate,
                                        '\">'
                                    ),
                                    IF (
                                        filetype = 'image/svg+xml',
                                        CONCAT(
                                            '<img class=\"thumbnail\" style=\"max-width:90px;max-height:100px;float:left;margin:0 10px 0 0;\" src=\"../media/',
                                            filename,
                                            '?buster=',
                                            updatedate,
                                            '\">'
                                        ),
                                        '<i class=\"rex-mime rex-mime-pdf\" style=\"float:left;margin:0 10px 0 0;\" title=\"\" data-extension=\"pdf\"></i>'
                                    )
                                )
                            )
                        ),
                        CONCAT(
                          IF (LENGTH(title), 
                              CONCAT(title, ' [', filename, ']'),
                              filename
                          ), 
                          '<br/><span style=\"color:#777\">', DATE_FORMAT(createdate, '%d. %b %Y - %H:%i'), 'h</span><div style=\"clear:both;\"></div>'
                        ) 
                    ) AS name 
                {$from}
                LIMIT {$offset}, {$limit}
            ";
            echo "<!-- Media-Query: {$query} -->";
            $link_list = $sql->getArray($query, $params);
        } else if ('table' == $type) {
            if (count($this->tables)) {
                $params = [];
                $table  = $this->tables[$category_id] ?: $this->tables[0];
                $where  = array_merge([1], (array)$table['filter']);
                $order  = strlen($table['order'][0]) ? $table['order'][0] : $table['fields'][0] . ' ASC';

                if (strlen($search)) {
                    $where[] = implode(" LIKE '%{$search}%' OR ", $table['fields']) . " LIKE '%{$search}%'";
                }

                $sql  = \rex_sql::factory();
                $from = "
                    FROM {$table['tablename']}
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY {$order}
                ";

                $_total      = $sql->getArray("SELECT COUNT(id) AS cnt {$from}", $params);
                $this->total = $_total[0]['cnt'];

                $query = "
                    SELECT 
                        id, 
                        CONCAT('table://{$table['tablename']}-', id, '-{$clang_id}') AS url, 
                        CONCAT(" . implode('," ",', $table['fields']) . ") AS name 
                    {$from}
                    LIMIT {$offset}, {$limit}
                ";
                echo "<!-- Table-Data: " . print_r($table, true) . " -->";
                echo "<!-- Table-Query: {$query} -->";
                $link_list = $sql->getArray($query);
            }
        }

        return $this->container->get('RenderService')
            ->render('frontend/file_list.php', [
                'link_list'   => $link_list,
                'UrlService'  => $this->container->get('UrlService'),
                'Translator'  => $this->container->get('TranslatorService'),
                'form'        => $this->container->get('FormService'),
                'category_id' => $category_id,
                'type'        => $type,
                'clang_id'    => $clang_id,
                'search'      => $search,
                'total'       => $this->total,
                'offset'      => $offset,
                'page'        => $page,
            ]);
    }

}


