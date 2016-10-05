<table class="table">
    <tr>
        <th>Selector</th>
        <th>Plugins</th>
        <th>Toolbar</th>
        <th>
        <a class="btn btn-default"
            href="<?php echo $UrlService->getUrl('/profile/edit/0');?>"
            >Hinzufügen</a>
        </th>
    </tr>
<?php foreach ($profile_list as $key => $profile):?>
    <tr>
    <td><?php echo $profile->selector;?></td>
    <td><?php echo htmlspecialchars($profile->plugins);?></td>
    <td><?php echo htmlspecialchars($profile->toolbar);?></td>
    <td>
        <a class="btn btn-default"
            href="<?php echo $UrlService->getUrl('/profile/edit/'.$profile->id);?>"
            >Ändern</a>
        <a class="btn btn-default"
            onclick="return confirm('Löschen?');"
            href="<?php echo $UrlService->getUrl('/profile/remove/'.$profile->id);?>"
            >Löschen</a>
    </td>
    </tr>
<?php endforeach;?>
</table>
