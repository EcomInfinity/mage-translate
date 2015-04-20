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
        if(preg_match('/.*[^ ].*/', $_params['website_name']) == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Modify Failure.',
                    'data' => array(),
                ),
                'json'
            );
        }else{
            $_result = D('website')->save(array('id' => session('website_id'),'name' => $_params['website_name']));
            if($_result > 0){
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
                        'message' => 'Modify Failure.',
                        'data' => array(),
                    ),
                    'json'
                );
            }
        }
    }
}