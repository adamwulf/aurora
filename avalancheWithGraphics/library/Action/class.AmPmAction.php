<?

/**
 * selects the value of a text input
 */
class AmPmAction extends KeyAction{
	public function __construct(TextInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		$jsthis = "xGetElementById(\"" . $this->e->getId() . "\")";
		return "var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} if(String.fromCharCode(key).toLowerCase() == \"a\") " . $jsthis . ".value=\"am\"; else if(String.fromCharCode(key).toLowerCase() == \"p\") " . $jsthis . ".value=\"pm\";";
	}
}


?>