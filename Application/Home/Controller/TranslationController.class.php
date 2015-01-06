<?php
namespace Home\Controller;
use Think\Controller;
// header('Content-Type: application/json; charset=utf-8');
class TranslationController extends Controller {
    public function index(){
    	$translation_model = M('translation');
		$where['en'] = '';
		$where['de'] = '';
		$where['nl'] = '';
		$where['fr'] = '';
		$where['_logic'] = 'or';
    	$translation_list=$translation_model->where($where)->select();
    	$this->assign('translation_list',$translation_list);
        $this->display();
    }

    public function lang_del(){
    	$translation_model=M('translation');
    	$back=json_decode(file_get_contents("php://input"),true);
    	$where['id'] = intval($back['id']);
    	$translation_list=$translation_model->where($where)->delete();
    	echo '1';
    }
    public function translation_export(){
    	$translation_model=M('translation');
    	$data=$translation_model->select();
    	$title=array('id','EN','DE','NL','FR','Remarks');
    	exportexcel($data,$title);
    }
    public function lang_add(){
    	$translation_model = M('translation');
    	$back=json_decode(file_get_contents("php://input"),true);
		if($back['en']!=null||$back['de']!=null||$back['nl']!=null||$back['fn']!=null){
	    	$trans_data['en']=$back['en'];
			$trans_data['de']=$back['de'];
			$trans_data['nl']=$back['nl'];
			$trans_data['fr']=$back['fr'];
			$trans_data['remarks']=$back['remarks'];
			$res=$translation_model->add($trans_data);
			echo '45';
		}
    }
    public function lang_list(){
    	$translation_model=M('translation');
    	$back=json_decode(file_get_contents("php://input"),true);
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
    	$translation_list=$translation_model->where($where)->select();
    	echo json_encode($translation_list);
    }
    public function lang_edit_detail(){
		$translation_model = M('translation');
		$images_model = M('translation_image');
		$back=json_decode(file_get_contents("php://input"),true);
    	$where['id']=intval($back['id']);
		$translation_detail=$translation_model->where($where)->find();
		$images=$images_model->where(array('lang_id'=>intval($where['id'])))->field('image_name,id')->select();
		$this->assign('translation_detail',$translation_detail);
		$this->assign('images_list',$images);
		$lang_detail['images']=$images;
		$lang_detail['detail']=$translation_detail;
		echo json_encode($lang_detail);
    }
    public function lang_edit_info(){
    	$translation_model = M('translation');
    	$back=json_decode(file_get_contents("php://input"),true);
		$edit_data['id']=intval($back['langId']);
		$edit_data[$back['langType']]=$back['langInfo'];
		$res=$translation_model->save($edit_data);
		echo '1';
    }
    public function lang_image_del(){
    	$images_model = M('translation_image');
    	$back=json_decode(file_get_contents("php://input"),true);
		$images_model->where(array('id'=>intval($back['imageId'])))->delete();
		$image_path='./Uploads/Translation/'.$back['imageName'];
		if(file_exists($image_path)){
			unlink($image_path);
		}
		echo '1';
    }
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
		$images['lang_id']=intval($_GET['lang_id']);
		$images['image_name']=$info['savename'];
		$images_model->add($images);
		echo '1';
    }
}