<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class DisplayInlineAction extends NonKeyAction{
	public function __construct(Component $e){
		$this->e = $e;
	}
	
	public function toJS(){
		return "xDisplayInline(\"" . $this->e->getId() . "\");\n";
	}
}


?>