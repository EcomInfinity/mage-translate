<?php
namespace Home\Controller;
use Think\Controller;

class RoleController extends Controller {

    public function add() {
        $_params = json_decode(file_get_contents("php://input"), true);

        $_rules = D('rule')->getRuleList();
        foreach ($_rules as $_key => $_rule) {
            $purview = $purview.$_params[strtolower($val['rule_name'])];
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

    public function gets() {
        $_params = json_decode(file_get_contents("php://input"),true);

        if (isset($_params) && isset($_params['search'])) {
            $_role_list = D('role')->searchRole($_params['search'],session('website_id'));
        } else {
            $_role_list = D('role')->getRoleList(session('website_id'));
        }
        
        if (! $_role_list) {
            $_role_list = array();
        }

        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $_role_list,
                ),
                'json'
            );
    }

    public function get() {
        $_role_model = D('role');
        $_rule_model = D('rule');
        $_params = json_decode(file_get_contents("php://input"),true);


        $_rule_count = $_rule_model->getRuleCount();
        $_rules = $_rule_model->getRuleList();

        $_role = $_role_model->getOneRole($_params['role_id']);

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
        $_rules = D('rule')->getRuleList();
        foreach ($_rules as $k => $val) {
            # code...
            $purview = $purview.$_params[strtolower($val['rule_name'])];
        }

        $_result = D('role')->setRole(array(
            'role_name' => $_params['role_name'],
            'id' => $_params['role_id'],
            'purview' => bindec($purview),
        ));

        if (is_string($_result) === false){
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