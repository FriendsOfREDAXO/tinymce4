<table class="table">
    <tr>
        <th>Selector</th>
        <th>Plugins</th>
        <th>Toolbar</th>
        <th>Weitere Parameter</th>
        <th>
        <a class="btn btn-default"
            href="<?php echo $UrlService->getUrl('/profile/edit/0');?>"
            title="<?php $Translator->trans('Add');?>"
            ><i class="fa fa-plus" aria-hidden="true"></i></a>
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
Die Tinymce-Init-Funktion wird wie folgt zusammen gesetzt:
<pre>
tinymce.init({
    file_browser_callback : redaxo5FileBrowser,

    selector: '&lt;?php echo $profile-&gt;selector;?&gt;'

    &lt;?php if (in_array(\rex_config::get('tinymce4', 'content_css'), ['default', ''])):?&gt;
        content_css: '&lt;?php echo rex_url::addonAssets('tinymce4', 'bootstrap/css/bootstrap.min.css');?&gt;'
    &lt;?php else: ?&gt;  
        content_css: '&lt;?php echo \rex_config::get('tinymce4', 'content_css');?&gt;'
    &lt;?php endif;?&gt;

    &lt;?php if ('' != $profile-&gt;plugins):?&gt;
        ,plugins: '&lt;?php echo $profile-&gt;plugins;?&gt;',
    &lt;?php endif;?&gt;

    &lt;?php if ('' != $profile-&gt;toolbar):?&gt;
        ,toolbar: '&lt;?php echo $profile-&gt;toolbar;?&gt;'
    &lt;?php endif;?&gt;

    &lt;?php if ('' != $profile-&gt;initparams):?&gt;
        ,&lt;?php echo $profile-&gt;initparams;?&gt;
    &lt;?php endif;?&gt;

});
</pre>
Man kann also plugins und toolbar leer lassen und mit den weiteren Parametern (initparams) den Tinymce-Editor komplett nach eigenen Vorstellungen konfigurieren.
<br/>Hier findet man alle Informationen zur Konfiguration von Tinymce4: 
<a href="https://www.tinymce.com/docs/configure/content-appearance/">Tinymce.com</a>
</div>
