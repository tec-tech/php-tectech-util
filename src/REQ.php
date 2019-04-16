<?php
class REQ{
	
	//===========================================
	// SESSION
	//===========================================
	public static function SS($key){
		if(is_array($key)){
			if(isset($_SESSION[$key[0]])){
				$retVal = $_SESSION[$key[0]];
			}else{
				return '';
			}
			$c = 0;
			foreach($key as $k => $val){
				if($c > 0){
					if(isset($retVal[$val])){
						$retVal = $retVal[$val];
					}else{
						return '';
					}
				}
				$c ++;
			}
			return Util::getTrim($retVal);
		}
		if(isset($_SESSION[$key])){
			return Util::getTrim($_SESSION[$key]);
		}else{
			return '';
		}
	}
	
	//===========================================
	// POST
	//===========================================
	public static function POST($key, $val=null){
		if($val !== null){
			$_POST[$key] = $val;
		}
		$retVal = "";
		if(isset($_POST[$key])){
			if(is_array($_POST[$key])){
				foreach($_POST[$key] as $val){
					$retVal[] = strval(Util::getTrim($val));
				}
			}else{
				$retVal = strval(Util::getTrim($_POST[$key]));
			}
		}
		return $retVal;
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
					$retVal[] = strval(Util::getTrim($val));
				}
			}else{
				$retVal = strval(Util::getTrim($_GET[$key]));
			}
		}
		return $retVal;
	}
	
}
?>