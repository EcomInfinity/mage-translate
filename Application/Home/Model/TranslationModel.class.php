<?php
namespace Home\Model;
use Think\Model;

class TranslationModel extends Model{
    public function getTranslateList($field,$_status,$_website_id,$modify,$order='id'){
        $where['website_id'] = $_website_id;
        $where['status'] = $_status;
        if($modify&&$modify!=''){
            $where['modify'] = $modify;
        }
        $list['count'] = $this->where($where)->count();
        $list['list'] = $this->where($where)->order($order)->field($field)->select();
        return $list;
    }

    public function searchTranslate($_search,$_website_id,$modify,$_status){
        $where['en'] = array('like','%'.$_search.'%');
        $where['website_id'] = $_website_id;
        $where['status'] = $_status;
        $where['modify'] = $modify;
        $list['count'] = $this->where($where)->count();
        $list['list'] = $this->where($where)->order('id desc')->select();
        return $list;
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

    public function getTranslateCount($_website_id){
        return $this->where(
            array(
                'website_id' => intval($_website_id),
                'status' => 1,
            )
        )->count();
    }
}
?>