<?php
namespace Home\Controller;
use Think\Controller;

class WebsiteController extends PermissionController {
    public function load() {
        $_result = D('website')->get(array('id' => session('website_id')));
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => $_result,
            ),
            'json'
        );
    }

    public function edit(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_result = D('website')->setWeb(array('id' => session('website_id'),'name' => $_params['website_name']));
        if($_result === true){
            session('website_name', $_params['website_name']);
            $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
        }else{
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => $_result,
                    'data' => array(),
                ),
                'json'
            );
        }
    }
}