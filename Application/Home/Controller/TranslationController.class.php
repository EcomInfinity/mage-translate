<?php
namespace Home\Controller;
use Think\Controller;
// header('Content-Type: application/json; charset=utf-8');
class TranslationController extends Controller {
	//index
    public function index(){
        $this->display();
    }
    //lang list del
    public function lang_del(){
    	$translation_model = M('translation');
    	$images_model = M('translation_image');
    	$back = json_decode(file_get_contents("php://input"),true);
    	$where['id'] = intval($back['id']);
    	$images=$images_model->where(array('lang_id'=>intval($back['id'])))->field('image_name')->select();
    	foreach ($images as $val) {
    			$image_path = './Uploads/Translation/'.$val['image_name'];
				if(file_exists($image_path)){
					unlink($image_path);
				}
    	}
    	$images_model->where(array('lang_id'=>intval($back['id'])))->delete();
    	$translation_list = $translation_model->where($where)->delete();
    	echo '1';
    }

    public function translation_export(){
    	$translation_model = M('translation');
    	$data = $translation_model->select();
    	$title = array('id','EN','DE','NL','FR','Remarks');
    	exportexcel($data,$title);
    }
    //lang add
    public function lang_add(){
    	$translation_model = M('translation');
    	$images_model = M('translation_image');
    	$back = json_decode(file_get_contents("php://input"),true);
		if($back['en']!=null||$back['de']!=null||$back['nl']!=null||$back['fn']!=null){
	    	$trans_data['en'] = $back['en'];
			$trans_data['de'] = $back['de'];
			$trans_data['nl'] = $back['nl'];
			$trans_data['fr'] = $back['fr'];
			$trans_data['remarks'] = $back['remarks'];
			$id=$translation_model->add($trans_data);
			$image_data['lang_id'] = $id;
			$images_model->where(array('lang_id'=>'0'))->save($image_data);
			echo '1';
		}
    }
    //lang lists
    public function lang_list(){
    	$translation_model = M('translation');
    	$back = json_decode(file_get_contents("php://input"),true);
    	if($back['search']!=null){
    		$where['en'] = array('like','%'.$back['search'].'%');
    	}
    	if($back['inrender'] == '0'){
			$where['en'] = '';
			$where['de'] = '';
			$where['nl'] = '';
			$where['fr'] = '';
			$where['_logic'] = 'or';
    	}
    	$translation_list = $translation_model->where($where)->order('id desc')->select();
    	$list['lists'] = $translation_list;
    	echo json_encode($list);
    }
    //lang edit detail
    public function lang_edit_detail(){
		$translation_model = M('translation');
		$images_model = M('translation_image');
		$back = json_decode(file_get_contents("php://input"),true);
    	$where['id'] = intval($back['id']);
		$translation_detail = $translation_model->where($where)->find();
		$images = $images_model->where(array('lang_id'=>intval($where['id'])))->field('image_name,id')->select();
		$this->assign('translation_detail',$translation_detail);
		$this->assign('images_list',$images);
		$lang_detail['images'] = $images;
		$lang_detail['detail'] = $translation_detail;
		echo json_encode($lang_detail);
    }
    //edit lang info
    public function lang_edit_info(){
    	$translation_model = M('translation');
    	$back = json_decode(file_get_contents("php://input"),true);
		$edit_data['id'] = intval($back['langId']);
		$edit_data[$back['langType']] = $back['langInfo'];
		$res = $translation_model->save($edit_data);
		echo '1';
    }
    //lang add or edit del image
    public function lang_image_del(){
    	$images_model = M('translation_image');
    	$back = json_decode(file_get_contents("php://input"),true);
		$images_model->where(array('id'=>intval($back['imageId'])))->delete();
		$image_path = './Uploads/Translation/'.$back['imageName'];
		if(file_exists($image_path)){
			unlink($image_path);
		}
		echo '1';
    }
    //before new lang clear iamges
    public function lang_image_clear(){
    	$images_model = M('translation_image');
    	$images_model->where(array('lang_id'=>'0'))->delete();
    	echo '1';
    }
    //lang add or edit add images
    public function lang_image_add(){
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
		$images['lang_id'] = intval($_GET['lang_id']);
		$images['image_name'] = $info['savename'];
		$images_model->add($images);
		echo '1';
    }
    //new lang images
    public function lang_image_detail(){
    	$images_model = M('translation_image');
    	$images_detail = $images_model->where(array('lang_id'=>'0'))->select();
    	echo json_encode($images_detail);
    }
}