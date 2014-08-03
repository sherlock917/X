<?php
/**
 * 路由器类
 * 用于解析用户的请求
 * 
 * @author vincent
 *        
 */
class Router {
	/**
	 * index.php后的路径
	 *
	 * @var unknown
	 */
	private $pathInfo;
	/**
	 * 路由结果
	 *
	 * @var array
	 */
	private $result = array ();
	/**
	 * 构造函数
	 *
	 * @param string $pathInfo
	 */
	function __construct($pathInfo) {
		$router_config = $GLOBALS ['router_config'];
		$this->pathInfo = $pathInfo;
		$this->result ['controller_name'] = $router_config ['default_controller'];
		$this->result ['action_name'] = $router_config ['default_action'];
	}
	/**
	 * 获取路由结果
	 *
	 * @return multitype:
	 */
	function getResult() {
		return $this->result;
	}
	/**
	 * 解析字符串查询串
	 */
	function parse() {
		// pathInfo为空则使用默认路由
		if (! $this->pathInfo || $this->pathInfo == '/')
			return;
			
			// 分割pathInfo
		$infos = explode ( '/', $this->pathInfo );
		
		$controllerName = NULL;
		$actionName = NULL;
		// 提取controllerName 和 actionName
		foreach ( $infos as $info ) {
			if ($info && ! isset ( $controllerName ))
				$controllerName = $info;
			
			else if (isset ( $controllerName ) && $info) {
				$actionName = $info;
				break;
			}
		}
		if (isset ( $controllerName ))
			$this->result ['controller_name'] = ucfirst ( $controllerName ); // controller名首字母变大写
		
		if (isset ( $actionName ))
			$this->result ['action_name'] = $actionName;
		
		return;
	}
}