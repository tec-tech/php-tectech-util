<?php
class Util{
	//===========================================
	// SQL文文字列用
	// 第1引数にシングルコーテーションをつけてリターン
	//===========================================
	public static function SqlStr($str, $nullStr=null){
		if(isset($str) && is_string($str)){
			$str = preg_replace('/[\t]/', '    ', $str);	//タブをスペース4個に
			$str = addslashes(self::Trim($str));
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
	public static function SqlLike($str, $mode=0){
		if(isset($str) && is_string($str)){
			$str = preg_replace('/\\\/', '\\\\\\', $str);	//「\」エスケープ
			$str = preg_replace('/%/', '\%', $str);			//「%」エスケープ
			$str = preg_replace('/_/', '\_', $str);			//「_」エスケープ
			$str = addslashes($str);						//「'」エスケープ
		}else{
			$str = '';
		}
		if($mode == 1){
			return "'%".$str."'";
		}else if($mode == 2){
			return "'".$str."%'";
		}
		return "'%".$str."%'";
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
	public static function Trim($str){
		if(isset($str) && is_string($str)){
			$str = trim($str);
			return preg_replace('/^[ 　]*(.*?)[ 　]*$/u', '$1', $str);
		}
	}
	//==================================================
	// 配列のtrim
	//==================================================
	public static function ArrTrim($val){
		if(is_array($val)){
			foreach($val as &$val2){
				$val2 = self::ArrTrim($val2);
			}
		}else{
			$val = self::Trim($val);
		}
		return $val;
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
	
	//==================================================
	// NULLまたは空文字チェック
	//==================================================
	public static function IsNullOrEmpty(&$val){
		if(!isset($val)) return true;
		if($val == "") return true;
		if($val == null) return true;
		if($val === false) return true;
		return false;
	}

}
