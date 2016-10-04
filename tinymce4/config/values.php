<?php

$dbconfig = \rex::getProperty('db');
$DBID = 1;
$container->setParameter('data_dir', __DIR__.'/../../../../data/addons/tinymce4');
$container->setParameter('db_name',  $dbconfig[$DBID]['name']);
$container->setParameter('db_host',  $dbconfig[$DBID]['host']);
$container->setParameter('db_user',  $dbconfig[$DBID]['login']);
$container->setParameter('db_pass',  $dbconfig[$DBID]['password']);
$container->setParameter('render_pathes',  array(
    __DIR__.'/../views',
    $container->getParameter('data_dir').'/views',
));
$container->setParameter('translator_pathes',  array(
    __DIR__.'/../translations',
    $container->getParameter('data_dir').'/translations',
));

