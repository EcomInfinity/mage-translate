<?php
namespace Home\Model;
use Think\Model;
class TranslationModel extends Model{
    //$field='1,2,3'需要查询的字段，$_status是否删除状态
    //$_website_id网站id,$modify是否需要修改1-需要修改0-不需修改，$order排序规则
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
    //$_search搜索条件
    public function searchTranslate($_search,$_website_id,$modify,$_status){
        $where['en'] = array('like','%'.$_search.'%');
        $where['website_id'] = $_website_id;
        $where['status'] = $_status;
        $where['modify'] = $modify;
        $list['count'] = $this->where($where)->count();
        $list['list'] = $this->where($where)->order('id desc')->select();
        return $list;
    }
    //$_params数组(各种语言，remarks,status,website_id,modify)
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
    //获取总数
    public function getTranslateCount($_params){
        $where['website_id'] = intval($_params['website_id']);
        $where['status'] = '1';
        return $this->where($where)->count();
    }
}
?>