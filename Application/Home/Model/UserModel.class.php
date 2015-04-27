<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
    private function _login($_username, $_uid) {
        session('username' , $_username);
        if (isset($_uid) === true) {
            session('id', $_uid);
        }
        // cookie('admin', session('id'), 10);
    }

    public function login($_username, $_password) {
        // username and password correct
        $_where = array(
            'username' => $_username,
            'password' => md5($_password),
        );
        $_result = $this -> validate($rules)
                         -> where($_where)
                         -> find();

        if (isset($_result) === true && $_result['allow'] == '1') {
            $this->_login($_username, $_result['id']);
            return $_result['id'];
        } else {
            return false;
        }
     }

    public function logout(){
        session('id', null);
        session('username', null);
        session('website_id', null);
        session('website_name', null);
        session('purview', null);
    }

    public function isExisted($_username) {
        $res = $this->where(array('username'=>$_username))->find();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function validateUsername($_username) {
        return filter_var($_username, FILTER_VALIDATE_EMAIL);
    }

    public function register($_params) {
        $_username = $_params['username'];
        $_password = $_params['password'];
        $_repeat_password = $_params['repeat-password'];

        if (isset($_username) && isset($_password)) {
            if ($_password != $_repeat_password) {
                return 'Password doesn\'t match.';
            }

            if ($this->isExisted($_username) === true) {
                return 'User already registered.';
            }

            if (preg_match('/^.{6,30}$/', $_password) == 0) {
                return 'The password must have 6-30 characters.';
            }

            if ($this->validateUsername($_username) === false) {
                return 'Email address is not correct.';
            }

            $_user_id = $this->add(
                array(
                    'username' => $_username,
                    'password' => md5($_password)
                )
            );

            $this->_login($_username, $_user_id);

            return intval($_user_id);
        }
    }

    public function addUser($_username, $_password){
        if (isset($_username) && isset($_password)) {
            if ($this->isExisted($_username) === true) {
                return 'User already registered.';
            }

            if (preg_match('/^.{6,30}$/', $_password) == 0) {
                return 'The password must have 6-30 characters.';
            }

            if (! filter_var($_username, FILTER_VALIDATE_EMAIL)) {
                return 'Email address is not correct.';
            }

            $_user_id = $this->add(
                array(
                    'username' => $_username,
                    'password' => md5($_password)
                )
            );

            return intval($_user_id);
        } else {
            return 'Username or Password cannot be empty.';
        }
    }

    public function gets($_where){
        return $this->where($_where)->select();
    }

    public function get($_user_id) {
        return $this->where(array('id'=>intval($_user_id)))->find();
    }

    public function setUsername($_username, $_user_id){
        if ($this->isExisted($_username) === true) {
            return 'User already registered.';
        }

        if ($this->validateUsername($_username) === true) {
            return 'Email address is not correct.';
        }

        $this->save(array(
            'id' => intval($_user_id),
            'username' => $_username,
        ));

        return true;
    }

    public function setPassword($_password, $_user_id) {
        if (preg_match('/^.{5,30}.*[^ ].*$/', $_password) == 0) {
            return 'The password must have 6-30 characters.';
        }
        $_result = $this->save(array(
            'id' => intval($_user_id),
            'password' => md5($_password),
        ));
        if($_result > 0){
            return true;
        }else{
            return 'Modify Failure.';
        }
    }

    public function enable($_user_id) {
        return $this->save(array(
            'id' => $_user_id,
            'allow' => '1'
        ));
    }

    public function disable($_user_id) {
        return $this->save(array(
            'id' => $_user_id,
            'allow' => '0'
        ));
    }
}
?>