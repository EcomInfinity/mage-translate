<?php
namespace Home\Controller;
use Think\Controller;
class TranslationController extends BaseController {
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
                foreach ($lang_arr as $k => $val) {
                    if($k == '0'){
                        continue;
                    }
                    foreach ($lang_arr['0'] as $key => $value) {
                        $lang_add[strtolower($value)] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                    }
                    if($lang_add['en']!=''&&$lang_add['de']!=''&&$lang_add['nl']!=''){
                        $lang_add['modify'] = '0';
                    }
                    $lang_add['website_id'] = session('website_id');
                    $_import['en'] = $lang_add['en'];
                    $_import['website_id'] = session('website_id');
                    $_result = D('translation')->where($_import)->select();
                    $_repeat_lang = false;
                    foreach ($_result as $val) {
                        if(strcmp($_import['en'],$val['en']) === 0){
                            $_repeat_lang = true;
                            $_repeat_id = $val['id'];
                        }
                    }
                    if($_repeat_lang === true){
                        $lang_save = $lang_add;
                        $lang_save['id'] = $_repeat_id;
                        $lang_save['status'] = '1';
                        D('translation')->save($lang_save);
                        $modify = D('translation')->where(array('id'=>$_result['id']))->find();
                        if($modify['en']!=''&&$modify['ne']!=''&&$modify['nl']!=''){
                            $lang_modify['id'] = $_result['id'];
                            $lang_modify['modify'] = '0';
                            D('translation')->setTranslate($lang_modify);
                        }
                    }else{
                        D('translation')->addTranslate($lang_add);
                    }
                }
            }
            echo true;
        }
    }

    public function add(){
        $Model = new \Think\Model();
        $_params = json_decode(file_get_contents("php://input"),true);
        $_translation = M('translation')->where(array('en' => $_params['en'],'website_id' => session('website_id')))->select();
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

    public function gets(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if ($_params['inrender'] === false) {
            $_list = D('translation')->gets(
                        '', 
                        array(
                            'status' => 1,
                            'website_id' => session('website_id'),
                            'modify' => 1,
                        ),
                        'id desc',
                        array()
                    );
        } else {
            if($_params['complete'] === true){
                $_list = D('translation')->gets(
                            '',
                            array(
                                'en' => array('like', '%'.$_params['search'].'%'),
                                'website_id' => session('website_id'),
                                'status' => 1,
                                'modify' => 0,
                            ),
                            'id desc',
                            array()
                        );
            }else{
                $_list = D('translation')->gets(
                            '',
                            array(
                                'en' => array('like', '%'.$_params['search'].'%'),
                                'website_id' => session('website_id'),
                                'status' => 1,
                            ),
                            'id desc',
                            array()
                        );
            }
        }

        foreach ($_list as $key => $value) {
            foreach ($value as $k => $val) {
                $translation_list[$key][$k] = htmlentities($val);
            }
        }

        $this->ajaxReturn(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                    'total' => D('translation')->total(session('website_id')),
                    'count' => count($_list),
                    'list' => $translation_list,
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