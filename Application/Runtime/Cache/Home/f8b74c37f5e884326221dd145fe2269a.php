<?php if (!defined('THINK_PATH')) exit();?><form action="" method="post" enctype="multipart/form-data">
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