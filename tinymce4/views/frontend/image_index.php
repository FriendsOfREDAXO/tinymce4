<?php include 'top.php'; ?>
<div class="col-xs-12">
<form 
    method="GET" 
    class="form"
    action="index.php"
    id="category_selection"
>
<input type="hidden" name="tinymce4_call" value="/image/index" />
<div class="form-group" style="padding-top: 10px;">
<?php echo $form->select('category_id', $category_choices, $category_id, array(
    'onchange' => 'reload()',
    'class' => 'form-control',

));?>
</div>
<div class="form-group">
<?php echo $form->text('search', $search, array(
    'onchange' => 'reload()',
    'class' => 'form-control',
    'placeholder' => 'Suche',

));?>
</div>
</form>

<?php if (0 == count($media_list)):?>
Keine Bilder gefunden
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

<script type="text/javascript">
var win = top.tinymce.activeEditor.windowManager.getParams().window;
var field_name = top.tinymce.activeEditor.windowManager.getParams().input;

function returnImage(element) {
    win.document.getElementById(field_name).value = element.dataset.value;
    win.tinymce.activeEditor.windowManager.close();
    return false;
}
function reload(){
    document.getElementById('category_selection').submit();
    return false;
}

</script>
<?php include 'bottom.php'; ?>
