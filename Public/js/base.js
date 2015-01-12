'use strict';
var self = this;
if (Backbone.emulateJSON) {
    params.contentType = 'application/x-www-form-urlencoded';
    params.data = params.data ? {model: params.data} : {};
}
jQuery(function() {
    (function(){
        var root = this;
        var lang;
        lang = root.lang = {};
        var _ = root._,
        $ = root.jQuery;

        lang.Model = {};
        lang.Collection = {};
        lang.View = {};

        lang.Model.Base = Backbone.Model.extend({
            defaults:{
                'timestamp':-1
            }
        });

        lang.Model.Language = lang.Model.Base.extend({
            constructor: function(){
                Backbone.Model.apply(this,arguments);
            }
        });

         lang.Collection.LangList = Backbone.Collection.extend({
            model: lang.Model.Language,
            localStorage: new Backbone.LocalStorage('lang.langlocator,langs'),
        });
        //Navigation
        lang.View.LanguageNavView = Backbone.View.extend({
            template: _.template($('#tpl-lang-nav').html()),
            events:{
                'click .btn-new': 'langClear'
            },
            langClear: function(event){
                var _self = this;
                this.translate.save({},
                    {url:'Translation/imageClear'}
                    ).done(function (response){
                        _self._events.trigger('refresh','nav');
                    });
            },
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
                this.render();
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });
        //LanguageAdd
        lang.View.LanguageAddView = Backbone.View.extend({
            template: _.template($('#tpl-lang-add').html()),
            events: {
                'click .btn-add': 'addLanguage',
                'change .batch-import': 'batchImport'
            },
            addLanguage: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
                    {url:'Translation/add'}
                    ).done(function (response){
                        _self.render();
                        _self._events.trigger('refresh','add');
                    });
            },
            batchImport: function(event){
                ajaxFileUpload(
                    'Translation/import',
                    'batch-import',
                    function() {
                        alert('Import Success');
                    }, 
                    function() {
                        alert('Import Fail');
                    }
                );
            },
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });
        //Language Add Imgaes
        lang.View.LanguageImagesView = Backbone.View.extend({
            template:_.template($('#tpl-lang-images').html()),
            events:{
                'change #images-add': 'imagesAdd',
                'click .btn-image-delete': 'imgageDel'
            },
            imagesAdd: function(event){
                var _self = this;
                ajaxFileUpload(
                    'Translation/imageAdd',
                    'images-add',
                    function() {
                        _self.render();
                    }, 
                    function() {
                        alert('Add Fail');
                    }
                );
            },
            imgageDel: function(event){
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save({imageId:this.imageId},
                    {url:'Translation/imageDel'}
                    ).done(function (response){
                    _self.render();
                });
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                this.render();
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({},
                    {url:'Translation/imageList'}
                    ).done(function (response){
                    data['imagesDetail'] = response;
                    _self.$el.html(_self.template(data));
                });
            }
        });
        //LanguageSearch
        lang.View.LanguageSearchView = Backbone.View.extend({
            template: _.template($('#tpl-lang-search').html()),
            events:{
                'keypress .search': 'searchLanguage'
            },
            searchLanguage: function(event){
                if(event.keyCode == '13'){
                    this.search = $(event.target).val();
                    this.inrender = '1';
                    this.searchData = {search:this.search,inrender:this.inrender};
                    this._events.trigger('alernately',this.searchData,'search');
                }
            },
            initialize:function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
                this.render();
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });
        //LanguageList
        lang.View.LanguageListView = Backbone.View.extend({
            template: _.template($('#tpl-lang-list').html()),
            events:{
                'click .btn-edit': 'editLanguage',
                'click .btn-delete': 'deleteLanguage'
            },
            editLanguage: function(event){
                this.edit_id = $(event.target).closest('tr').data('id');
                this._events.trigger('alernately',this.edit_id,'list');
            },
            deleteLanguage: function(event){
                var _self = this;
                this.del_id = $(event.target).closest('tr').data('id');
                this.translate.save({id:this.del_id},
                    {url:'Translation/del'}
                    ).done(function (response){
                        _self.render();
                });
            },
            setList: function(data){
                this.search = data.search;
                this.inrender = data.inrender;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.lists = options.lists;
                this._events = options._events;
                this.translate = options.translate;
                this.inrender = '0';
                this.render();
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({search:this.search,inrender:this.inrender},
                    {url:'Translation/getList'}
                    ).done(function (response){
                    data['lists'] = response.lists;
                    _self.$el.html(_self.template(data));
                });
            }
        });
        //LanguageEdit
        lang.View.LanguageEditView = Backbone.View.extend({
            template: _.template($('#tpl-lang-edit').html()),
            events:{
                'change .lang_edit': 'editInfo',
                'click .btn-image-delete': 'imgageDel',
                'change #images': 'imagesAdd'
            },
            editInfo: function(event){
                var _change = $(event.target);
                this.langType = _change.attr("lang_type");
                this.langInfo = _change.val();
                this.langId = _change.closest('.form-holder').data("id");
                this.translate.save({langId:this.langId,langInfo:this.langInfo,langType:this.langType},
                    {url:'Translation/editInfo'}
                    ).done(function (response){
                });
            },
            imgageDel: function(event){
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save({imageId:this.imageId},
                    {url:'Translation/imageDel'}
                    ).done(function (response){
                    _self.render();
                });
            },
            imagesAdd: function(event){
                this.langId = $(event.target).closest('.form-holder').data("id");
                var _self = this;
                ajaxFileUpload(
                    'Translation/imageAdd/lang_id/'+this.langId,
                    'images',
                    function() {
                        _self.render();
                    }, 
                    function() {
                        alert('Add Fail');
                    }
                );
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                //this.render();
            },
            setLanguage: function(langId){
                this.langId = parseInt(langId);
                return this;
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({id:this.langId},
                    {url:'Translation/getInfo'}
                    ).done(function (response){
                    data['langDetail'] = response.detail;
                    data['langImages'] = response.images;
                    _self.$el.html(_self.template(data));
                });
            }
        });
        //export
        lang.View.LanguageExportView = Backbone.View.extend({
            template: _.template($('#tpl-lang-export').html()),
            events:{
                'click .btn-export': 'exportLanguage'
            },
            exportLanguage: function(event){
                this.select = $('#export').val();
                console.log(this.select);
                this.translate.save({exrender:'0',field:this.select},
                    {url:'Translation/export'}
                    ).done(function (response){
                        window.open('Translation/download');
                    });
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                this.exrender = '1';
                this.render();
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({exrender:this.exrender},
                    {url:'Translation/export'}
                    ).done(function (response){
                    data['allField'] = response;
                    _self.$el.html(_self.template(data));
                    });
            }
        });

        lang.View.TranslationApp = Backbone.View.extend({
            initialize: function(options){
                var _self = this;
                options || (options = {});
                this.translate = options.translate;

                var _events = {};
                _.extend(_events, Backbone.Events);

                var editView = new lang.View.LanguageEditView({
                    el: '.block-translation-detail',
                    translate: this.translate
                });

                var listView = new lang.View.LanguageListView({
                    el: '.block-translation-list',
                    _events: _events,
                    translate: this.translate
                });

                var imagesView = new lang.View.LanguageImagesView({
                    el: '.image-add',
                    translate: this.translate
                });

                var addView = new lang.View.LanguageAddView({
                    el: '.info-add',
                    _events: _events,
                    translate: this.translate
                });

                this.navView = new lang.View.LanguageNavView({
                    el: '.navbar-collapse',
                    _events: _events,
                    translate: this.translate
                });

                this.searchView = new lang.View.LanguageSearchView({
                    el: '.search-box',
                    _events:_events,
                    translate: this.translate
                });

                this.exportView = new lang.View.LanguageExportView({
                    el: '.block-translation-export',
                    translate: this.translate
                });

                _events.on('refresh', function (view){
                    if(view == 'add'){
                        listView.render();
                        imagesView.render();
                    }
                    if(view == 'nav'){
                        addView.render();
                        imagesView.render();
                    }
                });

                _events.on('alernately', function (data,view){
                    if(view == 'search'){
                        listView.setList(data).render();
                    }
                    if(view == 'list'){
                        editView.setLanguage(data).render();
                    }
                });

            }
        });

        var translation = new lang.View.TranslationApp({
            translate: new lang.Model.Language()
        });

    }).call(self);

});