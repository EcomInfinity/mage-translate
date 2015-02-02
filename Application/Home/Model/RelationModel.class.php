<?php
namespace Home\Model;
use Think\Model;
class RelationModel extends Model{
    public function getUserRelation($uid){
        return $this->where(array('user_id'=>intval($uid)))->find();
    }

    public function addRelation($_params){
        return $this->add($_params);
    }

    public function  getSubUser($uid){
        $user_id = $this->where(array('parent_id'=>intval($uid)))->select();
        if($user_id){
            foreach ($user_id as $key => $val) {
                # code...
                $ids = $ids.','.$val['user_id'];
            }
            $ids = substr($ids,1);
        }
        return $ids;
    }

    public function setUserRole($_role_id,$uid){
        $save['role_id'] = $_role_id;
        return $this->where(array('user_id'=>intval($uid)))->save($save);
    }
}
?>