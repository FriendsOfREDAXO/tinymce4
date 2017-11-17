<?php include 'top.php'; ?>
<div class="col-xs-12">
<form 
    method="GET" 
    class="form"
    action="index.php"
    id="category_selection"
>
<input type="hidden" name="tinymce4_call" value="/media/index" />
<div class="form-group" style="padding-top:10px">
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

<?php echo $list_content;?>

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
function loadMore(more_link){
    var href = more_link.href;
    more_link.innerHTML = '...';
    more_link.onclick='return false;';
    $.get(href, function(dat) {
        $(more_link).replaceWith(dat);
    });
    return false;
}


</script>
<?php include 'bottom.php'; ?>
