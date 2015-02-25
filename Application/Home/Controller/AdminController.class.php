<?php
namespace Home\Controller;
use Think\Controller;

class AdminController extends Controller {
    public function index(){
        $_session_id = session('id');
        if (isset($_session_id) && $_session_id > 0) {
            $this->redirect('/lang');
        } else {
            $this->display();
        }
    }

    public function login(){
        $_user = D('user');
        
        $_uid = session('uid');
        if (isset($_uid) === false) {
            $_params = json_decode(file_get_contents("php://input"), true);
            $_username = $_params['username'];
            $_password = $_params['password'];

            $_uid = $_user->login($_username, $_password);
        }

        if ($_uid === false) {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Incorrect Username or Password',
                    'data' => array(),
                ),
                'json'
            );
        } else {
            $_relation = D('relation')->getUserRelation($_uid);
            session('website_id', $_relation['website_id']);
            session('website_name', D('website')->getWebsiteName($_relation['website_id']));
            session('purview', getPurviewJson(D('role')->getPurview($_relation['role_id'])));

            $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
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
        $_params = json_decode(file_get_contents("php://input"),true);
        $_params['username'] = $_params['username'];
        $_params['password'] = $_params['password'];
        $_params['repeat-password'] = $_params['password-rpt'];

        $_user_id = D('user')->register($_params);
        if (is_string($_user_id)) {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => $_user_id,
                    'data' => array(),
                ),
                'json'
            );
        }

        $_website_id = D('website')->addWebsite($_params['website_name']);
        
        $_relation_id = D('relation')->addRelation(
            array(
                'user_id' => $_user_id,
                'website_id' => $_website_id,
                'role_id' => 1
            )
        );

        session('website_id', $_website_id);
        session('website_name', $_params['website_name']);
        session('purview', getPurviewJson(D('role')->getPurview($_relation_id)));

        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
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