<?

/**
 * selects the value of a text input
 */
class AlphaNumericOnlyAction extends KeyAction{
	public function __construct(){
	}
	
	public function toJS(){
		return "var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} if(String.fromCharCode(key).toLowerCase() >= \"0\" && String.fromCharCode(key).toLowerCase() <= \"9\" || String.fromCharCode(key).toLowerCase() >= \"a\" && String.fromCharCode(key).toLowerCase() <= \"z\" || String.fromCharCode(key).toLowerCase() >= \"A\" && String.fromCharCode(key).toLowerCase() <= \"Z\" || xNonVisibleChar(key)) return true; else return false;";
	}
}


?>