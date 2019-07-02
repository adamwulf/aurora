<?

/**
 * shows an alert box with the code of the key that was pressed
 */
class AlertKeyCodeAction extends KeyAction{
	public function __construct(TextInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		$jsthis = "xGetElementById(\"" . $this->e->getId() . "\")";
		return "var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} alert(\"key code: \" + key);";
	}
}


?>