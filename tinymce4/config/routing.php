<?php

if (rex::isBackend()) {
    $container->addRoute('/image/index', '\Tinymce4\Controller\ImageController:indexAction');
    $container->addRoute('/file/index', '\Tinymce4\Controller\FileController:indexAction');
    $container->addRoute('/link/index', '\Tinymce4\Controller\DataController:indexAction');
}

