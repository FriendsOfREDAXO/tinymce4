<?php
spl_autoload_register(function ($class) {
    $prefix = 'Tinymce4';
    $base_dir = dirname(__FILE__).'/src';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

if (rex::isBackend()) {
    // Tinymce core
    rex_view::addJsFile(rex_url::addonAssets('tinymce4', 'tinymce4/tinymce.min.js'));
    $user = \rex::getUser();
    if ($user) {
        $lang = $user->getLanguage();
        if ('' == $lang) {
            $lang = strtolower($dbconfig = \rex::getProperty('lang'));
        }
        $service_container = Tinymce4\Services\ServiceContainer::getInstance();
        $map = $service_container->getParameter('be_lang_map');
        if (!isset($map[$lang])) {
            $lang_pack = 'en_US';
        } else {
            $lang_pack = $map[$lang];
        } 
        // Tinymce Übersetzungen laden
        rex_view::addJsFile(rex_url::addonAssets('tinymce4', 'tinymce4/langs/'.$lang_pack.'.js'));
        rex_view::addJsFile('index.php?tinymce4_call=/asset_js/'.urlencode(
            'tinymce4_init.'.$lang_pack.'.js'));
    }
}

rex_extension::register('PACKAGES_INCLUDED', function($ep) {
    if (isset($_GET['tinymce4_call'])) {
        $service_container = Tinymce4\Services\ServiceContainer::getInstance();
        echo $service_container->handleRoute($_GET['tinymce4_call']);
        die();
    }
});
