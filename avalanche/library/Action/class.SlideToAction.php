<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class SlideToAction extends NonKeyAction{
	private $x;
	private $y;
	public function __construct(Component $e, $x, $y){
		if(!is_integer($x)){
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be an integer.");
		}
		if(!is_integer($y)){
			throw new IllegalArgumentException("Second argument to " . __METHOD__ . " must be an integer.");
		}
		$this->e = $e;
		$this->x = $x;
		$this->y = $y;
	}
	
	public function toJS(){
		return "xSlideTo(\"" . $this->e->getId() . "\", " . $this->x . ", " . $this->y . ");\n";
	}
}


?>