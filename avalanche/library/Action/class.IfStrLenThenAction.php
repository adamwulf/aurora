<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class IfStrLenThenAction extends Action{
	private $a;
	
	public function __construct(TextInput $e, $op, $len, Action $a){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		if(!is_int($len)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be an int");
		}
		$this->e = $e;
		$this->a = array($a);
		$this->len = $len;
		$this->op = $op;
	}
	
	public function toJS(){
		$len = "xGetElementById(\"" . $this->e->getId() . "\").value.length";

		$actions = "";
		foreach($this->a as $a){
			$actions .= $a->toJS();
		}
		return "if($len " . $this->op . " " . $this->len . ") { $actions }";
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