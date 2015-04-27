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
        var user;
        lang = root.lang = {};
        user = root.user = {};
        var _ = root._,
        $ = root.jQuery;

        lang.Model = {};
        lang.Collection = {};
        lang.View = {};

        // user.Model = {};
        // user.Collection = {};
        // user.View = {};

        lang.Model.Base = Backbone.Model.extend({
            defaults:{
                'timestamp':-1
            },
            save: function(attributes, options) {
                $.fancybox.showLoading();
                var _success = options.success;
                options.success = function(resp) {
                    $.fancybox.hideLoading();
                    if (_success) _success(model, resp, options);
                };
                return Backbone.Model.prototype.save.call(this, attributes, options);
            }
        });

        lang.Model.Language = lang.Model.Base.extend({
            constructor: function(){
                Backbone.Model.apply(this,arguments);
            },
        });

        // user.Model.Base = Backbone.Model.extend({
        //     defaults:{
        //         'timestamp':-1
        //     },
        //     save: function(attributes, options) {
        //         $.fancybox.showLoading();
        //         var _success = options.success;
        //         options.success = function(resp) {
        //             $.fancybox.hideLoading();
        //             if (_success) _success(model, resp, options);
        //         };
        //         return Backbone.Model.prototype.save.call(this, attributes, options);
        //     }
        // });

        lang.Collection.LangList = Backbone.Collection.extend({
            model: lang.Model.Language,
            localStorage: new Backbone.LocalStorage('lang.langlocator,langs'),
        });

        var LangRouter = Backbone.Router.extend({
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
            },
            routes: {
                "add": "add",
                // "export": "export",
                "edit/:base_id\d/:other_id\d": "edit",
                "delete/:id\d": "delete",
            },

            // export: function(){
            //     //导出权限
            //     if(Purview('export') == '1'||PurviewVal() == '-1'){
            //         this._events.trigger('refresh','export');
            //     }else{
            //         $.fancybox($('.message'),{
            //            afterClose: function () {
            //                 window.history.back();
            //             }
            //         });
            //     }
            // },

            add: function(){
                //增加权限
                if(Purview('create') == '1'||PurviewVal() == '-1'||Purview('update') == '1'){
                    this._events.trigger('refresh','addRender');
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                }
            },

            edit: function(base_id,other_id){
                //更新权限
                if(Purview('update') == '1'||PurviewVal() == '-1'||Purview('create') == '1'){
                    this._events.trigger('alernately',{base_id:base_id,other_id:other_id},'edit');
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                }
            },

            delete: function(id){
                //删除权限
                if(Purview('delete') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('alernately',id,'delete');
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                }
            },

        });

        //LanguageAdd
        lang.View.LanguageAddView = Backbone.View.extend({
            template: _.template($('#tpl-lang-add').html()),
            events: {
                'click .btn-add': 'clickBtnLangAdd',
                'change #batch-import': 'batchImport',
                'change #images-add': 'imagesAdd',
                'click .btn-image-delete': 'imgageDel',
                'change .create-language': 'createLanguage'
            },
            createLanguage: function (event){
                var create_language = $(event.target).val();
                if(create_language != '-1'){
                    $(event.target).before('<div class="entry textarea form-group"><label for="'+create_language.toLowerCase()+'">'+create_language+':</label><textarea name="'+create_language.toLowerCase()+'" id="'+create_language.toLowerCase()+'"></textarea></div>');
                    $(event.target).find(':selected').hide();
                }
            },
            _add: function(){
                this.translate.clear();
                var _self = this;
                var $form = $('.btn-add').closest('form');
                // console.log($form.serializeObject());
                this.translate.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/langadd'}
                ).done(function (response){
                    if (response.success === true) {
                        $('input[name="notify"]').val('Success');
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        lang_add.reset();
                        $('.images_list ul li').hide();
                        _self._events.trigger('refresh','translate-list-view');
                    } else {
                        $('input[name="notify"]').val(response.message);
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                // this.translate.clear();
                return false;
            },
            clickBtnLangAdd: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            batchImport: function(event){
                $.fancybox.showLoading();
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimport',
                    'batch-import',
                    function() {
                        _self._events.trigger('refresh','translate-list-view');
                        $.fancybox.hideLoading();
                        $('input[name="notify"]').val('Success');
                        $('.batch-import').notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                    },
                    function() {
                        $.fancybox.hideLoading();
                        $('input[name="notify"]').val(response.message);
                        $('.batch-import').notify(
                            'Import Failure.',
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                );
            },
            imagesAdd: function(event){
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimgadd',
                    'images-add',
                    function(data) {
                        $('input[name="notify"]').val('Success');
                        $('.images_list').notify(
                                'Success', 
                                { 
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+data['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+data['id']+'">X</a></div></li>');
                    },
                    function() {
                        $('input[name="notify"]').val('Upload Failure');
                        $('.images_list').notify(
                                'Upload Failure', 
                                { 
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                    }
                );
            },
            imgageDel: function(event){
                this.translate.clear();
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save(
                    {imageId:this.imageId},
                    {url:UrlApi('_app')+'/langimgdel'}
                ).done(function (response){
                    if(response.success === true){
                        _click.closest('li').hide();
                        $('#enlarge_images').html('');
                    }
                });
                // this.translate.clear();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
            },
            render: function(){
                this.translate.clear();
                var _self = this;
                this.translate.save({}, 
                    {url: UrlApi('_app')+'/weblang'}
                ).done(function (response){
                    // console.log(response.data);
                    var data = {};
                    data['weblanglist'] = response.data;
                    _self.$el.html(_self.template(data));
                    _self.$el.find('[name="lang_add"]').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._add.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el,{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                // this.translate.clear();
            }
        });

        //LanguageSearch
        lang.View.LanguageSearchView = Backbone.View.extend({
            template: _.template($('#tpl-lang-search').html()),
            events:{
                'keypress .search': 'searchLanguage',
                'focus .search': 'searchFocus',
                'click .search-clear': 'searchClear',
                'click .search-enter': 'searchEnter'
            },
            searchLanguage: function(event){
                if(event.keyCode == '13'){
                    $(event.target).blur();
                    $('.search-enter').hide();
                    $('.search-clear').show();
                    this.search = $(event.target).val();
                    this._events.trigger('alernately',{search:this.search},'search');
                }
            },
            searchFocus: function (event){
                $('.search-enter').show();
                $('.search-clear').hide();
            },
            searchClear: function (event){
                $('.search').val('');
                $('.search').focus();
                this._events.trigger('alernately',{search:''},'search');
                $('.search-enter').show();
                $('.search-clear').hide();
                return false;
            },
            searchEnter: function (event){
                $('.search').blur();
                $('.search-enter').hide();
                $('.search-clear').show();
                this.search = $('.search').val();
                this._events.trigger('alernately',{search:this.search},'search');
                return false;
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
                'click .btn-list-export': 'exportList',
                'click .btn-list-sort': 'backSort',
                'change .batch-app': 'appLang',
                'click .translate-modify': 'showNeedModify',
                'click .translate-complete': 'showNoModify',
                'click .translate-total': 'showTotalRecords',
                'change .lang-list': 'showLangList'
            },
            // langInfo: function (event){
            //     console.log($('.lang-list').val());
            //     // alert('1');
            // },
            showLangList: function (event){
                this.lang_id = $(event.target).val();
                this.render();
            },
            showNeedModify: function (event){
                this.record = 1;
                this.render();
            },
            showNoModify: function (event){
                this.record = 0;
                this.render();
            },
            showTotalRecords: function (event){
                this.record = -1;
                this.render();
            },
            backSort: function(event){
                var sort = $('.table').attr('data-sort');
                if(sort == 'modify'){
                    this.setList({complete: true}).render();
                    this.inrender = true;
                }
                if(sort == 'search'){
                    this.setList({inrender: false,complete: false}).render();
                }
            },
            appLang: function(event){
                this.translate.clear();
                var _self = this;
                this.data = {};
                this.operation = $(event.target).val();
                if($('tbody input:checked').length < 1){
                    return;
                }
                $('tbody input:checked').each(function (i){
                    _self.data[i] = $(this).val();
                });

                if(this.operation == 'update'){
                    if(Purview('update') == '1'||PurviewVal() == '-1'){
                        var modify_message;
                        if(this.record === 1){
                            modify_message = 'Are you sure setting the selected to not need modify?';
                        }else{
                            modify_message = 'Are you sure setting the selected to need modify?';
                        }
                        if(confirm(modify_message) == true){
                            this.translate.save(
                                {ids: this.data},
                                {url: UrlApi('_app')+'/setmodify'}
                            ).done(function (response){
                                if(response.success === true){
                                    _self.render();
                                }
                            });
                            // this.translate.clear();
                        }else{
                            $(event.target).find('option')[0].selected = true;
                        }
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                $(event.target).find('option')[0].selected = true;
                            }
                        });
                    }
                }

                if(this.operation == 'delete'){
                    if(Purview('delete') == '1'||PurviewVal() == '-1'){
                            if(confirm('Are you sure to delete?') == true){
                                this.translate.save(
                                    {ids: this.data},
                                    {url:UrlApi('_app')+'/langsdel'}
                                ).done(function (response){
                                    if(response.success === true){
                                        _self.render();
                                    }
                                });
                                // this.translate.clear();
                            }else{
                                $(event.target).find('option')[0].selected = true;
                            }
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                $(event.target).find('option')[0].selected = true;
                            }
                        });
                    }
                }
            },
            deleteLanguage: function(id){
                this.translate.clear();
                if(confirm('Are you sure to delete?') == true){
                    var _self = this;
                    this.translate.save(
                        {id: id},
                        {url:UrlApi('_app')+'/langdel'}
                    ).done(function (response){
                        // if(response.success === true){
                            _self.render();
                        // }
                    });
                    // this.translate.clear();
                }
                window.history.back();
                return false;
            },
            exportList: function(event){
                this.translate.clear();
                //导出权限
                if(Purview('export') == '1'||PurviewVal() == '-1'){
                    // this._events.trigger('alernately',$('.lang-list').val(),'export-list');
                    this.translate.save(
                        {
                            lang_id: $('.lang-list').val()
                        },
                        {url:UrlApi('_app')+'/langexport'}
                        ).done(function (response){
                            if(response.success === true){
                                window.open(UrlApi('_app')+'/langdownload');
                            }
                        });
                        // this.translate.clear();
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            addRender: function(){
                this.translate.clear();
                var _self = this;
                this.translate.save(
                    {},
                    {url:UrlApi('_app')+'/langimgclear'}
                    ).done(function (response){
                        _self._events.trigger('refresh','list-add');
                    });
                    // this.translate.clear();
                return false;
            },
            setList: function(data){
                this.search = data.search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.lists = options.lists;
                this._events = options._events;
                this.translate = options.translate;
                // this.inrender = false;
                this.lang_id = false;
                this.record = -1;
                this.render();
            },
            render: function(){
                this.translate.clear();
                var _self = this;
                this.translate.save(
                    {
                        search: this.search,
                        language: this.lang_id,
                        record: this.record,
                    },
                    { url:UrlApi('_app')+'/langlist' }
                ).done(function (response){
                    var data = {
                        'list': response.data.list,
                        'langs': response.data.langs,
                        'lang_id': _self.lang_id,
                        'total': response.data.total,
                        'need_modify': response.data.need_modify,
                        'no_modify': response.data.no_modify,
                        'click_show': _self.record,
                        // 'inrender': _self.inrender
                    };
                    _self.$el.html(_self.template(data));
                    // _self.record = -1;
                });
                // this.translate.clear();
                return this;
            }
        });
        //LanguageEdit
        lang.View.LanguageEditView = Backbone.View.extend({
            template: _.template($('#tpl-lang-edit').html()),
            events:{
                'click .btn-lang-save': 'clickBtnEditInfo',
                'click .btn-image-delete': 'imgageDel',
                'change #images': 'imagesAdd',
            },
            _edit: function(){
                this.translate.clear();
                var _self = this;
                var _change = $('.btn-lang-save');
                var $form = _change.closest('form');
                if($form.serializeObject().modify != this.modify){
                    if(confirm('Are you sure you change the status of this record?') === false){
                        return;
                    }
                }
                this.translate.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/langedit'}
                ).done(function (response){
                    if (response.success === true) {
                        $('input[name="notify"]').val('Success');
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        _self._events.trigger('refresh','translate-list-view');
                    } else {
                        $('input[name="notify"]').val(response.message);
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                // this.translate.clear();
                return false;
            },
            clickBtnEditInfo: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            imgageDel: function(event){
                this.translate.clear();
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save(
                    { imageId:this.imageId },
                    { url:UrlApi('_app')+'/langimgdel' }
                ).done(function (response){
                    // if(response.success === true){
                    if(response){
                        _click.closest('li').hide();
                        $('#enlarge_images').html('');
                    }
                    // }else{
                    //     $('.images_list').notify(
                    //             'Has been deleted.', 
                    //             { 
                    //                 position: 'top',
                    //                 className: 'error'
                    //             }
                    //         );
                    // }
                });
                // this.translate.clear();
                return false;
            },
            imagesAdd: function(event){
                this.langId = $(event.target).closest('.images_list').data("id");
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimgadd/lang_id/'+this.langId,
                    'images',
                    function(response) {
                        $('input[name="notify"]').val('Success');
                        $('.images_list').notify(
                                'Success', 
                                { 
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+response['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+response['id']+'">X</a></div></li>');
                    }, 
                    function() {
                        $('input[name="notify"]').val('Upload Failure');
                        $('.images_list').notify(
                                'Upload Failure', 
                                { 
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                    }
                );
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                this._events = options._events;
                this.modify = '';
            },
            setLanguage: function(data){
                this.base_id = parseInt(data.base_id);
                this.other_id = parseInt(data.other_id);
                return this;
            },
            render: function(){
                this.translate.clear();
                var _self = this;
                this.translate.save(
                    {base_id:this.base_id, other_id:this.other_id},
                    {url:UrlApi('_app')+'/langinfo'}
                ).done(function (response){
                    var data = {};
                    data['base_info'] = response.data.base;
                    data['other_info'] = response.data.other;
                    data['langImages'] = response.data.images;
                    _self.modify = response.data.base['modify'];
                    _self.$el.html(_self.template(data));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._edit.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el,{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                // this.translate.clear();
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
                    translate: this.translate,
                    _events: _events
                });

                var listView = new lang.View.LanguageListView({
                    el: '.block-translation-list',
                    _events: _events,
                    translate: this.translate
                });


                var addView = new lang.View.LanguageAddView({
                    el: '.block-translation-add',
                    _events: _events,
                    translate: this.translate
                });


                var searchView = new lang.View.LanguageSearchView({
                    el: '.search-box-lang',
                    _events:_events,
                    translate: this.translate
                });

                // var exportView = new lang.View.LanguageExportView({
                //     el: '.block-translation-export',
                //     translate: this.translate
                // });

                var router = new LangRouter({
                    _events:_events
                });

                _events.on('refresh', function (view){
                    switch (view)
                    {
                        case 'translate-list-view':
                            listView.render();
                            break;
                        case 'list-add':
                            addView.render();
                            break;
                        case 'addRender':
                            listView.addRender();
                            break;
                    }
                });

                _events.on('alernately', function (data,view){
                    switch (view)
                    {
                        case 'search':
                            listView.setList(data).render();
                            break;
                        case 'edit':
                            editView.setLanguage(data).render();
                            break;
                        case 'delete':
                            listView.deleteLanguage(data);
                            break;
                    }
                });

            }
        });

        var translation = new lang.View.TranslationApp({
            translate: new lang.Model.Language()
        });

    Backbone.history.start();

    }).call(self);

});
