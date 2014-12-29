<?php
namespace Home\Controller;
use Think\Controller;
class TranslationController extends Controller {
    public function index(){
    	$translation_model = M('translation');
		// $empyt_id_f=$translation_model->where("same_id=0 AND info=''")->field('id')->select();
		// $empyt_id_s=$translation_model->where("same_id>0 AND info=''")->field('same_id')->select();
  //   	foreach ($empyt_id_f as $val) {
  //   		$empyt_id[] = $val['id'];
  //   	}
  //   	foreach ($empyt_id_s as $val) {
  //   		$empyt_id[] = $val['same_id'];
  //   	}
  //   	// $empty_ida=array_unique($empyt_id);
  //   	// var_dump($empyt_ida);
  //   	foreach (array_unique($empyt_id) as $key=>$val) {
  //   			$empyt_id_n=','.$val.$empyt_id_n;
  //   	}
  //   	$empyt_id_n=substr($empyt_id_n,1);
  //   	$where['id'] = array('in',$empyt_id_n);
    	$translation_list=$translation_model->select();
  //   	$translation_select=$translation_model->where(array('same_id'=>'0'))->select();
  //   	$this->assign('translation_select',$translation_select);
    	$this->assign('translation_list',$translation_list);
        $this->display();
    }
    public function translation_list(){
 	    $translation_model = M('translation');
 	    if($_POST['render_search']==1){
			$search = $_POST['search'];
			$where['type'] = 'EN';
			$where['info'] = array('like','%'.$search.'%');
			if($_POST['search']!=null){
				$translation_list=$translation_model->where($where)->select();
			}else{
				$translation_list=$translation_model->where(array('same_id'=>'0'))->select();
			}
 	    	$this->assign('translation_list',$translation_list);
	        $this->display();
    	}
    	if(intval($_POST['render'])==1){
	    	$translation_list=$translation_model->where(array('same_id'=>'0'))->select();
	    	$this->assign('translation_list',$translation_list);
	        $this->display();
    	}
    }
    public function translation_add(){
    	$translation_model = M('translation');
		// $translation_select=$translation_model->where(array('same_id'=>'0'))->field('id,title')->select();
  //   	$this->assign('translation_select',$translation_select);
    	if($_POST['en']!=null||$_POST['de']!=null||$_POST['nl']!=null||$_POST['fn']!=null){
    		$trans_data['en']=$_POST['en'];
    		$trans_data['de']=$_POST['de'];
    		$trans_data['nl']=$_POST['nl'];
    		$trans_data['fn']=$_POST['fn'];
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
		$edit_info=$_POST['edit_info'];
		$edit_id=intval($_POST['edit_id']);
		if($edit_id!=null&&$edit_info!=null){
			$edit_data['id']=$edit_id;
			$edit_data['info']=$edit_info;
			$res=$translation_model->save($edit_data);
			if($res){
				echo '1';
			}
		}
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
		$images['lang_id']=intval($_GET['en_id']);
		$images['image_name']=$info['savename'];
		$images_model->add($images);
		}
		if($_POST['id']!=null){
		$where['id']=intval($_POST['id']);
		// $where['same_id']=intval($_POST['id']);
		// $where['_logic']= 'OR';
		$translation_detail=$translation_model->where($where)->find();
		$images=$images_model->where(array('lang_id'=>intval($_POST['id'])))->field('image_name')->select();
		$this->assign('translation_detail',$translation_detail);
		$this->assign('images_list',$images);
		$this->assign('en_id',intval($_POST['id']));
		$this->display();
		}
    }
    public function translation_search(){
    	$translation_model=M('translation');
    	$search = $_POST['search'];
    	$where['type'] = 'EN';
    	$where['info'] = array('like','%'.$search.'%');
    	$translation_list=$translation_model->where($where)->select();
    }
}