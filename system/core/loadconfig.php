<?php
/**
 * 加载各种配置
 */

// 加载各种配置文件 并保存到全局变量
$GLOBALS ['app_config'] = load_config ( 'app_config' ); // 应用配置文件
$GLOBALS ['db_config'] = load_config ( 'db_config' ); // 数据库配置文件
$GLOBALS ['router_config'] = load_config ( 'router_config' ); // 路由器配置文件
$GLOBALS ['smarty_config'] = load_config ( 'smarty_config' ); // smarty模板配置文件
                                                              
// 是否开启调试模式
if (isset ( $GLOBALS ['app_config'] ['debug'] ) && $GLOBALS ['app_config'] ['debug'] == true) {
	error_reporting ( E_ALL ); // 报告所有错误
	ini_set ( 'display_errors', 1 ); // 显示错误
}
else {
	error_reporting ( 0 );
	ini_set ( 'display_errors', 0 ); // 不显示错误
}

// 开启自动开启session
if (isset ( $GLOBALS ['app_config'] ['session_auto_start'] ) && $GLOBALS ['app_config'] ['session_auto_start'] == true) {
	session_start ();
}

// 是否自动新建数据表
if (isset ( $GLOBALS ['app_config'] ['table_auto_create'] ) && $GLOBALS ['app_config'] ['table_auto_create'] == true) {
	include_once SYSTEM_PATH . '/core/DB.php';
	// 判断数据库记录文件是否已创建
	if (! file_exists ( 'db.log' )) {
		// 记录下数据表的创建时间
		$log = '数据库初始化时间：' . date ( 'Y-m-d H:i:s' ) . "\n";
		foreach ( get_models () as $model ) {
			if (file_exists ( APPLICATION_PATH . '/models/' . $model . '.php' )) {
				include_once APPLICATION_PATH . '/models/' . $model . '.php';
				DB::dropTable ( $model ); // 先删除之前存在的表
				DB::createTable ( $model ); // 创建数据表
				$log .= '创建table：' . get_table_name ( $model ) . "\n";
			}
		}
		// 应用目录要有写入权限
		$dbLogFile = fopen ( 'db.log', 'a' ); // 创建db.log 表示数据库初始化完成
		fwrite ( $dbLogFile, $log );
		fclose ( $dbLogFile );
		to_custom_page ( 'index', '欢迎使用框架easyphp! 数据库初始化已完成' );
	}
}