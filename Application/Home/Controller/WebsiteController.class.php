<?php
namespace Home\Controller;
use Think\Controller;

class WebsiteController extends WebsitePermissionController {
    public function get() {
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

    public function langInfo(){
        $_web_lang_result = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
        if(empty($_web_lang_result)){
            $_checked = '25';
        }else{
            foreach ($_web_lang_result as $k => $val) {
                # code...
                $_checked = $val['lang_id'].','.$_checked;
            }
            $_checked = $_checked.'25';
        }
        $_lang_result = D('language')->where(array('id' => array('not in', $_checked)))->select();
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                        'checked' => $_web_lang_result,
                        'unchecked' => $_lang_result
                    ),
            ),
            'json'
        );
    }

    public function saveName(){
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