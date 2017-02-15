<form class="form form-horizontal"
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
<br/>
<br/>
</div>

<!-- name -->
<div class="form-group<?php if(isset($errors['name'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('name', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->text('model[name]', $model->name, array(
        'class' => 'form-control',
    ));?>
    <?php if (isset($errors['name'])):?>
        <div class="alert alert-danger"><?php echo $Translator->trans($errors['name'], 'backend');?></div>
    <?php endif; ?>
    </div>
</div>

<!-- plugins -->
<div class="form-group<?php if(isset($errors['json'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('json', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->textarea('model[json]', $model->json, array(
        'class' => 'form-control',
        'rows' => 20,
    ));?>
    <?php if (isset($errors['json'])):?>
        <div class="alert alert-danger"><?php echo $Translator->trans( $errors['json'],'backend');?></div>
    <?php endif; ?>
    </div>
</div>

</form>
