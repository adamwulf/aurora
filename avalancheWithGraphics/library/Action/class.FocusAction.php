<?

/**
 * selects the value of a text input
 */
class FocusAction extends NonKeyAction{
	public function __construct(Input $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").focus();\n";
	}
}


?>