<?php
namespace Home\Controller;
use Think\Controller;

class RuleController extends Controller {
    public function gets() {
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => D('rule')->gets(),
            ),
            'json'
        );
    }
}