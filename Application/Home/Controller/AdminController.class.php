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
            $userInfo = getUser($res['id']);
            if($res){
                echo '1';
                session('id',$res['id']);
                session('username',$res['username']);
                session('website_id',$userInfo['website']['id']);
                session('website_name',$userInfo['website']['website_name']);
                session('purview',$userInfo['role']['purview']);
                //session('role_id',);
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
        //var_dump($userList);
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

    public function userInfo(){
        $user_model = M('user');
        $rule_model = M('rule');
        $rule_model = M('rule');
        $ruleList = $rule_model->order('id desc')->select();
        $count = $rule_model->count();
        $back = json_decode(file_get_contents("php://input"),true);
        $user = $user_model->where(array('id'=>$back['user_id']))->find();
        $purview = getPurview($user['id']);
        $purview = str_split(str_pad(decbin($purview),$count,'0',STR_PAD_LEFT));
        // foreach ($rulelist as $key => $value) {
        //     # code...
        //     foreach ($value as $val) {
        //         # code...
        //         $test[$key]['purval'] = $val;
        //     }
        // }
        $userInfo['id'] = $user['id'];
        $userInfo['username'] = $user['username'];
        $userInfo['rulelist'] = $ruleList;
        echo json_encode($userInfo);
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