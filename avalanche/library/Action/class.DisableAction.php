<?

/**
 * selects the value of a text input
 */
class DisableAction extends NonKeyAction{
	public function __construct(Input $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").disabled = true;\n";
	}
}


?>