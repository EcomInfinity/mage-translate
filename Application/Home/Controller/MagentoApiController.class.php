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
                'translator_cmspage.list',
                array()
            );
        $_store_view_result = magentoApiSync(
                session('soap'),
                'translator_getwebinfo.storeViewList',
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
            $_all_store_view_flag[$_key_id] = false;
            $_base_store_view_flag[$_key_id] = false;
            foreach ($_cms_page_result as $key => $value) {
                if($_identifier == $value['identifier']){
                    //判断cms_page中的store_id是否有等于0的,并且确定一哪个为基准复制
                    foreach ($value['store_id'] as $k => $val) {
                        if($val == 0){
                            $_all_store_view_page[$_key_id] = $value;
                            $_all_store_view_flag[$_key_id] = true;
                            if(count($value['store_id']) == 1 && $value['is_active'] == 1){
                                // $_result = magentoApiSync(
                                //         session('soap'),
                                //         'translator_cmspage.update',
                                //         array($value['page_id'],array('is_active' => 0))
                                //     );
                                $_over_page_update[] = array('page_id' => $value['page_id'], 'is_active' => 0);
                            }
                            break;
                        }
                    }
                    if($_all_store_view_flag[$_key_id] !== true){
                        foreach ($value['store_id'] as $val) {
                            if($val == $_base_store_view_id['0']){
                                $_all_store_view_page[$_key_id] = $value;
                                $_base_store_view_flag[$_key_id] = true;
                                break;
                            }
                        }
                    }
                    if($_all_store_view_flag[$_key_id] === false && $_base_store_view_flag[$_key_id] === false){
                        $_all_store_view_page[$_key_id] = $value;
                        // break;
                    }
                    //该条page包含多个store_view
                    if(count($value['store_id']) > 1){
                        //去除store_id = 0的
                        $_all_store_view_checked = false;
                        foreach ($value['store_id'] as $k => $val) {
                            if($val == 0){
                                unset($value['store_id'][$k]);
                                $_all_store_view_checked = true;
                            }
                        }
                        //多个store_view，从哪个开始复制
                        if($_all_store_view_checked === true){
                            $_first = 1;
                        }else{
                            $_first = 0;
                        }
                        foreach ($value['store_id'] as $k => $val) {
                            $_exist_store_view_id[$_key_id][] = $val;
                            if($k == $_first){
                                // $_save['stores'] = array($val);
                                $_over_page_update[] = array('page_id' => $value['page_id'], 'stores' => $val);
                                // unset($_save['page_id']);
                                // $_result = magentoApiSync(
                                //         session('soap'),
                                //         'translator_cmspage.update',
                                //         array($value['page_id'],$_save)
                                //     );
                                // echo 'update';
                            }else{
                                // if($_result > 0){
                                    $_add = $value;
                                    $_add['stores'] = array($val);
                                    unset($_add['page_id']);
                                    $_over_page_add[] = $_add;
                                    // magentoApiSync(
                                    //         session('soap'),
                                    //         'translator_cmspage.create',
                                    //         array($_add)
                                    //     );
                                // }
                                    // echo 'create';
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
            // var_dump($_exist_store_view_id);
            foreach ($_over_store_id as $value) {
                $_over_add = $_all_store_view_page[$_key_id];
                $_over_add['stores'] = array($value);
                unset($_over_add['page_id']);
                $_over_page_add[] = $_over_add;
                // magentoApiSync(
                //         session('soap'),
                //         'translator_cmspage.create',
                //         array($_over_add)
                //     );
            }
        }
        // var_dump($_over_page_update);
        // var_dump($_over_page_add);
        if(count($_over_page_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmspage.update',
                    array($_over_page_update)
                );
        }
        if(count($_over_page_add) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmspage.create',
                    array($_over_page_add)
                );
        }
        // 添加到translation数据库
        $_cms_page_all_result = magentoApiSync(
                session('soap'),
                'translator_cmspage.list',
                array()
            );
        $_cms_page_all_result = json_decode($_cms_page_all_result, true);
        //指明每个cms_page语言
        foreach ($_cms_page_all_result as $k_p => $_page) {
            foreach ($_store_view_result as $k_s => $_store) {
                if($_page['store_id']['0'] == $_store['store_id']){
                    $_cms_page_all_result[$k_p]['lang_code'] = $_store['store_view_language'];
                    $_cms_page_all_result[$k_p]['store_view'] = $_store['name'];
                }
            }
        }
        // truncate table
        //删除magento上没有的
        $_translate_page_result = D('cms_translate')->gets(array('type' => 1, 'website_id' => session('website_id')));
        foreach ($_translate_page_result as $value) {
            foreach ($_cms_page_all_result as $val) {
                # code...
                if($value['type_id'] != $val['id']){
                    D('cms_translate')->delete($value['id']);
                }
            }
        }
        foreach ($_cms_page_all_result as $k => $val) {
            if($val['store_id']['0'] != 0){
                $_repeat_cms_page = D('cms_translate')->where(array('type_id' => $val['page_id'], 'website_id' => session('website_id'), 'type' => 1))->find();
                if($_repeat_cms_page){
                    $_cms_save['content'] = json_encode($val);
                    $_cms_save['title'] = $val['title'];
                    $_cms_save['identifier'] = $val['identifier'];
                    $_cms_save['store_view'] = $val['store_view'];
                    $_cms_save['id'] = $_repeat_cms_page['id'];
                    D('cms_translate')->save($_cms_save);
                }else{
                    // unset($value['page_id']);
                    $_cms_add['content'] = json_encode($val);
                    $_cms_add['title'] = $val['title'];
                    $_cms_add['identifier'] = $val['identifier'];
                    $_cms_add['store_view'] = $val['store_view'];
                    $_cms_add['website_id'] = session('website_id');
                    $_cms_add['type'] = 1;
                    $_cms_add['type_id'] = $val['page_id'];
                    $_lang_code = D('language')->where(array('simple_name' => $val['lang_code']))->find();
                    $_cms_add['lang_id'] = $_lang_code['id'];
                    D('cms_translate')->add($_cms_add);
                }
            }
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
        // var_dump($_cms_page_all_result);
    }

    public function syncMagentoPage(){
        //更新magento,cms_page
        $_cms_save =array();
        $_cms_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->select();
        foreach ($_cms_page_translate_result as $val) {
            $_cms_save = json_decode($val['content'], true);
            // unset($_cms_save['page_id']);
            unset($_cms_save['identifier']);
            unset($_cms_save['sort_order']);
            unset($_cms_save['creation_time']);
            unset($_cms_save['update_time']);
            unset($_cms_save['store_id']);
            unset($_cms_save['store_code']);
            $_cms_save['title'] = $val['title'];
            $_over_page_update[] = $_cms_save;
            // $_result = magentoApiSync(
            //         session('soap'),
            //         'translator_cmspage.update',
            //         array($val['type_id'],$_cms_save)
            //     );
            // if($_result === false){
            //     D('cms_translate')->delete($val['id']);
            // }
        }
        if(count($_over_page_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmspage.update',
                    array($_over_page_update)
                );
        }
        // var_dump($_cms_page_translate_result);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }
    //更新多条page
    public function syncSelectPage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        // $_identifier_page = D('cms_translate')->where(array('identifier' => $_params['identifier'], 'type' => 1, 'website_id' => session('website_id')))->select();
        foreach ($_params['page_ids'] as $val) {
            # code...
            $_select_pages[] = D('cms_translate')->find($val);
        }
        foreach ($_select_pages as $val) {
            $_cms_save = json_decode($val['content'], true);
            $_cms_save['title'] = $val['title'];
            $_over_page_update[] = $_cms_save;
            // $_result = magentoApiSync(
            //         session('soap'),
            //         'translator_cmspage.update',
            //         array($val['type_id'],$_cms_save)
            //     );
            // if($_result === false){
            //     D('cms_translate')->delete($val['id']);
            // }
        }
        if(count($_over_page_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmspage.update',
                    array($_over_page_update)
                );
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                    ),
                'json'
            );
    }
    //更新翻译到translation
    // public function syncSelectTOMagentoPage(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     foreach ($_params['block_ids'] as $val) {
    //         # code...
    //         $_select_pages[] = D('cms_translate')->find($val);
    //     }
    //     foreach ($_select_pages as $val) {
    //         # code...
            
    //     }
    // }
    //更新单条page
    // public function syncOnePage(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     $_update_page = D('cms_translate')->find($_params['cms_id']);
    //     $_cms_save = json_decode($_update_page['content']);
    //     $_cms_save['title'] = $_update_page['title'];
    //     $_result = magentoApiSync(
    //             session('soap'),
    //             'translator_cmspage.update',
    //             array($_update_page['type_id'],$_cms_save)
    //         );
    //     if($_result){
    //         $this->ajaxReturn(
    //                 array(
    //                     'success' => true,
    //                     'message' => '',
    //                     'data' => array(),
    //                     ),
    //                 'json'
    //             );
    //     }else{
    //         $this->ajaxReturn(
    //                 array(
    //                     'success' => false,
    //                     'message' => '',
    //                     'data' => array(),
    //                     ),
    //                 'json'
    //             );
    //     }
    // }

    public function syncTranslateBlock(){
        $_cms_block_result = magentoApiSync(
                session('soap'),
                'translator_cmsblock.list',
                array()
            );
        $_store_view_result = magentoApiSync(
                session('soap'),
                'translator_getwebinfo.storeViewList',
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
            $_all_store_view_flag[$_key_id] = false;
            $_base_store_view_flag[$_key_id] = false;
            foreach ($_cms_block_result as $key => $value) {
                if($_identifier == $value['identifier']){
                    //判断cms_block中的store_id是否有等于0的
                    foreach ($value['store_id'] as $k => $val) {
                        if($val == 0){
                            $_all_store_view_block[$_key_id] = $value;
                            $_all_store_view_flag[$_key_id] = true;
                            if(count($value['store_id']) == 1 && $value['is_active'] == 1){
                                $_over_block_update[] = array('block_id' => $value['block_id'], 'is_active' => 0);
                                // $_result = magentoApiSync(
                                //         session('soap'),
                                //         'translator_cmsblock.update',
                                //         array($value['block_id'],array('is_active' => 0))
                                //     );
                            }
                            break;
                        }
                    }
                    if($_all_store_view_flag[$_key_id] !== true){
                        foreach ($value['store_id'] as $val) {
                            if($val == $_base_store_view_id['0']){
                                $_all_store_view_block[$_key_id] = $value;
                                $_base_store_view_flag[$_key_id] = true;
                                break;
                            }
                        }
                    }
                    if($_all_store_view_flag[$_key_id] !== true && $_base_store_view_flag[$_key_id] !== true){
                        $_all_store_view_block[$_key_id] = $value;
                        // break;
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
                                // $_save['stores'] = array($val);
                                // unset($_save['block_id']);
                                $_over_block_update[] = array('block_id' => $value['block_id'], 'stores' => $val);
                                // $_result = magentoApiSync(
                                //         session('soap'),
                                //         'translator_cmsblock.update',
                                //         array($value['block_id'],$_save)
                                //     );
                                // echo 'update';
                            }else{
                                // if($_result > 0){
                                    // foreach ($_store_view_result as $store) {
                                    //     if($val == $store['store_id']){
                                    //         $_add['title'] = $store['name'];
                                    //     }
                                    // }
                                    $_add = $value;
                                    $_add['stores'] = array($val);
                                    unset($_add['block_id']);
                                    $_over_block_add[] = $_add;
                                    // magentoApiSync(
                                    //         session('soap'),
                                    //         'translator_cmsblock.create',
                                    //         array($_add)
                                    //     );
                                // }
                                    // echo 'create';
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
                // foreach ($_store_view_result as $store) {
                //     if($value == $store['store_id']){
                //         $_over_add['title'] = $store['name'];
                //     }
                // }
                $_over_add = $_all_store_view_block[$_key_id];
                $_over_add['stores'] = array($value);
                unset($_over_add['block_id']);
                $_over_block_add[] = $_over_add;
                // magentoApiSync(
                //         session('soap'),
                //         'translator_cmsblock.create',
                //         array($_over_add)
                //     );
            }
        }
        // var_dump($_over_page_update);
        // var_dump($_over_adds);
        if(count($_over_block_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmsblock.update',
                    array($_over_block_update)
                );
        }
        if(count($_over_block_add) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmsblock.create',
                    array($_over_block_add)
                );
        }
        //添加到translation数据库
        $_cms_block_all_result = magentoApiSync(
                session('soap'),
                'translator_cmsblock.list',
                array()
            );
        $_cms_block_all_result = json_decode($_cms_block_all_result, true);
        //指明每个cms_block语言
        foreach ($_cms_block_all_result as $k_p => $_block) {
            foreach ($_store_view_result as $k_s => $_store) {
                if($_block['store_id']['0'] == $_store['store_id']){
                    $_cms_block_all_result[$k_p]['lang_code'] = $_store['store_view_language'];
                    $_cms_block_all_result[$k_p]['store_view'] = $_store['name'];
                }
            }
        }
        // truncate table
        //删除magento上没有的
        $_translate_block_result = D('cms_translate')->gets(array('type' => 2, 'website_id' => session('website_id')));
        foreach ($_translate_block_result as $value) {
            foreach ($_cms_block_all_result as $val) {
                # code...
                if($value['type_id'] != $val['id']){
                    D('cms_translate')->delete($value['id']);
                }
            }
        }
        foreach ($_cms_block_all_result as $k => $val) {
            if($val['store_id']['0'] != 0){
                $_repeat_cms_block = D('cms_translate')->where(array('type_id' => $val['block_id'], 'website_id' => session('website_id'), 'type' => 2))->find();
                if($_repeat_cms_block){
                    $_cms_save['content'] = $val['content'];
                    $_cms_save['title'] = $val['title'];
                    $_cms_save['identifier'] = $val['identifier'];
                    $_cms_save['store_view'] = $val['store_view'];
                    $_cms_save['id'] = $_repeat_cms_block['id'];
                    D('cms_translate')->save($_cms_save);
                }else{
                    // unset($value['block_id']);
                    $_cms_add['content'] = $val['content'];
                    $_cms_add['title'] = $val['title'];
                    $_cms_add['identifier'] = $val['identifier'];
                    $_cms_add['store_view'] = $val['store_view'];
                    $_cms_add['website_id'] = session('website_id');
                    $_cms_add['type'] = 2;
                    $_cms_add['type_id'] = $val['block_id'];
                    $_lang_code = D('language')->where(array('simple_name' => $val['lang_code']))->find();
                    $_cms_add['lang_id'] = $_lang_code['id'];
                    D('cms_translate')->add($_cms_add);
                }
            }
        }
        // var_dump($_cms_block_all_result);
        // var_dump($_store_view_result);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }

    public function syncMagentoBlock(){
        //更新magento,cms_block
        $_cms_block_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 2))->select();
        foreach ($_cms_block_translate_result as $val) {
            # code...
            // $_cms_save['content'] = $val['content'];
            // unset($_cms_save['block_id']);
            $_cms_save['block_id'] = $val['type_id'];
            $_cms_save['content'] = $val['content'];
            $_cms_save['title'] = $val['title'];
            $_over_block_update[] = $_cms_save;
            // $_result = magentoApiSync(
            //         session('soap'),
            //         'translator_cmsblock.update',
            //         array($val['type_id'],$_cms_save)
            //     );
            // if($_result === false){
            //     D('cms_translate')->delete($val['id']);
            // }
        }
        // var_dump($_over_block_update);
        if(count($_over_block_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmsblock.update',
                    array($_over_block_update)
                );
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
            );
    }

    //更新多条block
    public function syncSelectBlock(){
        $_params = json_decode(file_get_contents("php://input"),true);
        // $_identifier_page = D('cms_translate')->where(array('identifier' => $_params['identifier'], 'type' => 1, 'website_id' => session('website_id')))->select();
        foreach ($_params['block_ids'] as $val) {
            # code...
            $_select_block[] = D('cms_translate')->find($val);
        }
        foreach ($_select_block as $val) {
            $_cms_save['block_id'] = $val['type_id'];
            $_cms_save['content'] = $val['content'];
            $_cms_save['title'] = $val['title'];
            $_over_block_update[] = $_cms_save;
            // $_result = magentoApiSync(
            //         session('soap'),
            //         'translator_cmsblock.update',
            //         array($val['type_id'],$_cms_save)
            //     );
            // if($_result === false){
            //     D('cms_translate')->delete($val['id']);
            // }
        }
        if(count($_over_block_update) > 0){
            magentoApiSync(
                    session('soap'),
                    'translator_cmsblock.update',
                    array($_over_block_update)
                );
        }
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                    ),
                'json'
            );
    }
    //更新单条block
    // public function syncOneBlock(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     $_update_block = D('cms_translate')->find($_params['cms_id']);
    //     $_cms_save = json_decode($_update_block['content']);
    //     $_cms_save['title'] = $_update_block['title'];
    //     $_result = magentoApiSync(
    //             session('soap'),
    //             'translator_cmsblock.update',
    //             array($_update_block['type_id'],$_cms_save)
    //         );
    //     if($_result){
    //         $this->ajaxReturn(
    //                 array(
    //                     'success' => true,
    //                     'message' => '',
    //                     'data' => array(),
    //                     ),
    //                 'json'
    //             );
    //     }else{
    //         $this->ajaxReturn(
    //                 array(
    //                     'success' => false,
    //                     'message' => '',
    //                     'data' => array(),
    //                     ),
    //                 'json'
    //             );
    //     }
    // }
    public function testPage(){
        $_client = new \SoapClient('http://127.0.0.1/renguangdo/api/soap/?wsdl');
        $_sessionId = $_client->login('123456', '123456');
        $_test = magentoApi(
                array(
                    'domain' => 'http://127.0.0.1/renguangdo',
                    'rest_user' => '123456',
                    'rest_password' => '123456'
                    )
            );
        // $_cms_page_result = $_client->call($_sessionId, 'translator_getwebinfo.storeViewList', array());
        // $_result = $_client->call($_sessionId, 'translator_cmsblock.list', array());
        $_cms_page_result = $_test['client']->call($_test['session_id'], 'translator_getwebinfo.storeViewList', array());
        $_result = $_test['client']->call($_test['session_id'], 'translator_cmsblock.list', array());
        // $_cms_page_result = json_decode($_cms_page_result, true);
        var_dump($_result);
        var_dump($_cms_page_result);
    }
    public function testBlock(){
        $_cms_block_result = magentoApiSync(
                session('soap'),
                'translator_cmsblock.list',
                array()
            );
        $_cms_block_result = json_decode($_cms_block_result, true);
        var_dump($_cms_block_result);
    }

    public function testStore(){
        $_store_view_result = magentoApiSync(
                session('soap'),
                'translator_getwebinfo.storeViewList',
                array()
            );
        $_store_view_result = json_decode($_store_view_result, true);
        var_dump($_store_view_result);
    }

    public function testWeb(){
        $_web_view_result = magentoApiSync(
                session('soap'),
                'translator_getwebinfo.list',
                array()
            );
        $_web_view_result = json_decode($_web_view_result, true);
        var_dump($_web_view_result);
    }
}