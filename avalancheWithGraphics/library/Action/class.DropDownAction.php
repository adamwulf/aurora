<?

/**
 * this action is applied to a drop down input. when this action is fired, it will
 * probe the value of the drop down and fire the action associated with that value.
 *
 * actions can be associated with values and added to this action.
 */
class DropDownAction extends NonKeyAction{
	private $a;
	private $e;
	public function __construct(DropDownInput $e){
		
		$this->e = $e;
		$this->a = array();
	}
	
	public function toJS(){
		$id = $this->e->getId();
		$index = "xGetElementById(\"$id\").selectedIndex";
		$ddvalue = "xGetElementById(\"$id\").options[$index].value";
		$actions = "";
		foreach($this->a as $a){
			$value  = $a[0];
			$action = $a[1];
			$actions .= "if($ddvalue == \"$value\"){" . $action->toJS() . "}"; 
		}
		return $actions;
	}


	/**
	 * adds an action that will be invoked when this document loads
	 */
	public function addAction($value, Action $a){
		if(!is_string($value)){
			throw new IllegalArgumentException("1st argument to " . __METHOD__ . " must be a string");
		}
		$this->a[] = array($value, $a);
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeAction(Action $a){
		$index = array_search($a, $this->a);
		$index = false;
		for($i=0;$i<count($this->a);$i++){
			if($this->a[$i][1] == $a){
				$index = $i;
			}
		}
		if(isset($this->a[$index][1])){
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