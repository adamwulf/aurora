<?
/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class Text extends Component implements Actionable{

	private $_on_click;
	private $_str;
	private $_align;
	
	function __construct($str){
		parent::__construct();
		$this->_on_click = array();
		$this->_on_dbl_click = array();
		$this->_on_mouse_over = array();
		if(is_string($str)){
			$this->_str = $str;
		}else{
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be a string.");
		}
		$this->_align = false;
	}
	
	public function getText(){
		return $this->_str;
	}
	
	public function setText($str){
		if(is_string($str)){
			$this->_str = $str;
		}else{
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be a string.");
		}
	}

	/**
	 * gets the horizontal alignment within this text
	 */
	function getAlign(){
		return $this->_align;
	}
	
	/**
	 * sets the vertical alignment within this text
	 */
	function setAlign($align){
		if((strcasecmp($align, "left") != 0)    &&
		   (strcasecmp($align, "center") != 0) &&
		   (strcasecmp($align, "right") != 0)){
			   throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either \"left\" \"center\" \"right\" or false");
		}else{
			$this->_align = $align;
		}
		
	}

	
	/**
	 * adds an action that will be invoked when this document loads
	 */
	public function addAction(NonKeyAction $a){
		$this->_on_click[] = $a;
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_click);
		if(isset($this->_on_click[$index])){
			array_splice($this->_on_click, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getActions(){
		return $this->_on_click;	 
	 }


	/**
	 * adds an action that will be invoked when this component is mouse over'd
	 */
	public function addMouseOverAction(NonKeyAction $a){
		$this->_on_mouse_over[] = $a;
	}
	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeMouseOverAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_mouse_over);
		if(isset($this->_on_mouse_over[$index])){
			array_splice($this->_on_mouse_over, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getMouseOverActions(){
		return $this->_on_mouse_over;	 
	 }

	 
	 /**
	 * adds an action that will be invoked when this component is double clicked
	 */
	public function addDblClickAction(NonKeyAction $a){
		$this->_on_dbl_click[] = $a;
	}
	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeDblClickAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_dbl_click);
		if(isset($this->_on_dbl_click[$index])){
			array_splice($this->_on_dbl_click, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getDblClickActions(){
		return $this->_on_dbl_click;	 
	 }

}



?>
