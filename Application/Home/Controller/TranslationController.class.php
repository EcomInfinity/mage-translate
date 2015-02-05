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
        $translation_model = M('translation');
        $back = json_decode(file_get_contents("php://input"),true);
        $fields = $back['field'];
        if($back['exrender'] == '0'){
            $field = 'en,'.$fields;
            $title = explode(",", $field);
            $where['website_id'] = session('website_id');
            $where['status'] = '1';
            $export_get = $translation_model->where($where)->field($field)->select();
            foreach ($export_get as $key => $value) {
                # code...
                foreach ($value as $k => $val) {
                    # code...
                    if(strpos($val,'"') == '0'){
                        $export[$key][$k] = '""'.$val.'""';
                    }else{
                        $export[$key][$k] = '"'.$val.'"';
                    }
                }
            }
            // S('title',$title);
            S('export',$export);
            S('filename',$fields);
            echo '1';
        }

            // $field = 'en,'.$fields;
            // $title = explode(",", $field);
            // $where['website_id'] = '1';
            // $where['status'] = '1';
            // $export = $translation_model->where($where)->field('en,de')->select();
            // foreach ($export as $key => $value) {
            //     # code...
            //     foreach ($value as $k => $val) {
            //         # code...
            //         if(strpos($val,'"') == '0'){
            //             $test[$key][$k] = '""'.$val.'""';
            //         }else{
            //             $test[$key][$k] = '"'.$val.'"';
            //         }
            //         // $test[$key][$k] = '"'.str_replace('"','""',$val).'"';
            //     }
            // }
            // echo strpos('qee"q"eqe','"');
            // var_dump($test);


        if($back['exrender'] == '1'){
        $data = $translation_model->find();
        foreach ($data as $k => $val) {
            # code...
            if($k!='id'&&$k!='remarks'&&$k!='status'&&$k!='en'&$k!='website_id'&&$k!='modify'){
                $allField[] = $k;
            }
        }
        echo json_encode($allField);
        }
    }

    public function download(){
       exportexcel(S('export'),'translation-'.S('filename'));
       S('export',null);
       // S('title',null);
       S('filename',null);
    }

    public function import(){
        $translation_model = M('translation');
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
                        $lang_add[strtolower($value)] = iconv(mb_detect_encoding($val[$key]), "UTF-8" , $val[$key]);
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
                        $translation_model->save($lang_save);
                        $modify = $translation_model->where(array('id'=>$res['id']))->find();
                        if($modify['en']!=''&&$modify['ne']!=''&&$modify['nl']!=''){
                            $lang_modify['id'] = $res['id'];
                            $lang_modify['modify'] = '0';
                            $translation_model->save($lang_modify);
                        }
                    }else{
                        $translation_model->add($lang_add);
                    }
                }
            }
            echo '1';
        }
    }
    //lang add
    public function add(){
        $translation_model = M('translation');
        $images_model = M('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $repear_lang = $translation_model->where(array('en'=>$back['en']))->find();
        if($repear_lang){
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
                $trans_data['website_id'] = session('website_id');
                $id=$translation_model->add($trans_data);
                $image_data['lang_id'] = $id;
                $images_model->where(array('lang_id'=>'0'))->save($image_data);
                echo '1';
            }else{
                echo '0';
            }
        }
    }
    //lang list del
    public function del(){
        $translation_model = M('translation');
        $images_model = M('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $save['status'] = '0';
        $res = $translation_model->where(array('id'=>intval($back['id'])))->save($save);
        $images_model->where(array('lang_id'=>intval($back['id'])))->save($save);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang lists
    public function getList(){
        $translation_model = M('translation');
        $back = json_decode(file_get_contents("php://input"),true);
        if($back['search']!=null){
            $where['en'] = array('like','%'.$back['search'].'%');
        }
        if($back['inrender'] == '0'){
            $where['status'] = '1';
            $where['modify'] = '1';
        }
        if($back['inrender'] == '1'){
            $where['status'] = '1';
            $where['modify'] = '0';
        }
        $where['website_id'] = session('website_id');
        $translation_list = $translation_model->where($where)->order('id desc')->select();
        // foreach ($translation_list as $key => $value) {
        //     # code...
        //     foreach ($value as $k => $val) {
        //         # code...
        //         $translation_list[$key][$k] = htmlentities($val);
        //     }
        // }
        $current_count = $translation_model->where($where)->count();
        $where_count['website_id'] = session('website_id');
        $where_count['status'] = '1';
        $count = $translation_model->where($where_count)->count();
        $list['current_count'] = $current_count;
        $list['lists'] = $translation_list;
        $list['count'] = $count;
        if($translation_list||$count){
            echo json_encode($list);
        }else{
            echo '0';
        }
    }
    //lang edit detail
    public function getInfo(){
        $translation_model = M('translation');
        $images_model = M('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $where['id'] = intval($back['id']);
        $translation_detail = $translation_model->where($where)->find();
        $whereImg['lang_id'] = intval($back['id']);
        $whereImg['status'] = '1';
        $images = $images_model->where($whereImg)->field('image_name,id')->select();
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
        $translation_model = M('translation');
        $back = json_decode(file_get_contents("php://input"),true);
        $edit_data['id'] = intval($back['id']);
        $edit_data['en'] = $back['en'];
        $edit_data['de'] = $back['de'];
        $edit_data['nl'] = $back['nl'];
        $edit_data['remarks'] = $back['remarks'];
        $edit_data['modify'] = $back['modify'];
        $res = $translation_model->save($edit_data);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang add or edit del image
    public function imageDel(){
        $images_model = M('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $save['id'] = $back['imageId'];
        $save['status'] = '0';
        $res = $images_model->save($save);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //before new lang clear iamges
    public function imageClear(){
        $images_model = M('translation_image');
        $save['status'] = '0';
        $res = $images_model->where(array('lang_id'=>'0'))->save($save);
        if($res){
            echo '1';
        }else{
            echo '0';
        }
    }
    //lang add or edit add images
    public function imageAdd(){
        $images_model = M('translation_image');
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
            $images['lang_id'] = intval($_GET['lang_id']);
            $images['image_name'] = $info['savename'];
            $id = $images_model->add($images);
            echo $id;
        }
    }
    //new lang images
    public function imageList(){
        $images_model = M('translation_image');
        $where['lang_id'] = '0';
        $where['status'] = '1';
        $images_detail = $images_model->where($where)->select();
        echo json_encode($images_detail);
    }
    //
    public function getImage(){
        $images_model = M('translation_image');
        $back = json_decode(file_get_contents("php://input"),true);
        $image = $images_model->where(array('id'=>$back['imageId']))->find();
        echo json_encode($image);
    }

}