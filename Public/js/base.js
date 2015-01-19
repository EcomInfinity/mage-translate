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
            }
        });

        lang.Model.Language = lang.Model.Base.extend({
            constructor: function(){
                Backbone.Model.apply(this,arguments);
            }
        });

        user.Model.Base = Backbone.Model.extend({
            defaults:{
                'timestamp':-1
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
                    {url:UrlApi('_app')+'/Translation/imageClear'}
                    ).done(function (response){
                    _self._events.trigger('refresh','nav');
                    }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
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
                'click .btn-add': 'addLanguage'
            },
            addLanguage: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
                    {url:UrlApi('_app')+'/Translation/add'}
                    ).done(function (response){
                        if(response == '1'){
                            _self.render();
                            _self._events.trigger('refresh','add');
                        }else{
                            $('.tip-langadd').text('Can not all be empty');
                        }
                    }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
                });
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

        lang.View.LanguageImportView = Backbone.View.extend({
            template: _.template($('#tpl-lang-import').html()),
            events: {
                'change #batch-import': 'batchImport'
            },
            batchImport: function(event){
                ajaxFileUpload(
                    UrlApi('_app')+'/Translation/import',
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
                this.render();
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
                    UrlApi('_app')+'/Translation/imageAdd',
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
                    {url:UrlApi('_app')+'/Translation/imageDel'}
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
                    {url:UrlApi('_app')+'/Translation/imageList'}
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
                    {url:UrlApi('_app')+'/Translation/del'}
                    ).done(function (response){
                        if(response == '1'){
                            _self.render();
                        }
                }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
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
                    {url:UrlApi('_app')+'/Translation/getList'}
                    ).done(function (response){
                    data['lists'] = response.lists;
                    _self.$el.html(_self.template(data));
                }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
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
                var _self = this;
                var _change = $(event.target);
                this.langType = _change.attr("lang_type");
                this.langInfo = _change.val();
                this.langId = _change.closest('.form-holder').data("id");
                this.translate.save({langId:this.langId,langInfo:this.langInfo,langType:this.langType},
                    {url:UrlApi('_app')+'/Translation/editInfo'}
                    ).done(function (response){
                        if(response == '1'){
                            _self._events.trigger('refresh','edit');
                        }
                }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
                });
            },
            imgageDel: function(event){
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save({imageId:this.imageId},
                    {url:UrlApi('_app')+'/Translation/imageDel'}
                    ).done(function (response){
                    if(response == '1'){
                        _self.render();
                    }
                }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
                });
            },
            imagesAdd: function(event){
                this.langId = $(event.target).closest('.images_list').data("id");
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/Translation/imageAdd/lang_id/'+this.langId,
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
                this._events = options._events;
            },
            setLanguage: function(langId){
                this.langId = parseInt(langId);
                return this;
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({id:this.langId},
                    {url:UrlApi('_app')+'/Translation/getInfo'}
                    ).done(function (response){
                        data['langDetail'] = response.detail;
                        data['langImages'] = response.images;
                        _self.$el.html(_self.template(data));
                }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
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
                this.translate.save({exrender:'0',field:this.select},
                    {url:UrlApi('_app')+'/Translation/export'}
                    ).done(function (response){
                        window.open(UrlApi('_app')+'/Translation/download');
                    }).fail(function (response){
                    window.open(UrlApi('_app')+'/Admin/logout','_self');
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
                    {url:UrlApi('_app')+'/Translation/export'}
                    ).done(function (response){
                    data['allField'] = response;
                    _self.$el.html(_self.template(data));
                    });
            }
        });

        user.View.RoleAddView = Backbone.View.extend({
            template: _.template($('#tpl-role-add').html()),
            events:{
                'click .btn-role-add': 'roleAdd'
            },
            roleAdd: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.userModel.save(this.data_form,
                    {url:UrlApi('_app')+'/Admin/roleAdd'}
                    ).done(function (response){
                        _self.render();
                        _self._userEvents.trigger('refresh','roleAdd');
                    });
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                if(PurviewVal()=='-1'){
                    this.render();
                }
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save({},
                    {url:UrlApi('_app')+'/Admin/ruleList'}
                    ).done(function (response){
                        data['ruleList'] = response;
                        _self.$el.html(_self.template(data));
                    });
            }
        });

        user.View.UserAddView = Backbone.View.extend({
            template: _.template($('#tpl-user-add').html()),
            events:{
                'click .btn-user-add': 'userAdd'
            },
            userAdd: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.userModel.save(this.data_form,
                    {url:UrlApi('_app')+'/Admin/userAdd'}
                    ).done(function (response){
                        if(response == '1'){
                            _self.render();
                            _self._userEvents.trigger('refresh','userAdd');
                        }else{
                            $('.tip-useradd').text('Username or duplicate username password is empty');
                        }
                    }).fail(function (response){
                        $('.tip-useradd').text('Username or duplicate username password is empty');
                    });
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                if(PurviewVal()=='-1'){
                    this.render();
                }
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save({},
                    {url:UrlApi('_app')+'/Admin/roleList'}
                    ).done(function (response){
                        data['rolelist'] = response;
                        _self.$el.html(_self.template(data));
                    });
            }
        });

        user.View.UserListView = Backbone.View.extend({
            template: _.template($('#tpl-user-list').html()),
            events:{
                'click .btn-allow': 'userAllow',
                'click .btn-detail': 'userInfo'
            },
            userAllow: function(event){
                var _self = this;
                var allow = $(event.target).data('allow'),
                user_id = $(event.target).closest('tr').data('id');
                this.userModel.save({user_id:user_id,allow:allow},
                    {url:UrlApi('_app')+'/Admin/userAllow'}
                    ).done(function (response){
                        _self.render();
                    });
            },
            userInfo: function(event){
                var user_id = $(event.target).closest('tr').data('id');
                this._userEvents.trigger('alernately',user_id,'userList');
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                if(PurviewVal()=='-1'){
                    this.render();
                }
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save({},
                    {url:UrlApi('_app')+'/Admin/userList'}
                    ).done(function (response){
                        data['userList'] = response;
                        _self.$el.html(_self.template(data));
                    });
            }
        });

        user.View.UserInfoView = Backbone.View.extend({
            template: _.template($('#tpl-user-info').html()),
            events:{
                'click .btn-edit': 'userEdit'
            },
            userEdit: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.userModel.save(this.data_form,
                    {url:UrlApi('_app')+'/Admin/userEdit'}
                    ).done(function (response){
                        _self._userEvents.trigger('refresh','userInfo');
                    });
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
                var data = {};
                this.userModel.save({user_id:this.user_id},
                    {url:UrlApi('_app')+'/Admin/userInfo'}
                    ).done(function (response){
                        data['username'] = response.username;
                        data['user_id'] = response.user_id;
                        data['role_id'] = response.role_id;
                        data['rolelist'] = response.rolelist;
                        _self.$el.html(_self.template(data));
                    });

            }
        });

        user.View.UserApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;

                var _userEvents = {};
                _.extend(_userEvents, Backbone.Events);

                var roleView = new user.View.RoleAddView({
                    el: '.block-role-add',
                    userModel: this.userModel,
                    _userEvents:_userEvents
                });

                var useraddView = new user.View.UserAddView({
                    el: '.block-user-add',
                    userModel: this.userModel,
                    _userEvents:_userEvents
                });

                var userlistView = new user.View.UserListView({
                    el: '.block-user-list',
                    userModel: this.userModel,
                    _userEvents:_userEvents
                });

                var userinfoView = new user.View.UserInfoView({
                    el: '.block-user-info',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                _userEvents.on('refresh', function (view){
                    if(view == 'roleAdd'){
                        useraddView.render();
                    }
                    if(view == 'userAdd'){
                        userlistView.render();
                    }
                    if(view == 'userInfo'){
                        userlistView.render();
                    }
                });

                _userEvents.on('alernately', function (data,view){
                    if(view == 'search'){
                        listView.setList(data).render();
                    }
                    if(view == 'userList'){
                        userinfoView.setUser(data).render();
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

                var imagesView = new lang.View.LanguageImagesView({
                    el: '.image-add',
                    translate: this.translate
                });

                var addView = new lang.View.LanguageAddView({
                    el: '.info-add',
                    _events: _events,
                    translate: this.translate
                });

                var importView = new lang.View.LanguageImportView({
                    el: '.batch-import'
                })

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
                    if(view == 'edit'){
                        listView.render();
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