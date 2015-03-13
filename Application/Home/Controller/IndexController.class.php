<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }
    public function lang(){
        // $file_path = './Uploads/csv/'.'test.csv';
        // if(file_exists($file_path)){
        //     $handle = fopen($file_path,'r');
        //     while ($data = fgetcsv($handle)) {
        //         $lang_arr[] = $data;
        //     }
        //     var_dump($lang_arr);
        //     foreach ($lang_arr as $k => $val) {
        //         if($k == '0'){
        //             continue;
        //         }
        //         foreach ($lang_arr['0'] as $key => $value) {
        //             if($value == 'en_us'){
        //                 $base_add[$k]['0'] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
        //             }else{
        //                 $base_add[$k]['other'][$value] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
        //             }
        //             //     else{
        //             //     $test[''] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
        //             //     $base_add[$K]['other'] = '1';
        //             // }
        //             // if($value == 'en_us'){
        //             //     $base_add['website_id'] = session('website_id');
        //             //     $base_add['content'] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
        //             //     $_base_id = M('base_translate')->add($base_add);
        //             // }else{
        //             //     $other_add['base_id'] = $_base_id;
        //             //     $other_add['content'] = iconv(mb_detect_encoding($val[$key], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "UTF-8" , $val[$key]);
        //             //     M('other_translate')->add($other_add);
        //             // }
        //         }
        //     }
        //     var_dump($base_add);
            // foreach ($base_add as $key => $value) {
            //     # code...
            //     $_base_id = M('base_translate')->add(array('content' => $value['0']));
            //     foreach ($value['other'] as $k => $val) {
            //         # code...
            //         $other_add['content'] = $val;
            //         $other_add['base_id'] = $_base_id;
            //         M('other_translate')->add($other_add);
            //     }
            // }
        // }
        $export_get = D('translation')->field('de,en')->select();
        foreach ($export_get as $key => $value) {
            foreach ($value as $k => $val) {
                $export[$key][$k] = '"'.str_replace('"','""',$val).'"';
            }
        }
        // // var_dump($export);
        $_translate_list = D('base_translate')->where(array('status' => 1, 'website_id' => session('website_id'), ))->order('id desc')->select();
        foreach ($_translate_list as $k => $val) {
            # code...
            $_other = D('other_translate')->where(array('base_id' => $val['id'], 'lang_id' => '18'))->relation(true)->find();
            if(!empty($_other['content'])){
                $_list[$k]['en_us'] = $val['content'];
                $_list[$k][strtolower($_other['simple_name'])] = $_other['content'];
            }
        }
        var_dump($_list);
        // foreach($export as $key=>$val){
        //     // foreach ($val as $ck => $cv) {
        //     //         $data[$key][$ck]=$cv;
        //     // }
        //     $export[$key]=implode(",", $export[$key]);
            
        // }
        // var_dump($export);


    }
}