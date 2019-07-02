<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class AndAction extends BooleanAction{
	private $a;
	
	public function __construct(BooleanAction $e1, BooleanAction $e2){
		$this->e1 = $e1;
		$this->e2 = $e2;
	}
	
	public function toJS(){
		$str1 = $this->e1->toJs();
		$str2 = $this->e2->toJs();

		return "($str1 && $str2)";
	}
}

?>