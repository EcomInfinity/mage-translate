<?php
namespace Home\Controller;
use Think\Controller;
class TranslationController extends TranslationPermissionController {
    //index
    public function index(){
        $this->display();
    }

    public function export(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_translate_list = D('base_translate')->gets(array('status' => 1, 'website_id' => session('website_id')), 'content asc');
        $_language = D('language')->find($_params['lang_id']);
        foreach ($_translate_list as $k => $val) {
            # code...
            $_other = D('other_translate')->get(array('base_id' => $val['id'], 'lang_id' => $_params['lang_id']));
            if(!empty($_other['content'])){
                $_list[$k]['en_us'] = '"'.str_replace('"','""',$val['content']).'"';
                $_list[$k][strtolower($_other['simple_name'])] = '"'.str_replace('"','""',$_other['content']).'"';
            }
        }
        S('export', $_list);
        S('filename', $_language['simple_name']);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
        );
    }

    public function download(){
       exportexcel(S('export'),S('filename').time());
       S('export',null);
       S('filename',null);
    }

    public function import(){
        $config = array(
            'maxSize' => 3145728,
            'rootPath' => './Uploads/',
            'savePath' => '',
            'saveName' => array('uniqid','csv_'),
            'exts' => array('csv'),
            'autoSub' => true,
            'subName' => 'csv',
        );
        $upload = new \Think\Upload($config );
        $info   =   $upload->uploadOne($_FILES['csv']);
        if(!$info){
            die();
        }else{
        $file_path = './Uploads/csv/'.$info['savename'];
            if(file_exists($file_path)){
                $handle = fopen($file_path,'r');
                while ($data = fgetcsv($handle)) {
                    $lang_arr[] = $data;
                }
                //整理csv数据
                foreach ($lang_arr as $k => $val) {
                    if($k == '0'){
                        continue;
                    }
                    foreach ($lang_arr['0'] as $key => $value) {
                        if($value == 'en_us'){
                            $_lang_list[$k]['0'] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                        }else{
                            $_lang_list[$k]['other'][$value] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                        }
                    }
                }
                //增加至数据表
                foreach ($_lang_list as $key => $value) {
                    # code...
                    if(!empty($value['0'])){
                        $_repeat_base_list = D('base_translate')->gets(array('content' => $value['0'], 'website_id' => session('website_id')));
                        //验证是否有重复
                        $_repeat_lang =false;
                        foreach ($_repeat_base_list as $val) {
                            # code...
                            if(strcmp($value['0'],$val['content']) === 0){
                                $_repeat_lang = true;
                                $_repeat_lang_id = $val['id'];
                            }
                        }
                        if($_repeat_lang === true){
                            //base content重复
                            D('base_translate')->save(array('id' =>$_repeat_lang_id, 'status' => 1));
                            foreach ($value['other'] as $k => $val) {
                                # code...
                                $_language = D('language')->where(array('simple_name' => trim($k)))->find();
                                $_repeat_other = D('other_translate')->get(array('base_id' => $_repeat_lang_id, 'lang_id' => $_language['id']));
                                // if($_repeat_other['id'] > 0){
                                //     //覆盖已有的其他语言
                                $_other_save['content'] = $val;
                                $_other_save['id'] = $_repeat_other['id'];
                                D('other_translate')->save($_other_save);
                                // }else{
                                //     //创建没有的其他语言
                                //     $_other_add['lang_id'] = $_language['id'];
                                //     $_other_add['content'] = $val;
                                //     $_other_add['base_id'] = $_repeat_lang_id;
                                //     D('other_translate')->add($_other_add);
                                // }
                            }
                        }else{
                            $_base_add['content'] = $value['0'];
                            $_base_add['website_id'] = session('website_id');
                            $_base_id = D('base_translate')->add($_base_add);
                            if($_base_id > 0){
                                $_website_lang = D('website_lang')->gets(array('website_id' => session('website_id')));
                                foreach ($_website_lang as $key => $val) {
                                    # code...
                                    $_other_add['lang_id'] = $val['lang_id'];
                                    $_other_add['content'] = $value['other'][strtolower($val['simple_name'])];
                                    $_other_add['base_id'] = $_base_id;
                                    D('other_translate')->add($_other_add);
                                }
                            }
                            // foreach ($value['other'] as $k => $val) {
                            //     # code...
                            //     $_language = D('language')->where(array('simple_name' => trim($k)))->find();
                            //     $_other_add['lang_id'] = $_language['id'];
                            //     $_other_add['content'] = $val;
                            //     $_other_add['base_id'] = $_base_id;
                            //     D('other_translate')->add($_other_add);
                            // }
                        }
                    }
                    // if($_base_id > 0){
                    //     $base_id = $_base_id;
                    // }else{
                    //     $base_id = $_repeat_lang_id;
                    // }
                    // $_website_lang_list = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
                    // foreach ($_website_lang_list as $k => $val) {
                    //     # code...
                    //     $_other = D('other_translate')->where(array('base_id' => $base_id, 'lang_id' => $val['lang_id']))->find();
                    //     if(empty($_other['content'])){
                    //         D('base_translate')->save(array('id' => $base_id,'modify' => 1));
                    //         break;
                    //     }
                    // }
                }
            }
            echo true;
        }
    }

    public function add(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_base = D('base_translate')->gets(array('content' => $_params['en_us'], 'website_id' => session('website_id')));
        $_repeat_lang =false;
        foreach ($_base as $val) {
            # code...
            if(strcmp($_params['en_us'],$val['content']) === 0){
                $_repeat_lang = true;
                $_repeat_lang_info = $val;
            }
        }
        //判断是否已经存在
        if($_repeat_lang === true){
            //判断是否已经删除
            if($_repeat_lang_info['status'] == 1){
                $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => 'The data already exists.',
                            'data' => array(),
                        ),
                        'json'
                    );
                return;
            }else{
                D('base_translate')->save(array('id' => $_repeat_lang_info['id'], 'status' => 1));
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
            if(preg_match('/.*[^ ].*/', $_params['en_us']) != 0){
                $_base_add['modify'] = $_params['modify'];
                $_base_add['content'] = $_params['en_us'];
                $_base_add['website_id'] = session('website_id');
                $_base_result = D('base_translate')->add($_base_add);
                $_images = D('translation_image')->where(array('lang_id' => '0', 'status' => 1))->select();
                foreach ($_images as $val) {
                    # code...
                    D('translation_image')->save(array('id' =>$val['id'] , 'lang_id' => $_base_result));
                }
                $_website_lang = D('website_lang')->gets(array('website_id' => session('website_id')));
                foreach ($_website_lang as $val) {
                    # code...
                    $_other_add['content'] = $_params[strtolower($val['simple_name'])];
                    $_other_add['base_id'] = $_base_result;
                    $_other_add['lang_id'] = $val['lang_id'];
                    D('other_translate')->add($_other_add);
                }
            }
            if($_base_result > 0){
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
                            'message' => 'Create Failure.',
                            'data' => array(),
                        ),
                        'json'
                    );
            }
        }
    }

    public function del(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_save['id'] = $_params['id'];
        $_save['status'] = 0;
        $_result = D('base_translate')->save($_save);
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

    public function dels(){
        $_params = json_decode(file_get_contents("php://input"),true);
        foreach ($_params['ids'] as $val) {
            D('base_translate')->save(array('id' => $val, 'status' => 0));
            D('translation_image')->del(
                    array(
                        'lang_id' => intval($val)
                        )
                );
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }

    public function needUpdate(){
        $_params = json_decode(file_get_contents("php://input"),true);
        foreach ($_params['ids'] as $val) {
            $_base = D('base_translate')->get($val);
            if($_base['modify'] == 0){
                $_modify = 1;
            }else{
                $_modify = 0;
            }
            D('base_translate')->save(array('id' => $val, 'modify' => $_modify));
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }

    public function gets(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if($_params['record'] === 0){
            //已完成
            $_translate_list = D('base_translate')->gets(array('status' => 1, 'modify' => 0, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')),'id desc');
        }elseif($_params['record'] === 1){
            //未完成
            $_translate_list = D('base_translate')->gets(array('status' => 1, 'modify' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')),'id desc');
        }else{
            //全部
            $_translate_list = D('base_translate')->gets(array('status' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')),'id desc');
        }
        $_language_list = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
        
        if($_params['language'] === false){
            //默认显示语言
            $_lang_id = $_language_list['0']['lang_id'];
        }else{
            $_lang_id = $_params['language'];
        }
        $_empty = false;
        foreach ($_translate_list as $k => $val) {
            # code...
            $_empty_other = D('other_translate')->gets(array('base_id' => $val['id']));
            foreach ($_empty_other as $value) {
                # code...
                if(empty($value['content'])){
                    $_empty = true;
                }
            }
            $_translate_list[$k]['other_empty'] = $_empty;
            $_translate_list[$k]['other'] = D('other_translate')->gets(array('base_id' => $val['id'], 'lang_id' => $_lang_id));
        }
        foreach ($_translate_list as $key => $value) {
            # code...
            $_translate_list[$key]['content'] = htmlentities($value['content']);
            foreach ($value['other'] as $k => $val) {
                # code...
                $_translate_list[$key]['other'][$k]['content'] = htmlentities($val['content']);
            }
        }
        $_search_count = D('base_translate')->where(array('status' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->count();
        $_need_modify_count = D('base_translate')->where(array('status' => 1, 'modify' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->count();
        $_no_modify_count = D('base_translate')->where(array('status' => 1, 'modify' => 0, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->count();
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                            'total' => $_search_count,
                            'list' => $_translate_list,
                            'langs' => $_language_list,
                            'need_modify' => $_need_modify_count,
                            'no_modify' => $_no_modify_count,
                        ),
            ),
            'json'
        );
    }

    public function get(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $base_info = D('base_translate')->get($_params['base_id']);
        $other_info = D('other_translate')->get(array('id' => $_params['other_id']));
        $images = D('translation_image')->gets($_params['base_id']);
        if($base_info){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(
                            'images' => $images,
                            'base' => $base_info,
                            'other' => $other_info
                        ),
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

    public function edit(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_base_result = D('base_translate')->save(array('id' => $_params['base_id'], 'content' => $_params['en_us'], 'remarks' => $_params['remarks'], 'modify' => $_params['modify']));
        if($_params['other_id'] != -1){
            $_other = D('other_translate')->get(array('id' => $_params['other_id']));
            $_other_result = D('other_translate')->save(array('id' => $_params['other_id'], 'content' => $_params[strtolower($_other['simple_name'])]));
        }
        if($_base_result || $_other_result){
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
}