<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function index(){
        if(session('id')>0&&session('id')){
            $this->redirect('Translation/index');
        }else{
            $this->display();
        }
    }

    public function login(){
        $user_model = D('user');
        $relation_model = D('relation');
        $website_model = D('website');
        $role_model = D('role');
        $username = $_POST['username'];
        $password = $_POST['password'];
        $uid = $user_model->login($username,$password);
        if($uid>'0'){
            $relation = $relation_model->getUserRelation($uid);
            session('id',$uid);
            session('username',$username);
            session('website_id',$relation['website_id']);
            session('website_name',$website_model->getWebsiteName($relation['website_id']));
            session('purview',getPurviewJson($role_model->getPurview($relation['role_id'])));
            echo '1';
        }else{
            echo '0';
        }
    }
    public function logout(){
        if(session('id')>0&&session('id')){
            D('user')->logout();
            session('[destroy]');
        }
        $this->redirect('index');
    }

    public function register(){
        $user_model = D('user');
        $relation_model = D('relation');
        $website_model = D('website');
        $_params['username'] = $_POST['username'];
        $_params['password'] = $_POST['password1'];
        $_params['repeat-password'] = $_POST['password2'];
        $uid = $user_model->register($_params);
        $wid = $website_model->addWebsite($_POST['website_name']);
        $_params_relation['user_id'] = $uid;
        $_params_relation['website_id'] = $wid;
        $_params_relation['role_id'] = '1';
        $res = $relation_model->addRelation($_params_relation);
        if($uid&&$wid&&$res){
            echo '1';
        }else{
            $this->display();
        }
    }

    public function userAdd(){
        $user_model = D('user');
        $relation_model = D('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $_params['username'] = $back['username'];
        $_params['password'] = $back['password'];
        $uid = $user_model->addUser($_params);
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
        if($back['search']&&$back['search']!=null){
            $where['username'] = array('like','%'.$back['search'].'%');
        }
        $ids = $relation_model->getSubUser(session('id'));
        if($ids){
            $where['id'] = array('in',$ids);
            $res = $user_model->getUser($where);
            if($res){
                echo json_encode($res);
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }

    public function userEdit(){
        $user_model = M('user');
        $relation_model = M('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $save_rela['role_id'] = $back['role_id'];
        $relation_model->where(array('user_id'=>$back['user_id']))->save($save_rela);
        $repeat_name = $user_model->where(array('username'=>$back['username']))->find();
        if($repeat_name){
            echo '0';
        }else{
            $save['id'] = $back['user_id'];
            $save['username'] = $back['username'];
            if($back['password']!=''){
                $save['password'] = md5($back['password']);
            }
            $res = $user_model->save($save);
            if($res){
                echo '1';
            }else{
                echo '0';
            }
        }
    }

    public function userAllow(){
        $user_model = M('user');
        $back = json_decode(file_get_contents("php://input"),true);
        if($back['user_id']!=null&&$back['allow']!==null){
            $save['id'] = intval($back['user_id']);
            $save['allow'] = intval($back['allow']);
            $user_model->save($save);
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userInfo(){
        $user_model = M('user');
        $role_model = M('role');
        $relation_model = M('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $rolelist = $role_model->where(array('website_id'=>session('website_id')))->select();
        $user = $user_model->where(array('id'=>$back['user_id']))->find();
        $relation = $relation_model->where(array('user_id'=>$back['user_id']))->find();
        $userInfo['user_id'] = $user['id'];
        $userInfo['role_id'] = $relation['role_id'];
        $userInfo['username'] = $user['username'];
        $userInfo['rolelist'] = $rolelist;
        echo json_encode($userInfo);
    }

    public function roleAdd(){
        $role_model = M('role');
        $rule_model = M('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $rule = $rule_model->order('id desc')->select();
        foreach ($rule as $k => $val) {
            # code...
            $purview = $purview.$back[strtolower($val['rule_name'])];
        }
        $role_name = $back['role'];
        $purview = bindec($purview);
        if($role_name!=''){
            $add['role_name'] = $role_name;
            $add['purview'] = $purview;
            $add['website_id'] = session('website_id');
            $role_model->add($add);
            echo '1';
        }else{
            echo '0';
        }
    }

    public function roleList(){
        $role_model = M('role');
        $back = json_decode(file_get_contents("php://input"),true);
        if($back['search']&&$back['search']!=null){
            $where['role_name'] = array('like','%'.$back['search'].'%');
        }
        $where['purview'] = array('neq','-1');
        $where['website_id'] = session('website_id');
        $role_list = $role_model->where($where)->select();
        if($role_list){
            echo json_encode($role_list);
        }else{
            echo '0';
        }
    }

    public function roleInfo(){
        $role_model = M('role');
        $rule_model = M('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $count = $rule_model->count();
        $rule = $rule_model->order('id desc')->field('rule_name')->select();
        $role = $role_model->where(array('id'=>intval($back['role_id'])))->find();
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
        $role_model = M('role');
        $rule_model = M('rule');
        $back = json_decode(file_get_contents("php://input"),true);
        $rule = $rule_model->order('id desc')->select();
        foreach ($rule as $k => $val) {
            # code...
            $purview = $purview.$back[strtolower($val['rule_name'])];
        }
        $role_name = $back['role_name'];
        $role_id = intval($back['role_id']);
        $purview = bindec($purview);
        $save['id'] = $role_id;
        $save['role_name'] = $role_name;
        $save['purview'] = $purview;
        $res = $role_model->save($save);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function ruleAdd(){
        $rule_model = M('rule');
        $rule_model->add();
    }

    public function ruleList(){
        $rule_model = M('rule');
        $ruleList = $rule_model->order('id desc')->select();
        echo json_encode($ruleList);
    }
}