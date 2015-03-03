<?php
namespace Home\Controller;
use Think\Controller;

class ImagePermissionController extends BaseController {
    public function _before_del(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['delete'] == 0 && $_purview['update'] == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Illegal delete data.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }
    }

    public function _before_clear(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['delete'] == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Illegal delete data.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }
    }

    public function _before_add(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['create'] == 0 && $_purview['update'] == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Illegal create data.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }
    }
}