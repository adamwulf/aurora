<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the
 * items in the panel is determined by the panel type.
 */
class Icon extends Component implements Sizeable, Alignable{

	/**
	 * an array of Actions to be executed when clicked
	 */
	private $_url;

	private $_align;
	private $_valign;
	private $_width;
	private $_height;

	function __construct($url){
		parent::__construct();
		if(!is_string($url)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type string");
		}
		$this->_url = $url;

		$this->setAlign("center");
		$this->setValign("middle");
	}


	public function getWidth(){
		return $this->_width;
	}

	public function setWidth($w){
		$this->_width = $w;
	}

	public function getHeight(){
		return $this->_height;
	}

	public function setHeight($h){
		$this->_height = $h;
	}


	/**
	 * sets the horizontal alignment within this panel
	 */
	public function setAlign($align){
		if((strcasecmp($align, "left") != 0)  &&
		   (strcasecmp($align, "right") != 0) &&
		   (strcasecmp($align, "center") != 0)){
			   throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either \"left\" \"right\" \"center\" or false");
		}else{
			$this->_align = $align;
		}

	}

	/**
	 * gets the horizontal alignment within this panel
	 */
	public function getAlign(){
		return $this->_align;
	}

	/**
	 * sets the vertical alignment within this panel
	 */
	public function setValign($valign){
		if((strcasecmp($valign, "top") != 0)    &&
		   (strcasecmp($valign, "bottom") != 0) &&
		   (strcasecmp($valign, "middle") != 0)){
			   throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either \"top\" \"bottom\" \"middle\" or false");
		}else{
			$this->_valign = $valign;
		}

	}

	/**
	 * @return the vertical alignment within this panel
	 */
	public function getValign(){
		return $this->_valign;
	}


	public function setURL($u){
		if(!is_string($u)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type string");
		}
		$this->_url = $u;
	}

	public function getURL(){
		return $this->_url;
	}
}
?>
