<?

/**
 * selects the value of a text input
 */
class SelectAction extends NonKeyAction{
	public function __construct(TextInput $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").select();\n";
	}
}


?>