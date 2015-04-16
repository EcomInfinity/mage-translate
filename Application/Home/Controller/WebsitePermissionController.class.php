<?php
namespace Home\Controller;
use Think\Controller;

class WebsitePermissionController extends Controller {

    public function _before_saveName(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0 && $_purview['create'] == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Illegal update data.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }
    }

}