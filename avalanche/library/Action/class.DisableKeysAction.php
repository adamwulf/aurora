<?

/**
 * selects the value of a text input
 */
class DisableKeysAction extends KeyAction{
	public function __construct(){
	}
	
	public function toJS(){
		return "var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} if(xNonVisibleChar(key)) return true; else return false;";
	}
}


?>