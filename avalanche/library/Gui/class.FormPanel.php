<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class FormPanel extends Panel{

	private $_location;
	private $_post;
	private $_on_submit;
	private $_hidden_fields;
	private $_enc_type;
	private $_name;
	
	function __construct($location){
		if(!is_string($location)){
			throw new IllegalArgumentException("the first argument to " . __METHOD__ . " must be a string");
		}
		parent::__construct();
		$this->_on_submit = array();
		$this->_location = $location;
		$this->_post = true;
		$this->_hidden_fields = array();
		$this->_enc_type = false;
		$this->_name = "";
	}
	
	public function setName($n){
		if(!is_string($n)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$this->_name = $n;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setEncType($type){
		if(!is_string($type) && $type !== false){
			throw new IllegalArgumentException("input to " . __METHOD__ . " must be false or a string");
		}
		$this->_enc_type = $type;
	}
	
	public function getEncType(){
		return $this->_enc_type;
	}
	
	public function addHiddenField($fieldname, $value){
		if(!is_string($fieldname)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($value)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		$this->_hidden_fields[] = array("field" => $fieldname, "value" => $value);
	}
	public function getHiddenFields(){
		return $this->_hidden_fields;
	}
	
	public function setLocation($location){
		if(!is_string($location)){
			throw new IllegalArgumentException("the first argument to " . __METHOD__ . " must be a string");
		}
		$this->_location = $location;
	}
	
	public function getLocation(){
		return $this->_location;
	}

	/**
	 * functions to set the method of the form
	 */
	public function setAsPost(){
		$this->_post = true;
	}

	/**
	 * removes a style from this element if the style is present, otherwise does nothing
	 */
	public function setAsGet(){
		$this->_post = false;
	}

	public function isGet(){
		return !$this->_post;
	}
	
	public function isPost(){
		return $this->_post;
	}


	/**
	 * adds an action that will be invoked when this document loads
	 */
	public function addAction(NonKeyAction $a){
		$this->_on_submit[] = $a;
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_submit);
		if(isset($this->_on_submit[$index])){
			array_splice($this->_on_submit, $index, 1);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getActions(){
		return $this->_on_submit;	 
	 }

}



?>
