<?php
namespace Home\Controller;
use Think\Controller;
ini_set('max_execution_time', 30000);
class MagentoApiController extends BaseController {
    public function index(){
    }
    public function syncTranslatePage(){
        $_cms_page_result = magentoApiSync(
                session('soap'),
                'info_cmspage.list',
                array()
            );
        $_store_view_result = magentoApiSync(
                session('soap'),
                'info_getwebinfo.storeViewList',
                array()
            );
        $_cms_page_result = json_decode($_cms_page_result, true);
        $_store_view_result = json_decode($_store_view_result, true);
        foreach ($_store_view_result as $k => $val) {
            $_store_view_id[]=$val['store_id'];
        }
        foreach ($_store_view_result as $key => $value) {
            # code...
            if(strtolower($value['store_view_language']) == 'en_us'){
                $_base_store_view_id[] = $value['store_id'];
            }
        }
        foreach ($_cms_page_result as $k => $val) {
            $_cms_page_identifier[$k] = $val['identifier'];
        }
        $_cms_page_identifier = array_unique($_cms_page_identifier);
        foreach ($_cms_page_identifier as $_key_id => $_identifier) {
            foreach ($_cms_page_result as $key => $value) {
                if($_identifier == $value['identifier']){
                    //判断cms_page中的store_id是否有等于0的
                    foreach ($value['store_id'] as $k => $val) {
                        if($val == 0){
                            $_all_store_view_page[$_key_id] = $value;
                        }else{
                            if($val == $_base_store_view_id['0']){
                                $_all_store_view_page[$_key_id] = $value;
                            }else{
                                $_all_store_view_page[$_key_id] = $value;
                            }
                        }
                    }
                    if(count($value['store_id']) > 1){
                        //去除store_id = 0的
                        $_all_store_view_checked = false;
                        foreach ($value['store_id'] as $k => $val) {
                            if($val == 0){
                                unset($value['store_id'][$k]);
                                $_all_store_view_checked = true;
                            }
                        }
                        if($_all_store_view_checked === true){
                            $_first = 1;
                        }else{
                            $_first = 0;
                        }
                        foreach ($value['store_id'] as $k => $val) {
                            $_exist_store_view_id[$_key_id][] = $val;
                            if($k == $_first){
                                $_save['stores'] = array($val);
                                unset($_save['page_id']);
                                $_result = magentoApiSync(
                                        session('soap'),
                                        'info_cmspage.update',
                                        array($value['page_id'],$_save)
                                    );
                                echo 'update';
                            }else{
                                if($_result > 0){
                                    $_add = $value;
                                    $_add['stores'] = array($val);
                                    unset($_add['title']);
                                    foreach ($_store_view_result as $store) {
                                        # code...
                                        if($val == $store['store_id']){
                                            $_add['title'] = $store['name'];
                                        }
                                    }
                                    unset($_add['page_id']);
                                    magentoApiSync(
                                            session('soap'),
                                            'info_cmspage.create',
                                            array($_add)
                                        );
                                }
                                    echo 'create';
                            }
                        }
                    }else{
                        foreach ($value['store_id'] as $val) {
                            $_exist_store_view_id[$_key_id][] = $val;
                        }
                    }
                }
            }
            $_over_store_id = array_diff($_store_view_id, $_exist_store_view_id[$_key_id]);
            foreach ($_over_store_id as $value) {
                $_over_add = $_all_store_view_page[$_key_id];
                $_over_add['stores'] = array($value);
                unset($_over_add['title']);
                foreach ($_store_view_result as $store) {
                    if($value == $store['store_id']){
                        $_over_add['title'] = $store['name'];
                    }
                }
                unset($_over_add['page_id']);
                magentoApiSync(
                        session('soap'),
                        'info_cmspage.create',
                        array($_over_add)
                    );
            }
        }
        //添加到translation数据库
        $_cms_page_all_result = magentoApiSync(
                session('soap'),
                'info_cmspage.list',
                array()
            );
        $_cms_page_all_result = json_decode($_cms_page_all_result, true);
        //指明每个cms_page语言
        foreach ($_cms_page_all_result as $k_p => $_page) {
            foreach ($_store_view_result as $k_s => $_store) {
                if($_page['store_id']['0'] == $_store['store_id']){
                    $_cms_page_all_result[$k_p]['lang_code'] = $_store['store_view_language'];
                }
            }
        }
        // truncate table
        foreach ($_cms_page_all_result as $k => $val) {
            if($val['store_id']['0'] != 0){
                $_repeat_cms_page = D('cms_translate')->where(array('type_id' => $val['page_id'], 'website_id' => session('website_id'), 'type' => 1))->find();
                if($_repeat_cms_page){
                    $_cms_save['content'] = json_encode($val);
                    $_cms_save['title'] = $val['title'];
                    $_cms_save['identifier'] = $val['identifier'];
                    $_cms_save['id'] = $_repeat_cms_page['id'];
                    D('cms_translate')->save($_cms_save);
                }else{
                    // unset($value['page_id']);
                    $_cms_add['content'] = json_encode($val);
                    $_cms_add['title'] = $val['title'];
                    $_cms_add['identifier'] = $val['identifier'];
                    $_cms_add['website_id'] = session('website_id');
                    $_cms_add['type'] = 1;
                    $_cms_add['type_id'] = $val['page_id'];
                    $_lang_code = D('language')->where(array('simple_name' => $val['lang_code']))->find();
                    $_cms_add['lang_id'] = $_lang_code['id'];
                    D('cms_translate')->add($_cms_add);
                }
            }
        }
        var_dump($_cms_page_all_result);
    }

