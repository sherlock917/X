<?php
/**
 * 框架自带的函数库
 *
 * @author vincent
 */

/**
 * 创建对象(单例模式)
 *
 * @param string $className
 * @param string $dirName
 */
function get_obj($className, $dirName) {
	$className = ucfirst ( $className ); // 类名首字母要大写
	static $objArray = array (); // 用于保存已创建对象的引用
	                             
	// 判断对象是否已经创建
	if (isset ( $objArray [$className] )) {
		return $objArray [$className];
	}
	
	// 只能创建application目录下定义的对象
	$filePath = APPLICATION_PATH . '/' . $dirName . '/' . $className . '.php';
	// 文件不存在则抛出异常
	if (! file_exists ( $filePath ))
		throw new Exception ( $filePath . ' is not exist' );
	
	include_once $filePath;
	$obj = new $className ();
	$objArray [$className] = $obj; // 保存引用
	return $obj;
}

/**
 * 加载配置文件
 *
 * @param string $configName
 */
function load_config($configName) {
	return include APPLICATION_PATH . '/config/' . $configName . '.php';
}

/**
 * 获取所有model的名字
 *
 * @return multitype:Ambigous <>
 */
function get_models() {
	// 读取models文件夹内所有model的名字
	$models = array ();
	$dir = opendir ( APPLICATION_PATH . '/models/' );
	while ( ($filename = readdir ( $dir )) !== false ) {
		if ($filename != "." && $filename != "..") {
			$modelName = explode ( '.', $filename );
			$models [] = $modelName [0];
		}
	}
	return $models;
}

/**
 * 获取应用目录内文件的绝对路径
 *
 * @param string $fileName
 * @return string
 */
function get_file_absolute_path($fileName) {
	return DOCUMENT_ROOT . '/' . $fileName;
}

/**
 * 获取一个model对应的table名
 *
 * @param string $modelName
 * @return string
 */
function get_table_name($modelName) {
	return strtolower ( $modelName ); // 大写转小写
}

/**
 * 返回基地址
 *
 * @return string
 */
function base_url() {
	return BASE_URL;
}
/**
 * 获取资源的路径
 *
 * @param string $resourcePath
 */
function resource_url($resourcePath) {
	return 'http://' . $_SERVER ['SERVER_NAME'] . ':' . $_SERVER ['SERVER_PORT'] . '/public/' . $resourcePath;
}

/**
 * 根据controller、method、参数获取其url
 *
 * @param string $controllerName
 * @param string $methodName
 * @param array $params
 */
function get_url($controllerName, $methodName, $params = array()) {
	$url = base_url () . '/' . lcfirst ( $controllerName ) . '/' . $methodName;
	if ($params) {
		$url .= '?' . array_to_query_string ( $params, '&' );
	}
	return $url;
}
/**
 * 返回a标签
 *
 * @param string $controllerName
 * @param string $methodName
 * @param array $params
 */
function html_tag_a($controllerName, $methodName, $hrefName, $params = array()) {
	$href = '<a href="%s">%s</a>';
	return sprintf ( $href, get_url ( $controllerName, $methodName, $params ), $hrefName );
}
/**
 * 数组转换成查询字符串
 *
 * @param array $arr
 * @param string $separator
 * @return string
 */
function array_to_query_string($arr, $separator) {
	$index = 0;
	$maxIndex = count ( $arr ) - 1;
	$str = '';
	foreach ( $arr as $key => $value ) {
		$str .= $key . '=';
		$str .= urlencode ( $value ); // 参数值要url编码
		if ($index != $maxIndex)
			$str .= $separator;
		$index ++;
	}
	return $str;
}
/**
 * 获取表单的action
 *
 * @param string $controllerName
 * @param string $methodName
 */
function form_action($controllerName, $methodName) {
	return base_url () . '/' . lcfirst ( $controllerName ) . '/' . $methodName; // controller名首字母变小写
}

/**
 * 跳转到自定义页面
 *
 * @param string $pageTitle
 * @param string $pageInfo
 */
function to_custom_page($pageTitle, $pageInfo) {
	include APPLICATION_PATH . '/views/custompage.php'; // 自定义页面
	exit ();
}
/**
 * 返回http响应
 *
 * @param int $code
 */
function send_http_status($code) {
	// 常见的http状态
	static $_status = array (
			// Success 2xx
			200 => 'OK',
			// Redirection 3xx
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ', // 1.1
			                             // Client Error 4xx
			400 => 'Bad Request',
			403 => 'Forbidden',
			404 => 'Not Found',
			// Server Error 5xx
			500 => 'Internal Server Error',
			503 => 'Service Unavailable' 
	);
	if (! isset ( $_status [$code] )) {
		throw new Exception ( 'the http code :' . $code . ' is not exist' );
	}
	header ( 'HTTP/1.1 ' . $code . ' ' . $_status [$code] );
	header ( 'Status:' . $code . ' ' . $_status [$code] );
	exit (); // 终止脚本的执行
}
/**
 * 重复某一个字符 构成字符串
 *
 * @param int $count
 * @param string $char
 * @param string $separator
 */
function char_repeat($count, $char, $separator) {
	$str = '';
	for($i = 1; $i <= $count; $i ++) {
		$str .= $char;
		if ($i != $count)
			$str .= ' ' . $separator . ' ';
	}
	return $str;
}
/**
 * 创建token令牌
 */
function create_token() {
	return md5 ( uniqid ( rand () ) );
}
