<?php

if (rex::isBackend() && null !== rex::getUser()) {
    $container->addRoute('/test', '\Tinymce4\Controller\FileController:testAction');
    $container->addRoute('/image/index', '\Tinymce4\Controller\ImageController:indexAction');
    $container->addRoute('/image/list', '\Tinymce4\Controller\ImageController:listAction');
    $container->addRoute('/file/index', '\Tinymce4\Controller\FileController:indexAction');
    //$container->addRoute('/file/list', '\Tinymce4\Controller\FileController:listAction');
    $container->addRoute('/media/index', '\Tinymce4\Controller\MediaController:indexAction');
    $container->addRoute('/media/list', '\Tinymce4\Controller\MediaController:listAction');
    $container->addRoute('/profile/index', '\Tinymce4\Controller\ProfileController:indexAction');
    $container->addRoute('/profile/edit', '\Tinymce4\Controller\ProfileController:editAction');
    $container->addRoute('/profile/remove', '\Tinymce4\Controller\ProfileController:removeAction');
    $container->addRoute('/config/edit', '\Tinymce4\Controller\ConfigController:editAction');
    $container->addRoute('/config/help', '\Tinymce4\Controller\ConfigController:helpAction');
    $container->addRoute('/config/createmodule', '\Tinymce4\Controller\ConfigController:createModuleAction');
}

