<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class HorizontallyMoveToCenterAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xMoveTo(\"" . $this->e->getId() . "\", xScrollLeft() + ((xClientWidth() - xWidth(\"" . $this->e->getId() . "\")) / 2), xPageY(\"" . $this->e->getId() . "\"));\n";
	}
}


?>