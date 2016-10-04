<?php
echo rex_view::title('Tinymce4');
$service_container = Tinymce4\Services\ServiceContainer::getInstance();
if (!isset($_GET['r'])){
    $route = '/index';
} else {
    $route = $_GET['r'];
}
echo $service_container->handleRoute($route);
