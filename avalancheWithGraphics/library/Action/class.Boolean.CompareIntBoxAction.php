<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class CompareIntBoxAction extends BooleanAction{
	private $a;
	
	public function __construct(TextInput $e1, $int, $op){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		if(!is_int($int)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an int");
		}
		$this->e1 = $e1;
		$this->e2 = $int;
		$this->op = $op;
	}
	
	public function toJS(){
		$str1 = "parseInt(xGetElementById(\"" . $this->e1->getId() . "\").value,10)";
		$str2 = $this->e2;

		return "($str1 " . $this->op . " $str2)";
	}
}

?>