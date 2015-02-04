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
            }
        });

        user.View.UserLoginView = Backbone.View.extend({
            template: _.template($('#tpl-user-login').html()),
            events:{
                'click .btn-login': 'userLogin',
                'keypress .login-container': 'clickLogin',
                'click .btn-register': 'userRegister'
            },
            userLogin: function(event){
                var $form = $(event.target).closest('form');
                this.data_form = $form.serializeObject();
                this.userModel.save(this.data_form,
                    {url:UrlApi('_app')+'/Admin/login'}
                    ).done(function (response){
                    if(response == '1'){
                        window.open(UrlApi('_app')+'/Translation','_self');
                    }
                });
            },
            clickLogin: function(event){
                if(event.keyCode == '13'){
                    $('.btn-login').click();
                }
            },
            userRegister: function(event){
                $('.block-user-register').show();
                $('.block-user-login').hide();
                this._userEvents.trigger('refresh','login');
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
                this.render();
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });

        user.View.UserRegisterView = Backbone.View.extend({
            template: _.template($('#tpl-user-register').html()),
            events:{
                'click .btn-register': 'userRegister',
                'click .btn-login': 'userLogin'
            },
            userRegister: function(event){
                var $form = $(event.target).closest('form');
                this.data_form = $form.serializeObject();
                var user_match = this.data_form['username'].match(/^[a-zA-Z0-9]{5,15}$/),
                pwd_match = this.data_form['password1'].match(/^[a-zA-Z0-9]{5,15}$/),
                web_match = this.data_form['website_name'].match(/^[a-zA-Z0-9]{1,15}$/);
                if(this.data_form['password1'] == this.data_form['password2']){
                    var pwd_confirm = '1';
                }else{
                    var pwd_confirm = '0';
                }
                if(user_match == null){
                    $('.tip-username').text('The username must have 5-15 digits or letters.');
                }else{
                    $('.tip-username').text('');
                }
                if(pwd_match == null){
                    $('.tip-password1').text('The password must have 5-15 digits or letters.');
                }else{
                    $('.tip-password1').text('');
                }
                if(pwd_confirm == '1'){
                    $('.tip-password2').text('');
                }else{
                    $('.tip-password2').text('Please make sure your passwords match.');
                }
                if(web_match == null){
                    $('.tip-website_name').text('The websiteName must have 1-15 digits or letters.');
                }else{
                    $('.tip-website_name').text('');
                }
                console.log(this.data_form);
                if(user_match!=null&&pwd_match!=null&&web_match!=null&&pwd_confirm == '1'){
                    this.userModel.save(this.data_form,
                        {url:UrlApi('_app')+'/Admin/register'}
                        ).done(function (response){
                        console.log(response);
                        if(response == '1'){
                            window.open(UrlApi('_app')+'/Admin/login','_self');
                        }
                    });
                }
                return false;
            },
            userLogin: function(event){
                $('.block-user-login').show();
                $('.block-user-register').hide();
                this._userEvents.trigger('refresh','register');
            },
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;
                this._userEvents = options._userEvents;
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
            }
        });

        user.View.UserApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.userModel = options.userModel;

                var _userEvents = {};
                _.extend(_userEvents, Backbone.Events);

                var userloginView = new user.View.UserLoginView({
                    el: '.block-user-login',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                var userregisterView = new user.View.UserRegisterView({
                    el: '.block-user-register',
                    userModel: this.userModel,
                    _userEvents: _userEvents
                });

                _userEvents.on('refresh', function (view){
                    switch (view)
                    {
                        case 'login':
                            userregisterView.render();
                            break;
                        case 'register':
                            userloginView.render();
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
