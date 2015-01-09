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
         $('body').on('click','.export',function(){
            window.location.href="Translation/export";
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