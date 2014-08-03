<?php
/**
 * 商家控制器
 *
 * @author vincent
 *        
 */
class Shops extends Controller {
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	/**
	 * 默认action
	 */
	function index() {
		$this->signUp ();
	}
	/**
	 * 商家注册
	 */
	function signUp() {
		$account = $this->input->get ( 'account' );
		$password = $this->input->get ( 'password' );
		// 判断参数是否齐全
		if (empty ( $account ) || empty ( $password ))
			$this->renderJSON ( array (
					'status' => 201,
					'msg' => 'no account or password' 
			) );
		
		$shopModel = $this->loadModel ( 'Shop' );
		$isexist = $shopModel->find ( array (
				'account' => $account 
		) );
		// 判断帐号是否已经注册
		if ($isexist) {
			$this->renderJSON ( array (
					'status' => 202,
					'msg' => 'the account is exist' 
			) );
		}
		// 保存商家信息
		$id = StringUtil::uuid ();
		$shopModel->save ( array (
				'id' => $id,
				'account' => $account,
				'password' => $password,
				'create_time' => time () 
		) );
		
		$this->renderJSON ( array (
				'status' => 200,
				'msg' => 'ok',
				'data' => array (
						'id' => $id 
				) 
		) );
	}
	/**
	 * 商家登录
	 */
	function signIn() {
		$account = $this->input->get ( 'account' );
		$password = $this->input->get ( 'password' );
		// 判断参数是否齐全
		if (empty ( $account ) || empty ( $password ))
			$this->renderJSON ( array (
					'status' => 201,
					'msg' => 'no account or password' 
			) );
			// 验证帐号密码
		$shopModel = $this->loadModel ( 'Shop' );
		$user = $shopModel->find ( array (
				'account' => $account,
				'password' => $password 
		) );
		
		if (empty ( $user )) {
			$this->renderJSON ( array (
					'status' => 203,
					'msg' => 'account or password wrong' 
			) );
		}
		$_SESSION ['id'] = $user [0] ['id'];
		$_SESSION ['account'] = $user [0] ['account'];
		$this->renderJSON ( array (
				'status' => 200,
				'msg' => 'ok' 
		) );
	}
	function test() {
		print_r ( $_SESSION );
	}
}