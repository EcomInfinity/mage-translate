<?php
namespace Home\Controller;
use Think\Controller;
// header('Content-Type: application/json; charset=utf-8');
class TranslationController extends BaseController {
    //index
    public function index(){
        $this->display();
    }

    public function export(){
        $translation_model = M('translation');
        $back = json_decode(file_get_contents("php://input"),true);
        $fields = $back['field'];
        if($back['exrender'] == '0'){
            $field = 'en,'.$fields;
            $title = explode(",", $field);
            $export = $translation_model->where(array('website_id'=>session('website_id')))->field($field)->select();
            S('title',$title);
            S('export',$export);
            echo '1';
        }
        if($back['exrender'] == '1'){
        $data = $translation_model->find();
        foreach ($data as $k => $val) {
            # code...
            if($k!='id'&&$k!='remarks'&&$k!='status'&&$k!='en'&$k!='website_id'){
                $allField[] = $k;
            }
        }
        echo json_encode($allField);
        }
    }

    public function download(){
       exportexcel(S('export'),S('title'));
       S('export',null);
       S('title',null);
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
                        $lang_add[strtolower($value)] = $val[$key];
                    }
                    $lang_add['website_id'] = session('website_id');
                    $translation_model->add($lang_add);
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
        if($back['en']!=null||$back['de']!=null||$back['nl']!=null||$back['fr']!=null||$back['remarks']!=null){
            $trans_data['en'] = $back['en'];
            $trans_data['de'] = $back['de'];
            $trans_data['nl'] = $back['nl'];
            $trans_data['fr'] = $back['fr'];
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
            $wheref['en'] = '';
            $wheref['de'] = '';
            $wheref['nl'] = '';
            //$where['fr'] = '';
            $wheref['_logic'] = 'or';
            $where['_complex'] = $wheref;
            $where['status'] = '1';
        }
        if($back['inrender'] == '1'){
            $where['status'] = '1';
        }
        $where['website_id'] = session('website_id');
        $translation_list = $translation_model->where($where)->order('id desc')->select();
        $list['lists'] = $translation_list;
        if($list){
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
        $this->assign('translation_detail',$translation_detail);
        $this->assign('images_list',$images);
        $lang_detail['images'] = $images;
        $lang_detail['detail'] = $translation_detail;
        if($lang_detail){
            echo json_encode($lang_detail);
        }else{
            echo '0';
        }
    }
    //edit lang info
    public function editInfo(){
        $translation_model = M('translation');
        $back = json_decode(file_get_contents("php://input"),true);
        $edit_data['id'] = intval($back['langId']);
        $edit_data[$back['langType']] = $back['langInfo'];
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
        $info   =   $upload->uploadOne($_FILES['images']);
        if(!$info){
            die();
        }else{
            $images['lang_id'] = intval($_GET['lang_id']);
            $images['image_name'] = $info['savename'];
            $images_model->add($images);
            echo '1';
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

}