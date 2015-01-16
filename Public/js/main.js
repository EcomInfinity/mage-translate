(function($) {
    $(function(){
         $('.block').on('click', '.btn-edit', function() {
             $('.block-translation-detail').show();
             $('.block-translation-add').hide();
             return false;
         });

         //add lang
         $('body').on('click', '.btn-new', function() {
             $(".block-translation-add").toggle();
             $('.block-translation-detail').hide();
             return false;
         });

         $('body').on('click', '.btn-user', function(){
            $('.block-view-user').show();
            $('.block-view-translate').hide();
            return false;
         });

         $('body').on('click', '.btn-list', function(){
            $('.block-view-translate').show();
            $('.block-view-user').hide();
            return false;
         });

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
                    var reVal = verify(_self.user.username,_self.user.password);
                    console.log(reVal);
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
    });

    verify = function (username,password){
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