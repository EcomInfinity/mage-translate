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
?>