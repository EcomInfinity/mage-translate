<?php
namespace Home\Controller;
use Think\Controller;

class WebsiteLangController extends PermissionController {
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
        if(empty($_params['site_lang_id'])){
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Please correct operation.',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            foreach ($_params['site_lang_id'] as $val) {
                # code...
                $_website_lang = D('website_lang')->get(array('lang_id' => $val, 'website_id' => session('website_id')));
                //判断是否已经添加过
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
                $_base_result = D('base_translate')->gets(array('website_id' => session('website_id')),'','id');
                foreach ($_base_result as $value) {
                    # code...
                    $_find = D('other_translate')->get(array('base_id' => $value['id'], 'lang_id' => $val));
                    if(empty($_find)){
                        D('other_translate')->add(array('lang_id' => $val, 'base_id' => $value['id']));
                    }
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
    }

    public function del(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if(empty($_params['site_lang_id'])){
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Please correct operation.',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
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
}