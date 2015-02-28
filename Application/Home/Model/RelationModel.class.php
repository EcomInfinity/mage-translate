<?php
namespace Home\Model;
use Think\Model;
class RelationModel extends Model{
    //$uid用户id
    public function get($uid){
        return $this->where(array('user_id'=>intval($uid)))->find();
    }
    //$_params(user_id,website_id,role_id)
    public function addRelation($_params){
        return $this->add($_params);
    }

    public function  gets($uid){
        $user_id = $this->where(array('parent_id'=>intval($uid)))->select();
        if($user_id){
            foreach ($user_id as $key => $val) {
                $ids = $ids.','.$val['user_id'];
            }
            $ids = substr($ids,1);
        }
        return $ids;
    }

    public function setUserRole($_role_id,$uid){
        $save['role_id'] = $_role_id;
        $_result = $this->where(array('user_id'=>intval($uid)))->save($save);
        if($_result > '0'){
            return $_result;
        }else{
            return 'Modify failure.';
        }
    }
}
?>