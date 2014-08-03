<?php
/**
 * 用户模型
 *
 * @author vincent
 *        
 */
class Shop extends Model {
	
	/**
	 * #varchar(20)#primary key#
	 *
	 * @var string
	 */
	private $id;
	/**
	 * #varchar(20)#unique#not null#
	 *
	 * @var string
	 */
	private $account;
	
	/**
	 * #varchar(20)#not null#
	 *
	 * @var string
	 */
	private $password;
	
	/**
	 * #varchar(20)#
	 *
	 * @var string 省份
	 */
	private $province;
	
	/**
	 * #varchar(20)#
	 *
	 * @var string 市区
	 */
	private $city;
	
	/**
	 * #varchar(20)#
	 *
	 * @var string 经度
	 */
	private $longitude;
	
	/**
	 * #varchar(20)#
	 *
	 * @var string 纬度
	 */
	private $latitude;
	/**
	 * #varchar(20)#
	 *
	 * @var string
	 */
	private $create_time;
	
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	/**
	 * 根据帐号查找用户
	 *
	 * @param string $account
	 * @return multitype:
	 */
	function findByAccount($account) {
		$rs = $this->find ( array (
				'account' => $account 
		) );
		if ($rs)
			return $rs [0];
		return NULL;
	}
}