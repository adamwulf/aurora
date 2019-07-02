<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class WidthAction extends NonKeyAction{
	private $w;
	public function __construct(Component $e, $w){
		$this->e = $e;
		if(!is_integer($w)){
			throw new IllegalArgumentException("Second argument to " . __METHOD__ . " must be a integer");
		}
		$this->w = $w;
	}
	
	public function toJS(){
		return "xWidth(\"" . $this->e->getId() . "\", " . $this->w . ");\n";
	}
}


?>