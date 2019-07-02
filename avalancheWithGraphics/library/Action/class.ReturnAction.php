<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class ReturnAction extends NonKeyAction{
	
	protected $a;
	
	public function __construct(Action $a){
		$this->a = $a;
	}
	
	public function toJS(){
		return "return " . $this->a->toJS() . ";";
	}
}

?>