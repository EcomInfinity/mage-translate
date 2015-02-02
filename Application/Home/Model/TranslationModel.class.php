<?php
namespace Home\Model;
use Think\Model;
class TranslationModel extends Model{
    public function getTranslateList($field,$_status,$_website_id,$modify){
        $where['website_id'] = $_website_id;
        $where['status'] = $_status;
        if($modify&&$modify!=''){
            $where['modify'] = $modify;
        }
        return $this->where($where)->order('id desc')->field($field)->select();
    }

    public function searchTranslate($_search,$_website_id,$modify,$_status){
        $where['en'] = array('like','%'.$_search.'%');
        $where['website_id'] = $_website_id;
        $where['status'] = $_status;
        $where['modify'] = $modify;
        return $this->where($where)->order('id desc')->select();
    }

    public function addTranslate($_params){
        return $this->add($_params);
    }

    public function setTranslate($_params){
        return $this->save($_params);
    }

    public function delTranslate($_tid){
        $save['id'] = intval($_tid);
        $save['status'] = '0';
        return $this->save($save);
    }

    public function getOneTranslate($_tid){
        return $this->where(array('id'=>intval($_tid)))->find();
    }

    public function getTranslateCount($_wid){
        $where['website_id'] = intval($_wid);
        $where['status'] = '1';
        return $this->where($where)->count();
    }
}
?>