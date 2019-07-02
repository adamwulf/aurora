<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class IfStrCompareThenAction extends Action{
	private $a;
	
	public function __construct(TextInput $e1, TextInput $e2, $op, Action $a){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		$this->e1 = $e1;
		$this->e2 = $e2;
		$this->a = array($a);
		$this->op = $op;
	}
	
	public function toJS(){
		$str1 = "xGetElementById(\"" . $this->e1->getId() . "\").value";
		$str2 = "xGetElementById(\"" . $this->e2->getId() . "\").value";

		$actions = "";
		foreach($this->a as $a){
			$actions .= $a->toJS();
		}
		return "if($str1 " . $this->op . " $str2) { $actions }";
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