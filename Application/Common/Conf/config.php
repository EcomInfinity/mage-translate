<?php
return array(
    //'配置项'=>'配置值'
    'DEFAULT_MODULE' => '2',
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'redesign',
    'DB_USER' => 'root',
    'DB_PWD' => '123456',
    'DB_PREFIX' =>'rs_',
    // 'SHOW_PAGE_TRACE'=>True,
    // 'DB_SQL_LOG' => true,
    // 'MODULE_ALLOW_LIST'    =>    array('Home'),
    // 'DEFAULT_MODULE' => 'Home',
    'SESSION_OPTIONS' => array(
    'expire' => '10',
    ),
    'TMPL_PARSE_STRING' => array(
    '__UPLOADS__' => __ROOT__.'/Uploads',
    ),

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

        array('admin','Admin/index'),
        array('login','Admin/login'),
        array('register','Admin/register'),
        array('logout','Admin/logout'),
        array('useradd','Admin/userAdd'),
        array('userlist','Admin/userList'),
        array('useredit','Admin/userEdit'),
        array('userallow','Admin/userAllow'),
        array('userinfo','Admin/userInfo'),

        array('roleadd','Admin/roleAdd'),
        array('rolelist','Admin/roleList'),
        array('roleinfo','Admin/roleInfo'),
        array('roleedit','Admin/roleEdit'),
        
        array('rulelist','Admin/ruleList'),
    ),
);