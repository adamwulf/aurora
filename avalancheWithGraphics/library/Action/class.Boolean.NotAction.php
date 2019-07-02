<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class NotAction extends BooleanAction{
	
	public function __construct(BooleanAction $e1){
		$this->e1 = $e1;
	}
	
	public function toJS(){
		$str1 = $this->e1->toJs();

		return "(!$str1)";
	}
}

?>