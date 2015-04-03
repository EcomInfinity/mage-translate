<?php
namespace Home\Model;
// use Think\Model;
use Think\Model\RelationModel;
class CmsTranslateModel extends RelationModel{
    protected $_link = array(
            'Language'=> array(  
                'mapping_type' =>self::BELONGS_TO,
                'class_name' => 'Language',
                'foreign_key' => 'lang_id',
                'mapping_name' => 'language',
                'mapping_fields' => 'simple_name',
                'as_fields' => 'simple_name',
            ),
        );
}
?>