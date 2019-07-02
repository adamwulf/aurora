<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class IsEmailAction extends BooleanAction{
	private $a;
	
	public function __construct(TextInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		$str = "xGetElementById(\"" . $this->e->getId() . "\").value";

		return "(xEmailCheck($str))";
	}
}

?>