    public function syncMagentoPage(){
        //更新magento,cms_page
        $_cms_save =array();
        $_cms_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->select();
        foreach ($_cms_page_translate_result as $val) {
            # code...
            $_cms_save = json_decode($val['content'], true);
            unset($_cms_save['page_id']);
            unset($_cms_save['identifier']);
            unset($_cms_save['sort_order']);
            unset($_cms_save['creation_time']);
            unset($_cms_save['update_time']);
            unset($_cms_save['store_id']);
            unset($_cms_save['store_code']);
            $_cms_save['title'] = $val['title'];
            var_dump($_cms_save);
            var_dump($val['type_id']);
            magentoApiSync(
                    session('soap'),
                    'info_cmspage.update',
                    array($val['type_id'],$_cms_save)
                );
        }
        var_dump($_cms_page_translate_result);
    }

    public function syncTranslateBlock(){
        $_cms_block_result = magentoApiSync(
                session('soap'),
                'info_cmsblock.list',
                array()
            );
        $_store_view_result = magentoApiSync(
                session('soap'),
                'info_getwebinfo.storeViewList',
                array()
            );
        $_cms_block_result = json_decode($_cms_block_result, true);
        $_store_view_result = json_decode($_store_view_result, true);
        foreach ($_store_view_result as $k => $val) {
            $_store_view_id[]=$val['store_id'];
        }
        foreach ($_store_view_result as $key => $value) {
            if(strtolower($value['store_view_language']) == 'en_us'){
                $_base_store_view_id[] = $value['store_id'];
            }
        }
        foreach ($_cms_block_result as $k => $val) {
            $_cms_block_identifier[$k] = $val['identifier'];
        }
        $_cms_block_identifier = array_unique($_cms_block_identifier);
        foreach ($_cms_block_identifier as $_key_id => $_identifier) {
            foreach ($_cms_block_result as $key => $value) {
                if($_identifier == $value['identifier']){
                    //判断cms_block中的store_id是否有等于0的
                    foreach ($value['store_id'] as $k => $val) {
                        if($val == 0){
                            $_all_store_view_block[$_key_id] = $value;
                        }else{
                            if($val == $_base_store_view_id['0']){
                                $_all_store_view_block[$_key_id] = $value;
                            }else{
                                $_all_store_view_block[$_key_id] = $value;
                            }
                        }
                    }
                    if(count($value['store_id']) > 1){
                        //去除store_id = 0的
                        $_all_store_view_checked = false;
                        foreach ($value['store_id'] as $k => $val) {
                            if($val == 0){
                                unset($value['store_id'][$k]);
                                $_all_store_view_checked = true;
                            }
                        }
                        if($_all_store_view_checked === true){
                            $_first = 1;
                        }else{
                            $_first = 0;
                        }
                        foreach ($value['store_id'] as $k => $val) {
                            $_exist_store_view_id[$_key_id][] = $val;
                            if($k == $_first){
                                $_save['stores'] = array($val);
                                unset($_save['block_id']);
                                $_result = magentoApiSync(
                                        session('soap'),
                                        'info_cmsblock.update',
                                        array($value['block_id'],$_save)
                                    );
                                echo 'update';
                            }else{
                                if($_result > 0){
                                    foreach ($_store_view_result as $store) {
                                        if($val == $store['store_id']){
                                            $_add['title'] = $store['name'];
                                        }
                                    }
                                    $_add = $value;
                                    $_add['stores'] = array($val);
                                    unset($_add['block_id']);
                                    magentoApiSync(
                                            session('soap'),
                                            'info_cmsblock.create',
                                            array($_add)
                                        );
                                }
                                    echo 'create';
                            }
                        }
                    }else{
                        foreach ($value['store_id'] as $val) {
                            $_exist_store_view_id[$_key_id][] = $val;
                        }
                    }
                }
            }
            $_over_store_id = array_diff($_store_view_id, $_exist_store_view_id[$_key_id]);
            foreach ($_over_store_id as $key => $value) {
                foreach ($_store_view_result as $store) {
                    if($value == $store['store_id']){
                        $_over_add['title'] = $store['name'];
                    }
                }
                $_over_add = $_all_store_view_block[$_key_id];
                $_over_add['stores'] = array($value);
                unset($_over_add['block_id']);
                magentoApiSync(
                        session('soap'),
                        'info_cmsblock.create',
                        array($_over_add)
                    );
            }
        }
        //添加到translation数据库
        $_cms_block_all_result = magentoApiSync(
                session('soap'),
                'info_cmsblock.list',
                array()
            );
        $_cms_block_all_result = json_decode($_cms_block_all_result, true);
        //指明每个cms_block语言
        foreach ($_cms_block_all_result as $k_p => $_block) {
            foreach ($_store_view_result as $k_s => $_store) {
                if($_block['store_id']['0'] == $_store['store_id']){
                    $_cms_block_all_result[$k_p]['lang_code'] = $_store['store_view_language'];
                }
            }
        }
        // truncate table
        foreach ($_cms_block_all_result as $k => $val) {
            if($val['store_id']['0'] != 0){
                $_repeat_cms_block = D('cms_translate')->where(array('type_id' => $val['block_id'], 'website_id' => session('website_id'), 'type' => 2))->find();
                if($_repeat_cms_block){
                    $_cms_save['content'] = json_encode($val);
                    $_cms_save['title'] = $val['title'];
                    $_cms_save['identifier'] = $val['identifier'];
                    $_cms_save['id'] = $_repeat_cms_block['id'];
                    D('cms_translate')->save($_cms_save);
                }else{
                    // unset($value['block_id']);
                    $_cms_add['content'] = json_encode($val);
                    $_cms_add['title'] = $val['title'];
                    $_cms_add['identifier'] = $val['identifier'];
                    $_cms_add['website_id'] = session('website_id');
                    $_cms_add['type'] = 2;
                    $_cms_add['type_id'] = $val['block_id'];
                    $_lang_code = D('language')->where(array('simple_name' => $val['lang_code']))->find();
                    $_cms_add['lang_id'] = $_lang_code['id'];
                    D('cms_translate')->add($_cms_add);
                }
            }
        }
        var_dump($_store_view_result);
    }

