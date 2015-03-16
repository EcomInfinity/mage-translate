<?php
namespace Home\Controller;
use Think\Controller;

class WebsiteController extends Controller {
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
}