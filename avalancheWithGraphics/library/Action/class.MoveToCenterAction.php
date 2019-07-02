<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class MoveToCenterAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}

	public function toJS(){
		$width = "xWidth(\"" . $this->e->getId() . "\") / 2";
		return "xMoveTo(\"" . $this->e->getId() . "\", xScrollLeft() + (xClientWidth() / 2) - $width, xScrollTop() + 200);\n";
	}
}


?>