<?php
namespace Home\Model;
use Think\Model;
class RuleModel extends Model{
    public function gets(){
        return $this->order('id desc')->select();
    }

    public function total(){
        return $this->count();
    }
}
?>