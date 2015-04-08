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

    public function gets($_where, $_order_by, $_field){
        return $this->where($_where)->order($_order_by)->field($_field)->relation(true)->select();
    }

    public function get($_where, $_field){
        return $this->where($_where)->field($_field)->relation(true)->find();
    }

    public function total($_where){
        return $this->where($_where)->count();
    }

}
?>