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
        array('roleinfo', 'Role/get'),
        array('rolelist', 'Role/gets'),
        array('roleedit', 'Role/edit'),
        
        array('rulelist', 'Rule/gets'),

        array('weblang', 'WebsiteLang/gets'),
        array('site-lang-add', 'WebsiteLang/add'),
        array('site-lang-del', 'WebsiteLang/del'),

        array('websiteinfo', 'Website/get'),
        array('lang-info', 'Website/langInfo'),
        array('save-name', 'Website/saveName'),

    ),
);