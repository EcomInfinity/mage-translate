<?php
namespace Home\Controller;
use Think\Controller;

class CmsPermissionController extends BaseController {
    public function _before_saveStorePage(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_saveStoreBlock(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['update'] == 0){
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

    public function _before_cmsExport(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['export'] == 0){
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

    public function _before_cmsExportZip(){
        $_purview = json_decode(session('purview'), true);
        if($_purview >= 0 && $_purview['export'] == 0){
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
}