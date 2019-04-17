<?php
class REQ{
	
	//===========================================
	// POST
	//===========================================
	public static function POST($key, $val=null){
		if(isset($_POST[$key])){
			return $_POST[$key];
		}
		return $val;
	}
	
	//===========================================
	// GET
	//===========================================
	public static function GET($key, $val=null){
		if($val !== null){
			$_GET[$key] = strval($val);
		}
		$retVal = "";
		if(isset($_GET[$key])){
			if(is_array($_GET[$key])){
				foreach($_GET[$key] as $val){
					$retVal[] = strval(Util::Trim($val));
				}
			}else{
				$retVal = strval(Util::Trim($_GET[$key]));
			}
		}
		return $retVal;
	}

	
}
?>