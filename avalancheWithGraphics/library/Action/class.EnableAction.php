<?

/**
 * selects the value of a text input
 */
class EnableAction extends NonKeyAction{
	public function __construct(Input $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").disabled = false;\n";
	}
}


?>