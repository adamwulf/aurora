<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class DisplayBlockAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xDisplayBlock(\"" . $this->e->getId() . "\");\n";
	}
}


?>