<?php
/**
 * easyphp框架
 * 
 * @version 1.0
 * @copyright Copyright (c) 2014 HCI@Vincent Chan
 */
define ( INDEX_PATH, $_SERVER ['SCRIPT_FILENAME'] ); // index.php的真实路径
define ( DOCUMENT_ROOT, $_SERVER ['DOCUMENT_ROOT'] ); // 应用的目录路径
define ( PATH_INFO, isset ( $_SERVER ['PATH_INFO'] ) ? $_SERVER ['PATH_INFO'] : NULL ); // url中index.php后面的路径(不包括queryString) 服务器需要开启PATH_INFO模式
define ( APPLICATION_PATH, 'application' ); // application文件夹路径
define ( SYSTEM_PATH, 'system' ); // system文件夹路径
define ( PUBLIC_PATH, 'public' ); // public文件夹路径
define ( REQUEST_URL, $_SERVER ['REQUEST_URI'] ); // 用户请求的url
define ( BASE_URL, 'http://' . $_SERVER ['SERVER_NAME'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['SCRIPT_NAME'] );

require_once SYSTEM_PATH . '/core/easyphp.php';