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

    public function get($_where, $_field, $_relation = true){
        return $this->where($_where)->field($_field)->relation($_relation)->find();
    }

    public function total($_where){
        return $this->where($_where)->count();
    }

    public function saveCms($_where, $_params){
        if(isset($_params['page_content'])){
            if(preg_match('/.*[^ ].*/', str_replace("\r\n", "", $_params['page_content'])) == 0){
                return 'Title and Content not all spaces or empty.';
            }
            unset($_params['page_content']);
        }
        if(preg_match('/.*[^ ].*/', $_params['title']) == 0 || preg_match('/.*[^ ].*/', str_replace("\r\n", "", $_params['content'])) == 0){
            return 'Title and Content not all spaces or empty.';
            break;
        }
        if(!empty($_where)){
            $_result = $this->where($_where)->save($_params);
        }else{
            $_result = $this->save($_params);
        }
        if($_result > 0){
            return true;
        }else{
            return 'Modify failure.';
        }
    }
}
?>