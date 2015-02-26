<?php
namespace Home\Model;
use Think\Model;
class WebsiteModel extends Model{
    //$_wid -> $website_id
    public function getWebsiteName($_wid){
        $name = $this->where(array('id'=>intval($_wid)))->field('name')->find();
        return $name['name'];
    }

    public function websiteMatch($_param){
        return preg_match('/^[a-zA-Z0-9]{1,15}$/',$_param);
    }

    public function addWebsite($website_name){
        if($this->websiteMatch($website_name) == '1'){
            $add['name'] = $website_name;
            $wid = $this->add($add);
            return $wid;
        }else{
            E('The websiteName must have 1-15 digits or letters.');
        }
    }
}
?>