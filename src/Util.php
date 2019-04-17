<?php
class Util{
	//===========================================
	// SQL文文字列用
	// 第1引数にシングルコーテーションをつけてリターン
	//===========================================
	public static function SqlStr($str, $nullStr=null){
		if(isset($str) && is_string($str)){
			$str = preg_replace('/[\t]/', '    ', $str);	//タブをスペース4個に
			$str = addslashes(self::getTrim($str));
		}else{
			$str = '';
		}
		if($str==null || $str == ''){
			if($nullStr===null){
				return ' NULL ';
			}else{
				$str = $nullStr;
			}
		}
		return "'".$str."'";
	}
	
	//===========================================
	// SQL文LIKE文字列用
	// 第1引数
	//===========================================
	public static function SqlLike($str){
		if(isset($str) && is_string($str)){
			$str = preg_replace('/\\\/', '\\\\\\', $str);	//「\」エスケープ
			$str = preg_replace('/%/', '\%', $str);			//「%」エスケープ
			$str = preg_replace('/_/', '\_', $str);			//「_」エスケープ
			$str = addslashes($str);						//「'」エスケープ
		}else{
			$str = '';
		}
		return $str;
	}
	
	//===========================================
	// SQL文数値用
	//===========================================
	public static function SqlNum($num, $nullNum=null){
		if(isset($num) && is_numeric($num)){
			return $num;
		}else{
			if(!isset($nullNum)){
				return 0;
			}else{
				return $nullNum;
			}
		}
	}
	
	//===========================================
	// カンマ追加
	//===========================================
	public static function AddComma($str){
		return number_format(self::SqlNum($str));
	}
	
	//==================================================
	// 全角スペースと半角スペースのtrim
	//==================================================
	public static function getTrim($str){
		if(isset($str) && is_string($str)){
			$str = trim($str);
			return preg_replace('/^[ 　]*(.*?)[ 　]*$/u', '$1', $str);
		}
	}

	
	//==================================================
	// ディレクトリを削除
	//==================================================
	public static function rmdir($dir){
		if (is_dir($dir) and !is_link($dir)) {
			$paths = array();
			while ($glob = glob($dir)) {
				$paths = array_merge($glob, $paths);
				$dir .= '/*';
			}
			array_map('unlink', array_filter($paths, 'is_file'));
			array_map('rmdir',  array_filter($paths, 'is_dir'));
		}
	}
	
}
