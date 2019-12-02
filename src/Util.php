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
	// スペース区切りキーワードを配列に格納
	//===========================================
	public static function getKeywordArray($str){
		$str = preg_replace('/　/', ' ', $str);	// 全角スペースを半角スペースに変換
		$array = explode(' ', $str);
		$array = self::ArrTrim($array);
		$retVal = [];
		foreach($array as $val){
			if($val != "") $retVal[] = $val;
		}
		return $retVal;
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
		return $str;
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
	// 古いファイルを削除
	//==================================================
	public static function rmOldFile($dir, $hour){
		$expire = strtotime($hour." hours ago");
		$list = scandir($dir);
		$deleteList = [];
		foreach($list as $value){
			$file = $dir."/".$value;
			if($value == "." || $value == "..") continue;
			if(preg_match("/^\.git(keep|ignore)/",$value)) continue;
			if(filemtime($file) < $expire){
				if(is_file($file)){
					unlink($file);
				}else{
					self::rmdir($file);
				}
				$deleteList[] = $file;
			}
		}
		return $deleteList;
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

	//==================================================
	// ランダム文字列生成
	//==================================================
	public static function random($length = 8){
		return array_reduce(range(1, $length), function($p){ return $p.str_shuffle('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')[0]; });
	}
	
	//====================================
	// 日付判定
	// YYYY/MM/DDとかYYYY-MM-DDとかYYYY月MM月DD日
	// 失敗時NULLを返す
	// $calcStr "-1 day", "-1 month", "-1 year", "-1 week"
	// 戻り値：
	// 			date:			yyyy-m-d
	//			date_w_num:		曜日の数値
	//			date_w_jp:		yyyy月mm月dd日
	//			date_md_w_jp:	yyyy月mm月dd日(ww)
	//====================================
	public static function getDate($str, $calcStr=null){
		// echo('対象文字列->'.$str);
		$str = self::Trim($str);
		if($str == ""){
			return null;
		}
		$Y = "";
		$M = "";
		$D = "";
		$C = 0;
		$notNumFlg = true;
		$preg_str = "/^[0-9]+$/";
		for($i = 0; $i < mb_strlen($str); $i ++){
			$str1 = mb_substr($str, $i , 1);
			// 数字の時
			if(preg_match($preg_str, $str1)){
				$notNumFlg = false;
				if($C == 0){
					$Y .= $str1;
				}else if($C == 1){
					$M .= $str1;
				}else if($C == 2){
					$D .= $str1;
				}
			}else{
				if(!$notNumFlg){
					$notNumFlg = true;
					$C ++;
				}
			}
			if($C > 2){
				$C = 2;
				break;
			}
		}
		if($C == 2 && $Y!="" && $M!="" && $D!=""){
			if($calcStr){
				$strDay = $Y."-".$M."-".$D." ".$calcStr;
				$strTime = strtotime($strDay);
				
				$Y = date('Y', $strTime);
				$M = date('m', $strTime);
				$D = date('d', $strTime);
			}
			if(checkdate($M, $D, $Y)){
				$weekday_jp = array( "日", "月", "火", "水", "木", "金", "土" );
				
				$retVal["date"] = $Y.'-'.$M.'-'.$D;
				$retVal["Y"] = $Y;
				$retVal["M"] = $M;
				$retVal["D"] = $D;
				$retVal["MM"] = str_pad($M, 2, 0, STR_PAD_LEFT);
				$retVal["DD"] = str_pad($D, 2, 0, STR_PAD_LEFT);
				
				$datetime = new DateTime($retVal["date"]);
				$w = (int)$datetime->format('w');
				$retVal["W"] = $w;
				$retVal["date_w_jp"] = $Y.'年'.$M.'月'.$D.'日'.'('.$weekday_jp[$w].')';
				$retVal["date_md_w_jp"] = $M.'月'.$D.'日'.'('.$weekday_jp[$w].')';
				return $retVal;
			}else{
				// Log::Warn('日付作成に失敗しました。$str:'.$str.' $calcStr:'.$calcStr, 1);
			}
		}
		
		return null;
    }

	//========================================
    // 日付(例：YYYY-MM-DD HH:MM:SS)を配列に変換
    // * 数値以外の文字列で分割
	//========================================
	public static function convertDate2Array($inputDate){
		$result = [];
		$rep = preg_replace("/[^0-9]/",",",$inputDate);
		$arr = explode(',', $rep);
		foreach($arr as $val){
			if($val) $result[] = $val;
		}
		return $result;
    }
    
	//========================================
	// 日付配列を"YYYY-MM-DD HH:MM:SS"に変換
	//========================================
	public static function convertArray2Date($dateList){
		$result = null;
		if(count($dateList) >= 5){
			$result .= $dateList[0];
			$result .= '-';
			$result .= $dateList[1];
			$result .= '-';
			$result .= $dateList[2];
			$result .= ' ';
			$result .= $dateList[3];
			$result .= ':';
			$result .= $dateList[4];
        }
        if(count($dateList) ==  6){
            $result .= ':'.$dateList[5];
        }
		return $result;
	}

	// ===================================
	// ファイルを出力
	// ===================================
	public static function output($path){
		if(file_exists($path)){
			header('Content-Type: '.mime_content_type($path));
			header('Content-Disposition: attachment; filename="'.basename($path).'"');
			header('Content-Length: ' . filesize($path));

			while (ob_get_level() > 0){
				ob_end_clean();
			}
			ob_start();
   
			// ファイル出力
			if($file = fopen($path, 'rb')){
				while(!feof($file) and (connection_status() == 0)) {
					echo fread($file, '4096'); //指定したバイト数ずつ出力
					ob_flush();
				}
				ob_flush();
				fclose($file);
			}
			ob_end_clean();
		}else{
			echo "404";
		}
	}
}
