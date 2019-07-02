<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the
 * items in the panel is determined by the panel type.
 */
class Panel extends Component implements Sizeable, Alignable{

	/**
	 * an array of Actions to be executed when clicked
	 */

	/* components this panel holds */
	private $_components;
	private $_align;
	private $_valign;
	private $_width;
	private $_height;
	private $_nowrap;

	function __construct(){
		parent::__construct();
		$this->_components = array();
		$this->_align = false;
		$this->_valign = false;
		$this->_nowrap = false;

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

	// /**
	 // * sets the no wrap for this
	 // */
	// public function setNoWrap($wrap){
		// if(!is_bool($wrap)){
			// throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		// }else{
			// $this->_nowrap = $wrap;
		// }
	// }
	//
	// /**
	 // * gets the no wrap for this
	 // */
	// public function isNoWrap(){
		// return $this->_nowrap;
	// }


	/**
	 * sets the horizontal alignment within this panel
	 */
	public function setAlign($align){
		if((strcasecmp($align, "left") != 0)  &&
		   (strcasecmp($align, "right") != 0) &&
		   ($align !== false) &&
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
		   ($valign !== false) &&
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

	public function add(Component $comp, $one=false, $two=false){
		if(!is_object($comp)){
			throw new IllegalArgumentException("argument to add must not be null");
		}
		$this->_components[] = $comp;
	}

	public function remove(Component $comp, $one=false){
		$index = array_search($comp, $this->_components, true);
		if(isset($this->_components[$index])){
			array_splice($this->_components, $index, 1);
		}
	}

	public function getComponents(){
		return $this->_components;
	}

}
?>
