<?php include 'top.php'; ?>
<script type="text/javascript">
var win = top.tinymce.activeEditor.windowManager.getParams().window;
var field_name = top.tinymce.activeEditor.windowManager.getParams().input;



/*
* Component article-type
 */
var ArticleType = {
    template: '#article-type-template',
    data: function(){
        return {
            search: '',
            category_id: 0,
        };
    },
    computed: {
        articles: function(){
            return this.$articles;
        },
        search_results: function(){
            if ('' == this.search) {
                return [];
            } else {
                let that = this;
                return this.articles.filter(function(art){
                    return (-1 != art.name.indexOf(that.search));
                });
            }
        },
        categories: function(){
            let that = this;
            let cats = this.articles.filter(function(art){
                return (1 == parseInt(art.startarticle)
                    && parseInt(art.parent_id)==parseInt(that.category_id));
            });
            return cats.sort(function(a, b){
                return a.catpriority-b.catpriority;
            });
        },
        current_category: function(){
            let that = this;
            let res = this.articles.filter(function(art){
                return parseInt(art.id) == parseInt(that.category_id);
            });
            if (0 == res.length){
                return { id: 0, catname: 'Root', };
            } else {
                return res[0];
            }

        },
        category_articles: function(){
            let that = this;
            let res = this.articles.filter(function(art){
                return (parseInt(art.parent_id) == parseInt(that.category_id)
                    && 0 == parseInt(art.startarticle));
            });
            return res.sort(function(a, b){
                return a.priority-b.priority;
            });
        },
    },
    methods: {
        setCategoryId: function(category_id){
            this.category_id = category_id;
        },
        selectArticle: function(art){
            win.document.getElementById(field_name).value = 'redaxo://'+art.id+'-'+art.clang_id;
            win.tinymce.activeEditor.windowManager.close();
        },
    }
};
</script>
<script type="text/x-template" id="article-type-template">
<div>
<div class="form-group">
    <input type="text"
        class="form-control"
        v-model="search"
        placeholder="Suche"
        />
</div>
<ul class="">
    <li class=""
        v-for="art in search_results"
        >
        <a @click="selectArticle(art)" class="article" >{{art.name}}</a>
    </li>
</ul>
<h4>
    <span v-if="category_id != 0" class="pull-left">
    <button  @click="setCategoryId(current_category.parent_id)" class="btn btn-default btn-xs">&lt;</button>&nbsp;
    </span>
    {{current_category.catname}}
</h4>
<div v-if="!search_results.length" class="row">
    <div class="col-xs-6">
        <ul class="">
        <li v-for="art in categories" class="category">
            <a @click="setCategoryId(art.id)">{{art.catname}}</a>
        </li>
        </ul>
    </div>
    <div class="col-xs-6">
        <ul class="">
        <li v-if="category_id != 0" class="article startarticle">
            <a @click="selectArticle(art)" title="Startartikel">{{current_category.name}}</a>
        </li>
        <li v-for="art in category_articles" class="article">
            <a @click="selectArticle(art)" >{{art.name}}</a>
        </li>
        </ul>
    </div>
</div>
</div>
</script>
<script type="text/javascript">
/*
* Component article-type
 */
var FileType = {
    template: '#file-type-template',
    data: function(){
        return {
            search: '',
            category_id: 0,
        };
    },
    computed: {
        files: function(){
            return this.$files;
        },
        media_categories: function(){
            return this.$media_categories;
        },
        search_results: function(){
            if ('' == this.search) {
                return [];
            } else {
                let that = this;
                return this.files.filter(function(file){
                    return (-1 != file.filename.indexOf(that.search));
                });
            }
        },
        categories: function(){
            let that = this;
            let cats = this.media_categories.filter(function(cat){
                return (parseInt(cat.parent_id)==parseInt(that.category_id));
            });
            return cats.sort(function(a, b){
                return a.catpriority-b.catpriority;
            });
        },
        current_category: function(){
            let that = this;
            let res = this.media_categories.filter(function(cat){
                return parseInt(cat.id) == parseInt(that.category_id);
            });
            if (0 == res.length){
                return { id: 0, catname: 'Root' };
            } else {
                return res[0];
            }

        },
        category_files: function(){
            let that = this;
            let res = this.files.filter(function(file){
                return (parseInt(file.category_id) == parseInt(that.category_id));
            });
            return res.sort(function(a, b){
                return a.filename-b.filename;
            });
        },
    },
    methods: {
        setCategoryId: function(category_id){
            this.category_id = category_id;
        },
        selectFile: function(file){
            if ('default' == this.$media_format.toLowerCase()){
                win.document.getElementById(field_name).value = '/media/'+encodeURIComponent(file.filename);
            } else {
                win.document.getElementById(field_name).value = this.$media_format.replace('{filename}', encodeURIComponent(file.filename));
            }
            win.tinymce.activeEditor.windowManager.close();
        },
    }
};
</script>
<script type="text/x-template" id="file-type-template">
<div>
<div class="form-group">
    <input v-model="search" type="text" class="form-control" placeholder="Suche" />
