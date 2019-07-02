<?

/**
 * this action is applied to a drop down input. when this action is fired, it will
 * probe the value of the drop down and fire the action associated with that value.
 *
 * actions can be associated with values and added to this action.
 */
class CompareToDropDownAction extends BooleanAction{
	private $e;
	private $str;
	private $op;
	public function __construct(DropDownInput $e, $str, $op){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		$this->e = $e;
		$this->str = $str;
		$this->op = $op;
	}

	public function toJS(){
		$id = $this->e->getId();
		$index = "xGetElementById(\"$id\").selectedIndex";
		$ddvalue = "xGetElementById(\"$id\").options[$index].value";
		return "(" . $ddvalue . $this->op . "\"" . $this->str . "\")";
	}
}


?>