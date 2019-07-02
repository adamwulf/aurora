<?

/**
 * selects the value of a text input
 */
class SetValueAction extends NonKeyAction{
	
	protected $v;
	
	public function __construct(TextInput $e, $value){
		if(!is_string($value)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$this->e = $e;
		$this->v = $value;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").value=\"" . addslashes($this->v) . "\";\n";
	}
}


?>