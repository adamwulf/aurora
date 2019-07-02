<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class NewFunctionAction extends NonKeyAction{
	private $a;
	private $name;
	public function __construct($name){
		$this->a = array();
		$this->name = $name;
	}
	
	public function toJS(){
		$actions = "";
		foreach($this->a as $a){
			$actions .= $a->toJS();
		}
		return "function " . $this->name . "() { $actions }";
	}

	public function getName(){
		return $this->name;
	}

	/**
	 * adds an action that will be invoked when this document loads
	 */
	public function addAction(Action $a){
		$this->a[] = $a;
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeAction(Action $a){
		$index = array_search($a, $this->a);
		if(isset($this->a[$index])){
			array_splice($this->a, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getActions(){
		return $this->a;	 
	 }
}


?>