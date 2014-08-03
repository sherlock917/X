<?php
require_once SYSTEM_PATH . '/common/functions.php'; // 框架自带函数库
require_once SYSTEM_PATH . '/util/StringUtil.php'; // 字符串工具类
require_once SYSTEM_PATH . '/core/Controller.php';
require_once SYSTEM_PATH . '/core/Model.php';
require_once SYSTEM_PATH . '/core/Router.php';
require_once SYSTEM_PATH . '/core/DB.php';

try {
	include_once SYSTEM_PATH . '/core/loadconfig.php'; // 加载应用配置
	                                                   
	// 解析url
	$router = new Router ( PATH_INFO );
	$router->parse (); // 解析url
	$result = $router->getResult ();
	
	// 获取controller名与action名
	$controllerName = $result ['controller_name'];
	$actionName = $result ['action_name'];
	
	// 实例化controller(单例模式)
	$controller = get_obj ( $controllerName, 'controllers' );
	
	// 调用controller的action进行逻辑处理
	$controller->$actionName ();
}
catch ( Exception $e ) {
	if (isset ( $GLOBALS ['app_config'] ['debug'] ) && $GLOBALS ['app_config'] ['debug'] == true) {
		to_custom_page ( 'error', $e->getMessage () );
	}
	else {
		send_http_status ( 404 );
	}
}
 
