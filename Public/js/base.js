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

        var LangRouter = Backbone.Router.extend({
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
            },
            routes: {
                "add": "add",
                "export": "export",
                "edit/:id\d": "edit",
                "delete/:id\d": "delete",
            },

            export: function(){
                this._events.trigger('refresh','export');
            },

            add: function(){
                this._events.trigger('refresh','addRender');
            },

            edit: function(id){
                this._events.trigger('alernately',id,'edit');
            },

            delete: function(id){
                this._events.trigger('alernately',id,'delete');
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
                'click .btn-user-center': 'userCenter'
            },
            showUser: function(){
                $('.block-view-user').slideDown("slow");
                $('.block-view-translate').slideUp("slow");
                return false;
            },
            showList: function(){
                $('.block-view-translate').slideDown("slow");
                $('.block-view-user').slideUp("slow");
                return false;
            },
            userCenter: function(event){
                this._userEvents.trigger('refresh','userCenter');
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
                'click .btn-add': 'addLanguage',
                'change #batch-import': 'batchImport',
                'change #images-add': 'imagesAdd',
                'click .btn-image-delete': 'imgageDel'
            },
            addLanguage: function(event){
                var _self = this;
                var $form = $(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
                    {url:UrlApi('_app')+'/langadd'}
                    ).done(function (response){
                        if(response == '1'){
                            $('.tip-langadd').html('<span style="color:green;">Add Success.</span>');
                            lang_add.reset();
                            setTimeout("$('.tip-langadd').empty()",1000);
                            _self._events.trigger('refresh','add');
                        }else if(response == '2'){
                            $('.tip-langadd').text('The data already exists');
                        }else{
                            $('.tip-langadd').text('Can not all be empty');
                        }
                    });
                return false;
            },
            batchImport: function(event){
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimport',
                    'batch-import',
                    function() {
                        _self._events.trigger('refresh','edit');
                        alert('Import Success');
                    },
                    function() {
                        alert('Import Fail');
                    }
                );
            },
            imagesAdd: function(event){
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimgadd',
                    'images-add',
                    function(data) {
                        _self.translate.save({imageId:data},
                            {url:UrlApi('_app')+'/langimg'}
                            ).done(function (response){
                                $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+response.image_name+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+response.id+'">X</a></div></li>');
                        });
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
                    {url:UrlApi('_app')+'/langimgdel'}
                    ).done(function (response){
                        _click.closest('li').hide();
                        $('#enlarge_images').html('');
                });
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._events = options._events;
                this.translate = options.translate;
            },
            render: function(){
                var _self = this;
                var data = {};
                this.$el.html(this.template(data));
                $.fancybox(this.$el,{
                   afterClose: function () {
                        window.history.back();
                    }
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
                'click .btn-list-modify': 'backModify'
            },
            deleteLanguage: function(id){
                if(Purview('delete') == '1'||PurviewVal() == '-1'){
                    if(confirm('Are you sure to delete?') == true){
                        var _self = this;
                        this.translate.save({id:id},
                            {url:UrlApi('_app')+'/langdel'}
                            ).done(function (response){
                                if(response == '1'){
                                    _self.render();
                                }
                        });
                    }
                    window.history.back();
                }
                return false;
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
                        {url:UrlApi('_app')+'/langimgclear'}
                        ).done(function (response){
                            _self._events.trigger('refresh','list-add');
                        });
                }
                return false;
            },
            backModify: function(){
                location.reload();
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
                this.old_showClass = 'show-btn-0';
                this.render();
            },
            render: function(){
                var _self = this;
                var data = {};
                this.translate.save({search:this.search,inrender:this.inrender},
                    {url:UrlApi('_app')+'/langlist'}
                    ).done(function (response){
                    data['lists'] = response.lists;
                    data['count'] = response.count;
                    data['current_count'] = response.current_count;
                    _self.$el.html(_self.template(data));
                });
            }
        });
        //LanguageEdit
        lang.View.LanguageEditView = Backbone.View.extend({
            template: _.template($('#tpl-lang-edit').html()),
            events:{
                'click .btn-lang-save': 'editInfo',
                'click .btn-image-delete': 'imgageDel',
                'change #images': 'imagesAdd'
            },
            editInfo: function(event){
                var _self = this;
                var _change = $(event.target);
                var $form = _change.closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
                    {url:UrlApi('_app')+'/langedit'}
                    ).done(function (response){
                        if(response == '1'){
                            $('.tip-langedit').html('<span style="color:green">Edit Success</span>');
                            setTimeout("$('.tip-langedit').empty()",1000);
                            _self._events.trigger('refresh','edit');
                        }else{
                            $('.tip-langedit').text('Edit Fail');
                            _self._events.trigger('refresh','edit');
                        }
                });
                return false;
            },
            imgageDel: function(event){
                var _self = this;
                var _click = $(event.target);
                this.imageId = _click.attr('image-id');
                this.translate.save({imageId:this.imageId},
                    {url:UrlApi('_app')+'/langimgdel'}
                    ).done(function (response){
                    if(response == '1'){
                        _click.closest('li').hide();
                        $('#enlarge_images').html('');
                    }
                });
                return false;
            },
            imagesAdd: function(event){
                this.langId = $(event.target).closest('.images_list').data("id");
                var _self = this;
                ajaxFileUpload(
                    UrlApi('_app')+'/langimgadd/lang_id/'+this.langId,
                    'images',
                    function(data) {
                        _self.translate.save({imageId:data},
                            {url:UrlApi('_app')+'/langimg'}
                            ).done(function (response){
                                $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+response.image_name+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+response.id+'">X</a></div></li>');
                        });
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
                    {url:UrlApi('_app')+'/langinfo'}
                    ).done(function (response){
                        data['langDetail'] = response.detail;
                        data['langImages'] = response.images;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
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
                    {url:UrlApi('_app')+'/langexport'}
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
                    {url:UrlApi('_app')+'/langexport'}
                    ).done(function (response){
                        data['allField'] = response;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
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

        user.View.UserCenterView = Backbone.View.extend({
            template: _.template($('#tpl-user-center').html()),
            events:{
                'click .btn-edit-center': 'editUserCenter'
            },
            editUserCenter: function(event){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                var pwd_match = this.data_form['original-password'].match(/^[a-zA-Z0-9]{5,15}$/),
                    npwd_match = this.data_form['new-password'].match(/^[a-zA-Z0-9]{5,15}$/),
                    cpwd_match = this.data_form['confirm-new-password'].match(/^[a-zA-Z0-9]{5,15}$/);
                    if(pwd_match!=null&&npwd_match!=null&&cpwd_match!=null){
                        if(this.data_form['new-password'] == this.data_form['confirm-new-password']){
                            this.userModel.save(this.data_form,
                                {url:UrlApi('_app')+'/centeredit'}
                                ).done(function (response){
                                    if(response == '1'){
                                        $('.tip-center-main').html('<span style="color: green;">Modify Success and Quit after 3 seconds</span>');
                                        setTimeout("window.open(UrlApi('_app')+'/logout','_self')",3000);
                                    }else if(response == '2'){
                                        $('.tip-confirm-new-password').text('Please make sure your passwords match.');
                                    }else if(response == '3'){
                                        $('.tip-center-main').text('The password is incorrect.');
                                    }else{
                                        $('.tip-center-main').text('Modify fail.');
                                    }
                                });
                        }else{
                            $('.tip-confirm-new-password').text('Please make sure your passwords match.');
                        }
                    }else{
                        $('.tip-center-main').text('Password must be from 5-15 digits or letters.');
                    }
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
                this.userModel = options.userModel;
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
                $.fancybox(this.$el);
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
                if(this.data_form['role'].match(/^.{1,20}$/)!=null){
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/roleadd'}
                        ).done(function (response){
                            if(response == '1'){
                                $('.tip-roleadd').html('<span style="color:green;">Add Success.</span>');
                                role_add.reset();
                                setTimeout("$('.tip-roleadd').empty()",1000);
                                _self._userEvents.trigger('refresh','roleAdd');
                            }else{
                                $('.tip-roleadd').text('Add Fail');
                            }
                        });
                }else{
                    $('.tip-roleadd').text('Rolename must have 1-20 characters');
                }
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
                this.userModel.save({},
                    {url:UrlApi('_app')+'/rulelist'}
                    ).done(function (response){
                        data['ruleList'] = response;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
                    });
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
                var data = {};
                this.userModel.save({search:this.search},
                    {url:UrlApi('_app')+'/rolelist'}
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
                if(this.data_form['role_name'].match(/^.{1,20}$/)!=null){
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/roleedit'}
                        ).done(function (response){
                            if(response == '1'){
                                $('.tip-roleedit').html('<span style="color: green;">Edit Success</span>');
                                setTimeout("$('.tip-roleedit').empty()",1000);
                                _self._userEvents.trigger('refresh','roleInfo');
                            }else{
                                $('.tip-roleedit').text('Edit Fail');
                            }
                        });
                }else{
                    $('.tip-roleedit').text('Rolename must have 1-20 characters');
                }
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
                this.userModel.save({role_id:this.role_id},
                    {url:UrlApi('_app')+'/roleinfo'}
                    ).done(function (response){
                        data['roleInfo'] = response;
                        data['ruleList'] = response.rule;
                        data['role_name'] = response.role_name;
                        data['role_id'] = response.role_id;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
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
                var user_match = this.data_form['username'].match(/^[a-zA-Z0-9]{5,15}$/),
                    pwd_match = this.data_form['password'].match(/^[a-zA-Z0-9]{5,15}$/);
                if(user_match!=null&&pwd_match!=null){
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/useradd'}
                        ).done(function (response){
                            if(response == '1'){
                                $('.tip-useradd').html('<span style="color:green;">Add Success.</span>');
                                user_add.reset();
                                setTimeout("$('.tip-useradd').empty()",1000);
                                _self._userEvents.trigger('refresh','userAdd');
                            }else{
                                $('.tip-useradd').text('Username duplicate or username password is empty');
                            }
                        }).fail(function (response){
                            $('.tip-useradd').text('Username duplicate or username password is empty');
                        });
                    }else{
                        $('.tip-useradd').text('Username and password must be from 5-15 digits or letters');
                    }
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
                this.userModel.save({},
                    {url:UrlApi('_app')+'/rolelist'}
                    ).done(function (response){
                        data['rolelist'] = response;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
                    });
            }
        });

        user.View.UserListView = Backbone.View.extend({
            template: _.template($('#tpl-user-list').html()),
            events:{
                'click .btn-allow': 'userAllow',
                'click .btn-role-list': 'roleList'
            },
            userAllow: function(event){
                var _self = this;
                var allow = $(event.target).data('allow'),
                user_id = $(event.target).closest('tr').data('id');
                this.userModel.save({user_id:user_id,allow:allow},
                    {url:UrlApi('_app')+'/userallow'}
                    ).done(function (response){
                        if(response == '1'){
                            $(event.target).addClass('btn-success');
                            $(event.target).siblings().removeClass('btn-success');
                            $(event.target).siblings().addClass('btn-default');
                        }
                    });
                return false;
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
                if(PurviewVal()=='-1'){
                    this.render();
                }
            },
            render: function(){
                var _self = this;
                var data = {};
                this.userModel.save({search:this.search},
                    {url:UrlApi('_app')+'/userlist'}
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
                var is_change = '0',
                _self = this,
                $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                var user_match = this.data_form['username'].match(/^[a-zA-Z0-9]{5,15}$/),
                    pwd_match = this.data_form['password'].match(/^[a-zA-Z0-9]{5,15}$/);
                if(this.data_form['password'] == ''){
                    pwd_match = '1';
                }
                if(user_match!=null&&pwd_match!=null){
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/useredit'}
                        ).done(function (response){
                            _self._userEvents.trigger('refresh','userInfo');
                            if(response == '1'){
                                $('.tip-useredit').html('<span style="color: green;">Edit Success</span>');
                                setTimeout("$('.tip-useredit').empty()",1000);
                            }else{
                                $('.tip-useredit').text('Edit Fail');
                            }
                        });
                    }else{
                        $('.tip-useredit').text('Username and password must be from 5-15 digits or letters');
                    }
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
                var data = {};
                this.userModel.save({user_id:this.user_id},
                    {url:UrlApi('_app')+'/userinfo'}
                    ).done(function (response){
                        data['username'] = response.username;
                        data['user_id'] = response.user_id;
                        data['role_id'] = response.role_id;
                        data['rolelist'] = response.rolelist;
                        _self.$el.html(_self.template(data));
                        $.fancybox(_self.$el,{
                           afterClose: function () {
                                window.history.back();
                            }
                        });
                    });
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

                var usercenterView = new user.View.UserCenterView({
                    el: '.user-center',
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
                        case 'userCenter':
                            usercenterView.render();
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


                // var navView = new lang.View.LanguageNavView({
                //     el: '.navbar-collapse',
                //     _events: _events,
                //     translate: this.translate
                // });

                var searchView = new lang.View.LanguageSearchView({
                    el: '.search-box',
                    _events:_events,
                    translate: this.translate
                });

                var exportView = new lang.View.LanguageExportView({
                    el: '.block-translation-export',
                    translate: this.translate
                });

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
                        case 'list-export':
                            exportView.render();
                            break;
                        case 'list-add':
                            addView.render();
                            break;
                        case 'addRender':
                            listView.addRender();
                            break;
                        case 'export':
                            listView.exportRender();
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
