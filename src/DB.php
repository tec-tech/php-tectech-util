<?php

class DB{
	private static $connInfo, $_con, $_rows;
	
	//======================================
	// DB 接続情報設定
	// host
	// dbname
	// port
	// user
	// password
	//======================================
	public static function setConnectionInfo($connInfo){
		// self::LogWrite('DB 接続情報設定');
		self::$connInfo = $connInfo;
	}
	
	//======================================
	// DB 接続
	//======================================
	public static function connect(){
		try{
			self::LogWrite("DB接続開始 DataBase Name:[".self::$connInfo["dbname"]."]");
			$dsn = 'mysql:host='.self::$connInfo["host"].';dbname='.self::$connInfo["dbname"].';port='.self::$connInfo["port"].';charset=utf8;';
			self::$_con = new PDO($dsn, self::$connInfo["user"], self::$connInfo["password"], 
				array(
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				));
			
		}catch (PDOException $e){
			self::LogWrite("DB 接続失敗：\n".$e->getMessage()."\n", true);
			throw new Exception($e->getMessage());
		}
		// self::LogWrite("DB接続成功");
	}
	
	
	//======================================
	// SQL文実行
	//======================================
	public static function exec($sql){
		try{
			self::LogWrite("SQL実行：".$sql);
			$stmt = self::$_con->exec($sql);
			// self::LogWrite("作用した行数：".$stmt."行");
			return $stmt;
		}catch (PDOException $e){
			self::LogWrite("SQL実行失敗：\n".$e->getMessage()."\nSQL:".$sql, true);
			throw new Exception($e->getMessage());
		}
	}
	
	
	//======================================
	// データ取得
	//======================================
	public static function getRec($sql){
		try{
			self::LogWrite("データ取得：".$sql);
			$result = self::$_con->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			self::$_rows = count($result);
			// self::LogWrite("取得行数：".self::$_rows);
			if(self::$_rows < 1) return null;
			return $result;
			
		}catch (PDOException $e){
			if(getenv('DERBUG')=='true'){
				echo "SQLデータ取得失敗：\n".$e->getMessage()."\nSQL:".$sql;
			}
			self::LogWrite("SQLデータ取得失敗：\n".$e->getMessage()."\nSQL:".$sql, true);
			throw new Exception($e->getMessage());
		}
	}
	//======================================
	// 直前のデータ取得行数を返す
	//======================================
	public static function getRecRows(){
		return self::$_rows;
	}
	
	
	//======================================
	// トランザクション開始
	//======================================
	public static function begin(){
		try{
			// self::LogWrite("DBトランザクション開始");
			return self::$_con->beginTransaction();
			
		}catch (PDOException $e){
			self::LogWrite("トランザクション開始失敗：".$e->getMessage(), true);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	
	//======================================
	// コミット
	//======================================
	public static function commit(){
		try{
			// self::LogWrite("DBコミット");
			self::$_con->commit();
			return self::$_con->exec("UNLOCK TABLES");
			
		}catch (PDOException $e){
			self::LogWrite("DBコミット失敗：".$e->getMessage(), true);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	
	//======================================
	// ロールバック
	//======================================
	public static function rollBack(){
		try{
			// self::LogWrite("DBロールバック");
			self::$_con->rollBack();
			return self::$_con->exec("UNLOCK TABLES");
			
		}catch (PDOException $e){
			self::LogWrite("DBロールバック失敗：".$e->getMessage(), true);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	
	//======================================
	// auto increment値取得
	//======================================
	public static function lastInsertId(){
		return self::$_con->lastInsertId();
	}
	
	
	//======================================
	// テーブルロック
	//======================================
	public static function LockTables($tables, $lockMode='WRITE'){
		// self::LogWrite("テーブルロック（".$lockMode."）".$lockMode);
		try{
			$sql = "LOCK TABLE ".$tables." ".$lockMode;
			$stmt = self::$_con->exec($sql);
			return $stmt;
		}catch (PDOException $e){
			self::LogWrite("テーブルロック失敗：\n".$e->getMessage()."\nSQL:".$sql, true);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	//======================================
	// ログ書き出し
	//======================================
	public static function LogWrite($message, $error=false){
		LOG::$traceDepth = 3;
		if($error){
			LOG::ERROR($message);
		}else{
			LOG::INFO($message);
		}
	}

}