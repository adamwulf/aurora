<?
/**
 * this is a utility class for DropDownInput
 */
class DropDownOption {
	
	/**
	 * the actual value of this option
	 */
	protected $_option;
	protected $_display;
	
	
	
	/**
	 * if this option is selected
	 */
	protected $_selected;
	
	
	public function __construct($display, $value){
		if(!is_string($display)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($value)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		
		$this->_value = $value;
		$this->_display = $display;
		$this->_selected = false;
	}
	
	public function setValue($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be a string");
		}
		$this->_value = $str;
	}
	
	public function getValue(){
		return $this->_value;
	}
	
	public function setDisplay($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be a string");
		}
		$this->_display = $str;
	}

	public function getDisplay(){
		return $this->_display;
	}
	
	public function setSelected($b){
		if(!is_bool($b)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a boolean");
		}
		$this->_selected = $b;
	}
	
	public function isSelected(){
		return $this->_selected;
	}
}


/**
 * This class represents an form dropdown.
 */
class DropDownInput extends Input{
	protected $_options;
	
	protected $_size;
	
	public function setSize($size){
		if(!is_int($size)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an int");
		}
		$this->_size = $size;
	}
	
	public function getSize(){
		return $this->_size;
	}
	
	function __construct(){
		parent::__construct();
		
		$this->_options = array();
		$this->_size = 1;
	}
	
	public function getOptions(){
		return $this->_options;
	}
	
	public function addOption(DropDownOption $opt){
		$this->_options[] = $opt;
	}
	
	public function removeOption(DropDownOption $opt){
		$index = array_search($opt, $this->_options);
		if(isset($this->_options[$index])){
			array_splice($this->_options, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	public function setValue($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$opts = $this->getOptions();
		$not_selected_yet = true;
		foreach($opts as $opt){
			if($opt->getValue() == $str && $not_selected_yet){
				$not_selected_yet = false;
				$opt->setSelected(true);
			}else{
				$opt->setSelected(false);
			}
		}
	}
	
	
	/**
	 * loads this fields value from form input if possible
	 */
	function loadFormValue($my_array){
		$name = $this->getName();
		if(isset($my_array[$name])){
			$val = $my_array[$name];
			$this->setValue($val);
			return true;
		}else{
			return false;
		}
	}
}
?>