</div>
<ul class="">
<li v-for="file in search_results" class="file">
    <a @click="selectFile(file)">{{file.filename}}</a>
</li>
</ul>
<h4>
    <span v-if="category_id != 0" class="pull-left">
    <button @click="setCategoryId(current_category.parent_id)" class="btn btn-default btn-xs">&lt;</button>&nbsp;
    </span>
    {{current_category.name}}
</h4>
<div v-if="!search_results.length" class="row">
    <div class="col-xs-6">
        <ul class="">
        <li v-for="cat in categories" class="category">
            <a @click="setCategoryId(cat.id)">{{cat.name}}</a>
        </li>
        </ul>
    </div>
    <div class="col-xs-6">
        <ul class="">
        <li v-for="file in category_files" class="file">
            <a @click="selectFile(file)">{{file.filename}}</a>
        </li>
        </ul>
    </div>
</div>
</div>
</script>
<script type="text/javascript">
/*
* Component table-type
 */
var TableType = {
    template: '#table-type-template',
    data: function(){
        return {
            search: '',
            table: '',
        };
    },
    computed: {
        tables: function(){
            return this.$tables;
        },
        search_results: function(){
            if ('' == this.search) {
                return [];
            } else {
                let that = this;
                return this.current_table.data.filter(function(item){
                    return (-1 != item.name.indexOf(that.search));
                });
            }
        },
        current_table: function(){
            let that = this;
            let res = this.tables.filter(function(data){
                return data.table == that.table;
            });
            if (0 == res.length){
                return { id: 0, name: '<?= rex_i18n::msg('tables') ?>', };
            } else {
                return res[0];
            }
        },
        rows: function(){
            return this.current_table.data;
        },
    },
    methods: {
        selectTable: function(table){
            this.table = table;
        },
        selectItem: function(item){
            win.document.getElementById(field_name).value = item.url;
            win.tinymce.activeEditor.windowManager.close();
        },
    }
};
</script>
<script type="text/x-template" id="table-type-template">
    <div>
        <h4>
            <span v-if="table != ''" class="pull-left">
                <button  @click="selectTable('')" class="btn btn-default btn-xs">&lt;</button>&nbsp;
            </span>
            {{current_table.tablename}}
        </h4>

        <div v-if="table != ''" >
            <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="<?= rex_i18n::msg('be_search_search') ?>" />
            </div>
            <ul>
                <li class="" v-for="item in search_results">
                    <a @click="selectItem(item)" class="article" >{{item.name}}</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div v-if="table == ''" class="col-xs-6">
                <ul class="">
                    <li v-for="data in tables" class="tables">
                        <a @click="selectTable(data.table)">{{data.tablename}}</a>
                    </li>
                </ul>
            </div>
            <div v-if="!search_results.length" class="col-xs-12">
                <ul class="">
                    <li v-for="item in rows" class="article">
                        <a @click="selectItem(item)" >{{item.name}}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</script>



<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
    Vue.prototype.$articles = <?php echo json_encode($all_arts);?>;
    Vue.prototype.$files    = <?php echo json_encode($all_files);?>;
    Vue.prototype.$tables   = <?php echo json_encode($all_tables);?>;
    Vue.prototype.$media_categories  = <?php echo json_encode($all_media_categories);?>;
    Vue.prototype.$media_format  = '<?php echo $media_format;?>';
    var app = new Vue({
        el: '#app',
        components: {
            'article-type': ArticleType,
            'file-type': FileType,
            'table-type': TableType,
        },
        data: {
            currentComponent: 'article-type',
        },
    });

});/* end of event handler */

</script>
<style>
a {cursor:pointer;}

ul{
    padding: 0;
    margin: 0;
}
li {
    list-style-type:none;

}
li.category:before {
    /*Using a Bootstrap glyphicon as the bullet point*/
    content: "\e117";
    font-family: 'Glyphicons Halflings';
    font-size: 9px;
    margin-right: 4px;
}
li.file:before {
    content: "\e144";
    font-family: 'Glyphicons Halflings';
    font-size: 9px;
    margin-right: 4px;
}
li.article:before {
    content: "\e144";
    font-family: 'Glyphicons Halflings';
    font-size: 9px;
    margin-right: 4px;
}
li.article.startarticle {
    font-weight:bold;
}
</style>
<div id="app" class="col-xs-12">

    <div class="form-group" style="padding-top:15px">
        <label>
            <input type="radio" value="article-type" v-model="currentComponent">
            <?= rex_i18n::msg('article') ?>
        </label>
        <label>
            <input type="radio" value="file-type" v-model="currentComponent">
            <?= rex_i18n::msg('media') ?>
        </label>
        <?php if (count($all_files)): ?>
            <label>
                <input type="radio" value="table-type" v-model="currentComponent">
                <?= rex_i18n::msg('table') ?>
            </label>
        <?php endif; ?>
    </div>
    <div :is="currentComponent"></div>
</div>

<?php include 'bottom.php'; ?>
