<?php
$service_container = Tinymce4\Services\ServiceContainer::getInstance();
$translator = $service_container->get('TranslatorService');
$cur_route = isset($_GET['r']) ?  $_GET['r'] : '/profile/index';
?>
<?php echo rex_view::title('Tinymce4');?>

<div class="nav rex-page-nav"><ul class="nav nav-tabs">
    <li class="<?php if ($cur_route == '/profile/index'):?>active<?php endif;?>">
        <a href="<?php echo rex_url::backend('index.php');?>?page=tinymce4"><?php echo $translator->trans('Profile');?></a>
    </li>
    <li class="<?php if ($cur_route == '/config/edit'):?>active<?php endif;?>">
        <a href="<?php echo rex_url::backend('index.php');?>?page=tinymce4&r=/config/edit"><?php echo $translator->trans('Einstellungen');?></a>
    </li>
    <li class="<?php if ($cur_route == '/config/help'):?>active<?php endif;?>">
        <a href="<?php echo rex_url::backend('index.php');?>?page=tinymce4&r=/config/help"><?php echo $translator->trans('Hilfe');?></a>
    </li>
</ul></div>
<?php 
if (!isset($_GET['r'])){
    $route = '/profile/index';
} else {
    $route = $_GET['r'];
}
echo $service_container->handleRoute($route);
