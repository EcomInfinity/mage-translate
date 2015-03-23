<?php
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;
class BaseTranslateModel extends Model {

    public function gets($_where, $_order_by, $_field) {
        return $this->where($_where)->order($_order_by)->field($_field)->select();
    }

    public function get($_base_id){
        return $this->find($_base_id);
    }

    //$_params数组(各种语言，remarks,status,website_id,modify)
    public function addTranslate($_params){
        return $this->add($_params);
    }

    public function setTranslate($_params){
        return $this->save($_params);
    }

    public function del($_tid){
        $save['id'] = intval($_tid);
        $save['status'] = '0';
        return $this->save($save);
    }
    
    public function total($_website_id){
        return $this->where(
            array(
                'website_id' => intval($_website_id),
                'status' => 1,
            )
        )->count();
    }

    public function setModify($_lang_id){
        $_result = $this->find($_lang_id);
        if($_result['modify'] == 0){
            $_save['modify'] = 1;
        }else{
            $_save['modify'] = 0;
        }
        $_save['id'] = $_lang_id;
        return $this->save($_save);
    }
}
?>