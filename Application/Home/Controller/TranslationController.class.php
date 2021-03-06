<?php
namespace Home\Controller;
// use Think\Controller;
class TranslationController extends PermissionController {
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
                    $_base_language_flag = false;
                    foreach ($lang_arr['0'] as $key => $value) {
                        if(strtolower($value) == 'en_us'){
                            $_base_language_flag = true;
                            $_lang_list[$k]['0'] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                        }else{
                            $_lang_list[$k]['other'][$value] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                        }
                    }
                }
                //增加至数据表
                if($_base_language_flag === true){
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
                                D('base_translate')->saveBase(array('id' =>$_repeat_lang_id, 'status' => 1));
                                foreach ($value['other'] as $k => $val) {
                                    # code...
                                    $_language = D('language')->where(array('simple_name' => trim($k)))->find();
                                    $_repeat_other = D('other_translate')->get(array('base_id' => $_repeat_lang_id, 'lang_id' => $_language['id']));
                                    // if($_repeat_other['id'] > 0){
                                    //     //覆盖已有的其他语言
                                    $_other_save['content'] = $val;
                                    $_other_save['id'] = $_repeat_other['id'];
                                    D('other_translate')->saveOther($_other_save);
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
                }else{
                    $this->ajaxReturn(
                            array(
                                    'success' => false,
                                    'message' => 'Import Failure.',
                                    'data' => array()
                                ),
                            'json'
                        );
                }
            }
            echo true;
        }
    }

    public function add(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_website_lang = D('website_lang')->gets(array('website_id' => session('website_id')));
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
                $_base_save['id'] = $_repeat_lang_info['id'];
                $_base_save['status'] = 1;
                if(!empty($_params['remarks'])){
                    $_base_save['remarks'] = $_params['remarks'];
                }
                $_base_save['modify'] = $_params['modify'];
                $_result = D('base_translate')->saveBase($_base_save);
                $_images = D('translation_image')->gets(array('lang_id' => '0', 'status' => 1, 'user_id' => session('id')));
                foreach ($_images as $val) {
                    D('translation_image')->save(array('id' =>$val['id'] , 'lang_id' => $_repeat_lang_info['id']));
                }
                foreach ($_website_lang as $val) {
                    $_other_where['base_id'] = $_repeat_lang_info['id'];
                    $_other_where['lang_id'] = $val['lang_id'];
                    $_other_id = D('other_translate')->where($_other_where)->find();
                    if(!empty($_params[strtolower($val['simple_name'])])){
                        $_other_save['content'] = $_params[strtolower($val['simple_name'])];
                        $_other_save['id'] = $_other_id['id'];
                        D('other_translate')->saveOther($_other_save);
                    }
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
        }else{
            $_base_add['modify'] = $_params['modify'];
            $_base_add['content'] = $_params['en_us'];
            $_base_add['website_id'] = session('website_id');
            $_base_add['remarks'] = $_params['remarks'];
            $_base_result = D('base_translate')->createBase($_base_add);
            if($_base_result > 0){
                $_images = D('translation_image')->where(array('lang_id' => '0', 'status' => 1, 'user_id' => session('id')))->select();
                foreach ($_images as $val) {
                    D('translation_image')->save(array('id' =>$val['id'] , 'lang_id' => $_base_result));
                }
                foreach ($_website_lang as $val) {
                    $_other_add['content'] = $_params[strtolower($val['simple_name'])];
                    $_other_add['base_id'] = $_base_result;
                    $_other_add['lang_id'] = $val['lang_id'];
                    D('other_translate')->add($_other_add);
                }
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
                            'message' => $_base_result,
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
        $_result = D('base_translate')->saveBase($_save);
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
            D('base_translate')->saveBase(array('id' => $val, 'status' => 0));
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
            D('base_translate')->saveBase(array('id' => $val, 'modify' => $_modify));
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
        $_language_list = D('website_lang')->gets(array('website_id' => session('website_id'), 'status' => 1));
        
        if($_params['language'] === false){
            //默认显示语言
            $_lang_id = $_language_list['0']['lang_id'];
        }else{
            $_lang_id = $_params['language'];
        }
        // session('lang_id', $_lang_id);
        if($_params['record'] === 0){
            //已完成
            $Model = M('base_translate')->where(
                    array('status' => 1, 'modify' => 0, 'website_id' => session('website_id'), 'rs_base_translate.content' => array('like', '%'.$_params['search'].'%'))
                );
        }elseif($_params['record'] === 1){
            //未完成
            $Model = M('base_translate')->where(
                    array('status' => 1, 'modify' => 1, 'website_id' => session('website_id'), 'rs_base_translate.content' => array('like', '%'.$_params['search'].'%'))
                );
        }else{
            //全部
            $Model = M('base_translate')->where(
                    array('status' => 1, 'website_id' => session('website_id'), 'rs_base_translate.content' => array('like', '%'.$_params['search'].'%'))
                );
        }
        $_translate_list = $Model->join('rs_other_translate ON rs_other_translate.base_id = rs_base_translate.id AND lang_id='.$_lang_id)->field('rs_base_translate.id,rs_base_translate.content,rs_other_translate.content as other_content, rs_other_translate.id as other_id, rs_other_translate.lang_id')->select();
        foreach ($_translate_list as $k => $val) {
            $_translate_list[$k]['content'] = htmlentities($val['content']);
            $_translate_list[$k]['other_content'] = htmlentities($val['other_content']);
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

    public function load(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $base_info = D('base_translate')->get($_params['base_id']);
        $base_info['content'] = htmlentities($base_info['content']);
        $other_info = D('other_translate')->get(array('id' => $_params['other_id']));
        $other_info['content'] = htmlentities($other_info['content']);
        $images = D('translation_image')->gets(array('lang_id' => $_params['base_id'], 'status' => 1));
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
        $_base_repeat = D('base_translate')->gets(array('content' => $_params['en_us'], 'website_id' => session('website_id')));
        $_base = D('base_translate')->get($_params['base_id']);
        if(strcmp($_params['en_us'],$_base['content']) !== 0){
            $_repeat_lang =false;
            foreach ($_base_repeat as $val) {
                # code...
                if(strcmp($_params['en_us'],$val['content']) === 0){
                    $_repeat_lang = true;
                    $_repeat_lang_info = $val;
                }
            }
            if($_repeat_lang === true){
                $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => 'The data already exists.',
                            'data' => array(),
                        ),
                        'json'
                    );
                return;
            }
        }
        $_base_result = D('base_translate')->saveBase(
            array('id' => $_params['base_id'], 
                'content' => $_params['en_us'], 
                'remarks' => $_params['remarks'], 
                'modify' => $_params['modify'])
            );
        // if($_base_result === true){
            if($_params['other_id'] != -1){
                $_other = D('other_translate')->get(array('id' => $_params['other_id']));
                $_other_result = D('other_translate')->saveOther(array('id' => $_params['other_id'], 'content' => $_params[strtolower($_other['simple_name'])]));
                // $this->ajaxReturn(
                //         array(
                //             'success' => true,
                //             'message' => '',
                //             'data' => array(),
                //         ),
                //         'json'
                //     );
            }
            if($_base_result === true || $_other_result === true){
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
                            'message' => 'Modify Failure',
                            'data' => array(),
                        ),
                        'json'
                    );
            }
    }
}