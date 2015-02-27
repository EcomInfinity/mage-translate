<?php
namespace Home\Controller;
use Think\Controller;

class RoleController extends Controller {
    public function gets() {
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => D('rule')->getRuleList(),
            ),
            'json'
        );
    }
}