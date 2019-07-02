<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class SlideToCenterAction extends NonKeyAction{
	private $t;
	public function __construct(Component $e, $t){
		$this->e = $e;
		if(!is_integer($t)){
			throw new IllegalArgumentException("second argument to " . __METHOD__ . " must be of type integer");
		}
		$this->t = $t;
	}
	
	public function toJS(){
		return "xSlideTo(\"" . $this->e->getId() . "\", xScrollLeft() + (xClientWidth() / 2), xScrollTop() + (xClientHeight() / 2), " . $this->t . ");\n";
	}
}


?>