 <?php

 /**************************************

 * バリデート関数たち

 * 2008/05/21

 * 正規表現メタ文字 → .^$[]*+?|()
 ************************************/
class Validate{

	/*-------------------------------------------------*
	*	関数: コンストラクタ
	*	引数: hostname , dbname
	*	戻値: コネクションインスタンス(失敗時はNULL)
	*	説明: クラスのインスタンスを作成する
	*--------------------------------------------------*/
	public function __construct($args=null){
	}
	//====================================
	//半角数字のみ
	//====================================
	public static function is_number($str, $ext=null){
		$preg_str = "/^[0-9]+$/";
		if($ext){
			$preg_str = "/^[0-9".$ext."]+$/";
		}
		if(preg_match($preg_str, $str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//半角英字のみ
	//====================================
	public static function is_alpha($str){
		$preg_str = "/^[a-zA-Z]+$/";
		if(preg_match($preg_str, $str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//半角英数字のみ
	//====================================
	public static function is_alpha_num($str, $ext=null){
		$preg_str = "/^[a-zA-Z0-9]+$/";
		if($ext){
			$preg_str = "/^[a-zA-Z0-9".$ext."]+$/";
		}
		if(preg_match($preg_str, $str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//URLとして正しいか
	//====================================
	public static function is_url($str){
		if(preg_match('/^(http|HTTP|ftp)(s|S)?:\/\/+[A-Za-z0-9]+\.[A-Za-z0-9]/',$str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//メールアドレスとして正しいか
	//====================================
	public static function is_email($str){
		$match = '/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i';
		if(preg_match($match, $str) || $str == ""){
			return true;
		}
		return false;
	}
	//====================================
	//全角カタカナのみ
	//====================================
	public static function is_zen_kata($str){
		$preg_str = "/^[ア-ン]+$/u";
		if(preg_match($preg_str,$str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//全角ひらがなのみ
	//====================================
	public static function is_zen_hira($str){
		$preg_str = "/^[あ-ん]+$/u";
		if(preg_match($$preg_str,$str) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//文字数判定（全角も1文字として返す）
	//====================================
	public static function zen_length($str, $min=0, $max=500){
		if((mb_strlen($str,"utf-8") >= $min && mb_strlen($str,"utf-8") <= $max) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//文字数判定（全角は2文字として返す）
	//====================================
	public static function han_length($str, $min=0, $max=500){
		if((strlen($str) >= $min && strlen($str) <= $max) || $str == ""){
			return true;
		}
		return false;
	}

	//====================================
	//数値の範囲
	//====================================
	public static function range($str, $min=0, $max=500){
		if(((int)$str >= (int)$min && (int)$str <= (int)$max) || $str == ""){
			return true;
		}
		return false;
	}

}
?>
