<?php
namespace Home\Controller;
use Think\Controller;
class MagentoCmsController extends PermissionController {
    public function index(){
        $this->display();
    }
    public function getPages(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if(!empty($_params['page_search'])){
            $_page_translate_result = D('cms_translate')->gets(
                    array(
                            'website_id' => session('website_id'), 
                            'type' => 1, 
                            'identifier' => array('like', '%'.$_params['page_search'].'%')
                        )
                );
            foreach ($_page_translate_result as $k => $_page) {
                $_cms_page_identifier[] = $_page['identifier'];
            }
            $_cms_page_identifier = array_unique($_cms_page_identifier);
            foreach ($_cms_page_identifier as $_identifier) {
                foreach ($_page_translate_result as $_page) {
                    if($_page['identifier'] == $_identifier){
                        $_cms_page_list[] = $_page;
                    }
                }
            }
        }else{
            $_page_translate_result = D('cms_translate')->gets(
                    array(
                            'website_id' => session('website_id'), 
                            'type' => 1
                        )
                );
            foreach ($_page_translate_result as $k => $_page) {
                //所有identifier
                $_cms_page_identifier[] = $_page['identifier'];
            }
            $_cms_page_identifier = array_unique($_cms_page_identifier);
            foreach ($_cms_page_identifier as $_identifier) {
                foreach ($_page_translate_result as $_page) {
                    if($_page['identifier'] == $_identifier){
                        $_cms_page_list[] = $_page;
                    }
                }
            }
        }
        $_page_translate_count = D('cms_translate')->total(
                array(
                        'website_id' => session('website_id'), 
                        'type' => 1
                    )
            );
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(
                        'list' => $_cms_page_list,
                        'count' => count($_cms_page_list),
                        'total' => $_page_translate_count
                        ),
                ),
                'json'
            );
    }

    public function getPage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_page = D('cms_translate')
                        ->get(
                                array('id' => $_params['cms_id']),
                                ''
                            );
        $_page_content = json_decode($_store_page['content'],true);
        $_store_page['content'] = $_page_content['content'];
        $_store_page['meta_keywords'] = $_page_content['meta_keywords'];
        $_store_page['meta_description'] = $_page_content['meta_description'];
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $_store_page,
                ),
                'json'
            );
    }

    public function savePage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_page_translate = D('cms_translate')
                            ->get(
                                    array('id' => $_params['cms_id']),
                                    ''
                                );
        $_page_content = json_decode($_page_translate['content'], true);
        $_page_content['content'] = $_params['content'];
        $_page_save['content'] = json_encode($_page_content);
        $_page_save['page_content'] = $_params['content'];
        $_page_save['title'] = $_params['title'];
        $_page_save['id'] = $_params['cms_id'];
        $_result = D('cms_translate')
                    ->saveCms(
                            array(),
                            $_page_save
                        );
        if($_result === true){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => $_result,
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function getBlocks(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if(!empty($_params['block_search'])){
            $_block_translate_result = D('cms_translate')->gets(
                    array(
                            'website_id' => session('website_id'), 
                            'type' => 2, 
                            'identifier' => array('like', '%'.$_params['block_search'].'%')
                        )
                );
            foreach ($_block_translate_result as $k => $_block) {
                //所有identifier
                $_cms_block_identifier[] = $_block['identifier'];
            }
            $_cms_block_identifier = array_unique($_cms_block_identifier);
            foreach ($_cms_block_identifier as $_identifier) {
                foreach ($_block_translate_result as $k => $_block) {
                    if($_block['identifier'] == $_identifier){
                        $_cms_block_list[] = $_block;
                    }
                }
            }
        }else{
            $_block_translate_result = D('cms_translate')->gets(
                    array(
                            'website_id' => session('website_id'), 
                            'type' => 2
                        )
                );
            foreach ($_block_translate_result as $k => $_block) {
                //所有identifier
                $_cms_block_identifier[] = $_block['identifier'];
            }
            $_cms_block_identifier = array_unique($_cms_block_identifier);
            foreach ($_cms_block_identifier as $_identifier) {
                foreach ($_block_translate_result as $k => $_block) {
                    if($_block['identifier'] == $_identifier){
                        $_cms_block_list[] = $_block;
                    }
                }
            }
        }
        $_block_translate_count = D('cms_translate')->total(
                array(
                        'website_id' => session('website_id'), 
                        'type' => 2
                    )
            );
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(
                        'list' => $_cms_block_list,
                        'count' => count($_cms_block_list),
                        'total' => $_block_translate_count
                        ),
                ),
                'json'
            );
    }

    public function getBlock(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_block = D('cms_translate')
                        ->get(
                                array('id' => $_params['cms_id']),
                                ''
                            );
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $_store_block,
                ),
                'json'
            );
    }

    public function saveBlock(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_block_save['content'] = $_params['content'];
        $_block_save['title'] = $_params['title'];
        $_block_save['id'] = $_params['cms_id'];
        $_result = D('cms_translate')
                    ->saveCms(
                            array(),
                            $_block_save
                        );
        if($_result === true){
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => $_result,
                        'data' => array(),
                    ),
                    'json'
                );
        }
    }

    public function exportTxt(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_cms_result = D('cms_translate')
                        ->get(
                                array('id' => $_params['cms_id']),
                                ''
                            );
        if($_cms_result['type'] == 1){
            $_content = json_decode($_cms_result['content'], true);
            S('data', $_content['content']);
        }else{
            S('data', $_cms_result['content']);
        }
        S('file_name',str_replace(" ", "", $_cms_result['store_view']).'-'.str_replace(" ","", $_cms_result['identifier']).'-'.time());
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(),
                ),
                'json'
        );
    }
    public function downloadTxt(){
        exportTxt(S('data'), S('file_name'));
        S('data', null);
        S('file_name', null);
    }

    public function exportZip(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_file_exit = false;
        foreach ($_params['page_ids'] as $val) {
            $_cms_result = D('cms_translate')
                            ->get(
                                    array('id' => $val),
                                    ''
                                );
            if($_cms_result){
                $_file_exit = true;
                $_cms_store_view = iconv(mb_detect_encoding($_cms_result['store_view'], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "GBK" , $_cms_result['store_view']);
                $_file_name = str_replace(" ", "", $_cms_store_view).'-'.str_replace(" ", "", $_cms_result['identifier']).'-'.time().'.txt';
                $_file_path = './Uploads/cms/'.$_file_name;
                $_file = fopen($_file_path, "w");
                if($_params['type'] == 1){
                    $_cms_content = json_decode($_cms_result['content'], true);
                    $_txt_content = $_cms_content['content'];
                }else{
                    $_txt_content = $_cms_result['content'];
                }
                fwrite($_file, $_txt_content);
                fclose($_file);
                $_file_names[] = $_file_name;
            }
        }
        if($_params['type'] == 1){
            $_zip_name = 'page'.'-'.time().'.zip';
        }else{
            $_zip_name = 'block'.'-'.time().'.zip';
        }
        if($_file_exit === true){
            S('file_names', $_file_names);
            S('zip_name', $_zip_name);
            $this->ajaxReturn(
                    array(
                        'success' => true,
                        'message' => '',
                        'data' => array(),
                    ),
                    'json'
            );
        }else {
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Download Failure.',
                        'data' => array(),
                    ),
                    'json'
            );
        }
    }
    public function downloadZip(){
        exportZip(S('file_names'), S('zip_name'));
        S('file_names', null);
        S('zip_name', null);
    }
}