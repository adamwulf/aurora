<?

/**
 * This class represents an form text input.
 */
abstract class TextInput extends Input{
	
	/**
	 * the default value for this text input
	 */
	protected $_value;
	
	/**
	 * the default size for this text input
	 */
	
	
	function __construct($str = ""){
		parent::__construct();
		
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		
		$this->_value = $str;
		$this->_size = 30;
		$this->_max_length = 0;
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
	
	/**
	 * loads this fields value from form input if possible
	 */
	function loadFormValue($my_array){
		$name = $this->getName();
		if(isset($my_array[$name])){
			$val = $my_array[$name];
			if(get_magic_quotes_gpc()){
				$val = stripslashes($val);
			}
			$this->setValue($val);
			return true;
		}else{
			return false;
		}
	}
}
?>
