(function($) {
    $(function(){
        // $(window).resize(function () {
        //     var other = $('.navbar').outerHeight(true)
        //     +$('h1').outerHeight(true)
        //     +$('.search-box').outerHeight(true)
        //     +$('.record-view').outerHeight(true)
        //     +$('.list-head').outerHeight(true)
        //     +$('.operation-view').outerHeight(true)
        //     +$('.line').outerHeight(true)*2;
        //     $('.data-list').height($(window).height() - other);
        // });
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

        var _shift_click = false;
        $('body').on('keydown', '', function (event){
            if(event.keyCode === 16){
                _shift_click = true;
            }
        });
        $('body').on('keyup', '', function(){
            _shift_click = false;
        });

        $('body').on('click', "td", function (event){
            $('.batch-app option')[0].selected = true;
            if(_shift_click === true){
                $(this).closest('tr').addClass('selection');
                if($('.selection').length > 1){
                    $(".selection:first").nextUntil($(".selection:last")).addClass('selection');
                }
            }else{
                if($(event.target).closest('table').attr('operation') == 'batch'){
                    if($(event.target).closest('tr').attr('class') == 'selection'){
                        $(event.target).closest('tr').attr('class','');
                    }else{
                        $(event.target).closest('tr').addClass('selection');
                    }
                }
            }
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