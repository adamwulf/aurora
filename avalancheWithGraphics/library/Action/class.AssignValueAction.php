<?

/**
 * sets the value of the left hand side to equal the right hand side
 */
class AssignValueAction extends NonKeyAction{
	
	protected $rhs;
	
	public function __construct(TextInput $e, TextInput $rhs){
		$this->e = $e;
		$this->rhs = $rhs;
	}
	
	public function toJS(){
		return "xGetElementById(\"" . $this->e->getId() . "\").value=xGetElementById(\"" . $this->rhs->getId() . "\").value;\n";
	}
}


?>