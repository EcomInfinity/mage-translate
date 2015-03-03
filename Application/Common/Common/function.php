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

function getPurviewJson($purNum){
    $rule_model = M('rule');
    $count = $rule_model->count();
    $rule = $rule_model->order('id desc')->field('rule_name')->select();
    if($purNum == '-1'){
        $purview = '-1';
    }else{
        $purview_f = str_split(str_pad(decbin($purNum),$count,'0',STR_PAD_LEFT));
        foreach ($rule as $k=>$val) {
            # code...
            $purview[strtolower($val['rule_name'])] = $purview_f[$k];
        }
        $purview = json_encode($purview);
    }
    return $purview;
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