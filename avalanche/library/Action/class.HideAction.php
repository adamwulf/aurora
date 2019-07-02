<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class HideAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xHide(\"" . $this->e->getId() . "\");\n";
	}
}


?>