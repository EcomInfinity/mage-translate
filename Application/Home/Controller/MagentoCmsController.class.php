<?php
namespace Home\Controller;
use Think\Controller;
class MagentoCmsController extends BaseController {
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
                    // if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
                    //     $_cms_page_kind[$_identifier] = $_page['title'];
                    //     break;
                    // }
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
                    // if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
                    //     $_cms_page_kind[$_identifier] = $_page['title'];
                    //     break;
                    // }
                }
            }
        }
        // var_dump($_cms_page_list);
        $_page_translate_count = D('cms_translate')->total(
                array(
                        'website_id' => session('website_id'), 
                        'type' => 1
                    )
            );
        // $_cms_page_identifier = array_unique($_cms_page_identifier);
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

    // public function getStorePages(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     $_store_pages = D('cms_translate')->where(array('identifier' => $_params['identifier'], 'type' => 1))->field('id, title, identifier, store_view')->select();
    //     // var_dump($_store_pages);
    //     $this->ajaxReturn(
    //             array(
    //                 'success' => true,
    //                 'message' => '',
    //                 'data' => array(
    //                     'store_pages' => $_store_pages,
    //                     'total' => count($_store_pages)
    //                     ),
    //             ),
    //             'json'
    //         );
    // }

    public function getStorePage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_page = D('cms_translate')->find($_params['cms_id']);
        $_page_content = json_decode($_store_page['content'],true);
        $_store_page['content'] = $_page_content['content'];
        $_store_page['meta_keywords'] = $_page_content['meta_keywords'];
        $_store_page['meta_description'] = $_page_content['meta_description'];
        // var_dump($_store_page);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $_store_page,
                ),
                'json'
            );
    }

    public function saveStorePage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if(preg_match('/.*[^ ].*/', $_params['title']) == 0 || preg_match('/.*[^ ].*/', $_params['content']) == 0){
            $this->ajaxReturn(
                    array(
                        'success' => false,
                        'message' => 'Title and Content not all spaces or empty.',
                        'data' => array(),
                    ),
                    'json'
                );
        }else{
            $_page_translate = D('cms_translate')->find($_params['cms_id']);
            $_page_content = json_decode($_page_translate['content'], true);
            $_page_content['content'] = $_params['content'];
            // $_page_content['meta_keywords'] = $_params['meta_keywords'];
            // $_page_content['meta_description'] = $_params['meta_description'];
            $_page_save['content'] = json_encode($_page_content);
            $_page_save['title'] = $_params['title'];
            $_page_save['id'] = $_params['cms_id'];
            $_result = D('cms_translate')->save($_page_save);
            if($_result > 0){
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
                            'message' => 'Modify failure.',
                            'data' => array(),
                        ),
                        'json'
                    );
            }
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
                    // if($_block['identifier'] == $_identifier && strtolower($_block['simple_name']) == 'en_us'){
                    //     $_cms_block_kind[$_identifier] = $_block['title'];
                    //     break;
                    // }
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
                    // if($_block['identifier'] == $_identifier && strtolower($_block['simple_name']) == 'en_us'){
                    //     $_cms_block_kind[$_identifier] = $_block['title'];
                    //     break;
                    // }
                }
            }
        }
        $_block_translate_count = D('cms_translate')->total(
                array(
                        'website_id' => session('website_id'), 
                        'type' => 2
                    )
            );
        // foreach ($_block_translate_result as $k => $_block) {
        //     //所有identifier
        //     $_cms_block_identifier[] = $_block['identifier'];
        // }
        // $_cms_block_identifier = array_unique($_cms_block_identifier);
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

    // public function getStoreBlocks(){
    //     $_params = json_decode(file_get_contents("php://input"),true);
    //     $_store_blocks = D('cms_translate')->where(array('identifier' => $_params['identifier'], 'type' => 2))->field('id, title, identifier, store_view')->select();
    //     $this->ajaxReturn(
    //             array(
    //                 'success' => true,
    //                 'message' => '',
    //                 'data' => array(
    //                     'store_blocks' => $_store_blocks,
    //                     'total' => count($_store_blocks)
    //                     ),
    //             ),
    //             'json'
    //         );
    // }

    public function getStoreBlock(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_block = D('cms_translate')->find($_params['cms_id']);
        // $_page_content = json_decode($_store_page['content'],true);
        // $_store_page['content'] = $_page_content['content'];
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => $_store_block,
                ),
                'json'
            );
    }

    public function saveStoreBlock(){
        $_params = json_decode(file_get_contents("php://input"),true);
        if(preg_match('/.*[^ ].*/', $_params['title']) == 0 || preg_match('/.*[^ ].*/', $_params['content']) == 0){
            $this->ajaxReturn(
                    array(
                            'success' => false,
                            'message' => 'Title and Content not all spaces or empty.',
                            'data' => array()
                        ),
                    'json'
                );
        }else{
            $_block_save['content'] = $_params['content'];
            $_block_save['title'] = $_params['title'];
            $_block_save['id'] = $_params['cms_id'];
            $_result = D('cms_translate')->save($_block_save);
            if($_result > 0){
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
                            'message' => 'Modify failure.',
                            'data' => array(),
                        ),
                        'json'
                    );
            }
        }
    }

    public function cmsExport(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_cms_result = D('cms_translate')->find($_params['cms_id']);
        if($_cms_result['type'] == 1){
            $_content = json_decode($_cms_result['content'], true);
            S('data', $_content['content']);
        }else{
            S('data', $_cms_result['content']);
        }
        S('file_name',$_cms_result['store_view'].'-'.$_cms_result['identifier'].'-'.time());
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

    public function cmsExportZip(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_file_exit = false;
        foreach ($_params['page_ids'] as $val) {
            $_cms_result = D('cms_translate')->find($val);
            if($_cms_result){
                $_file_exit = true;
                $_cms_store_view = iconv(mb_detect_encoding($_cms_result['store_view'], array('ASCII','UTF-8','GB2312','GBK','BIG5')), "GBK" , $_cms_result['store_view']);
                $_file_name = $_cms_store_view.'-'.$_cms_result['identifier'].'-'.time().'.txt';
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
            // exportZip($_file_names, $_zip_name);
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
    public function test(){
        // exportTxt();
        // var_dump(session('soap'));
        // $_test = '             s';
        // if(preg_match('/.*[^ ].*/', $_test) == 0){
        //     echo '0';
        // }else{
        //     echo '1';
        // }
        // $_cms_save['title'] = '仁光堂 Ren Guang Do-uyuuyuyuy';
        // $_result = magentoApiSync(
        //         session('soap'),
        //         'info_cmspage.update',
        //         array('20000',$_cms_save)
        //     );
        // var_dump($_result);
        // $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->relation(true)->select();
        // foreach ($_page_translate_result as $k => $_page) {
        //     //所有identifier
        //     $_cms_page_identifier[] = $_page['identifier'];
        //     //所有language
        //     $_cms_page_language[] = $_page['lang_id'];
        //     $_page_content = json_decode($_page['content'], true);
        //     $_page_translate_result[$k]['content'] = $_page_content['content'];
        //     $_page_translate_result[$k]['meta_keywords'] = $_page_content['meta_keywords'];
        //     $_page_translate_result[$k]['meta_description'] = $_page_content['meta_description'];
        // }
        // $_cms_page_identifier = array_unique($_cms_page_identifier);
        // $_cms_page_language = array_unique($_cms_page_language);
        // foreach ($_cms_page_identifier as $_identifier) {
        //     foreach ($_page_translate_result as $k => $_page) {
        //         if($_page['identifier'] == $_identifier){
        //             $_cms_page[$_identifier][] = $_page;
        //         }
        //     }
        // }
        // foreach ($_cms_page_identifier as $_identifier) {
        //     foreach ($_page_translate_result as $k => $_page) {
        //         if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
        //             $_cms_page_kind[$_identifier] = $_page['title'];
        //             break;
        //         }
        //     }
        // }
        // $_website_language = D('website_lang')->where(array('website_id' => session('website_id')))->select();
        // // var_dump($_website_language);
        // // var_dump($_cms_page_language);
        // // var_dump($_cms_page_kind);
        // var_dump($_cms_page_identifier);
        // var_dump($_cms_page);
    }
}