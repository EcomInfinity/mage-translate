<?php
function exportexcel($data=array(),$title=array(),$filename='report'){
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/csv");
    //header("Content-type:application/vnd.ms-excel");  
    header("Content-Disposition:attachment;filename=".$filename.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    if (!empty($title)){
        $title= implode(",", $title);
        echo "$title\n";
    }
    if (!empty($data)){
        foreach($data as $key=>$val){
            // foreach ($val as $ck => $cv) {
            //         $data[$key][$ck]=$cv;
            // }
            $data[$key]=implode(",", $data[$key]);
            
        }
        echo implode("\n",$data);
    }
}

function getUser($uid){
    if($uid&&$uid > '0'){
        $relation_model = M('relation');
        $website_model = M('website');
        $role_model = M('role');
        $relation = $relation_model->where(array('user_id'=>$uid))->find();
        $website = $website_model->where(array('id'=>$relation['website_id']))->find();
        $role = $role_model->where(array('id'=>$relation['role_id']))->find();
        $userInfo['relation'] = $relation;
        $userInfo['website'] = $website;
        $userInfo['role'] = $role;
        return $userInfo;
    }
}

function getPurview($uid){
    if($uid&&$uid > '0'){
        $relation_model = M('relation');
        $role_model = M('role');
        $relation = $relation_model->where(array('user_id'=>$uid))->find();
        $role = $role_model->where(array('id'=>$relation['role_id']))->find();
        return $role['purview'];
    }
}
//is allow login
function getAllow($uid){
    if($uid&&$uid > '0'){
        $user_model = M('user');
        $res = $user_model->where(array('id'=>$uid))->find();
        return $res['allow'];
    }
}

?>