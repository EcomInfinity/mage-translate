<?php
namespace Home\Controller;
use Think\Controller;

class RoleController extends PermissionController {

    public function add() {
        $_params = json_decode(file_get_contents("php://input"), true);

        $_rules = D('rule')->gets();
        foreach ($_rules as $_key => $_rule) {
            $purview = $purview.$_params[strtolower($_rule['rule_name'])];
        }

        $purview = bindec($purview);

        $_params['role_name'] = $back['role'];
        $_params['purview'] = $purview;
        $_params['website_id'] = session('website_id');

        $id = D('role')->addRole(array(
            'role_name' => $_params['role'],
            'purview' => $purview,
            'website_id' => session('website_id')
        ));

        if (is_string($id) === false) {
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => '',
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

    public function gets() {
        $_params = json_decode(file_get_contents("php://input"),true);

        if (isset($_params) && isset($_params['search'])) {
            $_role_list = D('role')->gets(
                    array(
                            'website_id' => session('website_id'),
                            'role_name' => array('like', '%'.$_params['search'].'%')
                        )
                );
        } else {
            $_role_list = D('role')->gets(
                    array(
                            'website_id' => session('website_id')
                        )
                );
        }

        // if (! $_role_list) {
        //     $_role_list = array();
        // }

        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(
                            'roles' => $_role_list,
                            'total' => D('role')->total(session('website_id')),
                            'count' => count($_role_list)
                        ),
                ),
                'json'
            );
    }

    public function load() {
        $_params = json_decode(file_get_contents("php://input"),true);
        $_rule_count = D('rule')->total();
        $_rules = D('rule')->gets();
        $_role = D('role')->get($_params['role_id']);
        $purview = str_split(str_pad(decbin($_role['purview']),$_rule_count,'0',STR_PAD_LEFT));
        foreach ($_rules as $key => $value) {
            $_rules[$key]['purview'] = $purview[$key];
        }

        $_result = array(
            'role_name' => $_role['role_name'],
            'role_id' => $_role['id'],
            'rule' => $_rules,
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

    public function edit() {
        $_params = json_decode(file_get_contents("php://input"), true);
        $_rules = D('rule')->gets();
        foreach ($_rules as $k => $val) {
            # code...
            $purview = $purview.$_params[strtolower($val['rule_name'])];
        }

        $_result = D('role')->setRole(array(
            'role_name' => $_params['role_name'],
            'id' => $_params['role_id'],
            'purview' => bindec($purview),
        ));

        if ($_result > 0){
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
}