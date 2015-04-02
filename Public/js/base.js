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

        user.Model = {};
        user.Collection = {};
        user.View = {};

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

        user.Model.Base = Backbone.Model.extend({
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

        var UserRouter = Backbone.Router.extend({
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
            },
            routes: {
                "useradd": "userAdd",
                "useredit/:id\d": "userEdit",
                "roleadd": "roleAdd",
                "roleedit/:id\d": "roleEdit",
            },

            userAdd: function(){
                this._userEvents.trigger('refresh','list-user-add');
            },

            userEdit: function(id){
                this._userEvents.trigger('alernately',id,'userList');
            },

            roleAdd: function(){
                this._userEvents.trigger('refresh','list-role-add');
            },

            roleEdit: function(id){
                this._userEvents.trigger('alernately',id,'roleList');
            }
        });


        //Navigation
        lang.View.LanguageNavView = Backbone.View.extend({
            template: _.template($('#tpl-lang-nav').html()),
            events:{
                'click .btn-user': 'showUser',
                'click .btn-list': 'showList',
                'click .btn-user-center': 'personalCenter'
            },
            showUser: function(){
                console.log('1');
                if(PurviewVal() == '-1'){
                    this._userEvents.trigger('refresh','userhandle');
                    $('.block-view-user').slideDown("slow");
                    $('.block-view-translate').slideUp("slow");
                    $('.block-view-personal').slideUp("slow");
                }
                return false;
            },
            showList: function(){
                // $('.block-view-translate').slideDown("slow");
                // $('.block-view-user').slideUp("slow");
                // $('.block-view-personal').slideUp("slow");
                location.reload();
                return false;
            },
            personalCenter: function(event){
                $('.block-view-personal').slideDown("slow");
                $('.block-view-user').slideUp("slow");
                $('.block-view-translate').slideUp("slow");
                // if(PurviewVal() == '-1'){
                    this._userEvents.trigger('refresh','personalCenter');
                // }else{
                //     this._userEvents.trigger('refresh','personal');
                // }
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
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
                var _self = this;
                var $form = $('.btn-add').closest('form');
                // console.log($form.serializeObject());
                this.translate.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/langadd'}
                ).done(function (response){
                    if (response.success === true) {
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        lang_add.reset();
                        $('.images_list ul li').hide();
                        _self._events.trigger('refresh','add');
                    } else {
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.translate.clear();
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
                        _self._events.trigger('refresh','edit');
                        $.fancybox.hideLoading();
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
                        $('.images_list').notify(
                                'Success.', 
                                { 
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+data['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+data['id']+'">X</a></div></li>');
                    },
                    function() {
                        $('.images_list').notify(
                                'Upload Failure.', 
                                { 
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                    }
                );
            },
            imgageDel: function(event){
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
                this.translate.clear();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
            },
            render: function(){
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
                this.translate.clear();
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
                var _self = this;
                this.data = {};
                this.operation = $(event.target).val();
                if($('input:checked').length < 1){
                    return;
                }
                $('input:checked').each(function (i){
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
                            this.translate.clear();
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
                                this.translate.clear();
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
                if(confirm('Are you sure to delete?') == true){
                    var _self = this;
                    this.translate.save(
                        {id: id},
                        {url:UrlApi('_app')+'/langdel'}
                    ).done(function (response){
                        if(response.success === true){
                            _self.render();
                        }
                    });
                    this.translate.clear();
                }
                window.history.back();
                return false;
            },
            exportList: function(event){
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
                        this.translate.clear();
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
                var _self = this;
                this.translate.save(
                    {},
                    {url:UrlApi('_app')+'/langimgclear'}
                    ).done(function (response){
                        _self._events.trigger('refresh','list-add');
                    });
                    this.translate.clear();
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
                this.translate.clear();
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
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        _self._events.trigger('refresh','edit');
                    } else {
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.translate.clear();
                return false;
            },
            clickBtnEditInfo: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            imgageDel: function(event){
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save(
                    { imageId:this.imageId },
                    { url:UrlApi('_app')+'/langimgdel' }
                ).done(function (response){
                    if(response.success === true){
                        _click.closest('li').hide();
                        $('#enlarge_images').html('');
                    }
                });
                this.translate.clear();
                return false;
            },
            imagesAdd: function(event){
                this.langId = $(event.target).closest('.images_list').data("id");
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimgadd/lang_id/'+this.langId,
                    'images',
                    function(response) {
                        $('.images_list').notify(
                                'Success.', 
                                { 
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+response['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+response['id']+'">X</a></div></li>');
                    }, 
                    function() {
                        $('.images_list').notify(
                                'Upload Failure.', 
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
                this.translate.clear();
            }
        });
        // //export
        // lang.View.LanguageExportView = Backbone.View.extend({
        //     template: _.template($('#tpl-lang-export').html()),
        //     exportLanguage: function(id){
        //         console.log(id);
        //         this.select = $('#export').val();
        //         this.translate.save(
        //             {
        //                 lang_id: id
        //             },
        //             {url:UrlApi('_app')+'/langexport'}
        //             ).done(function (response){
        //                 if(response.success === true){
        //                     window.open(UrlApi('_app')+'/langdownload');
        //                 }
        //             });
        //     },
        //     initialize: function(options){
        //         options || (options = {});
        //         this.translate = options.translate;
        //         this.exrender = true;
        //     },
        //     render: function(){
        //         var _self = this;
        //         var data = {};
        //         this.translate.save(
        //             {exrender:this.exrender},
        //             {url:UrlApi('_app')+'/langexport'}
        //         ).done(function (response){
        //             data['allField'] = response.data;
        //             _self.$el.html(_self.template(data));
        //             $.fancybox(_self.$el,{
        //                afterClose: function () {
        //                 }
        //             });
        //         });
        //     }
        // });

        user.View.UserSearchView = Backbone.View.extend({
            template: _.template($('#tpl-user-search').html()),
            events:{
                'keypress .user-search': 'searchUser',
                'focus .user-search': 'searchFocus',
                'click .search-user-clear': 'searchClear',
                'click .search-user-enter': 'searchEnter'
            },
            searchUser: function(event){
                if(event.keyCode == '13'){
                    $('.search-user-enter').hide();
                    $('.search-user-clear').show();
                    $('.user-search').blur();
                    this.search = $(event.target).val();
                    this._userEvents.trigger('alernately',this.search,'user-search');
                }
            },
            searchFocus: function (event){
                $('.search-user-enter').show();
                $('.search-user-clear').hide();
            },
            searchClear: function (event){
                $('.user-search').val('');
                $('.user-search').focus();
                this._userEvents.trigger('alernately','','user-search');
                $('.search-user-enter').show();
                $('.search-user-clear').hide();
                return false;
            },
            searchEnter: function (event){
                $('.user-search').blur();
                $('.search-user-enter').hide();
                $('.search-user-clear').show();
                this.search = $('.user-search').val();
                this._userEvents.trigger('alernately',this.search,'user-search');
                return false;
            },
            initialize:function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
                // if(PurviewVal()=='-1'){
                //     this.render();
                // }
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });

        user.View.PersonalSidebar = Backbone.View.extend({
            template: _.template($('#tpl-personal-sidebar').html()),
            events: {
                'click .website': 'websiteSetting',
                'click .personal': 'personalSetting',
                'click .rest-sync': 'restSyncSetting',
                'click .site-language': 'siteLanguageSetting'
            },
            'websiteSetting': function (event){
                if(Purview('update') == '1'||PurviewVal() == '-1'){
                    $('.menu-selection').removeClass('menu-selection');
                    $(event.target).closest('li').addClass('menu-selection');
                    this._userEvents.trigger('refresh', 'website-setting');
                    $('.block-personal-container .block').hide();
                    $('.block-website-setting').show();
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
            },
            'personalSetting': function (event){
                $('.menu-selection').removeClass('menu-selection');
                $(event.target).closest('li').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'personal-setting');
                $('.block-personal-container .block').hide();
                $('.block-personal-setting').show();
            },
            'restSyncSetting': function (event){
                if(Purview('update') == '1'||PurviewVal() == '-1'){
                    $('.menu-selection').removeClass('menu-selection');
                    $(event.target).closest('li').addClass('menu-selection');
                    this._userEvents.trigger('refresh', 'rest-setting');
                    $('.block-personal-container .block').hide();
                    $('.block-rest-sync').show();
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
            },
            'siteLanguageSetting': function (event){
                if(Purview('update') == '1'||PurviewVal() == '-1'){
                    $('.menu-selection').removeClass('menu-selection');
                    $(event.target).closest('li').addClass('menu-selection');
                    this._userEvents.trigger('refresh', 'language-setting');
                    $('.block-personal-container .block').hide();
                    $('.block-site-language').show();
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
            },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.render();
            },
            render: function(){
                this.$el.html(this.template({}));
            }
        });

        user.View.WebsiteSettingView = Backbone.View.extend({
            template: _.template($('#tpl-website-setting').html()),
            events: {
                'click .btn-website-setting': 'clickBtnWebsiteSetting',
            },
            changeWebsiteName: function(){
                var _self = this;
                var $form = this.$el.find('form');
                this.userModel.save(
                    $form.serializeObject(),
                    {url: UrlApi('_app')+'/save-name'}
                ).done(function (response){
                    if(response.success === true){
                        _self.$el.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                    }else{
                        _self.$el.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
            },
            clickBtnWebsiteSetting: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
            },
            render: function(){
                var _self = this;
                this.$el.html(this.template({}));
                this.$el.find('form').validator().on('submit', function(e) {
                    if (e.isDefaultPrevented()) {
                    } else {
                        _self.changeWebsiteName.call(_self);
                        return false;
                    }
                });
            }
        });

        user.View.PersonalSettingView = Backbone.View.extend({
            template: _.template($('#tpl-personal-setting').html()),
            events:{
                'click .btn-personal-setting': 'clickBtnPersonalSetting',
            },
            _edit: function(){
                var _self = this;
                var $form = this.$el.find('form');
                // console.log($form.serializeObject());
                this.userModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/personal-setting'}
                ).done(function (response){
                    if(response.success === true){
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                    }else{
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            clickBtnPersonalSetting: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
            },
            render: function(){
                var _self = this;
                this.$el.html(this.template({}));
                this.$el.find('form').validator().on('submit', function(e) {
                    if (e.isDefaultPrevented()) {
                    } else {
                        _self._edit.call(_self);
                        return false;
                    }
                });
                // $.fancybox(this.$el);
            }
        });

        user.View.RestSyncView = Backbone.View.extend({
            template: _.template($('#tpl-rest-sync').html()),
            events:{
                'click .btn-rest-sync': 'clickBtnRestSync'
            },
            _edit: function(){
                var _self = this;
                var $form = this.$el.find('form');
                // console.log($form.serializeObject());
                this.userModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/rest-sync'}
                ).done(function (response){
                    if(response.success === true){
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                    }else{
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            clickBtnRestSync: function(event){
                $(event.target).closest('form').submit();
                return false; 
            },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
            },
            render: function(){
                var _self = this;
                this.userModel.save({},
                    {url: UrlApi('_app')+'/websiteinfo'}
                ).done(function (response){
                    _self.$el.html(_self.template({'website': response.data}));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._edit.call(_self);
                            return false;
                        }
                    });
                });
                this.userModel.clear();
            }
        });

        user.View.SiteLanguageView = Backbone.View.extend({
            template: _.template($('#tpl-site-language').html()),
            events:{
                'click .btn-checked': 'addLang',
                'click .btn-unchecked': 'delLang'
                // 'click .btn-personal-setting': 'clickBtnPersonalSetting'
            },
            addLang: function (){
                var _self = this;
                this.userModel.save(
                    {'site_lang_id': $('.language-unchecked select').val()},
                    {url: UrlApi('_app')+'/site-lang-add'}
                ).done(function (response){
                    if(response.success === true){
                        _self.$el.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        setTimeout(_self.render(),2000);
                    }else{
                        _self.$el.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            delLang: function (){
                var _self = this;
                this.userModel.save(
                    {'site_lang_id': $('.language-checked select').val()},
                    {url: UrlApi('_app')+'/site-lang-del'}
                ).done(function (response){
                    if(response.success === true){
                        _self.$el.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        setTimeout(_self.render(),2000);
                    }else{
                        _self.$el.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            _edit: function(){
                var _self = this;
                var $form = this.$el.find('form');
                // console.log($form.serializeObject());
                this.userModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/personal-setting'}
                ).done(function (response){
                    if(response.success === true){
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                    }else{
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            // clickBtnPersonalSetting: function(event){
            //     $(event.target).closest('form').submit();
            //     return false;
            // },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
            },
            render: function(){
                var _self = this;
                this.userModel.save({},
                    {url: UrlApi('_app')+'/lang-info'}
                ).done(function (response){
                    var data = {};
                    data['lang_checked'] = response.data.checked;
                    data['lang_unchecked'] = response.data.unchecked;
                    data['lang_needchecked'] = response.data.needchecked;
                    _self.$el.html(_self.template(data));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._edit.call(_self);
                            return false;
                        }
                    });
                });
                this.userModel.clear();
            }
        });

        user.View.RoleSearchView = Backbone.View.extend({
            template: _.template($('#tpl-role-search').html()),
            events:{
                'keypress .role-search': 'searchRole',
                'focus .role-search': 'searchFocus',
                'click .search-clear': 'searchClear',
                'click .search-enter': 'searchEnter'
            },
            searchRole: function(event){
                if(event.keyCode == '13'){
                    $('.search-enter').hide();
                    $('.search-clear').show();
                    $('.role-search').blur();
                    this.search = $(event.target).val();
                    this._userEvents.trigger('alernately',this.search,'role-search');
                }
            },
            searchFocus: function (event){
                $('.search-enter').show();
                $('.search-clear').hide();
            },
            searchClear: function (event){
                $('.role-search').val('');
                $('.role-search').focus();
                this._userEvents.trigger('alernately','','role-search');
                $('.search-enter').show();
                $('.search-clear').hide();
                return false;
            },
            searchEnter: function (event){
                $('.role-search').blur();
                $('.search-enter').hide();
                $('.search-clear').show();
                this.search = $('.role-search').val();
                this._userEvents.trigger('alernately',this.search,'role-search');
                return false;
            },
            initialize:function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.translate = options.translate;
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });

        user.View.RoleAddView = Backbone.View.extend({
            template: _.template($('#tpl-role-add').html()),
            events:{
                'click .btn-role-add': 'clickBtnRoleAdd'
            },
            _add: function(){
                var _self = this;
                var $form = this.$el.find('form');
                    this.userModel.save(
                        $form.serializeObject(),
                        {url:UrlApi('_app')+'/roleadd'}
                    ).done(function (response){
                        if (response.success === true) {
                            $form.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            role_add.reset();
                            setTimeout("$('.tip-roleadd').empty()",1000);
                            _self._userEvents.trigger('refresh','roleAdd');
                        } else {
                            $form.notify(
                                response.message,
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                this.userModel.clear();
                return false;
            },
            clickBtnRoleAdd: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save(
                    {},
                    { url: UrlApi('_app')+'/rulelist' }
                ).done(function (response){
                    _self.$el.html(_self.template({ ruleList: response.data }));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._add.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el, {
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                this.userModel.clear();
            }
        });

        user.View.RoleListView = Backbone.View.extend({
            template: _.template($('#tpl-role-list').html()),
            events: {
                'click .btn-user-list': 'userList'
            },
            userList: function(){
                this._userEvents.trigger('refresh','user-list');
                $('.block-role').slideUp("slow");
                $('.block-user').slideDown("slow");
                return false;
            },
            setList: function(search){
                this.search = search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            render: function(){
                var _self = this;
                this.userModel.save(
                    { search: this.search },
                    { url: UrlApi('_app')+'/rolelist' }
                ).done(function (response){
                    _self.$el.html(_self.template({roleList: response.data.roles,current_count:response.data.count,'count':response.data.total}));
                });
                this.userModel.clear();
            }
        });

        user.View.RoleInfoView = Backbone.View.extend({
            template: _.template($('#tpl-role-info').html()),
            events: {
                'click .btn-edit': 'clickBtnRoleEdit'
            },
            _edit: function(){
                var _self = this;
                var $form = this.$el.find('form');
                    this.userModel.save(
                        $form.serializeObject(),
                        { url:UrlApi('_app')+'/roleedit' }
                    ).done(function (response){
                       if (response.success === true) {
                            $form.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            _self._userEvents.trigger('refresh','roleInfo');
                        } else {
                            $form.notify(
                                response.message,
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                this.userModel.clear();
                return false;
            },
            clickBtnRoleEdit: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            setRole: function(roleId){
                this.role_id = parseInt(roleId);
                return this;
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save(
                    {role_id:this.role_id},
                    {url:UrlApi('_app')+'/roleinfo'}
                ).done(function (response){
                    data['roleInfo'] = response.data;
                    data['ruleList'] = response.data.rule;
                    data['role_name'] = response.data.role_name;
                    data['role_id'] = response.data.role_id;
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
                this.userModel.clear();
            }
        });

        user.View.UserAddView = Backbone.View.extend({
            template: _.template($('#tpl-user-add').html()),
            events: {
                'click .btn-user-add': 'clickBtnUserAdd'
            },
            _add: function() {
                var _self = this,
                    $form = this.$el.find('form');

                this.userModel.save(
                    $form.serializeObject(), 
                    { url: UrlApi('_app') + '/useradd' }
                ).done(function(response) {
                    if (response.success === true) {
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        user_add.reset();
                            _self._userEvents.trigger('refresh', 'userAdd');
                    } else {
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
            },
            clickBtnUserAdd: function(event) {
                this.$el.find('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            render: function(){
                var _self = this;
                this.userModel.save(
                    {},
                    {
                        url: UrlApi('_app')+'/rolelist'
                    }
                ).done(function (response){
                    _self.$el.html(_self.template({'rolelist': response.data.roles}));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._add.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el, {
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                this.userModel.clear();
            }
        });

        user.View.UserListView = Backbone.View.extend({
            template: _.template($('#tpl-user-list').html()),
            events:{
                'click .btn-role-list': 'roleList'
            },
            enable: function(elem) {
                var user_id = $(elem).closest('tr').data('id');
                this.userModel.save(
                    { user_id: user_id },
                    { url: UrlApi('_app')+'/User/enable' }
                ).done(function(response) {
                });
                this.userModel.clear();
            },
            disable: function(elem) {
                var user_id = $(elem).closest('tr').data('id');
                this.userModel.save(
                    { user_id: user_id },
                    { url: UrlApi('_app')+'/User/disable' }
                ).done(function(response) {
                });
                this.userModel.clear();
            },
            roleList: function(){
                this._userEvents.trigger('refresh','role-list');
                $('.block-user').slideUp("slow");
                $('.block-role').slideDown("slow");
                return false;
            },
            setList: function(search){
                this.search = search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                // if(PurviewVal()=='-1'){
                //     this.render();
                // }
            },
            render: function(){
                var _self = this;
                this.userModel.save(
                    {search:this.search},
                    {url:UrlApi('_app')+'/userlist'}
                ).done(function (response){
                    if(response.success === true){
                        _self.$el.html(_self.template({userList: response.data.users,current_count:response.data.count,count:response.data.total}));
                        _self.$el.find('.ipt-checkbox-allow').bootstrapSwitch();
                        _self.$el.find('.ipt-checkbox-allow').on('switchChange.bootstrapSwitch', function(event, state) {
                            if (state === true) {
                                _self.enable.call(_self, this);
                            } else {
                                _self.disable.call(_self, this);
                            }
                        });
                    }
                });
                this.userModel.clear();
            }
        });

        user.View.UserInfoView = Backbone.View.extend({
            template: _.template($('#tpl-user-info').html()),
            events:{
                'click .btn-edit': 'clickBtnUserEdit'
            },
            _edit: function(){
                var _self = this,
                    $form=this.$el.find('form');
                this.userModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/useredit'}
                ).done(function (response){
                    if(response.success === true){
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        _self._userEvents.trigger('refresh','userInfo');
                    }else{
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                this.userModel.clear();
                return false;
            },
            clickBtnUserEdit: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            setUser: function(userId){
                this.user_id = parseInt(userId);
                return this;
            },
            render: function(){
                var _self = this;
                this.userModel.save(
                    {user_id:this.user_id},
                    {url:UrlApi('_app')+'/userinfo'}
                ).done(function (response) {
                    if(response.success === true){
                        var data = {};
                        data['username'] = response.data.username;
                        data['user_id'] = response.data.user_id;
                        data['role_id'] = response.data.role_id;
                        data['rolelist'] = response.data.rolelist;
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
                    }
                });
                this.userModel.clear();
            }
        });

        user.View.UserApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;

                var _userEvents = {};
                _.extend(_userEvents, Backbone.Events);

                this.navView = new lang.View.LanguageNavView({
                    el: '.navbar-collapse',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var personalsidebarView = new user.View.PersonalSidebar({
                    el: '.block-personal-sidebar',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var websitesettingView = new user.View.WebsiteSettingView({
                    el: '.block-website-setting',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var personalsettingView = new user.View.PersonalSettingView({
                    el: '.block-personal-setting',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var restsyncView = new user.View.RestSyncView({
                    el: '.block-rest-sync',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var sitelanguageView = new user.View.SiteLanguageView({
                    el: '.block-site-language',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var usersearchView = new user.View.UserSearchView({
                    el: '.search-box-user',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var rolesearchView = new user.View.RoleSearchView({
                    el: '.search-box-user',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var roleinfoView = new user.View.RoleInfoView({
                    el: '.block-role-info',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var rolelistView = new user.View.RoleListView({
                    el: '.block-role-list',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var roleaddView = new user.View.RoleAddView({
                    el: '.block-role-add',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var useraddView = new user.View.UserAddView({
                    el: '.block-user-add',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var userlistView = new user.View.UserListView({
                    el: '.block-user-list',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var userinfoView = new user.View.UserInfoView({
                    el: '.block-user-info',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var router = new UserRouter({
                    _userEvents: _userEvents
                });

                _userEvents.on('refresh', function (view){
                    switch (view)
                    {
                        case 'userAdd':
                            userlistView.render();
                            break;
                        case 'roleInfo':
                            rolelistView.render();
                            break;
                        case 'list-user-add':
                            useraddView.render();
                            break;
                        case 'role-list':
                            rolelistView.render();
                            rolesearchView.render();
                            break;
                        case 'roleAdd':
                            rolelistView.render();
                            break;
                        case 'list-role-add':
                            roleaddView.render();
                            break;
                        case 'user-list':
                            userlistView.render();
                            usersearchView.render();
                            break;
                        case 'userInfo':
                            userlistView.render();
                            break;
                        case 'personalCenter':
                            // websitesettingView.render();
                            personalsettingView.render();
                            // restsyncView.render();
                            // sitelanguageView.render();
                            break;
                        // case 'personalCenter':
                        //     websitesettingView.render();
                        //     personalsettingView.render();
                        //     break;
                        case 'website-setting':
                            websitesettingView.render();
                            break;
                        case 'personal-setting':
                            personalsettingView.render();
                        case 'rest-setting':
                            restsyncView.render();
                            break;
                        case 'language-setting':
                            sitelanguageView.render();
                            break;
                        case 'userhandle':
                            usersearchView.render();
                            userlistView.render();
                            break;
                    }
                });

                _userEvents.on('alernately', function (data,view){
                    switch (view)
                    {
                        case 'search':
                            listView.setList(data).render();
                            break;
                        case 'userList':
                            userinfoView.setUser(data).render();
                            break;
                        case 'roleList':
                            roleinfoView.setRole(data).render();
                            break;
                        case 'user-search':
                            userlistView.setList(data).render();
                            break;
                        case 'role-search':
                            rolelistView.setList(data).render();
                            break;
                    }
                });
            }
        });

        var userApp = new user.View.UserApp({
            userModel: new user.Model.Base()
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
                        case 'add':
                            listView.render();
                            break;
                        case 'edit':
                            listView.render();
                            break;
                        // case 'list-export':
                        //     exportView.render();
                        //     break;
                        case 'list-add':
                            addView.render();
                            break;
                        case 'addRender':
                            listView.addRender();
                            break;
                        // case 'export':
                        //     listView.exportList();
                        //     break;
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
                        // case 'export-list':
                        //     exportView.exportLanguage(data);
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
