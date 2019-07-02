<?


/**
 * this class represents an abstract Gui Component. All displayable gui components will
 * extends this class.
 */
abstract class Component extends StyledElement implements Actionable{

	/**
	 * this variable holds the id of the most recent Component to get an id, or 0
	 * if no id's have been handed out.
	 */
	private static $_nextId = 0;

	/**
	 * the id starts out as unset. it is set with the first call to getId()
	 */
	private $_id = false;

	/**
	 * all components default to visible
	 */
	private $_visible = true;

	private $_on_mouse_over;
	private $_on_mouse_out;
	private $_on_click;
	private $_on_dbl_click;

	final protected function getNextId(){
		Component::$_nextId++;
		return Component::$_nextId;
	}

	function __construct(){
		parent::__construct();
		$this->setStyle(new Style());
		// init actions
		$this->_on_mouse_over = array();
		$this->_on_mouse_out = array();
		$this->_on_click = array();
		$this->_on_dbl_click = array();
	}


	/**
	 * returns a string representation of this components id
	 */
	final public function getId(){
		if($this->_id === false){
			$this->_id = $this->getNextId();
		}
		return get_class($this) . "_" . $this->_id;
	}


	public function setVisible($b){
		if(!is_bool($b)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
		$this->_visible = $b;
	}

	public function isVisible(){
		return $this->_visible;
	}

	/** actions */

	/**
	 * adds an action that will be invoked when this component is clicked
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
	 * adds an action that will be invoked when this component is mouse over'd
	 */
	public function addMouseOutAction(NonKeyAction $a){
		$this->_on_mouse_out[] = $a;
	}
	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeMouseOutAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_mouse_out);
		if(isset($this->_on_mouse_out[$index])){
			array_splice($this->_on_mouse_out, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getMouseOutActions(){
		return $this->_on_mouse_out;
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
