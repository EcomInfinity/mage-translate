<?php
namespace Home\Controller;
use Think\Controller;

class WebsiteLangController extends Controller {
    public function gets() {
        $_result = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => $_result,
            ),
            'json'
        );
    }
    public function add(){
        $_params = json_decode(file_get_contents("php://input"),true);
        foreach ($_params['site_lang_id'] as $val) {
            # code...
            $_website_lang = D('website_lang')->get(array('lang_id' => $val, 'website_id' => session('website_id')));
            if(!empty($_website_lang)){
                if($_website_lang['status'] == 1){
                    $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => 'Already exists.',
                            'data' => array(),
                        ),
                        'json'
                    );
                }else{
                    D('website_lang')->save(array('id' => $_website_lang['id'], 'status' => 1));
                }
            }else{
                D('website_lang')->add(array('lang_id' => $val, 'website_id' => session('website_id')));
            }
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }

    public function del(){
        $_params = json_decode(file_get_contents("php://input"),true);
        foreach ($_params['site_lang_id'] as $val) {
            # code...
            D('website_lang')->save(array('id'=>$val, 'status' => 0));
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }
}