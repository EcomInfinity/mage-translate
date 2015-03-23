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
        var _ctrl_click = false;
        $('body').on('keydown', '', function (event){
            if(event.keyCode === 16){
                _shift_click = true;
            }
            if(event.keyCode === 17){
                _ctrl_click = true;
            }
        });
        $('body').on('keyup', '', function (event){
            if(event.keyCode === 16){
                _shift_click = false;
            }
            if(event.keyCode === 17){
                _ctrl_click = false;
            }
        });

        $('body').on('change', '[name="checked-all"]', function (event){
            if($(event.target).prop('checked') == true){
                $('.tbl-translation-list tbody tr input[type="checkbox"]').prop("checked",true);
                $('.tbl-translation-list tbody tr').addClass('selection');
            }else{
                $('.tbl-translation-list tbody tr input[type="checkbox"]').prop("checked",false);
                $('.tbl-translation-list tbody tr').removeClass('selection');
            }
            return false;
        });

        $('body').on('click', 'tbody input[type="checkbox"]', function (event){
            event.stopPropagation();
            if($(event.target).closest('tr').attr('class') == 'selection'){
                $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",false);
                $(event.target).closest('tr').attr('class','');
            }else{
                $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",true);
                $(event.target).closest('tr').addClass('selection');
            }
        });

        $('body').on('click', "tbody tr", function (event){
            $('.batch-app').find('option')[0].selected = true;
            $('[name="checked-all"]').prop("checked",false);
            if($(event.target).closest('table').attr('operation') == 'batch'){
                if(_ctrl_click === true){
                    // console.log('_ctrl_click');
                    if($(event.target).closest('tr').attr('class') == 'selection'){
                        $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",false);
                        $(event.target).closest('tr').attr('class','');
                    }else{
                        $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",true);
                        $(event.target).closest('tr').addClass('selection');
                    }
                }else if(_shift_click === true){
                    // console.log('_shift_click');
                    if($('.selection').length > 1){
                        $('.selection input[type="checkbox"]').prop("checked",false);
                        $('.selection').removeClass('selection');
                    }
                    $(event.target).closest('tr').addClass('selection');
                    $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",true);
                    if($('.selection').length == 2){
                        $(".selection:first").nextUntil($(".selection:last")).addClass('selection');
                        $(".selection:first").nextUntil($(".selection:last")).find('input[type="checkbox"]').prop("checked",true);
                    }
                }else{
                    // console.log('none');
                    $('.selection input[type="checkbox"]').prop("checked",false);
                    $('.selection').attr('class','');
                    $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",true);
                    $(event.target).closest('tr').addClass('selection');
                }
                // var _total = $('.selection').length;
                // var _click_location;
                // if(_total >1){
                //     $('.selection').each(function (i){
                //         if($(this).attr('class') == 'selection click'){
                //             _click_location = i+1;
                //         }
                //     });
                //     console.log(_total);
                //     console.log(_click_location);
                // }
                // $('.batch-app option')[0].selected = true;
                // if(_shift_click === true){
                //     $(this).closest('tr').addClass('selection');
                //     if($('.selection').length > 1){
                //         $(".selection:first").nextUntil($(".selection:last")).addClass('selection');
                //     }
                // }else{
                //     // if($(event.target).closest('table').attr('operation') == 'batch'){
                //         if($(event.target).closest('tr').attr('class') == 'selection'){
                //             $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",false);
                //             $(event.target).closest('tr').attr('class','');
                //         }else{
                //             $(event.target).closest('tr').find('input[type="checkbox"]').prop("checked",true);
                //             $(event.target).closest('tr').addClass('selection');
                //         }
                //     // }
                // }
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