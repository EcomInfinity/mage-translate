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
}