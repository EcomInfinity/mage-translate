<?php
namespace Home\Model;
use Think\Model;
class RuleModel extends Model{
    public function getRuleList(){
        return $this->order('id desc')->select();
    }

    public function getRuleCount(){
        return $this->count();
    }
}
?>