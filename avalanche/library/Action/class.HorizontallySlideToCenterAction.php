<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class HorizontallySlideToCenterAction extends NonKeyAction{
	private $t;
	public function __construct(Component $e, $t){
		$this->e = $e;
		if(!is_integer($t)){
			throw new IllegalArgumentException("Third argument to " . __METHOD__ . " must be an integer.");
		}
		$this->t = $t;
	}
	
	public function toJS(){
		return "xSlideTo(\"" . $this->e->getId() . "\", xScrollLeft() + ((xClientWidth() - xWidth(\"" . $this->e->getId() . "\")) / 2), xPageY(\"" . $this->e->getId() . "\"), " . $this->t . ");\n";
	}
}


?>