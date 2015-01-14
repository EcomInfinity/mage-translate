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
        $role_model = M('role');
        $username = $_POST['username'];
        $password = $_POST['password'];
        if($username!=null&&$password!=null){
            $where['username'] = $username;
            $where['password'] = md5($password);
            $where['allow'] = '1';
            $res = $user_model->where($where)->find();
            $relation = $relation_model->where(array('user_id'=>$res['id']))->find();
            $website = $website_model->where(array('id'=>$relation['website_id']))->find();
            $purview = $role_model->where(array('id'=>$relation['role_id']))->find();
            if($res){
                echo '1';
                session('id',$res['id']);
                session('username',$res['username']);
                session('website_id',$website['id']);
                session('website_name',$website['website_name']);
                session('purview',$purview['purview']);
            }else{
                echo '0';
            }
        }else{
            echo '0';
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
        $back = json_decode(file_get_contents("php://input"),true);
        $username = $back['username'];
        $password = $back['password'];
        $role_id = $back['role_id'];
        $userAdd['username'] = $username;
        $userAdd['password'] = md5($password);
        $user_id = $user_model->add($userAdd);
        $relaAdd['website_id'] = session('website_id');
        $relaAdd['user_id'] = $user_id;
        $relaAdd['role_id'] = $role_id;
        $relation_model->add($relaAdd);
        echo '1';
    }

    public function userList(){
        $user_model = M('user');
        $where['id'] = array('neq',session('id'));
        $userList = $user_model->where($where)->field('id,username,allow')->select();
        echo json_encode($userList);
    }

    public function userEdit(){
        $user_model = M('user');
        $back = json_decode(file_get_contents("php://input"),true);
        $save['id'] = $back['user_id'];
        $save['allow'] = $back['allow'];
        $user_model->save($save);
        echo '1';
    }

    public function roleAdd(){
        $role_model = M('role');
        $back = json_decode(file_get_contents("php://input"),true);
        $role_name = $back['role'];
        $create = $back['create'];
        $retrieve = $back['retrieve'];
        $update = $back['update'];
        $delete = $back['delete'];
        $purview = $create.$retrieve.$update.$delete;
        $purview = bindec($purview);
        $add['role_name'] = $role_name;
        $add['purview'] = $purview;
        $role_model->add($add);
        echo '1';
    }

    public function roleList(){
        $role_model = M('role');
        $where['purview'] = array('neq','-1');
        $role_list = $role_model->where($where)->select();
        echo json_encode($role_list);
    }
}