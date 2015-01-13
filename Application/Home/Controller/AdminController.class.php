<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function index(){
       $this->display();
    }
    public function login(){
        $user_model = M('user');
        $relation_model = M('relation');
        $website_model = M('website');
        $username = $_POST['username'];
        $password = $_POST['password'];
        if($username!=null&&$password!=null){
            $where['username'] = $username;
            $where['password'] = md5($password);
            $res = $user_model->where($where)->find();
            $relation = $relation_model->where(array('user_id'=>$res['id']))->find();
            $website = $website_model->where(array('id'=>$relation['website_id']))->find();
            if($res){
                echo '1';
                session('id',$res['id']);
                session('username',$res['username']);
                session('website_id',$website['id']);
                session('website_name',$website['website_name']);
            }else{
                echo '0';
            }
        }
    }
    public function logout(){
        if(session('id')>0||session('id')){
            session('[destroy]');
        }
        $this->redirect('index');
    }

    public function userAdd(){
        $user_model = M('user');
        $relation_model = M('relation');
    }

    public function roleAdd(){
        $role_model = M('role');
        if(IS_POST){
            $role_name = $_POST['role'];
            $create = $_POST['create'];
            $retrieve = $_POST['retrieve'];
            $update = $_POST['update'];
            $delete = $_POST['delete'];
            $purview = $create.$retrieve.$update.$delete;
            $purview = bindec($purview);
            $add['role_name'] = $role_name;
            $add['purview'] = $purview;
            $role_model->add($add);
            $this->success('ok');
        }else{
            $this->display();
        }
    }
}