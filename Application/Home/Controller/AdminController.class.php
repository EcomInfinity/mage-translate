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
            $_relation = D('relation')->get($_uid);
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

        $_relation = D('relation')->get($_user_id);
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
        $_params = json_decode(file_get_contents("php://input"),true);
        $ids = D('relation')->gets(session('id'));
        if($ids){
            if($_params['search']&&$_params['search']!=null){
                $list = D('user')->gets(
                        array(
                                'id' => array('in', $ids),
                                'username' => array('like','%'.$_params['search'].'%')
                            )
                    );
            }else{
                $list = D('user')->gets(
                        array(
                                'id' => array('in', $ids)
                            )
                    );
            }
            if($list){
                $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => $list,
                    ),
                    'json'
                );
            }else{
                $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
            }
        }else{
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

    public function userAllow(){
        $user_model = D('user');
        $_params = json_decode(file_get_contents("php://input"),true);
        $_result = $user_model->setAllow($_params['user_id'],$_params['allow']);
        if($_result){
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
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function userInfo(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $rolelist = D('role')->gets(
                array(
                        'website_id' => session('website_id')
                    )
            );
        $user = D('user')->get($_params['user_id']);
        $relation = D('relation')->get($_params['user_id']);
        $userInfo['user_id'] = $user['id'];
        $userInfo['role_id'] = $relation['role_id'];
        $userInfo['username'] = $user['username'];
        $userInfo['rolelist'] = $rolelist;
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $userInfo,
                ),
                'json'
            );
    }

}