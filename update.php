<?php
// Zur Prüfung der korrekten Funktion bei install/update
// (DEV only, do not uncomment these lines)
/*
$profiles = array(
    array(
    'id' => time(),
    'selector' =>'test',
    'plugins' => 'testplugins',
    'toolbar' => 'testtoolbar',
    'initparams' => 'testinitparams',
)
);
rex_config::set('tinymce4', 'profiles', serialize($profiles));
 */

if(null === rex_config::get('tinymce4', 'profiles')){
    include_once __DIR__.'/lib/Models/Profile.php';
    $profile = new Tinymce4\Models\Profile();
    // Gespeichert als array, darum umformen
    $profiles = array(array(
        'id' => time(),
        'name' => 'default',
        'json' => $profile->json,
    ));
    rex_config::set('tinymce4', 'profiles', serialize($profiles));
} else {
    $profiles = unserialize(rex_config::get('tinymce4', 'profiles'));
    foreach ($profiles as $i => $profile) {
        // Wenn profile.json vorhanden, dann hat das update schon stattgefunden
        if (isset($profile['json'])) continue;
        $content_css = rex_url::addonAssets('tinymce4', 'bootstrap/css/bootstrap.min.css');
        if (!isset($profile['initparams'])) {
            // Kann fehlen, sehr frühe Version
            $initparams = '';
        } else {
            $initparams = '' == trim($profile['initparams']) ? '' : ",\n".$profile['initparams'];
        }
        $profiles[$i]= array(
            'id' => $profile['id'],
            'name' => 'Profile '.($i+1),
            'json' => "{
                selector: '{$profile['selector']}',
                file_browser_callback: redaxo5FileBrowser,
                convert_urls: false,
                content_css: '$content_css',
                plugins: '{$profile['plugins']}',
                toolbar: '{$profile['toolbar']}' $initparams
                }",
        );
    }
    rex_config::set('tinymce4', 'profiles', serialize($profiles));
}
/*
if(null === rex_config::get('tinymce4', 'content_css')){
    rex_config::set('tinymce4', 'content_css', 'default');
}
 */
if(null === rex_config::get('tinymce4', 'image_format')){
    rex_config::set('tinymce4', 'image_format', 'default');
}
if(null === rex_config::get('tinymce4', 'media_format')){
    rex_config::set('tinymce4', 'media_format', 'default');
}

$service_container = Tinymce4\Services\ServiceContainer::getInstance();
$service_container->get('ProfileRepository')->rebuildInitScripts();
