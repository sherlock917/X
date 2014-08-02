<?php
/**
 * 框架自带验证码类
 * 
 * @author vincent
 *        
 *        
 */
class Captcha {
	/**
	 * 验证码的值
	 *
	 * @var string
	 */
	private $code;
	/**
	 * 返回验证码上的字符串
	 *
	 * @return string
	 */
	function getCode() {
		return $this->code;
	}
	
	/**
	 * 生成验证码图片
	 *
	 * @param int $length
	 * @param array $param
	 */
	function &create($length = 4, $param = array()) {
		Header ( "Content-type: image/PNG" );
		$authnum = $this->random ( $length ); // 生成验证码字符.
		$this->code = $authnum; // 保存随机数
		
		$width = isset ( $param ['width'] ) ? $param ['width'] : 80; // 图片宽度
		$height = isset ( $param ['height'] ) ? $param ['height'] : 25; // 图片高度
		$pnum = isset ( $param ['pnum'] ) ? $param ['pnum'] : 80; // 干扰象素个数
		$lnum = isset ( $param ['lnum'] ) ? $param ['lnum'] : 2; // 干扰线条数
		
		$pw = $width; // 图片宽度 高度
		$ph = $height;
		
		$im = imagecreate ( $pw, $ph ); // 新建图像，大小为 x_size 和 y_size 的空白图像。
		$black = ImageColorAllocate ( $im, 238, 238, 238 ); // 设置背景颜色
		
		$values = array (
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ),
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ),
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ),
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ),
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ),
				mt_rand ( 0, $pw ),
				mt_rand ( 0, $ph ) 
		);
		imagefilledpolygon ( $im, $values, 6, ImageColorAllocate ( $im, mt_rand ( 170, 255 ), mt_rand ( 200, 255 ), mt_rand ( 210, 255 ) ) ); // 設置干擾多邊形底圖
		                                                                                                                                      
		// 文字
		for($i = 0; $i < strlen ( $authnum ); $i ++) {
			$font = ImageColorAllocate ( $im, mt_rand ( 0, 50 ), mt_rand ( 0, 150 ), mt_rand ( 0, 200 ) ); // 设置文字颜色
			$x = $i / $length * $pw + rand ( 1, 6 ); // 设置随机X坐标
			$y = rand ( 1, $ph / 3 ); // 设置随机Y坐标
			imagestring ( $im, mt_rand ( 4, 6 ), $x, $y, substr ( $authnum, $i, 1 ), $font );
		}
		
		// 加入干扰像素
		for($i = 0; $i < $pnum; $i ++) {
			$dist = ImageColorAllocate ( $im, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) ); // 设置杂点颜色
			imagesetpixel ( $im, mt_rand ( 0, $pw ), mt_rand ( 0, $ph ), $dist );
		}
		
		// 加入干扰线
		for($i = 0; $i < $lnum; $i ++) {
			$dist = ImageColorAllocate ( $im, mt_rand ( 50, 255 ), mt_rand ( 150, 255 ), mt_rand ( 200, 255 ) ); // 設置線顏色
			imageline ( $im, mt_rand ( 0, $pw ), mt_rand ( 0, $ph ), mt_rand ( 0, $pw ), mt_rand ( 0, $ph ), $dist );
		}
		
		return $im;
	}
	
	/**
	 * 产生随机数
	 *
	 * @param int $length
	 * @return string
	 */
	private function random($length) {
		$hash = '';
		$chars = '1234567890';
		$max = strlen ( $chars ) - 1;
		for($i = 0; $i < $length; $i ++) {
			$hash .= $chars [mt_rand ( 0, $max )];
		}
		return $hash;
	}
}  
 