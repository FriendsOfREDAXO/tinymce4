<?php
$service_container = Tinymce4\Services\ServiceContainer::getInstance();
$translator = $service_container->get('TranslatorService');
if (isset($_GET['r']) && 0 === strpos($_GET['r'], '/config')) {
    $config = true;
} else {
    $config = false;
}
?>
<?php echo rex_view::title('Tinymce4');?>

<div class="nav rex-page-nav"><ul class="nav nav-tabs">
    <li class="<?php if (!$config):?>active<?php endif;?>">
        <a href="/redaxo/index.php?page=tinymce4"><?php echo $translator->trans('Profile');?></a>
    </li>
    <li class="<?php if ($config):?>active<?php endif;?>">
        <a href="/redaxo/index.php?page=tinymce4&r=/config/edit"><?php echo $translator->trans('Einstellungen');?></a>
    </li>
</ul></div>
<?php 
if (!isset($_GET['r'])){
    $route = '/profile/index';
} else {
    $route = $_GET['r'];
}
echo $service_container->handleRoute($route);
