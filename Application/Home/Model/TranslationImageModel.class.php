<?php
namespace Home\Model;
use Think\Model;
class TranslationImageModel extends Model{
    //
    public function saveImage($_params){
        // $save['lang_id'] = $_lang_id;
        return $this->save($_params);
    }

    public function addImage($_tid,$imageName){
        $add['lang_id'] = intval($_tid);
        $add['image_name'] = $imageName;
        return $this->add($add);
    }

    public function del($_where){
        $save['status'] = '0';
        return $this->where($_where)->save($save);
    }

    public function clear($_tid){
        $save['status'] = '0';
        return $this->where(array('lang_id'=>$_tid))->save($save);
    }

    public function gets($_where){
        // $where['lang_id'] = $_tid;
        // $where['status'] = '1';
        return $this->where($_where)->select();
    }

    // public function getOneImage($_iid){
    //     return $this->where(array('id'=>intval($_iid)))->find();
    // }
}
?>