(function($) {
    $(function(){
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

    });

    ajaxFileUpload = function (url, fileId, callback, failure){
       $.ajaxFileUpload(
           {
                url:url,
                secureuri:false,
                fileElementId:fileId,
                dataType: 'json',
                success: function (data, status) {
                    if (typeof callback === 'function') {
                        callback(data);
                    }
                },
                error: function (data, status) {
                    if (typeof failure === 'function') {
                        failure(data);
                    }
                }
            }
       );
    }
})(jQuery);