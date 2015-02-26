<?php
namespace Home\Model;
use Think\Model;
class TranslationImageModel extends Model{
    //
    public function saveImage($_lang_id){
        $save['lang_id'] = $_lang_id;
        return $this->where(array('lang_id'=>'0'))->save($save);
    }

    public function addImage($_tid,$imageName){
        $add['lang_id'] = intval($_tid);
        $add['image_name'] = $imageName;
        return $this->add($add);
    }

    public function delImage($_iid){
        $save['id'] = intval($_iid);
        $save['status'] = '0';
        return $this->save($save);
    }

    public function delImages($_tid){
        $save['status'] = '0';
        return $this->where(array('lang_id'=>intval($_tid)))->save($save);
    }

    public function clearImgaes($_tid){
        $save['status'] = '0';
        return $this->where(array('lang_id'=>$_tid))->save($save);
    }

    public function getImages($_tid){
        $where['lang_id'] = $_tid;
        $where['status'] = '1';
        return $this->where($where)->select();
    }

    // public function getOneImage($_iid){
    //     return $this->where(array('id'=>intval($_iid)))->find();
    // }
}
?>