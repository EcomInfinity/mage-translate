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
        $user_model = M('user');
        $relation_model = M('relation');
        $website_model = M('website');
        $role_model = M('role');
        $username = $_POST['username'];
        $password = $_POST['password'];
        if($username!=null&&$password!=null){
            $where['username'] = $username;
            $where['password'] = md5($password);
            $where['allow'] = '1';
            $res = $user_model->where($where)->find();
            $userInfo = getUser($res['id']);
            if($res){
                echo '1';
                session('id',$res['id']);
                session('username',$res['username']);
                session('website_id',$userInfo['website']['id']);
                session('website_name',$userInfo['website']['name']);
                session('purview',$userInfo['role']['purview']);
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }
    public function logout(){
        if(session('id')>0&&session('id')){
            session('[destroy]');
        }
        $this->redirect('index');
    }

    public function register(){
        $user_model = M('user');
        $relation_model = M('relation');
        $website_model = M('website');
        $username = $_POST['username'];
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
        $website_name = $_POST['website_name'];
        if(session('id')>0&&session('id')){
            $this->redirect('Translation/index');
        }else{
            if($password1 == $password2){
                $password = $password1;
            }
            $varity = $user_model->where(array('username'=>$username))->find();
            if($username!=null&&$password1!=null&&$password2!=null&&$website_name!=null&&!$varity&&$password){
                $userAdd['username'] = $username;
                $userAdd['password'] = md5($password);
                $webAdd['name'] = $website_name;
                $user_id = $user_model->add($userAdd);
                $website_id = $website_model->add($webAdd);
                $relaAdd['user_id'] = $user_id;
                $relaAdd['website_id'] = $website_id;
                $relaAdd['role_id'] = '1';
                $relation_model->add($relaAdd);
                echo '1';
            }else{
                $this->display();
            }
        }
    }

    public function userAdd(){
        $user_model = M('user');
        $relation_model = M('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $username = $back['username'];
        $password = $back['password'];
        $role_id = $back['role_id'];
        $varity = $user_model->where(array('username'=>$username))->find();
        if($username!=''&&$password!=''&&!$varity){
            $userAdd['username'] = $username;
            $userAdd['password'] = md5($password);
            $user_id = $user_model->add($userAdd);
            $relaAdd['website_id'] = session('website_id');
            $relaAdd['user_id'] = $user_id;
            $relaAdd['role_id'] = $role_id;
            $relaAdd['parent_id'] = session('id');
            $relation_model->add($relaAdd);
            echo '1';
        }else{
            echo '0';
        }
    }

    public function userList(){
        $user_model = M('user');
        $relation_model = M('relation');
        $user_id = $relation_model->where(array('parent_id'=>session('id')))->field('user_id')->select();
        $where['id'] = array('neq',session('id'));
        foreach ($user_id as $k=>$val) {
            # code...
            $userList[] = $user_model->where(array('id'=>$val['user_id']))->field('id,username,allow')->find();
        }
        echo json_encode($userList);
    }

    public function userEdit(){
        $user_model = M('user');
        $relation_model = M('relation');
        $back = json_decode(file_get_contents("php://input"),true);
        $save_rela['role_id'] = $back['role_id'];
        $relation_model->where(array('user_id'=>$back['user_id']))->save($save_rela);
        $save['id'] = $back['user_id'];
        $save['username'] = $back['username'];
        if($back['password']!=''){
            $save['password'] = md5($back['password']);
        }
        $user_model->save($save);
        echo '1';
    }

    public function userAllow(){
        $user_model = M('user');
        $back = json_decode(file_get_contents("php://input"),true);
        $save['id'] = $back['user_id'];
        $save['allow'] = $back['allow'];
        $user_model->save($save);
        echo '1';
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
        }
    }

    public function roleList(){
        $role_model = M('role');
        $where['purview'] = array('neq','-1');
        $where['website_id'] = session('website_id');
        $role_list = $role_model->where($where)->select();
        echo json_encode($role_list);
    }

    public function ruleList(){
        $rule_model = M('rule');
        $ruleList = $rule_model->order('id desc')->select();
        echo json_encode($ruleList);
    }
}