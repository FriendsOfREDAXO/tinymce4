function redaxo5FileBrowser (field_name, url, type, win) {
    console.debug({
        field_name: field_name,
        url: url,
        type: type,
        win: win
    }); // debug/testing
    // console.debug(tinymce.activeEditor);
    // console.debug(tinymce);

    /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

    var cmsURL = 'index.php?mce_profile='+ tinymce.activeEditor.profile +'&tinymce4_call=';    // script URL - use an absolute path!
    if ('image' == type) {
        cmsURL+= '/image/index';
        var browser_name = 'Bild auswählen';
    } else if ('file' == type) {
        var browser_name = 'Link auswählen';
        cmsURL+= '/file/index';
    } else if ('media' == type) {
        var browser_name = 'Medium auswählen';
        cmsURL+= '/media/index';
    }
    var m = location.href.match(/clang=[0-9]+/);
    if (null != m) {
        cmsURL+= '&' + m[0].replace('clang', 'clang_id');
    }

    tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        <?php if ('' != $lang_pack):?>
            language:'<?php echo $lang_pack;?>',
        <?php endif;?>
        title : browser_name,
        width : 420,  // Your dimensions may differ - toy around with them!
        height : 400,
        resizable : true,
        inline : true,  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : true
    }, {
        window : win,
        input : field_name
    });
    return false;
}

function tinymce4_remove() {
    tinymce.remove();
}

function tinymce4_init(){
    <?php foreach ($profiles as $profile): ?>
        var profile = <?= $profile->json ?>;
        profile.selector += ':not(.mce-initialized)';

        profile.setup = function(editor) {
            editor.profile = '<?= $profile->id ?>';
        };
        tinymce.init(profile).then(function(editors) {
            for(var i in editors) {
                $(editors[i].targetElm).addClass('mce-initialized');
            }
        });
    <?php endforeach;?>
    return false;
}

$(document).on('ready pjax:success',function() {
    // Erst instanzen zerstören, erforderlich für "Block übernehmen"
    tinymce4_remove();
    tinymce4_init();

    if (typeof mblock_module === 'object') {
        mblock_module.registerCallback('add_item_start', tinymce4_remove);
        mblock_module.registerCallback('reindex_end', tinymce4_init);
    }
});

$(document).on('be_table:row-added',function() {
    tinymce4_init();
});

