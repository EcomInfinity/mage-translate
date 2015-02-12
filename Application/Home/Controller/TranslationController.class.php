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
        $back = json_decode(file_get_contents("php://input"),true);
        $fields = $back['field'];
        if($back['exrender'] == '0'){
            $field = 'en,'.$fields;
            $title = explode(",", $field);
            $export_get = $translation_model->getTranslateList($field,'1',session('website_id'),'','id');
            foreach ($export_get['list'] as $key => $value) {
                # code...
                foreach ($value as $k => $val) {
                    # code...
                    $export[$key][$k] = '"'.str_replace('"','""',$val).'"';
                }
            }
            // S('title',$title);
            S('export',$export);
            S('filename',$fields);
            echo '1';
        }
        if($back['exrender'] == '1'){
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
        $translation_model = D('translation');
        $images_model = D('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $repeat_where['en'] = $back['en'];
        $repeat_where['website_id'] = session('website_id');
        $repeat_lang = $translation_model->where($repeat_where)->find();
        if($repeat_lang['status'] == '1'){
            echo '2';
        }else{
            if($back['en']!=null||$back['de']!=null||$back['nl']!=null||$back['fr']!=null||$back['remarks']!=null){
                $trans_data['en'] = $back['en'];
                $trans_data['de'] = $back['de'];
                $trans_data['nl'] = $back['nl'];
                $trans_data['fr'] = $back['fr'];
                if($back['en']!=null&&$back['de']!=null&&$back['nl']!=null){
                    $trans_data['modify'] = '0';
                }
                $trans_data['remarks'] = $back['remarks'];
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
                echo '1';
            }else{
                echo '0';
            }
        }
    }
    //lang list del
    public function del(){
        $translation_model = D('translation');
        $images_model = D('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $res = $translation_model->delTranslate($back['id']);
        $images_model->delImages($back['id']);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang lists
    public function getList(){
        $translation_model = D('translation');
        // $_tid = $_GET['id'];
        // if($_tid){
        //     $translation_list = $translation_model->getOneTranslate($_tid);
        // }else{
        $back = json_decode(file_get_contents("php://input"),true);
        if($back['inrender'] == '0'){
            $translation_list_get = $translation_model->getTranslateList('','1',session('website_id'),'1','id desc');
        }
        if($back['inrender'] == '1'){
            $translation_list_get = $translation_model->searchTranslate($back['search'],session('website_id'),'0','1');
        }
        // }
        foreach ($translation_list_get['list'] as $key => $value) {
            # code...
            foreach ($value as $k => $val) {
                # code...
                $translation_list[$key][$k] = htmlentities($val);
            }
        }
        $count = $translation_model->getTranslateCount(session('website_id'));
        $list['lists'] = $translation_list;
        $list['current_count'] = $translation_list_get['count'];
        $list['count'] = $count;
        if($translation_list||$count){
            echo json_encode($list);
        }else{
            echo '0';
        }
    }
    //lang edit detail
    public function getInfo(){
        $translation_model = D('translation');
        $images_model = D('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $translation_detail = $translation_model->getOneTranslate($back['id']);
        $images = $images_model->getImages($back['id']);
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
        $back = json_decode(file_get_contents("php://input"),true);
        $edit_data['id'] = intval($back['id']);
        $edit_data['en'] = $back['en'];
        $edit_data['de'] = $back['de'];
        $edit_data['nl'] = $back['nl'];
        $edit_data['remarks'] = $back['remarks'];
        $edit_data['modify'] = $back['modify'];
        $res = $translation_model->setTranslate($edit_data);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang add or edit del image
    public function imageDel(){
        $images_model = D('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $res = $images_model->delImage($back['imageId']);
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