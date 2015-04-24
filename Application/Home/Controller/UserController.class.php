<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends PermissionController {
    public function index(){
        $this->display();
    }

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

    public function load(){
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

    public function personalSetting() {
        $_params = json_decode(file_get_contents("php://input"),true);
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
        if(strlen($_params['new-password']) > 5 && strlen($_params['new-password']) < 31) {
            $_result = D('user')->setPassword($_params['new-password'], $_params['id']);
        }
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
                    'message' => 'Modify Failure.',
                    'data' => array(),
                ),
                'json'
            );
        }
    }

    public function restSync() {
        $_params = json_decode(file_get_contents("php://input"),true);
        $_communication = magentoApiSync(
                array(
                        'domain' => $_params['domain'],
                        'rest_user' => $_params['rest_user'],
                        'rest_password' => $_params['rest_password']
                    )
            );
        if($_communication === false){
            $this->ajaxReturn(
                array(
                    'success' => false,
                    'message' => 'Communication Failure.',
                    'data' => array(),
                ),
                'json'
            );
        }else{
            $_save['id'] = session('website_id');
            $_save['domain'] = $_params['domain'];
            $_save['rest_user'] = $_params['rest_user'];
            $_save['rest_password'] = $_params['rest_password'];
            $_save['communication_status'] = 1;
            $_result = D('website')->save($_save);
            if($_result > 0){
                session('soap',array('domain' => $_params['domain'], 'rest_user' => $_params['rest_user'], 'rest_password' => $_params['rest_password']));
                $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => 'Communication Success.',
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
    }

    public function magentoStore(){
        $_website = D('website')->find(session('website_id'));
        if($_website['communication_status'] == 1){
            $_web_view_result = magentoApiSync(
                    session('soap'),
                    'translator_getwebinfo.list',
                    array()
                );
            $_store_view_result = magentoApiSync(
                    session('soap'),
                    'translator_getwebinfo.storeViewList',
                    array()
                );
            $_store_view_result = json_decode($_store_view_result, true);
            $_web_view_result = json_decode($_web_view_result, true);
            foreach ($_web_view_result as $k1 => $val) {
                foreach ($val['stores'] as $k2 => $val2) {
                    foreach ($_store_view_result as $k3 => $val3) {
                        if($val3['group_id'] == $val2['store_id']){
                            $_web_view_result[$k1]['stores'][$k2]['store_views'][] = $val3;
                        }
                    }
                    $_web_view_result[$k1]['stores'][$k2]['count'] = count($_web_view_result[$k1]['stores'][$k2]['store_views']);
                    $count[$k1] += $_web_view_result[$k1]['stores'][$k2]['count'];
                }
                $_web_view_result[$k1]['count'] = $count[$k1];
            }
        }
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => $_web_view_result,
            ),
            'json'
        );
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