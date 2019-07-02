<?

/**
 * resizes an object to the same height as it's parent
 */
class ParentHeightAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xHeight(\"" . $this->e->getId() . "\", xHeight(xParent(\"" . $this->e->getId() . "\")));\n";
	}
}


?>