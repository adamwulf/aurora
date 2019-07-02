<?


/**
 * This class represents an abstract form input. it can be extended for text or select inputs etc. 
 * inputs can be clicked, and their value can change. actions can be registered for each of these events.
 */
abstract class Input extends Component{

	/**
	 * an array of Actions to be executed when clicked
	 */
	private $_on_click;
	/**
	 * an array of Actions to be executed when the value changes
	 */
	private $_on_change;
	/**
	 * an array of Actions to be executed when the input gains focus
	 */
	private $_on_focus_gained;
	/**
	 * an array of Actions to be executed when the input loses focus
	 */
	private $_on_focus_lost;
	/**
	 * an array of Actions to be executed when the input has focus when a key is pressed
	 */
	private $_on_key_press;
	/**
	 * an array of Actions to be executed when the input has focus when a key is released
	 */
	private $_on_key_up;
	/**
	 * an array of Actions to be executed when the input has focus when a mousebutton is released
	 */
	private $_on_mouse_up;
	/**
	 * an array of Actions to be executed when the input has focus when a mousebutton is depressed
	 */
	private $_on_mouse_down;
	/**
	 * boolean if the field is read only or not
	 */
	private $_read_only;
	/**
	 * boolean if the input is disabled or not
	 */
	private $_disabled;
	/**
	 * the name of this field
	 */
	private $_name;
	
	
	function __construct(){
		parent::__construct();
		$this->_on_click = array();
		$this->_on_change = array();
		$this->_on_focus_gained = array();
		$this->_on_focus_lost = array();
		$this->_on_key_press = array();
		$this->_on_key_up = array();
		$this->_read_only = false;
		$this->_disabled = false;
		$this->_n = "";
	}

	/**
	 * load this form field's data from form input
	 */
	abstract public function loadFormValue($my_array);
	
	
	/**
	 * returns the name of the input, or the empty string for no name
	 */
	public function getName(){
		return $this->_name;
	}
	
	/**
	 * sets the name for this input (null string represents no name)
	 */
	public function setName($n){
		if(!is_string($n)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		$this->_name = $n;
	}
	
	
	/**
	 * returns true if the input is readonly, false otherwise
	 */
	public function isReadOnly(){
		return $this->_read_only;
	}
	
	/**
	 * sets if the input should be readonly
	 */
	public function setReadOnly($ro){
		if(!is_bool($ro)){
			throw new IllegalArgumentException("Argument to " . __METHOD . " must be a boolean");
		}
		$this->_read_only = $ro;
	}
	
	public function setDisabled($d){
		if(!is_bool($d)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a boolean");
		}
		$this->_disabled = $d;
	}
	
	public function isDisabled(){
		return $this->_disabled;
	}
	
	
	
	
	//
	//
	//  ACTION SETTERS AND GETTERS BELOW
	//
	//
	
	/**
	 * adds an action that will be invoked when this input is clicked
	 */
	public function addClickAction(NonKeyAction $a){
		$this->_on_click[] = $a;
	}

	/**
	 * removes an click Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeClickAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_click);
		if(isset($this->_on_click[$index])){
			array_splice($this->_on_click, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the value change actions registered with this input
	 */
	public function getClickActions(){
		return $this->_on_click;	 
	}
	
	/**
	 * adds an action that will be invoked when a key is pressed and this input has focus
	 */
	public function addKeyPressAction(Action $a){
		$this->_on_key_press[] = $a;
	}
	
	/**
	 * removes an keypress Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeKeyPressAction(Action $a){
		$index = array_search($a, $this->_on_key_press);
		if(isset($this->_on_key_press[$index])){
			array_splice($this->_on_key_press, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the keypress actions registered with this input
	 */
	public function getKeyPressActions(){
		return $this->_on_key_press;	 
	}
	
	/**
	 * adds an action that will be invoked when a key is released and this input has focus
	 */
	public function addKeyUpAction(Action $a){
		$this->_on_key_up[] = $a;
	}
	
	/**
	 * removes an keypress Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeKeyUpAction(Action $a){
		$index = array_search($a, $this->_on_key_up);
		if(isset($this->_on_key_up[$index])){
			array_splice($this->_on_key_up, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the keypress actions registered with this input
	 */
	public function getKeyUpActions(){
		return $this->_on_key_up;
	}
	
	/**
	 * adds an action that will be invoked when a mouse button is released and this input has focus
	 */
	public function addMouseUpAction(Action $a){
		$this->_on_mouse_up[] = $a;
	}
	/**
	 * removes an mouse up Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeMouseUpAction(Action $a){
		$index = array_search($a, $this->_on_mouse_up);
		if(isset($this->_on_mouse_up[$index])){
			array_splice($this->_on_mouse_up, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * returns an array of the mouse up actions registered with this input
	 */
	public function getMouseUpActions(){
		return $this->_on_mouse_up;
	}
	
	
	/**
	 * adds an action that will be invoked when a mouse button is pressed and this input has focus
	 */
	public function addMouseDownAction(Action $a){
		$this->_on_mouse_down[] = $a;
	}
	/**
	 * removes an mouse down Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeMouseDownAction(Action $a){
		$index = array_search($a, $this->_on_mouse_down);
		if(isset($this->_on_mouse_down[$index])){
			array_splice($this->_on_mouse_down, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	/**
	 * returns an array of the mouse down actions registered with this input
	 */
	public function getMouseDownActions(){
		return $this->_on_mouse_down;
	}
	

	/**
	* adds an action that will be invoked when this input's value is changed
	*/
	public function addChangeAction(NonKeyAction $a){
		$this->_on_change[] = $a;
	}

	/**
	 * removes an click Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeChangeAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_change);
		if(isset($this->_on_change[$index])){
			array_splice($this->_on_change, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the value change actions registered with this input
	 */
	 public function getChangeActions(){
		return $this->_on_change;	 
	 }



	/**
	 * adds an action that will be invoked when this input gains focus
	 */
	public function addFocusGainedAction(NonKeyAction $a){
		$this->_on_focus_gained[] = $a;
	}

	/**
	 * removes an click Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeFocusGainedAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_focus_gained);
		if(isset($this->_on_focus_gained[$index])){
			array_splice($this->_on_focus_gained, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the focus gained actions registered with this input
	 */
	 public function getFocusGainedActions(){
		return $this->_on_focus_gained;	 
	 }


	 
	/**
	 * adds an action that will be invoked when this input loses focus
	 */
	public function addFocusLostAction(NonKeyAction $a){
		$this->_on_focus_lost[] = $a;
	}

	/**
	 * removes an click Action from this input
	 * @return true if successful, false otherwise
	 */
	public function removeFocusLostAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_focus_lost);
		if(isset($this->_on_focus_lost[$index])){
			array_splice($this->_on_focus_lost, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the focus loses actions registered with this input
	 */
	 public function getFocusLostActions(){
		return $this->_on_focus_lost;
	 }
}
?>
