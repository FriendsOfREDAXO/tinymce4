<div class="row">

<?php if (0 == $offset && 0 == count($media_list)):?>
<div class="col-xs-12">Keine gefunden</div>
<?php endif;?>

<?php foreach ($media_list as $medium):?>
<div class="col-xs-3">
<a style="cursor:pointer;width:80px;height:80px;display:inline-block;background-repeat:no-repeat;background-position:center center;background-image:url(
    index.php?rex_media_type=rex_mediapool_preview&rex_media_file=<?php
    echo urlencode($medium->filename);?>);" 
    title="<?php echo $medium->originalname;?> | <?php echo $medium->title;?>"
    data-value="index.php?rex_media_type=tinymcewysiwyg&rex_media_file=<?php
    echo urlencode($medium->filename);?>" 
    onclick="returnImage(this)" ></a>
</div>
<?php endforeach;?>
</div>

<?php if ($total > $offset+$limit):?>
    <a class="btn btn-default btn-block" href="<?php echo $UrlService->getAjaxUrl('/image/list', array(
        'offset' => $offset + $limit,
        'limit' => $limit,
        'search' => $search, 
        'category_id' => $category_id,
    ));?>" onclick="return loadMore(this)">Mehr</div>
<?php endif;?>
