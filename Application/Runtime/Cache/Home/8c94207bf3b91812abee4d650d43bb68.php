<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
	<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
	<script type="text/javascript" src="/redesign/Public/js/ajaxfileupload.js"></script>
</head>
<body>
	<div style="width:960px;margin:0 auto;">
		<div class="block block-translation-list" style="border:1px solid red;float:left;">
					<table border="1">
				<tr><th>ID</th><th>Title</th><th>Info</th><th>Operation</th></tr>
				<?php if(is_array($translation_list)): foreach($translation_list as $key=>$vo): ?><tr><td><?php echo ($vo["id"]); ?></td><td><?php echo ($vo["title"]); ?></td><td><?php echo ($vo["info"]); ?></td><td><a href="javascript:void(0);" translation-id="<?php echo ($vo["id"]); ?>" id="edit_translation">Operation</a></td></tr><?php endforeach; endif; ?>
			</table>
		</div>
		<div class="block block-translation-detail" style="border:1px solid red;float:left;">
		</div>
		<button id="translation_add">ADD</button>
		<div class="block block-translation-add" style="border:1px solid red; float: right;display: none;">
			<form action="" method="post" enctype="multipart/form-data">
	<label for="title">Title:</label>
	<input type="text" name="title" id="title" /><br/><br/>
	<label for="lang_select">Lang Select:</label>
	<select name="same_id" id="lang_select">
		<option value="0" selected >Please select...</option>
		<?php if(is_array($translation_list)): foreach($translation_list as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; ?>
	</select><br/><br/>
	<label for="info">Info:</label>
	<textarea rows="3" cols="20" name="info" id="info"></textarea><br/><br/>
	<label for="type">Type:</label>
	<input type="text" name="type" id="type" /><br/><br/>
	<input type="file" name="images" /><br><br>
	<a id="translation_submit" style="background-color: green;padding: 5px;">ADD</a>
</form>
		</div>
	</div>
	<script type="text/javascript">
	(function($) {
		$(function(){
			 $('body').on('click', '#edit_translation', function() {
			 	var translation_id = $(this).attr("translation-id");
			 	$.post("/redesign/index.php/Home/Translation/translation_edit",{id:translation_id},function(data){
			 		$('.block-translation-detail').html(data);
			 	});
			 });
			 $('body').on('change', '#transla-id', function(){
			 	var translation_id = $(this).attr("translation-id"),
			 		translation_info = $(this).val();
			 	$.post('/redesign/index.php/Home/Translation/translation_edit',{edit_id:translation_id,edit_info:translation_info},function(data){
			 			if(data==1){
			 				$.post('/redesign/index.php/Home/Translation/translation_list',{render:1},function(data){
			 					$('.block-translation-list').html(data);
			 				});
			 			}
			 	});
			 });
			 $('body').on('click', '#translation_add', function(){	
			 	$(".block-translation-add").toggle();
			 });
			 $('body').on('click', '#translation_submit', function(){	
			 	var title = $("input[name='title']").val(),
			 		same_id = $("select[name='same_id']").val(),
				 	info = $("textarea[name='info']").val(),
				 	type = $("input[name='type']").val();
				 $.post('/redesign/index.php/Home/Translation/translation_add',{title:title,same_id:same_id,info:info,type:type},function(data){
						$('.block-translation-add').html(data);
				 });
			 });
		});
	})(jQuery); 
	</script>



<script type="text/javascript">
function ajaxFileUpload(){
	$.ajaxFileUpload({
		url:'/redesign/index.php/Home/Translation/translation_image', 
		secureuri:false,
		fileElementId:'img',
		dataType: 'text',
		success:function(data, status){
			alert(status+data);
			$('#file_url').val(data.file_url);
		}
	});
	return false;
}
</script>

<form action="" method="post" enctype="multipart/form-data">
<input id="img" type="file" size="45" name="img" class="input">
<input type="hidden" id="file_url" name="file_url" />
<button class="button" id="buttonUpload" onclick="return ajaxFileUpload();">上传</button>
</form>

</body>
</html>