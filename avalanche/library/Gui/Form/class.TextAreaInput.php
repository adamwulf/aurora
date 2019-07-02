<?

/**
 * This class represents an form text input.
 */
class TextAreaInput extends TextInput{
	
	/**
	 * the default value for this text input
	 */
	protected $_value;
	protected $_max_length;
	protected $_rows;
	protected $_cols;
	protected $_word_wrap;
	/**
	 * the default size for this text input
	 */
	
	
	function __construct($str = ""){
		parent::__construct();
		
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		
		$this->_value = $str;
		$this->_cols = 30;
		$this->_rows = 5;
		$this->_max_length = 0;
		$this->_word_wrap = true;
	}
	
	public function wordWrapOn(){
		$this->_word_wrap = true;
	}
	
	public function wordWrapOff(){
		$this->_word_wrap = false;
	}
	
	public function wordWrap(){
		return $this->_word_wrap;
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
	 * returns the columns for this text input, (number of characters wide)
	 */
	public function getCols(){
		return $this->_cols;
	}
	
	public function setCols($c){
		if(!is_integer($c)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an integer");
		}
		if($c <= 0){
			throw new IllegalArgumentException("Integer argument to " . __METHOD__ . " must be greater than 0");
		}
		$this->_cols = $c;
	}

	/**
	 * returns the rows for this text input, (number of characters tall)
	 */
	public function getRows(){
		return $this->_rows;
	}
	
	public function setRows($r){
		if(!is_integer($r)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an integer");
		}
		if($r <= 0){
			throw new IllegalArgumentException("Integer argument to " . __METHOD__ . " must be greater than 0");
		}
		$this->_rows = $r;
	}
}
?>
