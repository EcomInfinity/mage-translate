<?php
namespace Home\Controller;
use Think\Controller;

class ImageController extends ImagePermissionController {
    public function del(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_result = D('translation_image')->del(
                array(
                        'id' => intval($_params['imageId'])
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

    public function clear(){
        $_result = D('translation_image')->clear('0');
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

    public function add(){
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
            $_image_id = D('translation_image')->addImage($_GET['lang_id'],$info['savename']);
            $image['image_name'] = $info['savename'];
            $image['id'] = $_image_id;
            echo json_encode($image);
        }
    }
}