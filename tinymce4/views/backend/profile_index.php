<table class="table">
    <tr>
        <th>Selector</th>
        <th>Plugins</th>
        <th>Toolbar</th>
        <th>Weitere Parameter</th>
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
    <td><?php echo htmlspecialchars($profile->initparams);?></td>
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

<div>
Die Tinymce-Init-Funktion wird wie folgt zusammen gesetzt:
<pre>
tinymce.init({
    file_browser_callback : redaxo5FileBrowser,
    selector: '&lt;?php echo $profile-&gt;selector;?&gt;',
    plugins: '&lt;?php echo $profile-&gt;plugins;?&gt;',
    toolbar: '&lt;?php echo $profile-&gt;toolbar;?&gt;'
    &lt;?php if ('' != $profile-&gt;initparams):?&gt;
    ,&lt;?php echo $profile-&gt;initparams;?&gt;
    &lt;?php endif;?&gt;
});
</pre>
Mit den weiteren Parametern (initparams) kann man die Funktion 
also beliebig erweitern.
</div>
