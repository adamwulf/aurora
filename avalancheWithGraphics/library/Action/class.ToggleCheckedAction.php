<?

/**
 * selects the value of a text input
 */
class ToggleCheckedAction extends NonKeyAction{
	public function __construct(CheckedInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").checked = !xGetElementById(\"" . $this->e->getId() . "\").checked;\n";
	}
}


?>