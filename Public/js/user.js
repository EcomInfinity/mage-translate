'use strict';
var self = this;
if (Backbone.emulateJSON) {
    params.contentType = 'application/x-www-form-urlencoded';
    params.data = params.data ? {model: params.data} : {};
}

jQuery(function() {
    (function(){
        var root = this;
        var user;
        user = root.user = {};
        var _ = root._,
        $ = root.jQuery;

        user.Model = {};
        user.Collection = {};
        user.View = {};

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

        var UserRouter = Backbone.Router.extend({
            initialize: function(options){
                options || (options = {});
                this._userEvents = options._userEvents;
            },
            routes: {
                "personal": "personalSetting",
                "website": "websiteSetting",
                "sync": "syncSetting",
                "language": "languageSetting",
                "user": "userSetting",
                "role": "roleSetting",
            },
            personalSetting: function (){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(0)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'personal-setting');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(0)').show();
            },
            websiteSetting: function(){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(1)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'website-setting');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(1)').show();
            },
            syncSetting: function(){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(2)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'rest-setting');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(2)').show();
            },
            languageSetting: function(){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(3)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'language-setting');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(3)').show();
            },
            userSetting: function(){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(4)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'user-search-list-view');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(4)').show();
            },
            roleSetting: function(){
                $('.block-user-sidebar ul li').removeClass('menu-selection');
                $('.block-user-sidebar ul li:eq(5)').addClass('menu-selection');
                this._userEvents.trigger('refresh', 'role-search-list-view');
                $('.block-user-content .block').hide();
                $('.block-user-content .block:eq(5)').show();
            }
        });

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

        // user.View.PersonalSidebar = Backbone.View.extend({
        //     template: _.template($('#tpl-personal-sidebar').html()),
        //     events: {
        //         'click .website': 'websiteSetting',
        //         'click .personal': 'personalSetting',
        //         'click .sync': 'restSyncSetting',
        //         'click .language': 'siteLanguageSetting',
        //         'click .user': 'userSetting',
        //         'click .role': 'roleSetting'
        //     },
        //     websiteSetting: function (event){
        //         if(Purview('update') == '1'||PurviewVal() == '-1'){
        //             $('.menu-selection').removeClass('menu-selection');
        //             $(event.target).closest('li').addClass('menu-selection');
        //             this._userEvents.trigger('refresh', 'website-setting');
        //             $('.block-user-content .block').hide();
        //             $('.block-website-setting').show();
        //         }else{
        //             $.fancybox($('.message'),{
        //                afterClose: function () {
        //                     // window.history.back();
        //                 }
        //             });
        //         }
        //     },
        //     personalSetting: function (event){
        //         $('.menu-selection').removeClass('menu-selection');
        //         $(event.target).closest('li').addClass('menu-selection');
        //         this._userEvents.trigger('refresh', 'personal-setting');
        //         $('.block-user-content .block').hide();
        //         $('.block-personal-setting').show();
        //     },
        //     restSyncSetting: function (event){
        //         if(Purview('update') == '1'||PurviewVal() == '-1'){
        //             $('.menu-selection').removeClass('menu-selection');
        //             $(event.target).closest('li').addClass('menu-selection');
        //             this._userEvents.trigger('refresh', 'rest-setting');
        //             $('.block-user-content .block').hide();
        //             $('.block-sync-setting').show();
        //         }else{
        //             $.fancybox($('.message'),{
        //                afterClose: function () {
        //                     // window.history.back();
        //                 }
        //             });
        //         }
        //     },
        //     siteLanguageSetting: function (event){
        //         if(Purview('update') == '1'||PurviewVal() == '-1'){
        //             $('.menu-selection').removeClass('menu-selection');
        //             $(event.target).closest('li').addClass('menu-selection');
        //             this._userEvents.trigger('refresh', 'language-setting');
        //             $('.block-user-content .block').hide();
        //             $('.block-language-setting').show();
        //         }else{
        //             $.fancybox($('.message'),{
        //                afterClose: function () {
        //                     // window.history.back();
        //                 }
        //             });
        //         }
        //     },
        //     userSetting: function(event){
        //         console.log('user');
        //         $('.menu-selection').removeClass('menu-selection');
        //         $(event.target).closest('li').addClass('menu-selection');
        //         // this._userEvents.trigger('refresh', 'language-setting');
        //         $('.block-user-content .block').hide();
        //         $('.block-user').show();
        //         this._userEvents.trigger('refresh', 'user-search-list-view');
        //     },
        //     roleSetting: function(event){
        //         console.log('role');
        //         $('.menu-selection').removeClass('menu-selection');
        //         $(event.target).closest('li').addClass('menu-selection');
        //         // this._userEvents.trigger('refresh', 'language-setting');
        //         $('.block-user-content .block').hide();
        //         $('.block-role').show();
        //         this._userEvents.trigger('refresh', 'role-search-list-view');
        //     },
        //     initialize: function(options){
        //         options || (options = {});
        //         this._userEvents = options._userEvents;
        //         this.render();
        //     },
        //     render: function(){
        //         this.$el.html(this.template({}));
        //     }
        // });

        user.View.WebsiteSettingView = Backbone.View.extend({
            template: _.template($('#tpl-website-setting').html()),
            events: {
                'click .btn-website-setting': 'clickBtnWebsiteSetting',
            },
            changeWebsiteName: function(){
                this.userModel.clear();
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
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
                this.render();
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
                this.userModel.clear();
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
                        _self.render();
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
                return false;
            },
            delLang: function (){
                this.userModel.clear();
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
                // this.userModel.clear();
                return false;
            },
            _edit: function(){
                this.userModel.clear();
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
                // this.userModel.clear();
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
                this.userModel.clear();
                // console.log('1');
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                            _self._userEvents.trigger('refresh','role-list-view');
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
            }
        });

        user.View.RoleListView = Backbone.View.extend({
            template: _.template($('#tpl-role-list').html()),
            events: {
                'click .btn-user-list': 'userList',
                'click .btn-role-detail': 'roleEdit',
                'click .btn-list-role-add': 'roleAdd'
            },
            // userList: function(){
            //     this._userEvents.trigger('refresh','user-list');
            //     $('.block-role').slideUp("slow");
            //     $('.block-user').slideDown("slow");
            //     return false;
            // },
            roleEdit: function (event){
                var role_id = $(event.target).closest('tr').data('id');
                this._userEvents.trigger('alernately',role_id,'roleList');
            },
            roleAdd: function (){
                this._userEvents.trigger('refresh','list-role-add');
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
                this.userModel.clear();
                var _self = this;
                this.userModel.save(
                    { search: this.search },
                    { url: UrlApi('_app')+'/rolelist' }
                ).done(function (response){
                    _self.$el.html(_self.template({roleList: response.data.roles,current_count:response.data.count,'count':response.data.total}));
                });
                // this.userModel.clear();
            }
        });

        user.View.RoleInfoView = Backbone.View.extend({
            template: _.template($('#tpl-role-info').html()),
            events: {
                'click .btn-edit': 'clickBtnRoleEdit'
            },
            _edit: function(){
                this.userModel.clear();
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
                            _self._userEvents.trigger('refresh','role-list-view');
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
            }
        });

        user.View.UserAddView = Backbone.View.extend({
            template: _.template($('#tpl-user-add').html()),
            events: {
                'click .btn-user-add': 'clickBtnUserAdd'
            },
            _add: function() {
                this.userModel.clear();
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
                            _self._userEvents.trigger('refresh', 'user-list-view');
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
            }
        });

        user.View.UserListView = Backbone.View.extend({
            template: _.template($('#tpl-user-list').html()),
            events:{
                'click .btn-role-list': 'roleList',
                'click .btn-user-detail': 'userEdit',
                'click .btn-list-user-add': 'userAdd'
            },
            userEdit: function (event){
                var user_id = $(event.target).closest('tr').data('id');
                this._userEvents.trigger('alernately',user_id,'userList');
            },
            userAdd: function (){
                this._userEvents.trigger('refresh','list-user-add');
            },
            enable: function(elem) {
                this.userModel.clear();
                var user_id = $(elem).closest('tr').data('id');
                this.userModel.save(
                    { user_id: user_id },
                    { url: UrlApi('_app')+'/User/enable' }
                ).done(function(response) {
                });
                // this.userModel.clear();
            },
            disable: function(elem) {
                this.userModel.clear();
                var user_id = $(elem).closest('tr').data('id');
                this.userModel.save(
                    { user_id: user_id },
                    { url: UrlApi('_app')+'/User/disable' }
                ).done(function(response) {
                });
                // this.userModel.clear();
            },
            // roleList: function(){
            //     this._userEvents.trigger('refresh','role-list');
            //     $('.block-user').slideUp("slow");
            //     $('.block-role').slideDown("slow");
            //     return false;
            // },
            setList: function(search){
                this.search = search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                // this.render();
                // if(PurviewVal()=='-1'){
                //     this.render();
                // }
            },
            render: function(){
                this.userModel.clear();
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
                // this.userModel.clear();
            }
        });

        user.View.UserInfoView = Backbone.View.extend({
            template: _.template($('#tpl-user-info').html()),
            events:{
                'click .btn-edit': 'clickBtnUserEdit'
            },
            _edit: function(){
                this.userModel.clear();
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
                        _self._userEvents.trigger('refresh','user-list-view');
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
                // this.userModel.clear();
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
                this.userModel.clear();
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
                // this.userModel.clear();
            }
        });

        user.View.UserApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;

                var _userEvents = {};
                _.extend(_userEvents, Backbone.Events);

                // this.navView = new lang.View.LanguageNavView({
                //     el: '.navbar-collapse',
                //     userModel: this.userModel,
                //     _userEvents: _userEvents
                // });

                // var personalsidebarView = new user.View.PersonalSidebar({
                //     el: '.block-user-sidebar',
                //     userModel: this.userModel,
                //     _userEvents: _userEvents
                // });

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
                    el: '.block-sync-setting',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var sitelanguageView = new user.View.SiteLanguageView({
                    el: '.block-language-setting',
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
                        case 'user-list-view':
                            userlistView.render();
                            break;
                        case 'user-search-list-view':
                            usersearchView.render();
                            userlistView.render();
                            break;
                        case 'role-list-view':
                            rolelistView.render();
                            break;
                        // case 'userAdd':
                        //     userlistView.render();
                        //     break;
                        // case 'roleInfo':
                        //     rolelistView.render();
                        //     break;
                        case 'list-user-add':
                            useraddView.render();
                            break;
                        case 'role-search-list-view':
                            rolelistView.render();
                            rolesearchView.render();
                            break;
                        // case 'roleAdd':
                        //     rolelistView.render();
                        //     break;
                        case 'list-role-add':
                            roleaddView.render();
                            break;
                        // case 'user-list':
                        //     userlistView.render();
                        //     usersearchView.render();
                        //     break;
                        // case 'userhandle':
                        //     usersearchView.render();
                        //     userlistView.render();
                        //     break;
                        // case 'userInfo':
                        //     userlistView.render();
                        //     break;
                        // case 'personalCenter':
                        //     personalsettingView.render();
                        //     break;
                        case 'website-setting':
                            websitesettingView.render();
                            break;
                        case 'personal-setting':
                            personalsettingView.render();
                            break;
                        case 'rest-setting':
                            restsyncView.render();
                            break;
                        case 'language-setting':
                            sitelanguageView.render();
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
        Backbone.history.start();

    }).call(self);

});
