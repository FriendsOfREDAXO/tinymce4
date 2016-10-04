<?php include 'top.php'; ?>
<div class="col-xs-12">
<form 
    class="form"
    method="GET" 
    action="index.php"
    id="category_selection"
>
<input type="hidden" name="tinymce4_call" value="/file/index" />
<label>Link-Typ</label>
<div class="form-group">
<?php echo $form->select('type', array('link' => 'Seite', 'media' => 'Datei'),
    $type, array(
        'onchange' => 'setType()',
        'class' => 'form-control',
    ));?>
</div>
<div class="form-group">
<label>Sprache</label>
<?php echo $form->select('clang_id', $language_choices, $clang_id, array(
        'onchange' => 'setType()',
        'class' => 'form-control',
    ));?>

</div>
<div class="form-group">
<label>Kategorie</label>
<?php echo $form->select('category_id', $category_choices, $category_id, array(
    'onchange' => 'reload()',
        'class' => 'form-control',
));?>
</div>
</form>
<ul class="list-group">
<?php foreach ($link_list as $link):?>
<li class="list-group-item">
<a href="" onclick="returnFile(this)"
data-value="<?php echo $link['url']?>"
><?php echo $link['name'];?></a><br/>
</li>
<?php endforeach;?>
</ul>
</div>

<script type="text/javascript">
var win = top.tinymce.activeEditor.windowManager.getParams().window;
var field_name = top.tinymce.activeEditor.windowManager.getParams().input;

function returnFile(element) {
    win.document.getElementById(field_name).value = element.dataset.value;
    win.tinymce.activeEditor.windowManager.close();
    return false;

}
function setType(){
    document.querySelector('select[name="category_id"] option[value="0"]').selected = 'selected';
    reload();
    return false;
}
function reload(){
    document.getElementById('category_selection').submit();
    return false;
}

</script>

<?php include 'bottom.php'; ?>
