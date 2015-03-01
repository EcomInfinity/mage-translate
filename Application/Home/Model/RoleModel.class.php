<?php
namespace Home\Model;
use Think\Model;

class RoleModel extends Model{
    //$_rid->$role_id
    public function getPurview($_rid){
        $purview = $this->where(array('id'=>intval($_rid)))->field('purview')->find();
        return $purview['purview'];
    }
    public function gets($_where){
        return $this->where($_where)->select();
    }

    public function get($_rid){
        return $this->where(array('id'=>intval($_rid)))->find();
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
            return 'Rolename must have 1-20 characters';
        }
    }

    public function setRole($_params){
        if (preg_match('/^.{1,20}$/', $_params['role_name']) == '1'){
            $_result = $this->save($_params);
            if($_result > 0){
                return $_result;
            }else{
                return 'Modify Failure.';
            }
        } else {
            return 'Role name must have 1-20 characters';
        }
    }
}
?>