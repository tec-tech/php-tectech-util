<?php
class Mail{

	//===========================================
	// メール送信
	//===========================================
	public static function sedMail($to, $subject, $body, $bcc=null, $opt=null){
		if(!is_array($to)){
			$to = array($to);
		}

		if($bcc == null){
			$bcc = array();
		}else if(!is_array($bcc)){
			$bcc = array($bcc);
		}
		// BCCに管理者を追加する
		if(!$opt || !$opt['no_admin_send']){
			if(isset($GLOBALS['@APP_CONF']) && isset($GLOBALS['@APP_CONF']['admin_mail'])){
				if(!is_array($GLOBALS['@APP_CONF']['admin_mail'])){
					$bcc[] = $GLOBALS['@APP_CONF']['admin_mail'];
				}else{
					foreach($GLOBALS['@APP_CONF']['admin_mail'] as $val){
						$bcc[] = $val;
					}
				}
			}
		}
		Log::Info($GLOBALS['@APP_CONF']['from_mail']);
		$from = array($GLOBALS['@APP_CONF']['from_mail'][0] => $GLOBALS['@APP_CONF']['from_mail'][1]);

		$transport = \Swift_SmtpTransport::newInstance('localhost', 25);

		$mailer = Swift_Mailer::newInstance($transport);

		// メールの作成
		$body = str_replace(array("\r\n","\r","\n"), "\r", $body);
		$message = Swift_Message::newInstance()
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
		// 送信
		$logMess  = "メール送信\n";
		$logMess .= "-----------------------------------\n";
		$logMess .= "TO:".implode(",", $to)."\n";
		$logMess .= "BCC:".implode(",", $bcc)."\n";
		$logMess .= "SUBJECT:".$subject."\n";
		$logMess .= "BODY:".$body."\n";
		$logMess .= "-----------------------------------\n";
		Log::Info($logMess);
		return $mailer->send($message);
	}

	//===========================================
	// メール送信
	//===========================================
	public static function sedHtmlMail($to, $subject, $body, $bcc=null, $opt=null){
		if(!is_array($to)){
			$to = array($to);
		}
		if($bcc == null){
			$bcc = array();
		}else if(!is_array($bcc)){
			$bcc = array($bcc);
		}
		if(isset($GLOBALS['@APP_CONF']) && isset($GLOBALS['@APP_CONF']['admin_mail']) && (!$opt || !$opt['noadmin'])){
			if(!is_array($GLOBALS['@APP_CONF']['admin_mail'])){
				$bcc[] = $GLOBALS['@APP_CONF']['admin_mail'];
			}else{
				foreach($GLOBALS['@APP_CONF']['admin_mail'] as $val){
					$bcc[] = $val;
				}
			}
		}
		$from = array($GLOBALS['@APP_CONF']['from_mail'][0] => $GLOBALS['@APP_CONF']['from_mail'][1]);
		
		$transport = \Swift_SmtpTransport::newInstance('localhost', 25);
		
		$mailer = Swift_Mailer::newInstance($transport);
		
		// メールの作成
		$message = Swift_Message::newInstance()
			->setMaxLineLength(0)	// テキストメールで改行させない
			->setSubject($subject)
			->setFrom($from)
			->setTo($to)
			->setBcc($bcc)
			->setBody($body, 'text/html')
			;
		
		// 送信
		if(!$opt || !isset($opt['nolog']) || $opt['nolog']){
			$logMess  = "メール送信\n";
			$logMess .= "-----------------------------------\n";
			// $logMess .= "FROM:".$from."\n";
			$logMess .= "FROM:".implode(",", $from)."\n";
			$logMess .= "TO:".implode(",", $to)."\n";
			$logMess .= "BCC:".implode(",", $bcc)."\n";
			$logMess .= "SUBJECT:".$subject."\n";
			$logMess .= "BODY:".$body."\n";
			$logMess .= "-----------------------------------\n";
			Log::Info($logMess);
		}
		return $mailer->send($message);
	}
	
	//===========================================
	// メールボディひな形作成
	//===========================================
	public static function getMailTemplate($type, $body){
		// お問い合わせページアドレス
		$contagtPage = 'https://tectech.jp/contact_us';
		// 画像アドレス
		$logoImageSrc = $GLOBALS['@APP_CONF']['url'].'/images/v2/onetouch_logo_1.png';
		
		if($type == 1){
			$retVal = "<html>";
			$retVal .= "<head></head>";
			$retVal .= "<body style='font-size:14px;color:#333;line-height:1.5em;background:#FFF;padding:0px;'>";
			
			$retVal .= "<div style='font-size: 18px;font-weight: bold;color: #39ae2d;border-bottom: solid 1px #AAA;padding: 0px 0px 10px;'>";
			$retVal .= "<img src='".$logoImageSrc."' style='max-height: 50px;max-width: 100%;vertical-align: -8px;'>";
			$retVal .= "　ワンタッチ勤怠";
			$retVal .= "</div>";
			//--MAIN CONTNET---
			$retVal .= "<div style='padding:0px 0px;line-height:1.6em;'>";
			//===========================================
			$retVal .= nl2br($body);	// 内容格納
			//===========================================
			$retVal .= "</div>";
			//--FOOTER---
			$retVal .= "<div style='border-top:solid 1px #AAA;margin:30px 0px 10px 0px;'></div>";
			$retVal .= "<div style=''>";
			$retVal .= "<p>※このメール内容に心あたりのない場合は、大変お手数ですが、<br />";
			$retVal .= "<a href='".$contagtPage."' style='color:#999;font-weight:bold;text-decoration:none;'>".$contagtPage."</a>";
			$retVal .= " へご連絡くださいますようお願いいたします。</p>";
			$retVal .= "</div>";
			
			$retVal .= "</body>";
			$retVal .= "</html>";
		}
		
		return $retVal;
	}
}
?>
