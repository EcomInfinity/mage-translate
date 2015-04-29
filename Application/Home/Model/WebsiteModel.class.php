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
        if(preg_match('/.*[^ ].*/', $_website_name) == 0){
            return 'Website name not all spaces or empty.';
        }else{
            return $this->add(array(
                'name' => $_website_name
            ));
        }
    }

    public function get($_where, $_field){
        return $this->where($_where)->field($_field)->find();
    }

    public function setWeb($_save, $_where){
        if(isset($_save['name']) && preg_match('/.*[^ ].*/', $_save['name']) == 0){
            return 'Website name not all spaces or empty.';
        }else{
            if(empty($_where)){
                $_result = $this->save($_save);
            }else{
                $_result = $this->where($_where)->save($_save);
            }
            if($_result > 0){
                return true;
            }else{
                return 'Modify Failure.';
            }
        }
    }
}
?>