<?php
namespace Home\Model;
use Think\Model;

class WebsiteModel extends Model{
    public function getWebsiteName($_id){
        $_result = $this->where(array('id' => intval($_id)))
                        ->field('name')
                        ->find();
        return $_result['name'];
    }

    public function addWebsite($_website_name){
        // if (preg_match('/^[a-zA-Z0-9]{5,15}$/', $_website_name) == 0) {
        //     return 'The website name must have 1-15 digits or letters.';
        // }

        return $this->add(array(
            'name' => $_website_name
        ));
    }
}
?>