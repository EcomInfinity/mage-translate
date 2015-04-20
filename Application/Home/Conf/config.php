<?php
return array(
	//'配置项'=>'配置值'
    'URL_ROUTER_ON' => true, // 是否开启URL路由
    'URL_ROUTE_RULES' => array(
        array('lang', 'Translation/index'),
        array('langlist', 'Translation/gets'),
        // array('langlist/:id\d','Translation/getList','',array('method'=>'get')),
        array('langexport', 'Translation/export'),
        array('langimport', 'Translation/import'),
        array('langadd', 'Translation/add'),
        array('langdel', 'Translation/del'),
        array('langinfo', 'Translation/load'),
        array('langedit', 'Translation/edit'),
        array('langdownload', 'Translation/download'),
        array('langsdel', 'Translation/dels'),
        array('setmodify','Translation/needUpdate'),

        array('langimgdel', 'Image/del'),
        array('langimgclear', 'Image/clear'),
        array('langimgadd', 'Image/add'),

        array('admin', 'Admin/index'),
        array('login', 'Admin/login'),
        array('register', 'Admin/register'),
        array('logout', 'Admin/logout'),
        
        array('useradd', 'User/add'),
        array('userinfo', 'User/load'),
        array('userlist', 'User/gets'),
        array('useredit', 'User/edit'),
        array('personal-setting', 'User/personalSetting'),
        array('rest-sync', 'User/restSync'),

        array('roleadd', 'Role/add'),
        array('roleinfo', 'Role/load'),
        array('rolelist', 'Role/gets'),
        array('roleedit', 'Role/edit'),
        
        array('rulelist', 'Rule/gets'),

        array('weblang', 'WebsiteLang/gets'),
        array('site-lang-add', 'WebsiteLang/add'),
        array('site-lang-del', 'WebsiteLang/del'),

        array('websiteinfo', 'Website/load'),
        array('save-name', 'Website/edit'),

        array('lang-info', 'Language/gets'),

        array('cms', 'MagentoCms/index'),
        array('page-list', 'MagentoCms/getPages'),
        array('page-info', 'MagentoCms/getPage'),
        array('page-save', 'MagentoCms/savePage'),
        array('block-list', 'MagentoCms/getBlocks'),
        array('block-info', 'MagentoCms/getBlock'),
        array('block-save', 'MagentoCms/saveBlock'),
        array('export-content', 'MagentoCms/exportTxt'),
        array('export-contents', 'MagentoCms/exportZip'),
        array('download-content', 'MagentoCms/downloadTxt'),
        array('download-contents', 'MagentoCms/downloadZip'),

        array('create-page', 'MagentoApi/syncTranslatePage'),
        array('sync-all-page', 'MagentoApi/syncMagentoPage'),
        array('sync-checked-page', 'MagentoApi/syncSelectPage'),
        array('create-block', 'MagentoApi/syncTranslateBlock'),
        array('sync-all-block', 'MagentoApi/syncMagentoBlock'),
        array('sync-checked-block', 'MagentoApi/syncSelectBlock'),

    ),
);