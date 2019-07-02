<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class StrLenEqualsAction extends BooleanAction{
	private $a;
	
	public function __construct(TextInput $e, $op, $len){
		$ops = array(">", "<", ">=", "<=", "==", "!=");
		if(!in_array($op, $ops)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be one of " . implode (",", $ops));
		}
		if(!is_int($len)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be an int");
		}
		$this->e = $e;
		$this->len = $len;
		$this->op = $op;
	}
	
	public function toJS(){
		$len = "xGetElementById(\"" . $this->e->getId() . "\").value.length";

		return "($len " . $this->op . " " . $this->len . ")";
	}
}

?>