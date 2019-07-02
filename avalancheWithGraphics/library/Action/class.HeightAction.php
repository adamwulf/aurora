<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class HeightAction extends NonKeyAction{
	private $h;
	public function __construct(Component $e, $h){
		$this->e = $e;
		if(!is_integer($h)){
			throw new IllegalArgumentException("Second argument to " . __METHOD__ . " must be a integer, given: " . gettype($h) . " = " . $h);
		}
		$this->h = $h;
	}
	
	public function toJS(){
		return "xHeight(\"" . $this->e->getId() . "\", " . $this->h . ");\n";
	}
}


?>