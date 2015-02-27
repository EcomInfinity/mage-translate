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
                //导出权限
                if(Purview('retrieve') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('refresh','export');
                }
            },

            add: function(){
                //增加权限
                if(Purview('create') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('refresh','addRender');
                }
            },

            edit: function(id){
                //更新权限
                if(Purview('update') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('alernately',id,'edit');
                }
            },

            delete: function(id){
                //删除权限
                if(Purview('delete') == '1'||PurviewVal() == '-1'){
                    this._events.trigger('alernately',id,'delete');
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
                'click .btn-add': 'clickBtnLangAdd',
                'change #batch-import': 'batchImport',
                'change #images-add': 'imagesAdd',
                'click .btn-image-delete': 'imgageDel'
            },
            _add: function(){
                var _self = this;
                var $form = $(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
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
                return false;
            },
            clickBtnLangAdd: function(event){
                $(event.target).closest('form').submit();
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
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+data['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+data['id']+'">X</a></div></li>');
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
                this.$el.find('[name="lang_add"]').validator().on('submit', function(e) {
                    if (e.isDefaultPrevented()) {
                    } else {
                        _self._add.call(_self);
                        return false;
                    }
                });
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
                'click .btn-list-sort': 'backSort'
            },
            deleteLanguage: function(id){
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
                return false;
            },
            exportRender: function(event){
                this._events.trigger('refresh','list-export');
            },
            addRender: function(){
                var _self = this;
                this.translate.save({},
                    {url:UrlApi('_app')+'/langimgclear'}
                    ).done(function (response){
                        _self._events.trigger('refresh','list-add');
                    });
                return false;
            },
            backSort: function(event){
                var sort = $('table').attr('data-sort');
                if(sort == 'modify'){
                    this.inrender = '1';
                    this.searchData = {search:'',inrender:this.inrender};
                    this._events.trigger('alernately',this.searchData,'search');
                }
                if(sort == 'search'){
                    this.setList({inrender:'0'}).render();
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
                    {url:UrlApi('_app')+'/langlist'}
                    ).done(function (response){
                    data['lists'] = response.lists;
                    data['count'] = response.count;
                    data['current_count'] = response.current_count;
                    _self.$el.html(_self.template(data));
                    if(_self.inrender == '1'){
                        $('table').attr('data-sort','search');
                    }
                });
            }
        });
        //LanguageEdit
        lang.View.LanguageEditView = Backbone.View.extend({
            template: _.template($('#tpl-lang-edit').html()),
            events:{
                'click .btn-lang-save': 'clickBtnEditInfo',
                'click .btn-image-delete': 'imgageDel',
                'change #images': 'imagesAdd'
            },
            _edit: function(){
                var _self = this;
                var _change = $(event.target);
                var $form = _change.closest('form');
                this.data_form = $form.serializeObject();
                this.translate.save(this.data_form,
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
                        $('.images_list ul').append('<li><a href="#"><img src="'+UrlApi('_uploads')+'/Translation/'+data['image_name']+'" alt=""></a><div class="btn-set"><a href="#" class="btn btn-image-delete" image-id="'+data['id']+'">X</a></div></li>');
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
            }
        });
        //export
        lang.View.LanguageExportView = Backbone.View.extend({
            template: _.template($('#tpl-lang-export').html()),
            events:{
                'click .btn-export': 'exportLanguage'
            },
            exportLanguage: function(event){
                this.clickExport = '1';
                this.select = $('#export').val();
                this.translate.save({exrender:'0',field:this.select},
                    {url:UrlApi('_app')+'/langexport'}
                    ).done(function (response){
                        window.open(UrlApi('_app')+'/langdownload');
                    });
            },
            initialize: function(options){
                options || (options = {});
                this.translate = options.translate;
                this.exrender = '1';
                this.clickExport = '0';
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
                                if(_self.clickExport == '0'){
                                    window.history.back();
                                }else{
                                    _self.clickExport = '0';
                                }
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
                'click .btn-edit-center': 'clickBtnUserCenter'
            },
            _edit: function(){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                            this.userModel.save(this.data_form,
                                {url:UrlApi('_app')+'/centeredit'}
                                ).done(function (response){
                                    if(response.success === true){
                                        $form.notify(
                                            'Success',
                                            {
                                                position: 'top',
                                                className: 'success'
                                            }
                                        );
                                        setTimeout("window.open(UrlApi('_app')+'/logout','_self')",3000);
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
                return false;
            },
            clickBtnUserCenter: function(event){
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
                'click .btn-role-add': 'clickBtnRoleAdd'
            },
            _add: function(){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                    this.userModel.save(this.data_form,
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
                this.userModel.save({},
                    {url:UrlApi('_app')+'/rulelist'}
                    ).done(function (response){
                        data['ruleList'] = response;
                        _self.$el.html(_self.template(data));
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
                'click .btn-edit': 'clickBtnRoleEdit'
            },
            _edit: function(){
                var _self = this;
                var $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/roleedit'}
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
                this.userModel.save({role_id:this.role_id},
                    {url:UrlApi('_app')+'/roleinfo'}
                    ).done(function (response){
                        data['roleInfo'] = response;
                        data['ruleList'] = response.rule;
                        data['role_name'] = response.role_name;
                        data['role_id'] = response.role_id;
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
            }
        });

        user.View.UserAddView = Backbone.View.extend({
            template: _.template($('#tpl-user-add').html()),
            events: {
                'click .btn-user-add': 'clickBtnUserAdd'
            },
            _add: function() {
                var _self = this,
                    $form = $(event.target).closest('form'),
                    data = $form.serializeObject();

                this.userModel.save(
                    data, 
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
                    _self.$el.html(_self.template({'rolelist': response}));
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
                'click .btn-edit': 'clickBtnUserEdit'
            },
            _edit: function(){
                var is_change = '0',
                _self = this,
                $form=$(event.target).closest('form');
                this.data_form = $form.serializeObject();
                    this.userModel.save(this.data_form,
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
                var data = {};
                this.userModel.save({user_id:this.user_id},
                    {url:UrlApi('_app')+'/userinfo'}
                    ).done(function (response){
                        data['username'] = response.username;
                        data['user_id'] = response.user_id;
                        data['role_id'] = response.role_id;
                        data['rolelist'] = response.rolelist;
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