    public function syncMagentoBlock(){
        //更新magento,cms_block
        $_cms_block_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 2))->select();
        foreach ($_cms_block_translate_result as $val) {
            # code...
            $_cms_save = json_decode($val['content'], true);
            unset($_cms_save['block_id']);
            $_cms_save['title'] = $val['title'];
            magentoApiSync(
                    session('soap'),
                    'info_cmsblock.update',
                    array($val['type_id'],$_cms_save)
                );
        }
        var_dump($_cms_block_translate_result);
    }
    public function test(){
        $_cms_save['title'] = 'test12323233';
        magentoApiSync(
                session('soap'),
                'info_cmspage.update',
                array('44', $_cms_save)
            );
        var_dump(session('soap'));
        // $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->relation(true)->select();
        // foreach ($_page_translate_result as $k => $_page) {
        //     # code...
        //     $_cms_page_identifier[] = $_page['identifier'];
        //     $_page_content = json_decode($_page['content'], true);
        //     $_page_translate_result[$k]['content'] = $_page_content['content'];
        //     $_page_translate_result[$k]['meta_keywords'] = $_page_content['meta_keywords'];
        //     $_page_translate_result[$k]['meta_description'] = $_page_content['meta_description'];
        // }
        // $_cms_page_identifier = array_unique($_cms_page_identifier);
        // foreach ($_cms_page_identifier as $_identifier) {
        //     # code...
        //     foreach ($_page_translate_result as $k => $_page) {
        //         # code...
        //         if($_page['identifier'] == $_identifier){
        //             $_cms_page[$_identifier][] = $_page;
        //         }
        //     }
        // }
        // foreach ($_cms_page_identifier as $_identifier) {
        //     # code...
        //     foreach ($_page_translate_result as $k => $_page) {
        //         # code...
        //         if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
        //             $_cms_page_kind[$_identifier] = $_page['title'];
        //             break;
        //         }
        //     }
        // }
        // foreach ($_cms_page_kind as $k => $val) {
        //     # code...
        //     echo $val.'/'.$k.'<br/>';
        // }
        // var_dump($_cms_page_kind);
        // var_dump($_cms_page_identifier);
        // var_dump($_cms_page);
    }
}