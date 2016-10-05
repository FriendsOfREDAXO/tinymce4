<form class="form"
    action="<?php echo $UrlService->getUrl('/profile/edit/'.$id);?>"
    method="POST"
    >
<div class="text-right">
    <button type="submit" 
        class="btn btn-success btn-xs"
        ><?php echo $Translator->trans('Save', 'backend');?></button>
    <a class="btn btn-default btn-xs btn-warning"
        href="<?php echo $UrlService->getUrl('/profile/index'); ?>"
        ><?php echo $Translator->trans('Cancel', 'backend');?></a>
<br/><br/>
</div>

<!-- selector -->
<div class="form-group<?php if(isset($errors['selector'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('selector', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->textarea('model[selector]', $model->selector, array(
        'class' => 'form-control',
    ));?>
    <?php if (isset($errors['selector'])):?>
        <div class="alert alert-danger"><?php echo $errors['selector'];?></div>
    <?php endif; ?>
    <i>
    Beispiel: textarea.tinyMCEEditor
    </i>
    </div>
</div>
    <br/>
    <br/>

<!-- plugins -->
<div class="form-group<?php if(isset($errors['plugins'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('plugins', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->textarea('model[plugins]', $model->plugins, array(
        'class' => 'form-control',
        'rows' => 5,
    ));?>
    <?php if (isset($errors['plugins'])):?>
        <div class="alert alert-danger"><?php echo $errors['plugins'];?></div>
    <?php endif; ?>
    <i>
    Beispiel: advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste code
    </i>
    </div>
</div>

    <br/>
    <br/>
<!-- toolbar -->
<div class="form-group<?php if(isset($errors['toolbar'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('toolbar', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->textarea('model[toolbar]', $model->toolbar, array(
        'class' => 'form-control',
        'rows' => 5,
    ));?>
    <?php if (isset($errors['toolbar'])):?>
        <div class="alert alert-danger"><?php echo $errors['toolbar'];?></div>
    <?php endif; ?>
    <i>
    Beispiel: insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image
    </i>
    </div>
    <br/>
    <br/>
</div>
    <br/>
    <br/>
<!-- initparams -->
<div class="form-group<?php if(isset($errors['initparams'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('initparams', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->textarea('model[initparams]', $model->initparams, array(
        'class' => 'form-control',
        'rows' => 5,
    ));?>
    <?php if (isset($errors['initparams'])):?>
        <div class="alert alert-danger"><?php echo $errors['initparams'];?></div>
    <?php endif; ?>
    <i>
    Beispiel (KEIN Komma nach dem letzten Eintrag!): 
    <pre>
table_class_list: [
    {title: 'None', value: ''},
    {title: 'Dog', value: 'dog'},
    {title: 'Cat', value: 'cat'}
], 
image_advtab: true,
image_class_list: [
    {title: 'None', value: ''},
    {title: 'Dog', value: 'dog'},
    {title: 'Cat', value: 'cat'}
]
    </pre>
    </i>
    </div>
    <br/>
    <br/>
</div>
</form>
