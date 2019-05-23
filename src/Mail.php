<?php
class Mail{

	//===========================================
	// メール送信
	//===========================================
	public static function sedMail($from, $to, $subject, $body, $bcc=null, $opt=null){
		if(!is_array($to)){
			$to = array($to);
		}
		if(!is_array($from)){
			$from = array($from);
		}
		if($bcc == null){
			$bcc = array();
		}else if(!is_array($bcc)){
			$bcc = array($bcc);
		}

		$transport = (new Swift_SmtpTransport('localhost', 25));
		$mailer = new Swift_Mailer($transport);;
		// メールの作成
		$body = str_replace(array("\r\n","\r","\n"), "\r", $body);
		$message = new Swift_Message();
		$message
			->setMaxLineLength(0)	// テキストメールで改行させない
			->setSubject($subject)
			->setFrom($from)
			->setTo($to)
			->setBcc($bcc)
			->setBody($body)
			;
		if($opt && $opt['attach'] && is_array($opt['attach'])){
			foreach($opt['attach'] as $val){
				$attachment = Swift_Attachment::fromPath($val['FilePath']);
				// ファイル名をリネームする。指定しなければ画像ファイル名になる
				if($val['FileName']){
					$attachment->setFilename($val['FileName']);
				}
				// ContentTypeを指定する。
				if($val['FileType']){
					$attachment->setContentType($val['FileType']);
				}
				$message->attach($attachment);
			}
		}
		return $mailer->send($message);
	}

	//===========================================
	// メール送信（HTML）
	//===========================================
	public static function sedHtmlMail($from, $to, $subject, $body, $bcc=null, $opt=null){
		if(!is_array($to)){
			$to = array($to);
		}
		if(!is_array($from)){
			$from = array($from);
		}
		if($bcc == null){
			$bcc = array();
		}else if(!is_array($bcc)){
			$bcc = array($bcc);
		}
		
		$transport = (new Swift_SmtpTransport('localhost', 25));
		$mailer = new Swift_Mailer($transport);
		// メールの作成
		$message = new Swift_Message();
		$message
			->setMaxLineLength(0)	// テキストメールで改行させない
			->setSubject($subject)
			->setFrom($from)
			->setTo($to)
			->setBcc($bcc)
			->setBody($body, 'text/html')
			;
		return $mailer->send($message);
	}
}
?>
