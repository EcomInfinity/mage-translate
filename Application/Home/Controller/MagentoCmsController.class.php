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
            $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1, 'identifier' => array('like', '%'.$_params['page_search'].'%')))->relation(true)->select();
            foreach ($_page_translate_result as $k => $_page) {
                //所有identifier
                $_cms_page_identifier[] = $_page['identifier'];
            }
            $_cms_page_identifier = array_unique($_cms_page_identifier);
            foreach ($_cms_page_identifier as $_identifier) {
                foreach ($_page_translate_result as $k => $_page) {
                    if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
                        $_cms_page_kind[$_identifier] = $_page['title'];
                        break;
                    }
                }
            }
        }else{
            $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->relation(true)->select();
            foreach ($_page_translate_result as $k => $_page) {
                //所有identifier
                $_cms_page_identifier[] = $_page['identifier'];
            }
            $_cms_page_identifier = array_unique($_cms_page_identifier);
            foreach ($_cms_page_identifier as $_identifier) {
                foreach ($_page_translate_result as $k => $_page) {
                    if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
                        $_cms_page_kind[$_identifier] = $_page['title'];

                        break;
                    }
                }
            }
        }
        $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->relation(true)->select();
        foreach ($_page_translate_result as $k => $_page) {
            //所有identifier
            $_cms_page_identifier[] = $_page['identifier'];
        }
        $_cms_page_identifier = array_unique($_cms_page_identifier);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(
                        'kind' => $_cms_page_kind,
                        'count' => count($_cms_page_kind),
                        'total' => count($_cms_page_identifier)
                        ),
                ),
                'json'
            );
    }

    public function getStorePages(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_pages = D('cms_translate')->where(array('identifier' => $_params['identifier'], 'type' => 1))->field('id, title, identifier, store_view')->select();
        // var_dump($_store_pages);
        $this->ajaxReturn(
                array(
                    'success' => true,
                    'message' => '',
                    'data' => array(
                        'store_pages' => $_store_pages,
                        'total' => count($_store_pages)
                        ),
                ),
                'json'
            );
    }

    public function getStorePage(){
        $_params = json_decode(file_get_contents("php://input"),true);
        $_store_page = D('cms_translate')->find($_params['cms_id']);
        $_page_content = json_decode($_store_page['content'],true);
        $_store_page['content'] = $_page_content['content'];
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
        $_page_translate = D('cms_translate')->find($_params['cms_id']);
        $_page_content = json_decode($_page_translate['content'], true);
        $_page_content['content'] = $_params['content'];
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
    public function test(){
        // $_cms_save['title'] = 'test12323233';
        // magentoApiSync(
        //         session('soap'),
        //         'info_cmspage.update',
        //         array('717',$_cms_save)
        //     );
        // var_dump(session('soap'));
        $_page_translate_result = D('cms_translate')->where(array('website_id' => session('website_id'), 'type' => 1))->relation(true)->select();
        foreach ($_page_translate_result as $k => $_page) {
            //所有identifier
            $_cms_page_identifier[] = $_page['identifier'];
            //所有language
            $_cms_page_language[] = $_page['lang_id'];
            $_page_content = json_decode($_page['content'], true);
            $_page_translate_result[$k]['content'] = $_page_content['content'];
            $_page_translate_result[$k]['meta_keywords'] = $_page_content['meta_keywords'];
            $_page_translate_result[$k]['meta_description'] = $_page_content['meta_description'];
        }
        $_cms_page_identifier = array_unique($_cms_page_identifier);
        $_cms_page_language = array_unique($_cms_page_language);
        foreach ($_cms_page_identifier as $_identifier) {
            foreach ($_page_translate_result as $k => $_page) {
                if($_page['identifier'] == $_identifier){
                    $_cms_page[$_identifier][] = $_page;
                }
            }
        }
        foreach ($_cms_page_identifier as $_identifier) {
            foreach ($_page_translate_result as $k => $_page) {
                if($_page['identifier'] == $_identifier && strtolower($_page['simple_name']) == 'en_us'){
                    $_cms_page_kind[$_identifier] = $_page['title'];
                    break;
                }
            }
        }
        $_website_language = D('website_lang')->where(array('website_id' => session('website_id')))->select();
        // var_dump($_website_language);
        // var_dump($_cms_page_language);
        // var_dump($_cms_page_kind);
        var_dump($_cms_page_identifier);
        // var_dump($_cms_page);
    }
}