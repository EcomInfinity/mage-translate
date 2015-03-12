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
        if($_params['exrender'] === false){
            $export_get = D('translation')->gets(
                            'en,' . $_params['field'],
                            array(
                                'status' => 1,
                                'website_id' => session('website_id'),
                                $_params['field'] => array('neq', ''),
                            ),
                            'en asc',
                            array()
                        );
            foreach ($export_get as $key => $value) {
                foreach ($value as $k => $val) {
                    $export[$key][$k] = '"'.str_replace('"','""',$val).'"';
                }
            }
            S('export', $export);
            S('filename', $_params['field']);
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
            );
        }
        if($_params['exrender'] === true){
            $data = D('translation')->find();
            foreach ($data as $k => $val) {
                if($k!='id'&&$k!='remarks'&&$k!='status'&&$k!='en'&$k!='website_id'&&$k!='modify'&&$k!='fr'){
                    $allField[] = $k;
                }
            }
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => $allField,
                    ),
                    'json'
            );
        }
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
                $_repeat_lang =false;
                foreach ($_lang_list as $key => $value) {
                    # code...
                    $_repeat_base_list = D('base_translate')->where(array('content' => $value['0']))->select();
                    //验证是否有重复
                    foreach ($_repeat_base_list as $val) {
                        # code...
                        if($value['0'] === $val['content']){
                            $_repeat_lang = true;
                            $_repeat_lang_id = $val['id'];
                        }
                    }
                    if($_repeat_lang === true){
                        //base content重复
                        foreach ($value['other'] as $k => $val) {
                            # code...
                            $_language = D('language')->where(array('simple_name' => trim($k)))->find();
                            $_repeat_other = D('other_translate')->where(array('base_id' => $_repeat_lang_id, 'lang_id' => $_language['id']))->find();
                            if($_repeat_other['id'] > 0){
                                //覆盖已有的其他语言
                                $_other_save['content'] = $val;
                                $_other_save['id'] = $_repeat_other['id'];
                                D('other_translate')->save($_other_save);
                            }else{
                                //创建没有的其他语言
                                $_other_add['lang_id'] = $_language['id'];
                                $_other_add['content'] = $val;
                                $_other_add['base_id'] = $_repeat_lang_id;
                                D('other_translate')->add($_other_add);
                            }
                        }
                    }else{
                        $_base_add['content'] = $value['0'];
                        $_base_add['website_id'] = session('website_id');
                        $_base_id = D('base_translate')->add($_base_add);
                        foreach ($value['other'] as $k => $val) {
                            # code...
                            $_language = D('language')->where(array('simple_name' => trim($k)))->find();
                            $_other_add['lang_id'] = $_language['id'];
                            $_other_add['content'] = $val;
                            $_other_add['base_id'] = $_base_id;
                            D('other_translate')->add($_other_add);
                        }
                    }
                }
            }
            echo true;
        }
    }

    public function add(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_translation = D('translation')->gets('',array('en' => $_params['en'],'website_id' => session('website_id')));
        foreach ($_translation as $val) {
            if(strcmp($_params['en'],$val['en']) === 0 && $val['status'] == 1){
                $repeat_lang = true;
            }elseif(strcmp($_params['en'],$val['en']) === 0){
                $repeat_lang = $val;
            }
        }
        if($repeat_lang === true){
                $this->ajaxReturn(
                        array(
                            'success' => false,
                            'message' => 'The data already exists.',
                            'data' => array(),
                        ),
                        'json'
                    );
        }else{
            if($_params['en']!=null||$_params['de']!=null||$_params['nl']!=null||$_params['fr']!=null||$_params['remarks']!=null){
                $trans_data['en'] = $_params['en'];
                $trans_data['de'] = $_params['de'];
                $trans_data['nl'] = $_params['nl'];
                $trans_data['fr'] = $_params['fr'];
                if($_params['en']!=null&&$_params['de']!=null&&$_params['nl']!=null){
                    $trans_data['modify'] = '0';
                }
                $trans_data['remarks'] = $_params['remarks'];
                if($repeat_lang['status'] == '0'){
                    $trans_data['status'] = '1';
                    $trans_data['id'] = $repeat_lang['id'];
                    $_result = D('translation')->setTranslate($trans_data);
                    $id = $repeat_lang['id'];
                }else{
                    $trans_data['website_id'] = session('website_id');
                    $id=D('translation')->addTranslate($trans_data);
                }
                D('translation_image')->saveImage($id);
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
                            'message' => 'Can not all be empty.',
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
        $_lang_ids = $_params['ids'];
        foreach ($_lang_ids as $val) {
            D('translation')->del($val);
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
            D('translation')->setModify($val);
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
            $_translate_list = D('base_translate')->where(array('status' => 1, 'modify' => 0, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->order('id desc')->select();
        }elseif($_params['record'] === 1){
            $_translate_list = D('base_translate')->where(array('status' => 1, 'modify' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->order('id desc')->select();
        }else{
            $_translate_list = D('base_translate')->where(array('status' => 1, 'website_id' => session('website_id'), 'content' => array('like', '%'.$_params['search'].'%')))->order('id desc')->select();
        }
        $_language_list = D('website_lang')->where(array('website_id' => session('website_id')))->relation(true)->select();
        if($_params['language'] === false){
            $_lang_id = $_language_list['0']['lang_id'];
        }else{
            $_lang_id = $_params['language'];
        }
        foreach ($_translate_list as $k => $val) {
            # code...
            $_translate_list[$k]['other'] = D('other_translate')->where(array('base_id' => $val['id'], 'lang_id' => $_lang_id))->relation(true)->select();
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
        // $translation_detail = D('translation')->get($_params['id']);
        // $images = D('translation_image')->gets($_params['id']);
        // if($images||$translation_detail){
        //     $this->ajaxReturn(
        //             array(
        //                 'success' => true,
        //                 'message' => '',
        //                 'data' => array(
        //                     'images'=>$images,
        //                     'detail'=>$translation_detail,
        //                 ),
        //             ),
        //             'json'
        //         );
        // }else{
        //     $this->ajaxReturn(
        //             array(
        //                 'success' => false,
        //                 'message' => '',
        //                 'data' => array(),
        //             ),
        //             'json'
        //         );
        // }
    }

    public function edit(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $edit_data['id'] = intval($_params['id']);
        $edit_data['en'] = $_params['en'];
        $edit_data['de'] = $_params['de'];
        $edit_data['nl'] = $_params['nl'];
        $edit_data['remarks'] = $_params['remarks'];
        $edit_data['modify'] = $_params['modify'];
        $_result = D('translation')->setTranslate($edit_data);
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
                        'message' => 'Modify failure.',
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

}