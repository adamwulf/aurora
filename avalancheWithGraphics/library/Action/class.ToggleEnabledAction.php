<?

/**
 * selects the value of a text input
 */
class ToggleEnabledAction extends NonKeyAction{
	public function __construct(Input $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").disabled = !xGetElementById(\"" . $this->e->getId() . "\").disabled;\n";
	}
}


?>