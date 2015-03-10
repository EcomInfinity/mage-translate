<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends UserPermissionController {
    // public function index(){
    //     $_session_id = session('id');
    //     if (isset($_session_id) && $_session_id > 0) {
    //         $this->redirect('/lang');
    //     } else {
    //         $this->display();
    //     }
    // }

    // public function login(){
    //     $_user = D('user');
        
    //     $_uid = session('id');
    //     if (isset($_uid) === false) {
    //         $_params = json_decode(file_get_contents("php://input"), true);
    //         $_username = $_params['username'];
    //         $_password = $_params['password'];

    //         $_uid = $_user->login($_username, $_password);
    //     }

    //     if ($_uid === false) {
    //         $this->ajaxReturn(
    //             array(
    //                 'success' => false,
    //                 'message' => 'Incorrect Username or Password',
    //                 'data' => array(),
    //             ),
    //             'json'
    //         );
    //     } else {
    //         $_relation = D('relation')->get($_uid);
    //         session('website_id', $_relation['website_id']);
    //         session('website_name', D('website')->getWebsiteName($_relation['website_id']));
    //         session('purview', getPurviewJson(D('role')->getPurview($_relation['role_id'])));

    //         $this->ajaxReturn(
    //             array(
    //                 'success' => true,
    //                 'message' => '',
    //                 'data' => array(),
    //             ),
    //             'json'
    //         );
    //     }
    // }
    // public function logout(){
    //     $_session_id = session('id');
    //     if (isset($_session_id) && $_session_id > 0) {
    //         D('user')->logout();
    //         session('[destroy]');
    //     }
    //     $this->redirect('/admin');
    // }

    // public function register(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     $_params['username'] = $_params['username'];
    //     $_params['password'] = $_params['password'];
    //     $_params['repeat-password'] = $_params['password-rpt'];

    //     $_user_id = D('user')->register($_params);
    //     if (is_string($_user_id)) {
    //         $this->ajaxReturn(
    //             array(
    //                 'success' => false,
    //                 'message' => $_user_id,
    //                 'data' => array(),
    //             ),
    //             'json'
    //         );
    //         return;
    //     }

    //     $_website_id = D('website')->addWebsite($_params['website_name']);
        
    //     $_relation_id = D('relation')->addRelation(
    //         array(
    //             'user_id' => $_user_id,
    //             'website_id' => $_website_id,
    //             'role_id' => 1
    //         )
    //     );

    //     $_relation = D('relation')->get($_user_id);
    //     session('website_id', $_relation['website_id']);
    //     session('website_name', D('website')->getWebsiteName($_relation['website_id']));
    //     session('purview', getPurviewJson(D('role')->getPurview($_relation['role_id'])));

    //     $this->ajaxReturn(
    //             array(
    //                 'success' => true,
    //                 'message' => '',
    //                 'data' => array(),
    //             ),
    //             'json'
    //         );
    // }

    public function add(){
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

    public function get(){
        $_params = json_decode(file_get_contents("php://input"), true);
        $_rolelist = D('role')->gets(
                        array('website_id' => session('website_id'))
                     );
        $_user = D('user')->get($_params['user_id']);
        $_relation = D('relation')->get($_params['user_id']);
        $_result = array(
            'user_id' => $_user['id'],
            'username' => $_user['username'],
            'role_id' => $_relation['role_id'],
            'rolelist' => $_rolelist,
        );
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => $_result,
            ),
            'json'
        );
    }

    public function gets(){
        $_params = json_decode(file_get_contents("php://input"),true);

        $_relations = D('relation')->gets(array('parent_id' => session('id')));
        // $_users = array();

        if (count($_relations) > 0) {
            $_user_ids = array();
            foreach ($_relations as $_relation) {
                $_user_ids[] = $_relation['user_id'];
            }

            $_where = array(
                'id' => array('in', implode(',', $_user_ids)),
            );

            if (isset($_params['search']) === true) {
                $_where['username'] = array('like', '%'.$_params['search'].'%');
            }

            $_users = D('user')->gets($_where);
            foreach ($_users as $_key => $_value) {
                unset($_users[$_key]['password']);
            }
        }

        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                        'users' => $_users,
                        'total' => count($_relations),
                        'count' => count($_users)
                    ),
            ),
            'json'
        );
    }

    public function changePassword() {
        $_params = json_decode(file_get_contents("php://input"),true);
        $user = D('user')->get($_params['id']);

        if(md5($_params['original-password']) != $user['password']) {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'The password is incorrect.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }

        if($_params['new-password'] != $_params['confirm-new-password']) {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Password doesn\'t match.',
                    'data' => array(),
                ),
                'json'
            );
            return;
        }

        $_result = D('user')->setPassword($_params['new-password'], $_params['id']);

        if ($_result === true) {
            $this->ajaxReturn(

                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
        } else {
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => $_result,
                    'data' => array(),
                ),
                'json'
            );
        }
    }

    public function edit() {
        $_params = json_decode(file_get_contents("php://input"),true);
        $_user_id = $_params['user_id'];
        $_username = $_params['username'];
        $_role_id = $_params['role_id'];

        $_user = D('user')->get($_user_id);

        // change user name
        if ($_user['username'] != $_username) {
            $_result = D('user')->setUsername($_username, $_user_id);
            if ($_result !== true) {
                $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => $_result,
                        'data' => array(),
                    ),
                    'json'
                );
            }
        }

        // chnage role
        $_setRela = D('relation')->set($_user_id, $_role_id);

        // change password
        if(strlen($_params['password']) > 5 && strlen($_params['password']) < 31) {
            $_setPwd = D('user')->setPassword($_params['password'], $_user_id);
            if ($_setPwd !== true) {
                $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => $_setPwd,
                        'data' => array(),
                    ),
                    'json'
                );
            }
        }
        if($_result === true || $_setPwd === true || $_setRela === true){
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
                    'message' => 'Modify Failure.',
                    'data' => array(),
                ),
                'json'
            );
        }
    }

    public function enable() {
        $user_model = D('user');
        $_params = json_decode(file_get_contents("php://input"),true);
        $_result = D('user')->enable($_params['user_id']);
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(),
            ),
            'json'
        );
    }

    public function disable() {
        $user_model = D('user');
        $_params = json_decode(file_get_contents("php://input"),true);
        $_result = D('user')->disable($_params['user_id']);
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