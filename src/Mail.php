<?php
class Mail{
	// swiftmailer ラッパー
	public static $smtp_server = null;
	public static $smtp_port = null;
	public static $smtp_user = null;
	public static $smtp_pass = null;

	// コンストラクタ
	function __construct(){
		self::$smtp_server = getenv('SMTP_SERVER');
		self::$smtp_port = getenv('SMTP_PORT');
		self::$smtp_user = getenv('SMTP_USER');
		self::$smtp_pass = getenv('SMTP_PASS');
	}

	//===========================================
	// メール送信
	//===========================================
	public static function Send($From, $To, $Subject, $Body, $opt=null){
		$type = "text/plain";
		if(isset($opt["html"])) $type = "text/html";

		$transport = (new Swift_SmtpTransport(self::$smtp_server, self::$smtp_port))
		->setUsername(self::$smtp_user)
		->setPassword(self::$smtp_pass)
		;
		$mailer = new Swift_Mailer($transport);

		// メッセージ作成
		$message = (new Swift_Message($Subject));
		$message->setFrom($From)->setTo($To);
		$message->setBody($Body, $type);

		LOG::$traceDepth = 2;
		if(is_array($To)) $To = implode(", ", $To);
		LOG::INFO("メール送信 ".$To);
		// 送信
		return $mailer->send($message);

	}
		
}
?>