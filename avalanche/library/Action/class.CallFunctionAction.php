<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class CallFunctionAction extends NonKeyAction{
	private $name;
	public function __construct($name){
		if(!is_string($name)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$this->name = $name;
	}
	
	public function toJS(){
		return $this->name . "();";
	}

	public function getName(){
		return $this->name;
	}
}


?>