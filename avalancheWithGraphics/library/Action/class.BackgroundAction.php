<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class BackgroundAction extends NonKeyAction{
	private $c;
	private $i;
	public function __construct(Component $e, $color, $img = ""){
		$this->e = $e;
		if(!is_string($color)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($img)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string");
		}
		$this->c = $color;
		$this->i = $img;
	}
	
	public function toJS(){
		
		if(strlen($this->i)){
			return "xBackground(\"" . $this->e->getId() . "\", \"" . $this->c . "\", \"" . $this->i . "\");\n";
		}else{
			return "xBackground(\"" . $this->e->getId() . "\", \"" . $this->c . "\");\n";
		}
	}
}


?>