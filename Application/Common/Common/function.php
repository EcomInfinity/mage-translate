<?php
function exportexcel($data=array(),$filename='report'){
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/csv");
    //header("Content-type:application/vnd.ms-excel");  
    header("Content-Disposition:attachment;filename=".$filename.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    // if (!empty($title)){
    //     $title= implode(",", $title);
    //     echo "$title\n";
    // }
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
        $rule_model = M('rule');
        $count = $rule_model->count();
        $rule = $rule_model->order('id desc')->field('rule_name')->select();
        $relation = $relation_model->where(array('user_id'=>$uid))->find();
        $website = $website_model->where(array('id'=>$relation['website_id']))->find();
        $role = $role_model->where(array('id'=>$relation['role_id']))->find();
        if($role['purview'] == '-1'){
            $purview = '-1';
        }else{
            $purview_f = str_split(str_pad(decbin($role['purview']),$count,'0',STR_PAD_LEFT));
            foreach ($rule as $k=>$val) {
                # code...
                $purview[strtolower($val['rule_name'])] = $purview_f[$k];
            }
            $purview = json_encode($purview);
        }
        $userInfo['relation'] = $relation;
        $userInfo['website'] = $website;
        $userInfo['role'] = $role;
        $userInfo['purview'] = $purview;
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