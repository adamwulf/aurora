<?

/**
 * This class represents an form text input.
 */
class FileInput extends Input{
	
	
	function __construct($str = ""){
		parent::__construct($str);
		
		$this->_size = 30;
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
	
	public function loadFormValue($data){
		// noop
	}
}
?>
