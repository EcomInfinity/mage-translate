(function($) {
    $(function(){
        // alert($(window).height());
        $('body').on('scroll', '.data-list', function(){
            console.log($('.data-list'));
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

        $('body').on('click', "td", function (event){
            if($(event.target).closest('table').attr('operation') == 'batch'){
                if($(event.target).closest('tr').attr('class') == 'selection'){
                    $(event.target).closest('tr').attr('class','');
                }else{
                    $(event.target).closest('tr').addClass('selection');
                }
            }
        });

        // $('body').on('keydown', '', function (event){
        //     if(event.keyCode === 16){
        //         console.log('1');
        //         $('body').on('click', 'td', function (event){
        //             $(event.target).closest('tr').addClass('selection');
        //             if($('.selection').length > 1){
        //                 $(".selection:first").nextUntil($(".selection:last")).addClass('selection');
        //             }
        //         });
        //     }
        // });
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