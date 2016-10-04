

function redaxo5FileBrowser (field_name, url, type, win) {
     console.log("Field_Name: " + field_name + "nURL: " + url + "nType: " + type + "nWin: " + win); // debug/testing

    /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

    var cmsURL = 'index.php?tinymce4_call=';    // script URL - use an absolute path!
    if ('image' == type) {
        cmsURL+= '/image/index';
        var browser_name = 'Bild auswählen';
    } else if ('file' == type) {
        var browser_name = 'Link auswählen';
        cmsURL+= '/file/index';
    }
    var m = location.href.match(/clang=[0-9]+/);
    if (null != m) {
        cmsURL+= '&' + m[0].replace('clang', 'clang_id');
    }
    

    tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
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


$(document).on('ready pjax:success',function() {
    tinymce.remove("textarea.tinyMCEEditor");
    tinymce.init({
        selector: 'textarea.tinyMCEEditor',
        height: 500,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        //content_css: 'assets/addon/tinymce4/tinymce4.css',
        file_browser_callback : redaxo5FileBrowser

    });
});
