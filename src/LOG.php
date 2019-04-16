<?php
class LOG{
	public static $logDir = __DIR__.'/../../storage/log';
	public static $log_file_save_day = "7";

	//=========================
	// DEBUG
	//=========================
	public static function DEBUG($mess, $depth=0){
		self::DelOldFile(self::$logDir, self::$log_file_save_day);
		self::write(self::format("[DEBUG]", $mess, $depth));
	}

	//=========================
	// INFO
	//=========================
	public static function INFO($mess, $depth=0){
		self::DelOldFile(self::$logDir, self::$log_file_save_day);
		self::write(self::format("[INFO]", $mess, $depth));
	}

	//=========================
	// ERROR
	//=========================
	public static function ERROR($mess, $depth=0){
		self::DelOldFile(self::$logDir, self::$log_file_save_day);
		self::write(self::format("[ERROR]", $mess, $depth));
	}

	//=========================
	// ログファイル書き込み
	//=========================
	private static function write($mess, $error=null){
		$LOG_DIR = self::$logDir;
		if($LOG_DIR != '' && is_dir($LOG_DIR) == false){
			echo "ログファイル書き込みディレクトリ（".$LOG_DIR."）が正しくありません。<br />";
			exit;
		}else if(is_writable($LOG_DIR) == false){
			echo "ログファイル書き込みディレクトリ（".$LOG_DIR."）が書き込めません。<br />";
			exit;
		}
		
		if(isset(self::$log_file_save_day)){
			self::DelOldFile(self::$logDir, self::$log_file_save_day);
		}
		
		$log_time = date("[y/m/d H:i:s "). getenv("REMOTE_ADDR") . "]";
		$log_file = $LOG_DIR;
		$log_file .= "/".date("[y.m.d");
		
		$log_file .= "]LOG";
		
		if($error) $log_file .= "_error";
		$log_file .= ".txt";
		if(file_exists($log_file)){   
			//ファイルが存在するならばデータファイルを追記モードでオープン
			$fp = @fopen($log_file, "a");
		}else{
			//ファイルがなかった場合、ファイルを作る。
			$fp = fopen($log_file, "w");
			//ファイル権限を777に変更
			chmod($log_file,0777);
		}
		if($fp){
			//ファイルロック
			flock($fp, LOCK_EX);
			
			//書き込み
			fputs($fp,"$log_time $mess \n");
			
			//ファイルロック解除
			flock($fp, LOCK_UN);
			
			//ファイルクローズ
			fclose($fp);
		}
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
	//=========================
	private static function logTrace($depth=0){
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
	// ログメッセージをフォーマット
	//=========================
	private static function format($prefix, $mess, $depth=0){
		$mess = self::getDumpString($mess);
		$arys = self::logTrace($depth);
		$log_mess = $prefix . " : ".$arys[0]["file"] . " (". $arys[0]["line"] .") ".$mess;
		return $log_mess;
	}

}
