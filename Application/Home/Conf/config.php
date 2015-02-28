<?php
return array(
	//'配置项'=>'配置值'
    'URL_ROUTER_ON' => true, // 是否开启URL路由
    'URL_ROUTE_RULES' => array(
        array('lang','Translation/index'),
        array('langlist','Translation/gets'),
        // array('langlist/:id\d','Translation/getList','',array('method'=>'get')),
        array('langexport','Translation/export'),
        array('langimport','Translation/import'),
        array('langadd','Translation/add'),
        array('langdel','Translation/del'),
        array('langinfo','Translation/get'),
        array('langedit','Translation/edit'),
        array('langdownload','Translation/download'),

        array('langimgdel','Image/del'),
        array('langimgclear','Image/clear'),
        array('langimgadd','Image/add'),

        array('admin','Admin/index'),
        array('login','Admin/login'),
        array('register','Admin/register'),
        array('logout','Admin/logout'),
        
        array('useradd','User/add'),
        array('userinfo','User/get'),
        array('userlist','User/gets'),
        array('useredit','User/edit'),
        array('change-password','User/changePassword'),

        array('roleadd','Role/add'),
        array('roleinfo','Role/get'),
        array('rolelist','Role/gets'),
        array('roleedit','Role/edit'),
        
        array('rulelist','Rule/gets'),
    ),
);