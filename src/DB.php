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
		// LOG::DEBUG('DB 接続情報設定', 2);
		self::$connInfo = $connInfo;
	}
	
	//======================================
	// DB 接続
	//======================================
	public static  function connect(){
		try{
			// LOG::INFO("DB接続開始 DataBase Name:[".self::$connInfo["dbname"]."]", 2);
			$dsn = 'mysql:host='.self::$connInfo["host"].';dbname='.self::$connInfo["dbname"].';port='.self::$connInfo["port"].';charset=utf8;';
			self::$_con = new PDO($dsn, self::$connInfo["user"], self::$connInfo["password"], 
				array(
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				));
			
		}catch (PDOException $e){
			LOG::ERROR("DB 接続失敗：\n".$e->getMessage()."\nSQL:".$sql, 2);
			throw new Exception($e->getMessage());
		}
		// LOG::DEBUG("DB接続成功", 2);
	}
	
	
	//======================================
	// SQL文実行
	//======================================
	public static function exec($sql){
		try{
			LOG::INFO("SQL実行：".$sql, 2);
			$stmt = self::$_con->exec($sql);
			// LOG::INFO("作用した行数：".$stmt."行");
			return $stmt;
		}catch (PDOException $e){
			LOG::ERROR("SQL実行失敗：\n".$e->getMessage()."\nSQL:".$sql, 2);
			throw new Exception($e->getMessage());
		}
	}
	
	
	//======================================
	// データ取得
	//======================================
	public static function getRec($sql){
		try{
			LOG::INFO("データ取得：".$sql, 2);
			$result = self::$_con->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			self::$_rows = count($result);
			// LOG::INFO("取得行数：".self::$_rows, 2);
			if(self::$_rows < 1) return null;
			return $result;
			
		}catch (PDOException $e){
			if(getenv('DERBUG')=='true'){
				echo "SQLデータ取得失敗：\n".$e->getMessage()."\nSQL:".$sql;
			}
			LOG::ERROR("SQLデータ取得失敗：\n".$e->getMessage()."\nSQL:".$sql, 2);
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
			// LOG::INFO("DBトランザクション開始", 2);
			return self::$_con->beginTransaction();
			
		}catch (PDOException $e){
			LOG::ERROR("トランザクション開始失敗：".$e->getMessage(), 2);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	
	//======================================
	// コミット
	//======================================
	public static function commit(){
		try{
			// LOG::INFO("DBコミット", 2);
			self::$_con->commit();
			return self::$_con->exec("UNLOCK TABLES");
			
		}catch (PDOException $e){
			LOG::ERROR("DBコミット失敗：".$e->getMessage(), 2);
			throw new Exception($e->getMessage());
			return null;
		}
	}
	
	
	//======================================
	// ロールバック
	//======================================
	public static function rollBack(){
		try{
			// LOG::INFO("DBロールバック", 2);
			self::$_con->rollBack();
			return self::$_con->exec("UNLOCK TABLES");
			
		}catch (PDOException $e){
			LOG::ERROR("DBロールバック失敗：".$e->getMessage(), 2);
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
		// LOG::INFO("テーブルロック（".$lockMode."）".$lockMode, 2);
		try{
			$sql = "LOCK TABLE ".$tables." ".$lockMode;
			$stmt = self::$_con->exec($sql);
			return $stmt;
		}catch (PDOException $e){
			LOG::ERROR("テーブルロック失敗：\n".$e->getMessage()."\nSQL:".$sql, 2);
			throw new Exception($e->getMessage());
			return null;
		}
	}
}