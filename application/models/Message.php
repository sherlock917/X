<?php
/**
 * 留言模型
 *
 * @author vincent
 *        
 */
class Message extends Model {
	/**
	 * #varchar(20)#primary key#
	 *
	 * @var string 留言id
	 */
	private $id;
	/**
	 * #text#
	 *
	 * @var string 留言者姓名
	 */
	private $name;
	/**
	 * #text#
	 *
	 * @var string 文字留言
	 */
	private $message_text;
	/**
	 * #text#
	 *
	 * @var string 图片留言
	 */
	private $message_image;
	/**
	 * #varchar(20)#
	 *
	 * @var string 商家id
	 */
	private $shop_id;
	/**
	 * #varchar(20)#
	 *
	 * @var string 创建时间
	 */
	private $create_time;
	function __construct() {
		parent::__construct ();
	}
}