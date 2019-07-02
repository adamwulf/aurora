<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class ShowAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xShow(\"" . $this->e->getId() . "\");\n";
	}
}


?>