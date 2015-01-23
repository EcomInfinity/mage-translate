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
                'click .btn-delete': 'deleteLanguage',
                'click .btn-list-export': 'exportRender',
                'click .btn-list-add': 'addRender'
            },
            editLanguage: function(event){
                if(Purview('update') == '1'||PurviewVal() == '-1'){
                    this.edit_id = $(event.target).closest('tr').data('id');
                    this._events.trigger('alernately',this.edit_id,'list');
                }
            },
            deleteLanguage: function(event){
                if(Purview('delete') == '1'||PurviewVal() == '-1'){
                    var _self = this;
                    this.del_id = $(event.target).closest('tr').data('id');
                    this.translate.save({id:this.del_id},
                        {url:UrlApi('_app')+'/Translation/del'}
                        ).done(function (response){
                            if(response == '1'){
                                _self.render();
                            }
                    });
                }
            },
            exportRender: function(event){
                if(Purview('retrieve') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('refresh','list-export');
                }
            },
            addRender: function(){
                if(Purview('create') == '1'||PurviewVal() == '-1'){
                    var _self = this;
                    this.translate.save({},
                        {url:UrlApi('_app')+'/Translation/imageClear'}
                        ).done(function (response){
                        _self._events.trigger('refresh','list-add');
                        });
                }
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
                    });
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                this.exrender = '1';
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

        user.View.UserSearchView = Backbone.View.extend({
            template: _.template($('#tpl-user-search').html()),
            events:{
                'keypress .user-search': 'searchUser'
            },
            searchUser: function(event){
                if(event.keyCode == '13'){
                    this.search = $(event.target).val();
                    this._userEvents.trigger('alernately',this.search,'user-search');
                }
            },
            initialize:function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
                if(PurviewVal()=='-1'){
                    this.render();
                }
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });

        user.View.RoleSearchView = Backbone.View.extend({
            template: _.template($('#tpl-role-search').html()),
            events:{
                'keypress .role-search': 'searchRole'
            },
            searchRole: function(event){
                if(event.keyCode == '13'){
                    this.search = $(event.target).val();
                    this._userEvents.trigger('alernately',this.search,'role-search');
                }
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

        user.View.RoleListView = Backbone.View.extend({
            template: _.template($('#tpl-role-list').html()),
            events: {
                'click .btn-role-detail': 'roleInfo',
                'click .btn-list-role-add': 'roleAdd',
                'click .btn-user-list': 'userList'
            },
            roleInfo: function(event){
                var role_id = $(event.target).closest('tr').data('id');
                this._userEvents.trigger('alernately',role_id,'roleList');
            },
            roleAdd: function(){
                this._userEvents.trigger('refresh','list-role-add');
            },
            userList: function(){
                this._userEvents.trigger('refresh','user-list');
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
                var data = {};
                this.userModel.save({search:this.search},
                    {url:UrlApi('_app')+'/Admin/roleList'}
                    ).done(function (response){
                        data['roleList'] = response;
                        _self.$el.html(_self.template(data));
                    });
            }
        });

        user.View.RoleInfoView = Backbone.View.extend({
            template: _.template($('#tpl-role-info').html()),
            events: {
                'click .btn-edit': 'roleEdit'
            },
            roleEdit: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.userModel.save(this.data_form,
                    {url:UrlApi('_app')+'/Admin/roleEdit'}
                    ).done(function (response){
                        if(response == '1'){
                            _self._userEvents.trigger('refresh','roleInfo');
                        }
                    });
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
                this.userModel.save({role_id:this.role_id},
                    {url:UrlApi('_app')+'/Admin/roleInfo'}
                    ).done(function (response){
                        data['roleInfo'] = response;
                        data['ruleList'] = response.rule;
                        data['role_name'] = response.role_name;
                        data['role_id'] = response.role_id;
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
                'click .btn-user-detail': 'userInfo',
                'click .btn-list-user-add': 'userAdd',
                'click .btn-role-list': 'roleList'
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
            userAdd: function(){
                this._userEvents.trigger('refresh','list-user-add');
            },
            roleList: function(){
                this._userEvents.trigger('refresh','role-list');
            },
            setList: function(search){
                this.search = search;
                return this;
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
                this.userModel.save({search:this.search},
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

                _userEvents.on('refresh', function (view){
                    if(view == 'userAdd'){
                        userlistView.render();
                    }
                    if(view == 'userInfo'){
                        userlistView.render();
                    }
                    if(view == 'roleInfo'){
                        rolelistView.render();
                    }
                    if(view == 'list-user-add'){
                        useraddView.render();
                    }
                    if(view == 'role-list'){
                        rolelistView.render();
                        rolesearchView.render();
                    }
                    if(view == 'roleAdd'){
                        rolelistView.render();
                    }
                    if(view == 'list-role-add'){
                        roleaddView.render();
                    }
                    if(view == 'user-list'){
                        userlistView.render();
                        usersearchView.render();
                    }
                });

                _userEvents.on('alernately', function (data,view){
                    if(view == 'search'){
                        listView.setList(data).render();
                    }
                    if(view == 'userList'){
                        userinfoView.setUser(data).render();
                    }
                    if(view == 'roleList'){
                        roleinfoView.setRole(data).render();
                    }
                    if(view == 'user-search'){
                        userlistView.setList(data).render();
                    }
                    if(view == 'role-search'){
                        rolelistView.setList(data).render();
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

                var exportView = new lang.View.LanguageExportView({
                    el: '.block-translation-export',
                    translate: this.translate
                });

                _events.on('refresh', function (view){
                    if(view == 'add'){
                        listView.render();
                        imagesView.render();
                    }
                    if(view == 'edit'){
                        listView.render();
                    }
                    if(view == 'list-export'){
                        exportView.render();
                    }
                    if(view == 'list-add'){
                        addView.render();
                        imagesView.render();
                        importView.render();
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