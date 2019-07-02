<?

/**
 * This class represents an form text input.
 */
class RadioInput extends CheckedInput{
	
	/**
	 * the default value for this text input
	 */
	protected $_value;
	protected $_checkedHuh;
	protected $_label;
	/**
	 * the default size for this text input
	 */
	
	
	function __construct($str = ""){
		parent::__construct();
		
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		
		$this->_value = "";
		$this->_checkedHuh = false;
		$this->_label = $str;
	}
	
	public function setLabel($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		$this->_label = $str;
	}
	
	public function getLabel(){
		return $this->_label;
	}
	
	public function getValue(){
		return $this->_value;
	}
	
	public function setValue($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		$this->_value = $str;
	}
	
	public function setChecked($b){
		if(!is_bool($b)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a boolean");
		}
		if($b){
			$this->_checkedHuh = $b;
		}
	}
	
	public function isChecked(){
		return $this->_checkedHuh;
	}
	
	/**
	 * loads this fields value from form input if possible
	 */
	public function loadFormValue($my_array){
		$name = $this->getName();
		if(isset($my_array[$name])){
			$checkedHuh = true;
			$val = $my_array[$name];
			$this->setValue($val);
			return true;
		}else{
			$checkedHuh = false;
			return false;
		}
	}
}
?>
