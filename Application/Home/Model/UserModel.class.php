<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
            // protected $_link = array(
            //     'translation_image' => self::HAS_ONE,
            //     'translation_image'=> array(  
            //     'mapping_type'=> self::HAS_ONE,
            //     'class_name'=>'translation_image',
            //     'foreign_key'=>'lang_id',
            //     'mapping_name'=>'translation_image',
            //     'mapping_fields'=>'image_name',
            //     ),

            // );

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
        session('username', null);
        session('website_id', null);
        session('website_name', null);
        session('purview', null);
    }

     public function getUserName($uid){
        $name = $this->where(array('id'=>intval($uid)))->field('username')->find();
        return $name['username'];
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

    public function register($_params) {
        $_username = $_params['username'];
        $_password = $_params['password'];
        $_repeat_password = $_params['repeat-password'];
        if($_username!=null&&$_password!=null){
            if ($_password == $_repeat_password) {
                $register['password'] = md5($_password);
            }else {
                throw new Expection('Password doesn\'t match');
            }

            if ($this->isExisted($_username) == false) {
                throw new Expection('User already registered');
            }else{
                $register['username'] = $_username;
            }

            // ...
            $uid = $this->add($register);
            // ...
            $this->_login($uid);
        }
        return $uid;
    }

    public function addUser($_params){
        $_username = $_params['username'];
        $_password = $_params['password'];
        if($_username!=null&&$_password!=null){
            if ($this->isExisted($_username) == false) {
                throw new Expection('User already registered');
            }else{
                $add['username'] = $_username;
            }
            $add['password'] = md5($_password);
            $uid = $this->add($add);
        }
        return $uid;
    }

    public function getUser($_params){
        return $this->where($_params)->select();
    }

    // public function setUser($_params){
    //     $save['id'] = intval($_params['id']);
    //     if ($this->isExisted($_params['username']) == false) {
    //         throw new Expection('User already registered');
    //     }else{
    //         $save['username'] = $_params['username'];
    //     }
    //    // if($_params['password']!=''){
    //         $save['password'] = md5($_params['password']);
    //     //}
    //     $num = $this->save($save);
    //     return $num;
    // }
    // public function setPassword($uid,$_password){
    //     $save['id'] = $uid;
    //     $save['password'] = $_password;
    //     $this->save($save);
    // }

}
?>