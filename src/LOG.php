<?php
class LOG{
	public static $logDir = null;
	public static $log_file_save_day = -1;
	public static $traceDepthDefault = 1;
	public static $traceDepth = 1;

	//=========================
	// DEBUG
	//=========================
	public static function DEBUG(...$args){
		foreach((array) $args as $arg){
			$val = $info = print_r($arg, TRUE);
			self::write(self::format("[DEBUG]", $val));
		}
		// トレース深度をデフォルトに戻す
		self::$traceDepth = self::$traceDepthDefault;
	}

	//=========================
	// INFO
	//=========================
	public static function INFO(...$args){
		foreach((array) $args as $arg){
			$val = $info = print_r($arg, TRUE);
			self::write(self::format("[INFO]", $val));
		}
		// トレース深度をデフォルトに戻す
		self::$traceDepth = self::$traceDepthDefault;
	}

	//=========================
	// ERROR
	//=========================
	public static function ERROR(...$args){
		foreach((array) $args as $arg){
			$val = $info = print_r($arg, TRUE);
			self::write(self::format("[ERROR]", $val), true);
		}
		// トレース深度をデフォルトに戻す
		self::$traceDepth = self::$traceDepthDefault;
	}

	//=========================
	// ログファイル書き込み
	//=========================
	private static function write($mess, $error=false){
		if(self::$logDir === null || !file_exists(self::$logDir)) return;
		self::DelOldFile(self::$logDir, self::$log_file_save_day);
		
		$LOG_DIR = self::$logDir;
		if($LOG_DIR != '' && is_dir($LOG_DIR) == false){
			echo "ログファイル書き込みディレクトリ（".$LOG_DIR."）が正しくありません。<br />";
			exit;
		}else if(is_writable($LOG_DIR) == false){
			echo "ログファイル書き込みディレクトリ（".$LOG_DIR."）が書き込めません。<br />";
			exit;
		}
		
		$log_time = date("[y/m/d H:i:s "). getenv("REMOTE_ADDR") . "]";
		$log_file = $LOG_DIR;
		$log_file .= "/".date("[y.m.d");
		
		$log_file .= "]LOG";
		
		if($error) $log_file .= "_error";
		$log_file .= ".log";

		file_put_contents($log_file, $log_time." ".$mess."\n", FILE_APPEND);

		return;
	}
	
	//=========================
	// 古いログファイル削除
	//=========================
	private static function DelOldFile($dirPath, $day){
		if($day < 0) return;
		
		$today = time();	//本日のUnixタイムスタンプを取得
		$timeStamp = 60*60*24*$day;
		$hikakudate = $today - $timeStamp;
		
		$targetdir = opendir($dirPath);	//対象とするディレクトリ情報をセットして開く
		while (false !== ($targetfile = readdir($targetdir))){
			if($targetfile != "." && $targetfile != ".."){
				// ディレクトリの場合は再帰処理実行
				if(is_dir($dirPath.'/'.$targetfile)){
					self::DelOldFile($dirPath.'/'.$targetfile, $day);
				}else{
					$filepath = $dirPath . '/' . $targetfile;	//ファイルの場所をセット
					$filemtime = filemtime($filepath);			//ファイルの最終更新日時をUNIXタイムスタンプで取得
					
					// 指定日より前の更新日時だった場合にはファイルをフォルダから削除する
					if($hikakudate > $filemtime){
						unlink($filepath);
					}
				}
			}
		}
		closedir($targetdir);
		return true;
	}
	
	//=========================
	// 配列の中をStringで返す
	//=========================
	private static function getDumpString($arr){
		if(is_array($arr)){
			ob_start();
			var_dump($arr);
			$arr =ob_get_contents();
			ob_end_clean();
		}
		return $arr;
	}
	//=========================
	//=========================
	private static function logTrace($depth=1){
		//  例外オブジェクトを生成。
		$e = new Exception;
		//  トレース配列を取得。
		$arys = $e->getTrace();
		//  一つ目はこのtrace()を呼び出したファイル名と行番号なので削除する。
		array_shift($arys);
		for($i = 0; $i < $depth; $i ++){
			array_shift($arys);
		}
		return $arys;
	}
	
	
	//=========================
	// ログメッセージをフォーマット
	//=========================
	private static function format($prefix, $mess){
		$mess = self::getDumpString($mess);
		$arys = self::logTrace(self::$traceDepth);
		$log_mess = $prefix . " : ".$arys[0]["file"] . " (". $arys[0]["line"] .") ".$mess;
		return $log_mess;
	}

}
