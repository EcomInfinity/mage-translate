(function($) {
		$(function(){
			//click Edit
			 // $('body').on('click', '.btn-edit', function() {
			 // 	var id = $(this).closest('tr').data('id');
			 // 	$.post(
			 // 		"Translation/translation_edit",
			 // 		{ id: id },
			 // 		function(data){
				//  		$('.block-translation-detail').html(data).show()
				//  	});
			 // });

			 //click Delect
			 $('body').on('click', '.btn-delete', function(){
			 	var id = $(this).closest('tr').data('id');
			 	$.post(
			 		"Translation/translation_del",
			 		{ id: id },
			 		function() {
		 				$.post('Translation/translation_list',{render:1},function(data){
		 					$('.block-translation-list').html(data);
		 				});
				 	});
			 });

			 $('.block').on('click', '.btn-cancel', function() {
			 	$(this).closest('.block').hide();
			 	return false;
			 });

			 //edit lang info
			 $('body').on('change', '.lang_edit', function(){
			 	var lang_type = $(this).attr("lang_type"),
			 		lang_info = $(this).val(),
			 		lang_id = $("input[name='transla-id']").val();
			 		//alert(lang_info+lang_type);
			 	$.post('Translation/translation_edit',{lang_info:lang_info,lang_type:lang_type,lang_id:lang_id},function(data){
			 			if(data==1){
			 				$.post('Translation/translation_list',{render:1},function(data){
			 					$('.block-translation-list').html(data);
			 				});
			 			}
			 	});
			 });

			 //add lang
			 $('body').on('click', '.btn-new', function() {
			 	$(".block-translation-add").toggle();
			 	return false;
			 });

			 $('body').on('click', '.btn-add', function() {
			 	var $form = $(this).closest('form'),
			 		data_form = $form.serializeObject();
					 $.post('Translation/translation_add',data_form,function(data){
							$('.block-translation-add').html(data);
			 				$.post('Translation/translation_list',{render:1},function(data){
			 					$('.block-translation-list').html(data);
			 				});
					 });
			 });
			 //edit iamges
			 $('body').on('change','#images',function(){
			 	var lang_id = $("input[name='transla-id']").val();
			 	ajaxFileUpload('Translation/translation_edit/lang_id/'+lang_id,lang_id);
			 });
			 //search
			 $('body').on('keypress','.search',function(event){
			 	if(event.keyCode == '13'){
			 		var search = $(this).val();
			 		$.iajax(
			 			'Translation/translation_list',
			 			{ search: search, render_search: 1 },
			 			function(response) { 
				 			$('.block-translation-list').html(response);
				 		}
			 		);
			 	}
			 });
			 $('body').on('click','.btn-image-delete',function(){
			 	var image_id = $(this).attr('image-id'),
			 		image_name = $(this).attr('image-name'),
			 		lang_id = $("input[name='transla-id']").val();
			 		$.post('Translation/translation_edit',{image_id:image_id,image_name:image_name,lang_id_del:lang_id},function(data){
			 			 console.log(data);
			 			 $('.block-translation-detail').html(data);
			 		});
			 });
			 $('body').on('click','.export',function(){
				window.location.href="Translation/translation_export";
			 });
		});
	      function ajaxFileUpload(url,lang_id){
	           $.ajaxFileUpload(
	               {
		                url:url,            
		                secureuri:false,
		                fileElementId:'images',                      
		                dataType: 'xml',                                  
		                success: function (data, status)            
		                {      
						 	$.post("Translation/translation_edit",{id:lang_id},function(data){
						 		$('.block-translation-detail').html(data);
						 	});
		                },
		                error: function (data, status, e)            
		                {
		                    alert('Add Fail');
		                }
	        		}                  
	           );              
	      }
	})(jQuery); 