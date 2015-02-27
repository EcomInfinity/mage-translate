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
        
        $_uid = session('id');
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
        $_session_id = session('id');
        if (isset($_session_id) && $_session_id > 0) {
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
            return;
        }

        $_website_id = D('website')->addWebsite($_params['website_name']);
        
        $_relation_id = D('relation')->addRelation(
            array(
                'user_id' => $_user_id,
                'website_id' => $_website_id,
                'role_id' => 1
            )
        );

        $_relation = D('relation')->getUserRelation($_user_id);
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

    public function userAdd(){
        $_params = json_decode(file_get_contents("php://input"), true);
        $_user_id = D('user')->addUser($_params['username'], $_params['password']);
        
        if (is_string($_user_id)) {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => $_user_id,
                    'data' => array(),
                ),
                'json'
            );
            return;
        } 

        D('relation')->addRelation(array(
            'user_id' => $_user_id,
            'website_id' => session('website_id'),
            'role_id' => $_params['role_id'],
            'parent_id' => session('id'),
        ));

        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(),
            ),
            'json'
        );
    }

    public function userList(){
        $user_model = D('user');
        $relation_model = D('relation');
        $_params = json_decode(file_get_contents("php://input"),true);
        $ids = $relation_model->getSubUser(session('id'));
        if($ids){
            if($_params['search']&&$_params['search']!=null){
                $res = $user_model->searchUser($_params['search'],$ids);
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

    //修改个人密码
    public function centerEdit(){
        $user_model = D('user');
        $_params = json_decode(file_get_contents("php://input"),true);
        $user = $user_model->getOneUser($_params['id']);
        if(md5($_params['original-password']) == $user['password']){
            if($_params['new-password'] == $_params['confirm-new-password']){
                $res = $user_model->setPassword($_params['new-password'],$_params['id']);
                if($res){
                    $this->ajaxReturn(
                            array(
                                'success' => true,
                                'message' => '',
                                'data' => array(),
                            ),
                            'json'
                        );
                }else{
                    $this->ajaxReturn(
                            array(
                                'success' => false,
                                'message' => 'Modify failure.',
                                'data' => array(),
                            ),
                            'json'
                        );
                }
            }else{
                $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => 'Password doesn\'t match.',
                            'data' => array(),
                        ),
                        'json'
                    );
            }
        }else{
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'The password is incorrect.',
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function userEdit(){
        $user_model = D('user');
        $relation_model = D('relation');
        $_params = json_decode(file_get_contents("php://input"),true);
        $setName = $user_model->setUsername($_params['username'],$_params['user_id']);
        if(isset($_params['password'])  === true){
            $setPwd = $user_model->setPassword($_params['password'],$_params['user_id']);
        }
        $setRela = $relation_model->setUserRole($_params['role_id'],$_params['user_id']);
        if(is_string($setName) === false||is_string($setPwd) === false||is_string($setRela) === false){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Modify failure.',
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function userAllow(){
        $user_model = D('user');
        $_params = json_decode(file_get_contents("php://input"),true);
        $res = $user_model->setAllow($_params['user_id'],$_params['allow']);
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
        $_params = json_decode(file_get_contents("php://input"),true);
        $rolelist = $role_model->getRoleList(session('website_id'));
        $user = $user_model->getOneUser($_params['user_id']);
        $relation = $relation_model->getUserRelation($_params['user_id']);
        $userInfo['user_id'] = $user['id'];
        $userInfo['role_id'] = $relation['role_id'];
        $userInfo['username'] = $user['username'];
        $userInfo['rolelist'] = $rolelist;
        echo json_encode($userInfo);
    }
<<<<<<< HEAD

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
            if(is_string($id) === false){
                $this->ajaxReturn(
                        array(
                            'success' => true,
                            'message' => '',
                            'data' => array(),
                        ),
                        'json'
                    );
            }else{
                $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => $id,
                            'data' => array(),
                        ),
                        'json'
                    );
            }
    }

    public function roleList(){
        $role_model = D('role');
        $_params = json_decode(file_get_contents("php://input"),true);
        if($_params['search']&&$_params['search']!=null){
            $role_list = $role_model->searchRole($_params['search'],session('website_id'));
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
        $_params = json_decode(file_get_contents("php://input"),true);
        $count = $rule_model->getRuleCount();
        $rule = $rule_model->getRuleList();
        $role = $role_model->getOneRole($_params['role_id']);
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
        if($res > '0'){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Modify failure.',
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function ruleList(){
        $rule_model = D('rule');
        $ruleList = $rule_model->getRuleList();
        echo json_encode($ruleList);
    }
=======
>>>>>>> c778e6a4bbe7f418ace80d6b6abb2a7444ab45e5
}