<?php
namespace Home\Model;
use Think\Model;

class WebsiteModel extends Model{
    //$_wid -> $website_id
    public function getWebsiteName($_id){
        $_result = $this->where(array('id' => intval($_id)))
                        ->field('name')
                        ->find();
        return $_result['name'];
    }

    public function addWebsite($_website_name){
        // if (preg_match('/.*[^ ].*/', $_website_name) == 0) {
        //     return 'The website name must have 1-15 digits or letters.';
        // }
        if(preg_match('/.*[^ ].*/', $_website_name) == 0){
            return 'Website name not empty.';
        }else{
            return $this->add(array(
                'name' => $_website_name
            ));
        }
    }

    public function get($_where){
        return $this->where($_where)->find();
    }
}
?>