<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class QuotePanel extends Panel{

	private $indent;
	
	/**
	 * basic constructor
	 */
	function __construct($i = 10){
		parent::__construct();
		if(!is_integer($i)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an integer");
		}
		$this->indent = $i;
	}
	
	function getIndent(){
		return $this->indent;
	}
	
	function setIndent($i){
		if(!is_integer($i)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an integer");
		}
		$this->indent = $i;
	}
}



?>
