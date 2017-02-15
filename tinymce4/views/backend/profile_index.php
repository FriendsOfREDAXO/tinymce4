<table class="table">
    <tr>
        <th><?php echo $Translator->trans('name', 'backend');?></th>
        <th><?php echo $Translator->trans('json', 'backend');?></th>
        <th>
        <a class="btn btn-default"
            href="<?php echo $UrlService->getUrl('/profile/edit/0');?>"
            title="<?php echo $Translator->trans('Add');?>"
            ><i class="fa fa-plus" aria-hidden="true"></i></a>
        </th>
    </tr>
<?php foreach ($profile_list as $key => $profile):?>
    <tr>
    <td><?php echo $profile->name;?></td>
    <td><?php echo nl2br(htmlspecialchars($profile->json));?></td>
    <td>
        <a class="btn btn-default"
            href="<?php echo $UrlService->getUrl('/profile/edit/'.$profile->id);?>"
            ><i class="fa fa-pencil" aria-hidden="true"></i></a>
        <a class="btn btn-default"
            onclick="return confirm('Löschen?');"
            href="<?php echo $UrlService->getUrl('/profile/remove/'.$profile->id);?>"
            ><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
    </tr>
<?php endforeach;?>
</table>

<div>
Hier findet man alle Informationen zur Konfiguration von Tinymce4: 
<a href="https://www.tinymce.com/docs/configure/content-appearance/">Tinymce.com</a>
</div>
