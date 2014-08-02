<?php
require_once 'HttpClient.class.php';
require_once 'simple_html_dom.php';
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
 * 正方系统客户端
 *
 * @author vincent
 *        
 */
class ZFClient {
	/**
	 * 正方服务器的所有主机
	 *
	 * @var unknown
	 */
	private static $host = '202.116.160.170';
	/**
	 * 构造函数
	 */
	private function __construct() {
	}
	
	/**
	 * httpclient初始化
	 */
	private static function createClient() {
		$client = new HttpClient ( self::$host );
		$client->setPersistCookies ( true );
		$client->setPersistReferers ( true );
		$client->setHandleRedirects ( false );
		return $client;
	}
	/**
	 * 登录
	 *
	 * @param string $studentnumber
	 * @param string $password
	 */
	public static function signIn($studentnumber, $password) {
		// __viewstate不是固定的
		$client = self::createClient ();
		$client->get ( '/default_ysdx.aspx' );
		$dom = new simple_html_dom ( $client->getContent () );
		$queryStr = 'RadioButtonList1=' . urlencode ( '学生' );
		$queryStr .= '&Button1=' . urlencode ( '登录' );
		$queryStr .= '&TextBox1=' . $studentnumber;
		$queryStr .= '&TextBox2=' . $password;
		$queryStr .= '&__VIEWSTATE=' . urlencode ( $dom->find ( 'input[name=__VIEWSTATE]' )[0]->value );
		// 无验证码登录入口
		$client->get ( '/default_ysdx.aspx?' . $queryStr );
		
		// 返回的状态码不是200 登录失败
		if ($client->getStatus () != 200)
			return array ();
			// 只能通过判断页面内容去判断是否登录成功
		$content = $client->getContent ();
		if (! strstr ( $content, "window.open('xs_main.aspx?" )) {
			return array ();
		}
		
		// 获取cookies
		$cookies = $client->getHeader ( 'set-cookie' );
		preg_match ( '/([^=]+)=([^;]+);/', $cookies, $matches );
		$cookies = array (
				$matches [1] => $matches [2] 
		);
		return $cookies;
	}
	/**
	 * 获取课程表
	 */
	public static function getTimeTable($studentnumber, $cookies) {
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->get ( '/xskbcx.aspx?xh=' . $studentnumber . '&gnmkdm=N121603' );
		$dom = new simple_html_dom ( $client->getContent () );
		// 获取课程表的table
		$table = $dom->getElementById ( 'Table1' );
		$tds = $table->find ( 'td[align=Center]' );
		$timeTable = array ();
		
		foreach ( $tds as $td ) {
			if (! $td->rowspan)
				continue;
			
			$plaintext = $td->plaintext;
			$values = explode ( "\n", $plaintext );
			
			$curriculum ['class_name'] = $values [0];
			$curriculum ['time'] = $values [1];
			$curriculum ['teacher_name'] = $values [2];
			$curriculum ['class_room'] = $values [3];
			
			// 有部分课程上课时间不连续
			switch (count ( $values )) {
				case 9 :
					$curriculum ['time'] .= ' ' . $values [6];
					break;
				
				case 13 :
					$curriculum ['time'] .= ' ' . $values [8];
					break;
				
				default :
					break;
			}
			
			// print_r ( $values );
			$timeTable [] = $curriculum;
		}
		
		// print_r ( $timeTable );
		return $timeTable;
	}
	/**
	 * 获取成绩单
	 *
	 * @param unknown $studentnumber
	 * @param unknown $cookies
	 */
	public static function getReportCard($studentnumber, $cookies) {
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->get ( '/xscjcx.aspx?xh=' . $studentnumber . '&gnmkdm=N121605' );
		$dom = new simple_html_dom ( $client->getContent () );
		
		$data ['xh'] = $studentnumber;
		$data ['gnmkdm'] = 'N121605';
		$data ['hidLanguage'] = '';
		$data ['ddlXN'] = '2013-2014';
		$data ['ddlXQ'] = 2;
		$data ['ddl_kcxz'] = '';
		$data ['btn_xq'] = '学期成绩';
		$data ['__EVENTTARGET'] = '';
		$data ['__EVENTARGUMENT'] = '';
		$data ['__VIEWSTATE'] = $dom->find ( 'input[name=__VIEWSTATE]' )[0]->value;
		print_r ( $data );
		
		$client->get ( '/xscjcx.aspx?' . array_to_query_string ( $data, '&' ) );
		$dom = new simple_html_dom ( $client->getContent () );
		
		$table = $dom->getElementById ( 'Datagrid1' );
		$trs = $table->getElementsByTagName ( 'tr' );
		$reportCard = array ();
		for($i = 1; $i < count ( $trs ); $i ++) {
			$reportCardItem = array ();
			$tds = $trs [$i]->getElementsByTagName ( 'td' );
			for($j = 0; $j < count ( $tds ); $j ++) {
				switch ($j) {
					// 学年
					case 0 :
						$reportCardItem ['year'] = $tds [$j]->plaintext;
						break;
					// 学期
					case 1 :
						$reportCardItem ['term'] = $tds [$j]->plaintext;
						break;
					// 课程代码
					case 2 :
						$reportCardItem ['code'] = $tds [$j]->plaintext;
						break;
					// 课程名称
					case 3 :
						$reportCardItem ['name'] = $tds [$j]->plaintext;
						break;
					// 课程性质
					case 4 :
						$reportCardItem ['property'] = $tds [$j]->plaintext;
						break;
					// 课程归属
					case 5 :
						$reportCardItem ['ascription'] = $tds [$j]->plaintext;
						break;
					// 学分
					case 6 :
						$reportCardItem ['credit'] = $tds [$j]->plaintext;
						break;
					// 绩点
					case 7 :
						$reportCardItem ['point'] = $tds [$j]->plaintext;
						break;
					// 平时成绩
					case 8 :
						$reportCardItem ['grade'] = $tds [$j]->plaintext;
						break;
					// 期末成绩
					case 10 :
						$reportCardItem ['final_grade'] = $tds [$j]->plaintext;
						break;
					// 成绩
					case 12 :
						$reportCardItem ['grade'] = $tds [$j]->plaintext;
						break;
					// 开课学院
					case 16 :
						$reportCardItem ['academy'] = $tds [$j]->plaintext;
						break;
				}
			}
			$reportCard [] = $reportCardItem;
		}
		
		return $reportCard;
	}
	/**
	 * 考试查询
	 */
	public static function getExamTable($studentnumber, $cookies) {
		$client = self::createClient ();
		$client->setCookies ( $cookies );
		$client->get ( '/xskscx.aspx?xh=' . $studentnumber . '&gnmkdm=N121604' );
		$content = $client->getContent ();
		
		$dom = new simple_html_dom ( $content );
		$table = $dom->getElementById ( 'Datagrid1' );
		$trs = $table->getElementsByTagName ( '' );
		
		print_r ( $content );
	}
}

$cookies = array (
		'ASP.NET_SessionId' => 'qxzmno55bqtsvcykhmnkqfb5' 
);

$cookies = ZFClient::signIn ( '201230740605', '022971' );
if ($cookies) {
	echo 'ok';
	print_r ( $cookies );
	// print_r ( ZFClient::getTimeTable ( '201230740605', $cookies ) );
	// print_r ( ZFClient::getReportCard ( '201230740605', $cookies ) );
	print_r ( ZFClient::getExamTable ( '201230740605', $cookies ) );
}
else {
	echo 'fail';
}
