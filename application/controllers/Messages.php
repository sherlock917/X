<?php
/**
 * 留言控制器
 *
 * @author vincent
 *        
 */
class Messages extends Controller {
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * 保存用户留言
	 */
	function save() {
		$messageText = $this->input->get ( 'messagetext' );
		$messageImage = $this->input->get ( 'messageimage' );
		
		if (! isset ( $_SESSION ['id'] )) {
			$this->renderJSON ( array (
					'status' => 205,
					'msg' => 'please login' 
			) );
		}
		
		$messageModel = $this->loadModel ( 'Message' );
		$isok = $messageModel->save ( array (
				'id' => StringUtil::uuid (),
				'message_text' => $messageText,
				'message_image' => $messageImage,
				'shop_id' => $_SESSION ['id'],
				'create_time' => time () 
		) );
		if ($isok) {
			$this->renderJSON ( array (
					'status' => 200,
					'msg' => 'ok' 
			) );
		}
		$this->render ( array (
				'status' => - 1,
				'msg' => 'unknown error' 
		) );
	}
	/**
	 * 显示留言
	 */
	function showMessages() {
		if (! isset ( $_SESSION ['id'] ))
			$this->renderJSON ( array (
					'status' => 205,
					'msg' => 'please login' 
			) );
		$page = $this->input->get ( 'page' );
		$page = empty ( $page ) ? 1 : $page;
		
		$messageModel = $this->loadModel ( 'Message' );
		$messageList = $messageModel->getPage ( array (
				'shop_id' => $_SESSION ['id'] 
		), $page, 10, array (
				'create_time' 
		), TRUE );
		
		$this->renderJSON ( array (
				'status' => 200,
				'msg' => 'ok',
				'data' => $messageList 
		) );
	}
}

