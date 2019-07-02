<?

/**
 * This class represents an form text input.
 */
class ButtonInput extends Input{
	
	/**
	 * the default value for this text input
	 */
	protected $_value;
	
	
	
	function __construct($str = ""){
		parent::__construct();
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		
		$this->_value = $str;
		
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
	
	public function loadFormValue($data){
		// noop
	}
}
?>
