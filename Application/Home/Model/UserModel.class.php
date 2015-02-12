<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
    private function _login($uid) {
        return $uid;
    }

    public function login($_username, $_password) {
        // username and password correct
        $where['username'] = $_username;
        $where['password'] = md5($_password);
        $res = $this->validate($rules)->where($where)->find();
        if($res&&$res['allow'] == '1'){
            return $this->_login($res['id']);
        }else{
            E('Login fail.');
        }
     }

    public function logout(){
        session('id', null);
        session('uid',null);
        session('username', null);
        session('website_id', null);
        session('website_name', null);
        session('purview', null);
    }

    public function isExisted($_username) {
        // 
        $res = $this->where(array('username'=>$_username))->find();
        if($res){
            return false;
        }else{
            return ture;
        }
    }

    public function userMatch($_param){
        return preg_match('/^[a-zA-Z0-9]{5,15}$/',$_param);
    }

    public function register($_params) {
        $_username = $_params['username'];
        $_password = $_params['password'];
        $_repeat_password = $_params['repeat-password'];
        if($_username!=null&&$_password!=null){
            if($this->userMatch($_password) == '1'){
                if ($_password == $_repeat_password) {
                    $register['password'] = md5($_password);
                }else {
                    E('Password doesn\'t match');
                }
            }else{
                E('The password must have 5-15 digits or letters.');
            }
            if($this->userMatch($_username) == '1'){
                if ($this->isExisted($_username) == false) {
                    E('User already registered');
                }else{
                    $register['username'] = $_username;
                }
            }else{
                E('The username must have 5-15 digits or letters.');
            }
            // ...
            $uid = $this->add($register);
            // ...
            return $this->_login($uid);
        }
    }

    public function addUser($_username, $_password){
        if($_username!=null&&$_password!=null){
            if($this->userMatch($_username) == '1'){
                if ($this->isExisted($_username) == false) {
                    E('User already registered');
                }else{
                    $add['username'] = $_username;
                }
            }else{
                E('The username must have 5-15 digits or letters.');
            }
            if($this->userMatch($_password) == '1'){
                $add['password'] = md5($_password);
            }else{
                E('The password must have 5-15 digits or letters.');
            }
            $uid = $this->add($add);
        }
        return $uid;
    }

    public function getUserList($_ids){
        $where['id'] = array('in',$_ids);
        return $this->where($where)->select();
    }

    public function searchUser($_search,$_ids){
        $where['id'] = array('in',$_ids);
        $where['username'] = array('like','%'.$_search.'%');
        return $this->where($where)->select();
    }

    public function getOneUser($uid){
        return $this->where(array('id'=>intval($uid)))->find();
    }

    public function getUserName($uid){
        $user = $this->where(array('id'=>intval($uid)))->field('username')->find();
        return $user['username'];
    }

    public function setUsername($_username,$uid){
        if($this->userMatch($_username) == '1'){
            if ($this->isExisted($_username) == false) {
                return '0';
            }else{
                $save['id'] = intval($uid);
                $save['username'] = $_username;
                return $this->save($save);
            }
        }else{
            E('The username must have 5-15 digits or letters.');
        }
    }

    public function setPassword($_password,$uid){
        if($this->userMatch($_password) == '1'){
            $save['id'] = intval($uid);
            $save['password'] = md5($_password);
            return $this->save($save);
        }else{
            E('The password must have 5-15 digits or letters.');
        }
    }

    public function setAllow($uid,$_allow){
        $save['id'] = $uid;
        $save['allow'] = $_allow;
        return $this->save($save);
    }

}
?>