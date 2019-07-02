<?
/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the
 * items in the panel is determined by the panel type.
 */
class Link extends Text{

	private $_url;
	private $_target;

	function __construct($str, $url){
		parent::__construct($str);
		if(!is_string($url)){
			throw new IllegalArugmentException("Argument 2 to " . __METHOD__ . " must be a string");
		}
		$this->_url = $url;
		$this->_target = false;
	}

	public function setTarget($t){
		if(!is_string($t)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$this->_target = $t;
	}

	public function getTarget(){
		return $this->_target;
	}

	public function getURL(){
		return $this->_url;
	}

	public function setURL($url){
		if(!is_string($url)){
			throw new IllegalArugmentException("Argument to " . __METHOD__ . " must be a string");
		}
		$this->_url = $url;
	}

}



?>
