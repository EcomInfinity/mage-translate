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
                return Backbone.Model.prototype.save.call(this, attributes, options);
            }
        });

        user.View.UserLoginView = Backbone.View.extend({
            template: _.template($('#tpl-user-login').html()),
            events:{
                'click .btn-login': 'clickBtnLogin',
                'keypress .login-container': 'keypress',
                'click .btn-register': 'clickBtnRegister'
            },
            _login: function() {
                var _self = this,
                    $form = this.$el.find('form'),
                    data = $form.serializeObject();
                this.userModel.save(
                    data,
                    {
                        url: UrlApi('_app') + '/login'
                    }
                ).done(function (response){
                    $.fancybox.hideLoading();
                    if (response.success === true) {
                        _self.$el.find('.modal-container').notify(
                            'Success', 
                            { 
                                position: 'top', 
                                className: 'success'
                            }
                        );
                        setTimeout(function() {
                             window.open(UrlApi('_app')+'/lang', '_self');
                        }, 1000);
                    } else {
                        _self.$el.find('.modal-container').notify(
                            response.message, 
                            { 
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
            },
            clickBtnLogin: function(event){
                this.$el.find('form').submit();
            },
            keypress: function(event){
                if (event.keyCode === 13){
                    this.$el.find('form').submit();
                }
            },
            clickBtnRegister: function(event){
                this._events.trigger('show', 'register');
            },
            show: function() {
                this.$el.show();
            },
            hide: function() {
                this.$el.hide();
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._events = options._events;
                this.render();
            },
            render: function(){
                var _self = this;
                this.$el.html(this.template({}));
                this.$el.find('form').validator().on('submit', function(e) {
                    if (e.isDefaultPrevented()) {
                    } else {
                        _self._login.call(_self);
                        return false;
                    }
                });
                return this;
            }
        });

        user.View.UserRegisterView = Backbone.View.extend({
            template: _.template($('#tpl-user-register').html()),
            events:{
                'click .btn-register': 'clickBtnRegister',
                'click .btn-back': 'clickBtnBack'
            },
            _register: function() {
                var _self = this,
                    $form = this.$el.find('form'),
                    data = $form.serializeObject();
                
                this.userModel.save(
                    data,
                    {
                        url: UrlApi('_app') + '/register'
                    }
                ).done(function (response) {
                    $.fancybox.hideLoading();
                    if (response.success == true){
                        _self.$el.find('.modal-container').notify(
                            'Success', 
                            { 
                                position: 'top',
                                className: 'success'
                            }
                        );
                        setTimeout(function() {
                            window.open(UrlApi('_app')+'/admin', '_self');
                        }, 1000);
                    } else {
                        _self.$el.find('.modal-container').notify(
                            response.message, 
                            { 
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
            },
            clickBtnRegister: function(event){
                this.$el.find('form').submit();
                return false;
            },
            clickBtnBack: function(event){
                this._events.trigger('show', 'login');
            },
            show: function() {
                this.$el.show();  
            },
            hide: function() {
                this.$el.hide();
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._events = options._events;
            },
            render: function(){
                var _self = this;
                this.$el.html(this.template({}));
                this.$el.find('form').validator().on('submit', function(e) {
                    if (e.isDefaultPrevented()) {
                    } else {
                        _self._register.call(_self);
                        return false;
                    }
                });
                return this;
            }
        });

        user.View.UserApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;

                var _events = {};
                _.extend(_events, Backbone.Events);

                var loginView = new user.View.UserLoginView({
                    el: '.block-user-login',
                    userModel: this.userModel,
                    _events: _events
                });

                var registerView = new user.View.UserRegisterView({
                    el: '.block-user-register',
                    userModel: this.userModel,
                    _events: _events
                });

                _events.on('show', function(view) {
                    switch(view) {
                        case 'login': 
                            loginView.render().show();
                            registerView.hide();
                            break;
                        case 'register': 
                            loginView.hide();
                            registerView.render().show();
                            break;
                    }
                });
            }
        });

        var userApp = new user.View.UserApp({
            userModel: new user.Model.Base()
        });

    }).call(self);

});
