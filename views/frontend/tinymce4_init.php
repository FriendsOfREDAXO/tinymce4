function redaxo5FileBrowser (field_name, url, type, win) {
    //  console.debug({
    // field_name: field_name,
    // url: url,
    // type: type,
    // win: win
    // }); // debug/testing
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
        file : cmsURL + '&ts=' + new Date().getTime(),
        <?php if ('' != $lang_pack):?>
            language:'<?php echo $lang_pack;?>',
        <?php endif;?>
        title : browser_name,
        width : 800,  // Your dimensions may differ - toy around with them!
        height : 700,
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
    $('.mce-initialized').removeClass('mce-initialized');
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

$(document).on('rex:ready',function() {
    // Erst instanzen zerstören, erforderlich für "Block übernehmen"
    window.setTimeout(function() {
        tinymce4_remove();
        tinymce4_init();
    }, 100);

    if (typeof mblock_module === 'object') {
        mblock_module.registerCallback('add_item_start', tinymce4_remove);
        mblock_module.registerCallback('reindex_end', tinymce4_init);
    }
});

$(document).on('rex:change', function(e, container){
    container.find('.mce-initialized').removeClass('mce-initialized').show();
    container.find('.mce-tinymce.mce-container').remove();
    tinymce4_init();
});

$(document).on('be_table:row-added',function() {
    tinymce4_init();
});

