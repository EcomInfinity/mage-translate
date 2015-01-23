(function($) {
    $(function(){
         $('body').on('click', '.btn-edit', function() {
             $('.block-translation-detail').show();
             $('.block-translation-add').hide();
             return false;
         });

         //add lang
         $('body').on('click', '.btn-list-add', function() {
             $(".block-translation-add").show();
             $('.block-translation-detail').hide();
             return false;
         });
        //list and user
         $('body').on('click', '.btn-user', function(){
            $('.block-view-user').slideDown("slow");
            $('.block-view-translate').slideUp("slow");
            return false;
         });

         $('body').on('click', '.btn-list', function(){
            $('.block-view-translate').slideDown("slow");
            $('.block-view-user').slideUp("slow");
            return false;
         });
        //user add and edit
         $('body').on('click', '.btn-list-user-add', function(){
            $('.block-user-info').slideUp("slow");
            $('.block-user-add').slideDown("slow");
            return false;
         });

         $('body').on('click', '.btn-user-detail', function(){
            $('.block-user-add').slideUp("slow");
            $('.block-user-info').slideDown("slow");
            return false;
         });
        //role add and edit
         $('body').on('click', '.btn-list-role-add', function(){
            $('.block-role-info').slideUp("slow");
            $('.block-role-add').slideDown("slow");
            return false;
         });

         $('body').on('click', '.btn-role-detail', function(){
            $('.block-role-add').slideUp("slow");
            $('.block-role-info').slideDown("slow");
            return false;
         });
        //user and role
         $('body').on('click', '.btn-role-list', function(){
            $('.block-user').slideUp("slow");
            $('.block-role').slideDown("slow");
            return false;
         });

         $('body').on('click', '.btn-user-list', function(){
            $('.block-role').slideUp("slow");
            $('.block-user').slideDown("slow");
            return false;
         });
        //Enlarge Image
         $('body').on('click', 'ul img', function(){
            $('#enlarge_images').html('');
            $('#enlarge_images').html('<a href=""><img src="' + this.src + '" /></a>');
            $('#enlarge_images').show();
            return false;
         });

         $('body').on('click', '#enlarge_images', function(){
            $('#enlarge_images').html('');
            $('#enlarge_images').hide();
            return false;
         });
         //login
         $('body').keypress(function (event){
            if(event.keyCode == '13'){
                $('.btn-login').click();
            }
         });

         $('body').on('click', '.btn-login', function(){
            var _self = this;
            this.user = {
                username: $('#username').val(),
                password: $('#password').val()
            };
            $.ajax({
                type: 'POST',
                url: UrlApi('_app')+'/Admin/login',
                data: _self.user,
                beforeSend: function(){
                    var reVal = verifyLogin(_self.user.username,_self.user.password);
                    if(reVal[0] == '1'){
                        $('.tip-username').text('This is a required field.');
                    }else{
                        $('.tip-username').text('');
                    }
                    if(reVal[1] == '1'){
                        $('.tip-password').text('This is a required field.');
                    }else{
                        $('.tip-password').text('');
                    }
                    if(reVal[0] == '1'||reVal[1] == '1'){
                        return false;
                    }
                }
            }).done(function(data){
                if(data == '1'){
                    window.open(UrlApi('_app')+'/Translation/index',"_self");
                }
                if(data == '0'){
                    $('.tip-main').text('Invalid Username or Password.');
                }
            });
         });
        //register
         $('body').on('click', '.btn-register', function(){
            var _self = this;
            this.user = {
                username: $('#username').val(),
                password1: $('#password1').val(),
                password2: $('#password2').val(),
                website_name: $('#website_name').val()
            };
            $.ajax({
                type: 'POST',
                url: UrlApi('_app')+'/Admin/register',
                data: _self.user,
                beforeSend: function(){
                    var reVal = verifyRegister(_self.user.username,_self.user.password1,_self.user.password2,_self.user.website_name);
                    if(reVal[0] == '1'){
                        $('.tip-username').text('This is a required field.');
                    }else{
                        $('.tip-username').text('');
                    }
                    if(reVal[1] == '1'){
                        $('.tip-password1').text('This is a required field.');
                    }else{
                        $('.tip-password1').text('');
                    }
                    if(reVal[2] == '1'){
                        $('.tip-password2').text('This is a required field.');
                    }else{
                        $('.tip-password2').text('');
                    }
                    if(reVal[3] == '1'){
                        $('.tip-password2').text('');
                    }else{
                        $('.tip-password2').text('Please make sure your passwords match.');
                    }
                    if(reVal[4] == '1'){
                        $('.tip-website_name').text('This is a required field.');
                    }else{
                        $('.tip-website_name').text('');
                    }
                    if(reVal[0] == '1'||reVal[1] == '1'||reVal[3] == '0'||reVal[4] == '1'){
                        return false;
                    }
                }
            }).done(function(data){
                if(data == '1'){
                    window.open(UrlApi('_app')+'/Admin/index',"_self");
                }else{
                    $('.tip-main').text('Username or repeated failure to create.');
                }
            });
         });

    });

    verifyLogin = function (username,password){
        var reVal = {};
        if(username == ''){
            reVal['0'] = '1';
        }else{
            reVal['0'] = '0';
        }
        if(password == ''){
            reVal['1'] = '1';
        }else{
            reVal['1'] = '0';
        }
        return reVal;
    }

    verifyRegister = function (username,password1,password2,website_name){
        var reVal = {};
        if(username == ''){
            reVal['0'] = '1';
        }else{
            reVal['0'] = '0';
        }
        if(password1 == ''){
            reVal['1'] = '1';
        }else{
            reVal['1'] = '0';
        }
        if(password2 == ''){
            reVal['2'] = '1';
        }else{
            reVal['2'] = '0';
        }
        if(password1 == password2){
            reVal['3'] = '1';
        }else{
            reVal['3'] = '0';
        }
        if(website_name == ''){
            reVal['4'] = '1';
        }else{
            reVal['4'] = '0';
        }
        return reVal;
    }

    ajaxFileUpload = function (url, fileId, callback, failure){
       $.ajaxFileUpload(
           {
                url:url,
                secureuri:false,
                fileElementId:fileId,
                dataType: 'json',
                success: function (data, status) {
                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function (data, status) {
                    if (typeof failure === 'function') {
                        failure();
                    }
                }
            }
       );
    }
})(jQuery);