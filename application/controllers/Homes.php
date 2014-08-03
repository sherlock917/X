<?php
class Homes extends Controller {
	function __construct() {
		parent::__construct ();
	}
	function index() {
		$this->home ();
	}
	function home() {
		$page = $this->input->get ( 'page' );
		$page = empty ( $page ) ? 1 : $page;
		
		$messageModel = $this->loadModel ( 'Message' );
		$data ['messageList'] = $messageModel->getPage ( array (), 
				// 'shop_id' => $_SESSION ['id']
				$page, 40, array (
						'create_time' 
				), TRUE );
		
		$this->render ( 'home', $data );
	}
}