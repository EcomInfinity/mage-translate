<?php
return array(
	//'配置项'=>'配置值'
    'URL_ROUTER_ON' => true, // 是否开启URL路由
    'URL_ROUTE_RULES' => array(
        array('lang','Translation/index'),
        array('langlist','Translation/getList'),
        // array('langlist/:id\d','Translation/getList','',array('method'=>'get')),
        array('langexport','Translation/export'),
        array('langimport','Translation/import'),
        array('langadd','Translation/add'),
        array('langdel','Translation/del'),
        array('langinfo','Translation/getInfo'),
        array('langedit','Translation/editInfo'),
        array('langimgdel','Translation/imageDel'),
        array('langimgclear','Translation/imageClear'),
        array('langimgadd','Translation/imageAdd'),
        array('langimg','Translation/getImage'),
        array('langdownload','Translation/download'),

        array('admin','Admin/index'),
        array('login','Admin/login'),
        array('register','Admin/register'),
        array('logout','Admin/logout'),
        array('useradd','Admin/userAdd'),
        array('userlist','Admin/userList'),
        array('useredit','Admin/userEdit'),
        array('userallow','Admin/userAllow'),
        array('userinfo','Admin/userInfo'),
        array('centeredit','Admin/centerEdit'),

        array('roleadd','Role/add'),
        array('roleinfo','Role/get'),
        array('rolelist','Role/gets'),
        array('roleedit','Role/edit'),
        
        array('rulelist','Rule/gets'),
    ),
);