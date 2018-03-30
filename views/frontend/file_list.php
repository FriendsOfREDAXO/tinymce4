<?php die('deprecated. not used any more');?>
<?php if (0 == $offset && 0 == count($link_list)) :?>
<div class="alert alert-info">
    Keine gefunden 
</div>
<?php endif;?>

<?php foreach ($link_list as $link):?>
<li class="list-group-item">
<a href="" onclick="returnFile(this)"
data-value="<?php echo $link['url']?>"
><?php echo $link['name'];?></a><br/>
</li>
<?php endforeach;?>
<?php if ($total > $offset+$limit):?>
    <a class="btn btn-default btn-block" 
        href="<?php echo $UrlService->getAjaxUrl('/file/list', array(
        'type' =>$type,
        'clang_id' => $clang_id,
        'offset' => $offset + $limit,
        'limit' => $limit,
        'search' => $search, 
        'category_id' => $category_id,
    ));?>" onclick="return loadMore(this)">Mehr</div>
<?php endif;?>

