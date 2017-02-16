#Tinymce4 Addon für Redaxo5

## Default-Profil
```
{
selector: 'textarea.tinyMCEEditor',
file_browser_callback: redaxo5FileBrowser,
plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste code',
toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
convert_urls: false,
content_css: '/assets/addons/tinymce4/bootstrap/css/bootstrap.min.css',
}
```

Wichtig: **redaxo5FileBrowser** ist eine Funktion. Darum werden an dieser Stelle
**keine Quotes** verwendet. 

## Empfehlungen

### convert_urls: false

Wenn convert_urls: true ist, dann verändert Tinymce eingegebene URL's beim speichern.
Zum Beispiel wird eine URL /media/xxx in ../media/xxx umgewandelt. 
Dies ist meistens nicht gewünscht, daher sollte convert_urls: false 
im Profil unter "weitere Parameter" hinzugefügt werden.

Weitere Infos zum Thema: https://www.tinymce.com/docs/configure/url-handling/

### Bootstrap-Tabellenlayout im edit

```
table_class_list: [
    {title: 'None', value: ''},
    {title: 'Table', value: 'table'},
    {title: 'Table striped', value: 'table-striped'}
], 
image_advtab: true,
image_class_list: [
    {title: 'None', value: ''},
    {title: 'Abgerundet', value: 'img-rounded'},
    {title: 'Kreis', value: 'img-circle'}
    {title: 'Responsive', value: 'img-responsive'}
]
```
