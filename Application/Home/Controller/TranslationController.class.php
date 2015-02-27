<?php
namespace Home\Controller;
use Think\Controller;
class TranslationController extends BaseController {
    //index
    public function index(){
        //Initialization then del
        // $translation_model = M('translation');
        // $where['de'] = array('neq','');
        // $where['en'] = array('neq','');
        // $where['nl'] = array('neq','');
        // $where['website_id'] = session('website_id');
        // $ids = $translation_model->where($where)->field('id')->select();
        // $save['modify'] = '0';
        // foreach ($ids as $val) {
        //     # code...
        //     $save['id'] = $val['id'];
        //     $translation_model->save($save);
        // }
        //Initialization then del
        $this->display();
    }

    public function export(){
        $translation_model = D('translation');
        $_params = json_decode(file_get_contents("php://input"),true);

        if($_params['exrender'] == '0'){
            // $title = explode(",", $field);
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
            foreach ($export_get['list'] as $key => $value) {
                # code...
                foreach ($value as $k => $val) {
                    # code...
                    $export[$key][$k] = '"'.str_replace('"','""',$val).'"';
                }
            }
            // S('title',$title);
            S('export', $export);
            S('filename', $_params['field']);
            echo '1';
        }
        if($_params['exrender'] == '1'){
        $data = $translation_model->find();
        foreach ($data as $k => $val) {
            # code...
            if($k!='id'&&$k!='remarks'&&$k!='status'&&$k!='en'&$k!='website_id'&&$k!='modify'&&$k!='fr'){
                $allField[] = $k;
            }
        }
        echo json_encode($allField);
        }
    }

    public function download(){
       exportexcel(S('export'),S('filename').time());
       S('export',null);
       // S('title',null);
       S('filename',null);
        // $test = C('URL_ROUTE_RULES');
        // foreach ($test as $k => $val) {
        //     # code...
        //     $ryue[$k][$val['0']] = $val['1'];
        // }
        // // S('urlall',json_encode($ryue));
        // var_dump(json_encode($ryue));
        // var_dump($ryue);
        // // S('urlall',null);
    }

    public function import(){
        $translation_model = D('translation');
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
                    # code...
                    if($k == '0'){
                        continue;
                    }
                    foreach ($lang_arr['0'] as $key => $value) {
                        # code...
                        $lang_add[strtolower($value)] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
                    }
                    if($lang_add['en']!=''&&$lang_add['de']!=''&&$lang_add['nl']!=''){
                        $lang_add['modify'] = '0';
                    }
                    $lang_add['website_id'] = session('website_id');
                    $import['en'] = $lang_add['en'];
                    $import['website_id'] = session('website_id');
                    $res = $translation_model->where($import)->find();
                    if($res){
                        $lang_save = $lang_add;
                        $lang_save['id'] = $res['id'];
                        $lang_save['status'] = '1';
                        $translation_model->save($lang_save);
                        $modify = $translation_model->where(array('id'=>$res['id']))->find();
                        if($modify['en']!=''&&$modify['ne']!=''&&$modify['nl']!=''){
                            $lang_modify['id'] = $res['id'];
                            $lang_modify['modify'] = '0';
                            $translation_model->setTranslate($lang_modify);
                        }
                    }else{
                        $translation_model->addTranslate($lang_add);
                    }
                }
            }
            echo '1';
        }
    }
    //lang add
    public function add(){
        $Model = new \Think\Model();
        $translation_model = D('translation');
        // binary id
        $images_model = D('translation_image');
        $_params = json_decode(file_get_contents("php://input"),true);
        // $repeat_where['en'] = $_params['en'];
        // $repeat_where['website_id'] = session('website_id');
        $repeat_lang = $Model->query("select * from __PREFIX__translation where binary en='".$_params['en']."' and website_id='".session('website_id')."' ");
        // var_dump($repeat_lang);
        // $repeat_lang = $translation_model->where($repeat_where)->find();
        // echo $translation_model->getLastSql();
        if($repeat_lang['0']['status'] == '1'){
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
                    $res = $translation_model->setTranslate($trans_data);
                    $id = $repeat_lang['id'];
                }else{
                    $trans_data['website_id'] = session('website_id');
                    $id=$translation_model->addTranslate($trans_data);
                }
                $images_model->saveImage($id);
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
    //lang list del
    public function del(){
        $translation_model = D('translation');
        $images_model = D('translation_image');
        $_params = json_decode(file_get_contents("php://input"),true);
        $res = $translation_model->delTranslate($_params['id']);
        $images_model->delImages($_params['id']);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang lists
    public function getList(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if ($_params['inrender'] == '0') {
            $_list = D('translation')->gets(
                        '', 
                        array(
                            'statue' => 1,
                            'website_id' => session('website_id'),
                            'modify' => 1,
                        ),
                        'id desc',
                        array()
                    );
        } else {
            $_list = D('translation')->gets(
                        '',
                        array(
                            'en' => array('like', "%$_search%"),
                            'website_id' => session('website_id'),
                            'status' => 1,
                            'modify' => 0,
                        ),
                        'id desc',
                        array()
                    );
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
                    'list' => $_list,
                ),
            ),
            'json'
        );
    }
    //lang edit detail
    public function getInfo(){
        $translation_model = D('translation');
        $images_model = D('translation_image');
        $_params = json_decode(file_get_contents("php://input"),true);
        $translation_detail = $translation_model->getOneTranslate($_params['id']);
        $images = $images_model->getImages($_params['id']);
        $lang_detail['images'] = $images;
        $lang_detail['detail'] = $translation_detail;
        if($images||$translation_detail){
            echo json_encode($lang_detail);
        }else{
            echo '0';
        }
    }

    //edit lang info
    public function editInfo(){
        $translation_model = D('translation');
        $_params = json_decode(file_get_contents("php://input"),true);
        $edit_data['id'] = intval($_params['id']);
        $edit_data['en'] = $_params['en'];
        $edit_data['de'] = $_params['de'];
        $edit_data['nl'] = $_params['nl'];
        $edit_data['remarks'] = $_params['remarks'];
        $edit_data['modify'] = $_params['modify'];
        $res = $translation_model->setTranslate($edit_data);
        if($res){
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
    //lang add or edit del image
    public function imageDel(){
        $images_model = D('translation_image');
        $_params = json_decode(file_get_contents("php://input"),true);
        $res = $images_model->delImage($_params['imageId']);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //before new lang clear iamges
    public function imageClear(){
        $images_model = D('translation_image');
        $res = $images_model->clearImgaes('0');
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang add or edit add images
    public function imageAdd(){
        $images_model = D('translation_image');
        $config = array(
            'maxSize' => 3145728,
            'rootPath' => './Uploads/',
            'savePath' => '',
            'saveName' => array('uniqid','mm_'),
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub' => true,
            'subName' => 'Translation',
        );
        $upload = new \Think\Upload($config );
        $info = $upload->uploadOne($_FILES['images']);
        if(!$info){
            die();
        }else{
            $images_model->addImage($_GET['lang_id'],$info['savename']);
            $image['image_name'] = $info['savename'];
            $image['id'] = $id;
            echo json_encode($image);
        }
    }

}