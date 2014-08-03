<?php
class Homes extends Controller {
	function __construct() {
		parent::__construct ();
	}
	function index() {
		$this->render ( 'index' );
	}
	function home() {
		$this->render ( 'home' );
	}
}