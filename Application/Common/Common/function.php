<?php
function exportexcel($data=array(),$filename='report'){
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/csv"); 
    header("Content-Disposition:attachment;filename=".$filename.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
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

function exportTxt($_data,$_filename='report'){
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/txt");
    header("Content-Disposition:attachment;filename=".$_filename.".txt");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $_data;
}

function exportZip($_data, $_filename='report'){
    $zip =new \ZipArchive;
    if ($zip -> open('./Uploads/cms/'.$_filename, \ZipArchive::CREATE) === TRUE) {
        foreach ($_data as $val) {
            $zip->addFile('./Uploads/cms/'.$val);
        }
        $zip -> close();
    }
    $filename = './Uploads/cms/'.$_filename; //文件路径
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".basename($_filename));
    readfile($filename); 
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

// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
//检测通信
// function magentoApi($_website_api){
//     try{
//     $_client = new \SoapClient($_website_api['domain'].'/api/soap/?wsdl');
//     $_sessionId = $_client->login($_website_api['rest_user'], $_website_api['rest_password']);
//     // $_result = $_client->call($_sessionId, $_website_info_type, $_website_info_id);
//     }catch (SOAPFault $e){
//         // echo $e->getMessage();
//         // var_dump($e);
//         return false;
//     }
//     return true;
// }
//同步magento数据
function magentoApiSync($_website_api, $_website_info_type, $_website_info_id){
    try{
    $_client = new \SoapClient($_website_api['domain'].'/api/soap/?wsdl');
    $_sessionId = $_client->login($_website_api['rest_user'], $_website_api['rest_password']);
    // $_result = $_client->call($_sessionId, $_website_info_type, $_website_info_id);
    }catch (SOAPFault $e){
        return false;
    }
    if($_website_info_type){
        try{
            $_result = $_client->call($_sessionId, $_website_info_type, $_website_info_id);
        }catch (Exception $e){
            return false;
        }
        return $_result;
    }
}

// function magentoTest($_domain){
//     try{
//         $_client = new \SoapClient($_domain.'/api/soap/?wsdl', array('trace' => 1, "exceptions" => 0));
//         // try{
//         //     $_client->call('.....');
//         // } catch(SOAPFault $sp){

//         // }
//     } catch(SOAPFault $e){
//         return $e;
//     }
// }
?>