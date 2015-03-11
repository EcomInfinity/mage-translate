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
                foreach ($_lang_list as $key => $value) {
                    # code...
                    $_repeat_list = M('base_translate')->where(array('content' => $value['0']))->select();
                    $_base_add['content'] = $value['0'];
                    $_base_add['website_id'] = session('website_id');
                    $_base_id = M('base_translate')->add($_base_add);
                    foreach ($value['other'] as $k => $val) {
                        # code...
                        $_language = M('language')->where(array('simple_name' => trim($k)))->find();
                        $_other_add['lang_id'] = $_language['id'];
                        $_other_add['content'] = $val;
                        $_other_add['base_id'] = $_base_id;
                        M('other_translate')->add($_other_add);
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
        $_result = D('translation')->del($_params['id']);
        D('translation_image')->del(
                array(
                        'lang_id' => intval($_params['id'])
                    )
            );
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
        $_search = D('translation')->gets(
                        '',
                        array(
                                'status' => 1,
                                'website_id' => session('website_id'),
                                'en' => array('like', '%'.$_params['search'].'%')
                            ),
                        'id desc',
                        array()
                    );
        $_no_modify = D('translation')->gets(
                        '',
                        array(
                                'status' => 1,
                                'website_id' => session('website_id'),
                                'en' =>array('like', '%'.$_params['search'].'%'),
                                'modify' => 0
                            ),
                        'id desc',
                        array()
                    );
        $_need_modify = D('translation')->gets(
                        '',
                        array(
                                'status' => 1,
                                'website_id' => session('website_id'),
                                'en' =>array('like', '%'.$_params['search'].'%'),
                                'modify' => 1
                            ),
                        'id desc',
                        array()
                    );
        if($_params['modify'] === 1){
            $_list = $_need_modify;
            $_show = 1;
        }elseif($_params['modify'] === 0){
            $_list = $_no_modify;
            $_show = 2;
        }else{
            $_list = $_search;
            $_show = 3;
        }
        // if ($_params['inrender'] === false) {
        //     $_list = D('translation')->gets(
        //                 '',
        //                 array(
        //                     'status' => 1,
        //                     'website_id' => session('website_id'),
        //                     'modify' => 1,
        //                 ),
        //                 'id desc',
        //                 array()
        //             );
        // } else {
        //     if($_params['complete'] === true){
        //         $_list = D('translation')->gets(
        //                     '',
        //                     array(
        //                         'en' => array('like', '%'.$_params['search'].'%'),
        //                         'website_id' => session('website_id'),
        //                         'status' => 1,
        //                         'modify' => 0,
        //                     ),
        //                     'id desc',
        //                     array()
        //                 );
        //     }else{
        //         $_list = D('translation')->gets(
        //                     '',
        //                     array(
        //                         'en' => array('like', '%'.$_params['search'].'%'),
        //                         'website_id' => session('website_id'),
        //                         'status' => 1,
        //                     ),
        //                     'id desc',
        //                     array()
        //                 );
        //     }
        // }

        foreach ($_search as $key => $value) {
            foreach ($value as $k => $val) {
                $_search_list[$key][$k] = htmlentities($val);
            }
        }
        $search_list['show'] = -1;
        $search_list['list'] = $_search_list; 

        foreach ($_no_modify as $key => $value) {
            foreach ($value as $k => $val) {
                $_no_modify_list[$key][$k] = htmlentities($val);
            }
        }
        $no_modify_list['show'] = 0;
        $no_modify_list['list'] = $_no_modify_list;

        foreach ($_need_modify as $key => $value) {
            foreach ($value as $k => $val) {
                $_need_modify_list[$key][$k] = htmlentities($val);
            }
        }
        $need_modify_list['show'] = 1;
        $need_modify_list['list'] = $_need_modify_list;

        $translation_list['search'] = $search_list;
        $translation_list['no_modify'] = $no_modify_list;
        $translation_list['need_modify'] = $need_modify_list;
        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                    'total' => count($_search),
                    'need_modify' => count($_need_modify),
                    'no_modify' => count($_no_modify),
                    'list' => $translation_list,
                    'show' => $_show
                ),
            ),
            'json'
        );
    }

    public function get(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $translation_detail = D('translation')->get($_params['id']);
        $images = D('translation_image')->gets($_params['id']);
        if($images||$translation_detail){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(
                            'images'=>$images,
                            'detail'=>$translation_detail,
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