<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\AuthRuleModel;
use Home\Model\AuthGroupModel;
class BaseController extends Controller {
    protected function _empty(){
        $this->error('Please enter the correct URL');
    }
     protected function _initialize(){
        if(session('id')<0||!session('id')){
            $this->redirect('Admin/index');
        }
        if(session('id')>0&&session('id')){
            if(getAllow(session('id')) == '0'){
                $this->redirect('Admin/logout');
            }
        }
     }
}