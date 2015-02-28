<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
    private function _login($_username, $_uid) {
        session('username' , $_username);
        if (isset($_uid) === true) {
            session('id', $_uid);
        }
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

    public function userMatch($_param){
        return preg_match('/^.{6,15}$/',$_param);
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

            if (preg_match('/^.{6,15}$/', $_password) == 0) {
                return 'The password must have 5-15 digits or letters.';
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

            $this->_login($_username, $_user_id);

            return intval($_user_id);
        }
    }

    public function addUser($_username, $_password){
        if (isset($_username) && isset($_password)) {
            if ($this->isExisted($_username) === true) {
                return 'User already registered.';
            }

            if (preg_match('/^.{6,15}$/', $_password) == 0) {
                return 'The password must have 6-15 digits or letters.';
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

    public function get($uid){
        return $this->where(array('id'=>intval($uid)))->find();
    }

    public function setUsername($_username,$uid){
        if ($this->isExisted($_username) === true) {
            return 'User already registered.';
        }else{
            $save['id'] = intval($uid);
            $save['username'] = $_username;
            return $this->save($save);
        }
    }

    public function setPassword($_password,$uid){
        if($this->userMatch($_password) == '1'){
            $save['id'] = intval($uid);
            $save['password'] = md5($_password);
            return $this->save($save);
        }else{
            return 'The password must have 6-15 characters.';
        }
    }

    public function setAllow($uid,$_allow){
        $save['id'] = $uid;
        $save['allow'] = $_allow;
        return $this->save($save);
    }

}
?>