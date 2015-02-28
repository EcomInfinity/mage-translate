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

    public function gets($_where) {
        return $this->where($_where)->select();
    }

    public function set($_user_id, $_role_id) {
        $this->where(array('user_id' => $_user_id))
             ->save(array('role_id' => $_role_id));

        return true;
    }
}
?>