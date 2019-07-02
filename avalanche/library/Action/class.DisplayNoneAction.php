<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class DisplayNoneAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xDisplayNone(\"" . $this->e->getId() . "\");\n";
	}
}


?>