<?php
namespace Home\Controller;
use Think\Controller;
class TranslationController extends Controller {
    public function index(){
    	$translation_model = M('translation');
    	$translation_list=$translation_model->where(array('same_id'=>'0'))->select();
    	$this->assign('translation_list',$translation_list);
        $this->display();
    }
    public function translation_list(){
    	if(intval($_POST['render'])==1){
	    	$translation_model = M('translation');
	    	$translation_list=$translation_model->where(array('same_id'=>'0'))->select();
	    	$this->assign('translation_list',$translation_list);
	        $this->display();
    	}
    }
    public function translation_add(){
    	$translation_model = M('translation');
    	if($_POST['title']!=null||intval($_POST['same_id'])!==null||$_POST['info']!=null||$_POST['type']!=null){
    		$trans_data['title']=$_POST['title'];
    		$trans_data['same_id']=intval($_POST['same_id']);
    		$trans_data['info']=$_POST['info'];
    		$trans_data['type']=$_POST['type'];
    		// $trans_data['images']=$_POST['images'];
    		$res=$translation_model->add($trans_data);
			//  $config = array(
			// 	'maxSize' => 3145728,
			// 	'rootPath' => './Uploads/',
			// 	'savePath' => '',
			// 	'saveName' => array('uniqid','s_'),
			// 	'exts' => array('jpg', 'gif', 'png', 'jpeg'),
			// 	'autoSub' => true,
			// 	'subName' => 'Translation',
			// );
   //  		$upload = new \Think\Upload($config );
   //  		$info   =   $upload->upload();
    		if($res){
				$translation_list=$translation_model->where(array('same_id'=>'0'))->field('id,title')->select();
		    	$this->assign('translation_list',$translation_list);
		    	$this->display();
    		}
    	}

    }
    public function translation_edit(){
		$translation_model = M('translation');
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
		$info   =   $upload->upload();

		if($_POST['id']!=null){
		$where['id']=intval($_POST['id']);
		$where['same_id']=intval($_POST['id']);
		$where['_logic']= 'OR';
		$translation_detail=$translation_model->where($where)->select();
		$this->assign('translation_detail',$translation_detail);
		$this->assign('en_id',intval($_POST['id']));
		$this->display();
		}
    }
    public function translation_image(){
   //  		$config = array(
			// 	'maxSize' => 3145728,
			// 	'rootPath' => './Uploads/',
			// 	'savePath' => '',
			// 	'saveName' => array('uniqid','s_'),
			// 	'exts' => array('jpg', 'gif', 'png', 'jpeg'),
			// 	'autoSub' => true,
			// 	'subName' => 'Translation',
			// );
   //  		$upload = new \Think\Upload($config );
   //  		$info   =   $upload->upload();
	echo "{aa:'11'}";
    }
}