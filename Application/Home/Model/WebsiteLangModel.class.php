<?php
namespace Home\Model;
// use Think\Model;
use Think\Model\RelationModel;
class WebsiteLangModel extends RelationModel{
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

    public function gets($_where){
        return $this->where($_where)->relation(true)->select();
    }

    public function get($_where){
        return $this->where($_where)->relation(true)->find();
    }
}
?>