<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class HorizontallySlideToAction extends NonKeyAction{
	private $t;
	private $x;
	public function __construct(Component $e, $x, $t){
		$this->e = $e;
		if(!is_integer($t)){
			throw new IllegalArgumentException("Third argument to " . __METHOD__ . " must be an integer.");
		}
		$this->t = $t;
		if(!is_integer($x)){
			throw new IllegalArgumentException("Third argument to " . __METHOD__ . " must be an integer.");
		}
		$this->x = $x;
	}
	
	public function toJS(){
		return "xSlideTo(\"" . $this->e->getId() . "\", " . $this->x . ", xPageY(\"" . $this->e->getId() . "\"), " . $this->t . ");\n";
	}
}


?>