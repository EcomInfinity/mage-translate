<?php
namespace Home\Model;
// use Think\Model;
use Think\Model\RelationModel;
class OtherTranslateModel extends RelationModel{
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
    public function gets($_where, $_field){
        return $this->where($_where)->relation(true)->field($_field)->select();
    }

    public function get($_where){
        return $this->where($_where)->relation(true)->find();
    }

    public function saveOther($_params, $_where){
        if(!empty($_where)){
            $_result = $this->where($_where)->save($_params);
        }else{
            $_result = $this->save($_params);
        }
        if($_result > 0){
            return true;
        }else{
            return false;
        }
    }
}
?>