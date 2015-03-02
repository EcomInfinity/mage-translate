<?php
namespace Home\Controller;
use Think\Controller;
use Think\Controller\RestController;
use Home\Model\AuthRuleModel;
use Home\Model\AuthGroupModel;
class BaseController extends RestController {
    protected function _empty(){
        $this->error('Please enter the correct URL');
    }

    protected function _initialize(){
        if(session('id')<0||!session('id')){
            $this->redirect('/admin');
        }
        if(session('id')>0&&session('id')){
            if(getAllow(session('id')) == '0'){
                $this->redirect('/logout');
            }
        }
    }
}