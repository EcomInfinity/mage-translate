<?php
namespace Home\Model;
use Think\Model\RelationModel;
class TranslationModel extends RelationModel{
			protected $_link = array(
				'translation_image' => self::HAS_MANY,
				'translation_image'=> array(  
				'mapping_type'=>self::HAS_MANY,
				'class_name'=>'translation_image',
				'foreign_key'=>'lang_id',
				'mapping_name'=>'translation_image',
				'mapping_fields'=>'image_name',
	 			),

			);
}
?>