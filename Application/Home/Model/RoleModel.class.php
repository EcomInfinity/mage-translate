<?php
namespace Home\Model;
use Think\Model;
class RoleModel extends Model{
    //$_rid->$role_id
    public function getPurview($_rid){
        $purview = $this->where(array('id'=>intval($_rid)))->field('purview')->find();
        return $purview['purview'];
    }
    //$_wid->$website_id
    public function getRoleList($_wid){
        return $this->where(array('website_id'=>intval($_wid)))->select();
    }

    public function getOneRole($_rid){
        return $this->where(array('id'=>intval($_rid)))->find();
    }
    //$_search搜索条件
    public function searchRole($_search,$_wid){
        $where['role_name'] = array('like','%'.$_search.'%');
        $where['website_id'] = $_wid;
        return $this->where($where)->select();
    }

    public function roleMatch($_param){
        return preg_match('/^.{1,20}$/',$_param);
    }
    //$_params(role_name,purview,website_id)
    public function addRole($_params){
        if($this->roleMatch($_params['role_name']) == '1'){
            $add['role_name'] = $_params['role_name'];
            $add['purview'] = $_params['purview'];
            $add['website_id'] = $_params['website_id'];
            return intval($this->add($add));
        }else{
            return 'Rolename must have 4-20 characters';
        }
    }

    public function setRole($_params){
        if($this->roleMatch($_params['role_name']) == '1'){
            $save['role_name'] = $_params['role_name'];
            $save['purview'] = $_params['purview'];
            $save['id'] = intval($_params['role_id']);
            return intval($this->save($save));
        }else{
            return 'Rolename must have 1-20 characters';
        }
    }
}
?>