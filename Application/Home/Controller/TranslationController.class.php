<?php
namespace Home\Controller;
use Think\Controller;
header('Content-Type: application/json; charset=utf-8');
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
    public function translation_list(){
 	    $translation_model = M('translation');
 	    if($_POST['render_search']==1){
			$search = $_POST['search'];
			$where['en'] = array('like','%'.$search.'%');
/*			$where['de'] = array('like','%'.$search.'%');
			$where['nl'] = array('like','%'.$search.'%');
			$where['fr'] = array('like','%'.$search.'%');
			$where['_logic'] = 'or';*/
			if($_POST['search']!=null){
				$translation_list=$translation_model->where($where)->select();
			}else{
				$translation_list=$translation_model->select();
			}
 	    	$this->assign('translation_list',$translation_list);
	        $this->display();
    	}
    	if(intval($_POST['render'])==1){
	    	$translation_list=$translation_model->select();
	    	$this->assign('translation_list',$translation_list);
	        $this->display();
    	}
    }
    public function translation_add(){
    	$translation_model = M('translation');
    	if($_POST['en']!=null||$_POST['de']!=null||$_POST['nl']!=null||$_POST['fn']!=null){
    		$trans_data['en']=$_POST['en'];
    		$trans_data['de']=$_POST['de'];
    		$trans_data['nl']=$_POST['nl'];
    		$trans_data['fr']=$_POST['fr'];
    		$trans_data['remarks']=$_POST['remarks'];
    		// $trans_data['images']=$_POST['images'];
    		$res=$translation_model->add($trans_data);
    		if($res){
		    	$this->display();
    		}
    	}else{
	    	$this->display();
	    }
    }
    public function translation_edit(){
		$translation_model = M('translation');
		$images_model = M('translation_image');
		$lang_info=$_POST['lang_info'];
		$lang_type=$_POST['lang_type'];
		$lang_id=$_POST['lang_id'];
		//edit lang
		if($lang_type!=null&&$lang_info!=null&&$lang_id!=null){
			$edit_data['id']=$lang_id;
			$edit_data[$lang_type]=$lang_info;
			$res=$translation_model->save($edit_data);
			if($res){
				echo '1';
			}
		}
		//add image
		if($_FILES['images']['name']!=null){
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
		}
		//del image
		if($_POST['image_id']!=null&&$_POST['image_name']!=null){
			$images_model->where(array('id'=>intval($_POST['image_id'])))->delete();
			$image_path='./Uploads/Translation/'.$_POST['image_name'];
			if(file_exists($image_path)){
				unlink($image_path);
			}
			$where['id']=intval($_POST['lang_id_del']);
			$translation_detail=$translation_model->where($where)->find();
			$images=$images_model->where(array('lang_id'=>intval($_POST['lang_id_del'])))->field('image_name,id')->select();
			$this->assign('translation_detail',$translation_detail);
			$this->assign('images_list',$images);
			$this->display();
		}
		//click Edit
		if($_POST['id']!=null){
		$where['id']=intval($_POST['id']);
		$translation_detail=$translation_model->where($where)->find();
		$images=$images_model->where(array('lang_id'=>intval($_POST['id'])))->field('image_name,id')->select();
		$this->assign('translation_detail',$translation_detail);
		$this->assign('images_list',$images);
		$this->display();
		}
    }
    public function translation_del(){
    	$translation_model=M('translation');
    	$where['id'] = intval($_POST['id']);
    	$translation_list=$translation_model->where($where)->delete();
    }
    public function translation_export(){
    	$translation_model=M('translation');
    	$data=$translation_model->select();
    	$title=array('id','EN','DE','NL','FR','Remarks');
    	exportexcel($data,$title);
    }
    public function translation_test(){
    	$translation_model=M('translation');
    	//$m=json_decode(file_get_contents("php://input"),true);
    	//file_put_contents("data.txt", $man->id);
    	//$where['id'] = $m['id'];
    	$translation_list=$translation_model->select();
    	echo json_encode($translation_list);
    	// var_dump(json_encode($translation_list));
    }
}