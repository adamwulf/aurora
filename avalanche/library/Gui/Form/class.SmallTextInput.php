<?

/**
 * This class represents an form text input.
 */
class SmallTextInput extends TextInput{
	
	
	/**
	 * the default size for this text input
	 */
	protected $_max_length;
	
	/**
	 * true if the field should display as a password box
	 */
	protected $_is_password;
	
	function __construct($str = ""){
		parent::__construct($str);
		
		$this->_size = 30;
		$this->_max_length = 0;
		$this->_is_password = false;
	}
	
	/**
	 * sets the password state of the field
	 */
	 public function setPassword($b){
		 if(!is_bool($b)){
			 throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		 }
		 $this->_is_password = $b;
	 }
	 
	 public function isPassword(){
		 return $this->_is_password;
	 }
	
	/**
	 * returns the size for this text input, (number of characters wide)
	 */
	public function getSize(){
		return $this->_size;
	}
	
	public function setSize($size){
		if(!is_integer($size)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an integer");
		}
		if($size <= 0){
			throw new IllegalArgumentException("Integer argument to " . __METHOD__ . " must be greater than 0");
		}
		$this->_size = $size;
	}


	/**
	 * returns the size for this text input, (number of characters wide)
	 */
	public function getMaxLength(){
		return $this->_max_length;
	}
	
	/**
	 * sets the maximum length of the input to this field. setting to 0 represents no max length.
	 */
	public function setMaxLength($len){
		if(!is_integer($len)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an integer");
		}
		if($len < 0){
			throw new IllegalArgumentException("Integer argument to " . __METHOD__ . " must be greater than or equal to 0");
		}
		$this->_max_length = $len;
	}
}
?>
