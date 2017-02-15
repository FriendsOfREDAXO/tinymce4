#Tinymce4 Addon für Redaxo5

# Empfehlungen für intitparams

## convert_urls: false

Wenn convert_urls: true ist, dann verändert Tinymce eingegebene URL's beim speichern.
Zum Beispiel wird eine URL /media/xxx in ../media/xxx umgewandelt. 
Dies ist meistens nicht gewünscht, daher sollte convert_urls: false 
im Profil unter "weitere Parameter" hinzugefügt werden.

Weitere Infos zum Thema: https://www.tinymce.com/docs/configure/url-handling/

## Bootstrap-Tabellenlayout

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
