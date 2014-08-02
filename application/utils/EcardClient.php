<?php
require_once 'HttpClient.class.php';
require_once 'simple_html_dom.php';
/**
 * 校园卡客户端
 *
 * @author vincent
 *        
 */
class EcardClient {
	/**
	 * 校园卡服务器
	 *
	 * @var unknown
	 */
	private static $host = "ecard.scau.edu.cn";
	/**
	 * httpclient初始化
	 */
	private static function createClient() {
		$client = new HttpClient ( self::$host );
		$client->setPersistCookies ( true );
		$client->setHandleRedirects ( false );
		$client->setPersistReferers ( true );
		$client->cookie_host = self::$host;
		return $client;
	}
	/**
	 * 登录 登录成功则返回cookies
	 *
	 * @param unknown $account
	 * @param unknown $password
	 * @return multitype: boolean
	 */
	public static function signIn($account, $password) {
		$data = array (
				"name" => $account,
				"passwd" => $password,
				"userType" => "1",
				"loginType" => "2",
				"imageField.x" => "0",
				"imageField.y" => "0" 
		);
		$client = self::createClient ();
		$client->post ( "/loginstudent.action", $data );
		// 登录成功时 content-type为gbk 失败则为GBK
		$contentType = $client->getHeader ( "content-type" );
		if (strpos ( $contentType, "gbk" ) != false) {
			return $client->getCookies ();
		}
		return array ();
	}
	
	/**
	 * 获取基本信息
	 *
	 * @param unknown $cookies
	 * @return multitype:string NULL
	 */
	public static function getBasicInfo($cookies) {
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->get ( "/accountcardUser.action" );
		$html = $client->getContent ();
		// print_r($html);
		$result = array ();
		$dom = str_get_html ( $html );
		
		preg_match ( '/<td class="neiwen">[\d\.]*元/', $html, $matches );
		// 获取余额
		$balance = substr ( $matches [0], strpos ( $matches [0], ">" ) + 1, strlen ( $matches [0] ) - 1 );
		$result ["balance"] = $balance;
		// 获取姓名
		$namediv = $dom->find ( "td[width=25%] div[align=left]" );
		$result ["name"] = $namediv [0]->innertext;
		// 获取年级班级
		$gradediv = $dom->find ( "td[colspan=3] div[align=left]" );
		$result ["grade"] = $gradediv [0]->innertext;
		// 获取卡号
		$ciddiv = $dom->find ( "td[width=10%] div[align=left]" );
		$result ["cardId"] = $ciddiv [0]->innertext;
		
		return $result;
	}
	/**
	 * 获取卡号 卡号不是学号
	 *
	 * @param unknown $cookies
	 */
	private static function getCardId($cookies) {
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->get ( "/accounttodayTrjn.action" );
		
		$html = $client->getContent ();
		$dom = new simple_html_dom ();
		$dom->load ( $html );
		
		$options = $dom->find ( "option" );
		$account = $options [0];
		$dom->clear ();
		return $account->value;
	}
	
	/**
	 * 获取当日消费记录
	 *
	 * @param unknown $cookies
	 * @return multitype:multitype:NULL
	 */
	public static function getTodayRecord($cookies) {
		$data = array (
				"account" => self::getCardId ( $cookies ),
				"inputObject" => "all" 
		);
		$records = array ();
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->post ( "/accounttodatTrjnObject.action", $data );
		$html = iconv ( "gbk", "utf-8", $client->getContent () );
		$dom = new simple_html_dom ();
		$dom->load ( $html );
		$trs = $dom->find ( "tr[class*=listbg]" );
		foreach ( $trs as $tr ) {
			$record = array ();
			$record ["time"] = $tr->children ( 0 )->innertext;
			$record ["id"] = $tr->children ( 1 )->innertext;
			$record ["name"] = $tr->children ( 2 )->innertext;
			$record ["location"] = $tr->children ( 4 )->innertext;
			$record ["consume"] = $tr->children ( 6 )->innertext;
			$record ["balance"] = $tr->children ( 7 )->innertext;
			$records [] = $record;
		}
		$dom->clear ();
		return $records;
	}
	/**
	 * 获取历史消费记录 (3天内)
	 *
	 * @param unknown $cookies
	 */
	public static function getHistoryRecords($cookies, $from, $to) {
		$data = array (
				"inputStartDate" => $from,
				"inputEndDate" => $to 
		);
		$records = array ();
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->headers ["Referer"] = "http://ecard.scau.edu.cn/accounthisTrjn.action?__continue=c51bd570d74d0e94c33a0040e8193522";
		$client->post ( "/accounthisTrjn.action?__continue=d504b2b276d4de5143a3bb6ba06491f7", $data );
		$html = iconv ( "gbk", "utf-8", $client->getContent () );
		$dom = new simple_html_dom ();
		$dom->load ( $html );
		$trs = $dom->find ( "tr[class*=listbg]" );
		foreach ( $trs as $tr ) {
			$record = array ();
			$record ["time"] = $tr->children ( 0 )->innertext;
			$record ["id"] = $tr->children ( 1 )->innertext;
			$record ["name"] = $tr->children ( 2 )->innertext;
			$record ["location"] = $tr->children ( 4 )->innertext;
			$record ["consume"] = $tr->children ( 6 )->innertext;
			$record ["balance"] = $tr->children ( 7 )->innertext;
			$records [] = $record;
		}
		
		print_r ( $html );
		$dom->clear ();
		return $records;
	}
}

$client = new EcardClient ();
$cookies = $client->signIn ( '201230740605', '022971' );
print_r ( $client->getHistoryRecords ( $cookies, '20140701', '20140703' ) );
