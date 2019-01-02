<form class="form form-horizontal"
    action="<?php echo $UrlService->getUrl('/config/edit/');?>"
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

<!-- image_format -->
<div class="form-group<?php if(isset($errors['image_format'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('image_format', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->text('model[image_format]', $model->image_format, array(
        'class' => 'form-control',
    ));?>
    <?php if (isset($errors['image_format'])):?>
        <div class="alert alert-danger"><?php echo $errors['image_format'];?></div>
    <?php endif; ?>
    <i>
    <?php echo $Translator->trans('image_format info', 'backend');?><br/>
    default: index.php?rex_media_type=tinymcewysiwyg&rex_media_file={filename}<br/>
    </i>
    </div>
</div>
    <br/>
    <br/>
<!-- content_css -->
<div class="form-group<?php if(isset($errors['media_format'])):?> has-error<?php endif;?>">
    <label for="attributes" class="control-label col-sm-2">
        <?php echo $Translator->trans('media_format', 'backend');?>
    </label>
    <div class="col-sm-10">
    <?php echo $form->text('model[media_format]', $model->media_format, array(
        'class' => 'form-control',
    ));?>
    <?php if (isset($errors['media_format'])):?>
        <div class="alert alert-danger"><?php echo $errors['media_format'];?></div>
    <?php endif; ?>
    <i>
    <?php echo $Translator->trans('media_format info', 'backend');?><br/>
    default: /media/{filename}<br/>
    </i>
    </div>
</div>
    <br/>
    <br/>
</form>
