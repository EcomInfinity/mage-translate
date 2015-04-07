<?php
return array(
    //'配置项'=>'配置值'
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
    'MODULE_ALLOW_LIST'    =>    array('Home'),
    // 'DEFAULT_MODULE' => 'Home',
    'SESSION_OPTIONS' => array(
        'expire' => '10',
    ),
    'TMPL_PARSE_STRING' => array(
        '__UPLOADS__' => __ROOT__.'/Uploads',
    ),
    'REQUEST_VARS_FILTER' => true,
);