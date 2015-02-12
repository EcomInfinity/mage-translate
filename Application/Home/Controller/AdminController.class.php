<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function index(){
        if(session('id')>0&&session('id')){
            $this->redirect('/lang');
        }else{
            $this->display();
        }
    }

    public function login(){
        $user_model = D('user');
        $relation_model = D('relation');
        $website_model = D('website');
        $role_model = D('role');
        $back = json_decode(file_get_contents("php://input"),true);
        $username = $back['username'];
        $password = $back['password'];
        if(session('uid')>'0'){
            $uid = session('uid');
        }else{
            $uid = $user_model->login($username,$password);
        }
        if($uid>'0'){
            $relation = $relation_model->getUserRelation($uid);
            session('id',$uid);
            session('username',$user_model->getUserName($uid));
            session('website_id',$relation['website_id']);
            session('website_name',$website_model->getWebsiteName($relation['website_id']));
            session('purview',getPurviewJson($role_model->getPurview($relation['role_id'])));
            if(session('uid')>'0'){
                $this->redirect('/lang');
            }else{
                echo '1';
            }
        }else{
            echo '0';
        }
    }
    public function logout(){
        if(session('id')>0&&session('id')){
            D('user')->logout();
            session('[destroy]');
        }
        $this->redirect('/admin');
    }

    public function register(){
        $user_model = D('user');
        $relation_model = D('relation');
        $website_model = D('website');
        $back = json_decode(file_get_contents("php://input"),true);
        $_params['username'] = $back['username'];
        $_params['password'] = $back['password1'];
        $_params['repeat-password'] = $back['password2'];
        $uid = $user_model->register($_params);
        $wid = $website_model->addWebsite($back['website_name']);
        $_params_relation['user_id'] = $uid;
        $_params_relation['website_id'] = $wid;
        $_params_relation['role_id'] = '1';
        $res = $relation_model->addRelation($_params_relation);
        if($uid&&$wid&&$res){
            session('uid',$uid);
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userAdd(){
        $user_model = D('user');
        $relation_model = D('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $uid = $user_model->addUser($back['username'],$back['password']);
        $_params_relation['user_id'] = $uid;
        $_params_relation['website_id'] = session('website_id');
        $_params_relation['role_id'] = $back['role_id'];
        $_params_relation['parent_id'] = session('id');
        $res = $relation_model->addRelation($_params_relation);
        if($uid){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userList(){
        $user_model = D('user');
        $relation_model = D('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $ids = $relation_model->getSubUser(session('id'));
        if($ids){
            if($back['search']&&$back['search']!=null){
                $res = $user_model->searchUser($back['search'],$ids);
            }else{
                $res = $user_model->getUserList($ids);
            }
            if($res){
                echo json_encode($res);
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }

    public function centerEdit(){
        $user_model = D('user');
        $back = json_decode(file_get_contents("php://input"),true);
        $user = $user_model->getOneUser($back['id']);
        if(md5($back['original-password']) == $user['password']){
            if($back['new-password'] == $back['confirm-new-password']){
                $res = $user_model->setPassword($back['new-password'],$back['id']);
                if($res){
                    echo '1';
                }else{
                    echo '0';
                }
            }else{
                echo '2';
            }
        }else{
            echo '3';
        }
    }

    public function userEdit(){
        $user_model = D('user');
        $relation_model = D('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $setName = $user_model->setUsername($back['username'],$back['user_id']);
        if($back['password']!=''){
            $setPwd = $user_model->setPassword($back['password'],$back['user_id']);
        }
        $setRela = $relation_model->setUserRole($back['role_id'],$back['user_id']);
        if($setName||$setPwd||$setRela){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userAllow(){
        $user_model = D('user');
        $back = json_decode(file_get_contents("php://input"),true);
        $res = $user_model->setAllow($back['user_id'],$back['allow']);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userInfo(){
        $user_model = D('user');
        $role_model = D('role');
        $relation_model = D('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $rolelist = $role_model->getRoleList(session('website_id'));
        $user = $user_model->getOneUser($back['user_id']);
        $relation = $relation_model->getUserRelation($back['user_id']);
        $userInfo['user_id'] = $user['id'];
        $userInfo['role_id'] = $relation['role_id'];
        $userInfo['username'] = $user['username'];
        $userInfo['rolelist'] = $rolelist;
        echo json_encode($userInfo);
    }

    public function roleAdd(){
        $role_model = D('role');
        $rule_model = D('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $rule = $rule_model->getRuleList();
        foreach ($rule as $k => $val) {
            # code...
            $purview = $purview.$back[strtolower($val['rule_name'])];
        }
        $purview = bindec($purview);
        $_params['role_name'] = $back['role'];
        $_params['purview'] = $purview;
        $_params['website_id'] = session('website_id');
        $id = $role_model->addRole($_params);
        if($id > 0){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function roleList(){
        $role_model = D('role');
        $back = json_decode(file_get_contents("php://input"),true);
        if($back['search']&&$back['search']!=null){
            $role_list = $role_model->searchRole($back['search'],session('website_id'));
        }else{
            $role_list = $role_model->getRoleList(session('website_id'));
        }
        if($role_list){
            echo json_encode($role_list);
        }else{
            echo '0';
        }
    }

    public function roleInfo(){
        $role_model = D('role');
        $rule_model = D('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $count = $rule_model->getRuleCount();
        $rule = $rule_model->getRuleList();
        $role = $role_model->getOneRole($back['role_id']);
        $purview = str_split(str_pad(decbin($role['purview']),$count,'0',STR_PAD_LEFT));
        foreach ($rule as $key => $value) {
            $rule[$key]['purview'] = $purview[$key];
        }
        $roleInfo['role_name'] = $role['role_name'];
        $roleInfo['role_id'] = $role['id'];
        $roleInfo['rule'] = $rule;
        echo json_encode($roleInfo);
    }

    public function roleEdit(){
        $role_model = D('role');
        $rule_model = D('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $rule = $rule_model->getRuleList();
        foreach ($rule as $k => $val) {
            # code...
            $purview = $purview.$back[strtolower($val['rule_name'])];
        }
        $_params['role_name'] = $back['role_name'];
        $_params['role_id'] = $back['role_id'];
        $_params['purview'] = bindec($purview);
        $res = $role_model->setRole($_params);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function ruleList(){
        $rule_model = D('rule');
        $ruleList = $rule_model->getRuleList();
        echo json_encode($ruleList);
    }
}