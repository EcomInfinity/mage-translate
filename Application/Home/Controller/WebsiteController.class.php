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
        $_store_view_result = magentoApiSync(
                session('soap'),
                'info_getwebinfo.storeViewList',
                array()
            );
        $_store_view_result = json_decode($_store_view_result, true);
        foreach ($_store_view_result as $val) {
            $_lang_store = D('language')->where(array('simple_name' => $val['store_view_language']))->find();
            $_checked[] = $_magento_store_view_lang_id[] = $_lang_store['id'];
        }
        $_magento_store_view_lang_id = array_unique($_magento_store_view_lang_id);
        $_lang_base = D('language')->where(array('simple_name' => 'en_us'))->find();
        $_web_lang_result = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
        $_checked[] = $_web_lang_id[] = $_lang_base['id'];
        foreach ($_web_lang_result as $val) {
            $_checked[] = $_web_lang_id[] = $val['lang_id'];
        }
        $_need_add_lang_id = array_diff($_magento_store_view_lang_id, $_web_lang_id);
        foreach ($_need_add_lang_id as $val) {
            $_need_add_lang[] = D('language')->find($val);
        }
        $_checked = array_unique($_checked);
        $_checked = implode(',',$_checked );
        $_lang_result = D('language')->where(array('id' => array('not in', $_checked)))->select();
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                        'checked' => $_web_lang_result,
                        'unchecked' => $_lang_result,
                        'needchecked' => $_need_add_lang
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