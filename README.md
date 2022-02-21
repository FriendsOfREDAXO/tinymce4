# TinyMCE4-Editor für REDAXO 5

🚨 | Deprecated: Dieses AddOn ist veraltet. Verwendung auf eigene Gefahr | 🚨
:---: | :---: | :---


## Default-Profil

```yml
{
selector: 'textarea.tinyMCEEditor',
file_browser_callback: redaxo5FileBrowser,
plugins: 'autoresize  lists  autolink  link  visualblocks  fullscreen  paste  code  hr  tabfocus  visualchars  table  image',
toolbar: 'insertfile undo redo | styleselect | bold italic  underline  superscript  subscript | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink | table image pastetext removeformat | fullscreen visualblocks',
convert_urls: false,
branding:  false,
statusbar:  false,
autoresize_bottom_margin:  0,
autoresize_max_height:  900,
autoresize_min_height:  120,
autoresize_overflow_padding:  15,
content_css: '/assets/addons/tinymce4/bootstrap/css/bootstrap.min.css',
}
```

Wichtig: **redaxo5FileBrowser** ist eine Funktion. Darum werden an dieser Stelle **keine Quotes** verwendet. 

## Empfehlungen

### convert_urls: false

Wenn `convert_urls: true` ist, dann verändert Tinymce eingegebene URLs beim speichern. Zum Beispiel wird eine URL `/media/xxx` in `../media/xxx` umgewandelt. Dies ist meistens nicht gewünscht, daher sollte `convert_urls: false` im Profil enthalten sein (beim Default-Profil ist das schon drin).

Weitere Infos zum Thema: https://www.tinymce.com/docs/configure/url-handling/

### Bootstrap-Tabellenlayout im edit

Damit die Bootstrap-Tabellenklassen rsp. auch die Bootstrap-Bilder-Klassen im Editor verfügbar sind, kann das folgende Snippet dem Profil hinzu gefügt werden:

```yml
table_class_list: [
    {title: 'None', value: ''},
    {title: 'Table', value: 'table'},
    {title: 'Table striped', value: 'table-striped'}
], 
image_advtab: true,
image_class_list: [
    {title: 'None', value: ''},
    {title: 'Abgerundet', value: 'img-rounded'},
    {title: 'Kreis', value: 'img-circle'},
    {title: 'Responsive', value: 'img-responsive'}
]
```
### Microsoft Word HTML automatisch beim Einfügen entfernen

Damit unnötiger Word HTML Krempel beim Einfügen automatisch entfernt wird (Redakteure werden euch lieben), aber sonstige Formatierungen erhalten bleiben, einfach die Config mit folgendem Snippet erweitern (wichtig, das Plugin __paste__ wird benötigt, siehe Default-Profil oben).

Im __paste_word_valid_elements__ können die Tags erfasst werden, die beibelassen werden sollen. Alles andere wird herausgefiltert.

```yml
convert_fonts_to_spans: true,
paste_word_valid_elements: "b,strong,i,em,h1,h2,u,p,ol,ul,li,a[href],span,mark,table,tr,td",
paste_retain_style_properties: "all",
paste_postprocess: function(plugin, args) {
    args.node.innerHTML = tinymce4_cleanHTML(args.node.innerHTML);
}
```
