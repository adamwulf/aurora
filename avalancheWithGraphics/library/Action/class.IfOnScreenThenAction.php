<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class IfOnScreenThenAction extends NonKeyAction{
	private $a;
	public function __construct(Component $e, Action $a){
		$this->e = $e;
		$this->a = array($a);
	}
	
	public function toJS(){
		$x = "xPageX(\"" . $this->e->getId() . "\")";
		$y = "xPageY(\"" . $this->e->getId() . "\")";

		$actions = "";
		foreach($this->a as $a){
			$actions .= $a->toJS();
		}
		return "if($x>0 && $x<xClientWidth() && $y>0 && $y<xClientHeight()) { $actions }";
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