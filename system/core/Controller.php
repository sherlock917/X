<?php
require_once SYSTEM_PATH . '/lib/smarty/libs/Smarty.class.php';
/**
 * http请求参数(get和post)
 *
 * @author vincent
 *        
 */
class Input {
	/**
	 * 请求中的所有参数
	 *
	 * @var array
	 */
	protected $params = array ();
	/**
	 * 构造函数
	 */
	function __construct() {
		$this->params = array_merge ( $_GET, $_POST ); // 合并get和post请求的参数
	}
	/**
	 * 获取参数的值
	 *
	 * @param string $name
	 * @return multitype:
	 */
	function get($name) {
		if (isset ( $this->params [$name] ))
			return $this->params [$name];
		return NULL;
	}
	/**
	 * 获取所有参数
	 *
	 * @return multitype:
	 */
	function getParams() {
		return $this->params;
	}
}
/**
 * 控制器类
 * 所有控制器都要继承该类
 *
 * @author vincent
 *        
 */
abstract class Controller {
	/**
	 * get、post请求的参数
	 *
	 * @var array
	 */
	protected $input;
	/**
	 * 构造函数
	 */
	function __construct() {
		$this->input = new Input ();
	}
	/**
	 * 析构函数
	 */
	function __destruct() {
	}
	/**
	 * 返回视图 视图使用smarty模板
	 *
	 * @param string $viewPath
	 * @param array $variables
	 */
	function render($viewName, $variables = array()) {
		$smarty_config = $GLOBALS ['smarty_config']; // 加载smarty配置
		$viewPath = APPLICATION_PATH . '/views/' . $viewName . '.' . $smarty_config ['suffix'];
		if (! file_exists ( $viewPath )) { // 若模板不存在
			send_http_status ( 404 ); // 返回404状态码
		}
		$smarty = new Smarty ();
		// 绑定参数
		foreach ( $variables as $key => $value )
			$smarty->assign ( $key, $value );
		$smarty->compile_dir = $smarty_config ['compile_dir'];
		$smarty->display ( $viewPath );
	}
	/**
	 * 返回视图 不使用Smarty模板 使用php
	 *
	 * @param string $viewName
	 * @param array $variables
	 */
	function easyRender($viewName, $variables = array()) {
		$viewPath = APPLICATION_PATH . '/views/' . $viewName . '.php';
		if (! file_exists ( $viewPath )) {
			send_http_status ( 404 );
		}
		extract ( $variables, EXTR_OVERWRITE );
		include $viewPath;
	}
	/**
	 * 返回文本视图
	 *
	 * @param string $text
	 */
	function renderText($text, $charset = 'utf-8') {
		header ( 'Content-type: text/plain; charset=' . $charset );
		echo $text;
		exit ();
	}
	
	/**
	 * 返回json视图
	 *
	 * @param array $arr
	 */
	function renderJSON($arr, $charset = 'utf-8') {
		header ( 'Content-type: text/json; charset=' . $charset );
		echo json_encode ( $arr );
		exit ();
	}
	/**
	 * 重定向
	 *
	 * @param string $url
	 * @param array $params
	 */
	function redirect($url, $params = array()) {
		if ($params)
			header ( 'Location:' . base_url () . '/' . $url . '?' . array_to_query_string ( $params, '&' ) );
		else
			header ( 'Location:' . base_url () . '/' . $url );
		exit ();
	}
	/**
	 * 返回html视图
	 *
	 * @param string $html
	 * @param string $charset
	 */
	function renderHTML($html, $charset = 'utf-8') {
		header ( 'Content-type: text/html; charset=' . $charset );
		echo $html;
		exit ();
	}
	
	/**
	 * 加载model
	 *
	 * @param string $modelName
	 * @return object
	 */
	function loadModel($modelName) {
		// 单例模式创建model
		return get_obj ( $modelName, 'models' );
	}
	
	/**
	 * 将请求转发给其他的controller处理
	 *
	 * @param string $controllerName
	 * @param string $actionName
	 */
	function forward($controllerName, $actionName) {
		// 单例模式创建controller
		$controller = get_obj ( $controllerName, 'controllers' );
		$controller->input = $this->input; // 将参数也传递过去
		$controller->$actionName ();
	}
	
	/**
	 * 调用不存在的方法时调用
	 *
	 * @param string $actionName
	 * @param array $params
	 * @throws Exception
	 */
	function __call($actionName, $params) {
		throw new Exception ( 'action ' . $actionName . '  is not exist' );
	}
}
