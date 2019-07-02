<?

/**
 * selects the value of a text input
 */
class OnKeyAction extends KeyAction{
	// the key id that will trigger action
	private $k;
	// the action
	private $a;
	public function __construct($i, Action $a){
		if(!is_int($i)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an int");
		}
		$this->k = $i;
		$this->a = $a;
	}
	
	public function toJS(){
		return "var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} if(key == " . $this->k . ") " . $this->a->toJS() . ";";
	}
}


?>