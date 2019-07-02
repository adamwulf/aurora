<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class StrCompareAction extends BooleanAction{
	private $a;
	
	public function __construct(TextInput $e1, TextInput $e2, $op){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		$this->e1 = $e1;
		$this->e2 = $e2;
		$this->op = $op;
	}
	
	public function toJS(){
		$str1 = "xGetElementById(\"" . $this->e1->getId() . "\").value";
		$str2 = "xGetElementById(\"" . $this->e2->getId() . "\").value";

		return "($str1 " . $this->op . " $str2)";
	}
}

?>