<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class IconWithText extends Icon{

	private $_text;
	
	function __construct($url, $text){
		parent::__construct($url);
		if(!$text instanceof Text){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type Text");
		}
		$this->_text = $text;
	}

	public function setText($t){
		if(!$t instanceof Text){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type Text");
		}
		$this->_text = $t;
	}
	
	public function getText(){
		return $this->_text;
	}
}
?>
