<?

/**
 * selects the value of a text input
 */
class CheckAction extends NonKeyAction{
	public function __construct(CheckedInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").checked = true;\n";
	}
}


?>