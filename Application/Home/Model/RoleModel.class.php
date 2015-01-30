<?php
namespace Home\Model;
use Think\Model;
class RoleModel extends Model{
    public function getPurview($id){
        $purview = $this->where(array('id'=>intval($id)))->field('purview')->find();
        return $purview['purview'];
    }
}
?>