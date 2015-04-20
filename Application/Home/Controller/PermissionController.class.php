<?php
namespace Home\Controller;
use Think\Controller;

class PermissionController extends BaseController {
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

    public function _before_del(){
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

    public function _before_edit(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0 && $_purview['create'] == 0){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Illegal update data.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }
    }
    //image
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
    //magento api
    public function _before_syncTranslatePage(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0 && $_purview['create'] == 0){
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

    public function _before_syncMagentoPage(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_syncSelectPage(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_syncTranslateBlock(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0 && $_purview['create'] == 0){
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

    public function _before_syncMagentoBlock(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_syncSelectBlock(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    //csv translate
    public function _before_export(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['export'] == 0){
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

    public function _before_import(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['create'] == 0 && $_purview['update'] == 0){
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

    public function _before_dels(){
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

    public function _before_needUpdate(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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
    //user
    public function _before_restSync(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0 && $_purview['create'] == 0){
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

    public function _before_enable(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_disable(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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
}