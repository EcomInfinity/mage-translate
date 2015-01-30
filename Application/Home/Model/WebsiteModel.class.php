<?php
namespace Home\Model;
use Think\Model;
class WebsiteModel extends Model{
    public function getWebsiteName($id){
        $name = $this->where(array('id'=>intval($id)))->field('name')->find();
        return $name['name'];
    }
    public function addWebsite($website_name){
        $add['name'] = $website_name;
        $wid = $this->add($add);
        return $wid;
    }
}
?>