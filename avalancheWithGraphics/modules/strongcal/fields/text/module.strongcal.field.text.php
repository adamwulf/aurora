<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
//----------------------------------------------------------------------//
//  sub class for the strongcal module. this class represents a field	//
//  in a calendar.							//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
//----------------------------------------------------------------------//
//									//
//  NOTE: ALL MODULES WILL BE INCLUDE *INSIDE* OF THE avalanche'S MAIN	//
//	CLASS. SO REFER ANY FUNCTION CALLS THAT ARE *OUTSIDE* OF YOUR	//
//	CLASS TO avalanche BY USING *THIS->functionhere*		//
//									//
//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////         field           //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this field class defines a field that knows its home calendar//
// and own id. when updated, it will update mysql.		//
//////////////////////////////////////////////////////////////////
// THIS CLASS NEEDS UPDATING SO THAT MYSQL WILL UPDATE		//
// APPROPRIATELY WHEN THE FIELD IS RESET.			//
//////////////////////////////////////////////////////////////////
class module_strongcal_field_text extends module_strongcal_field {

	private $len;

	function __construct($avalanche){
		parent::__construct($avalanche);
		$this->_id 	   = false;
		$this->_prompt     = "text input: ";
		$this->_field      = false;
		$this->_type       = "text";
		$this->_value      = "";
		$this->_form_order = false;
		$this->_removeable = false;
		$this->_size       = 20;
		$this->_style       = false;
		$this->_ics       = false;
		$this->_loaded = true;
	}

	//////////////////////////////////////////////////////////////////
	//  init($cal, $id)						//
	//--------------------------------------------------------------//
	//  input: $cal - the calendar object to which this field	//
	//		  belongs					//
	//  output: the id of this field				//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function init($cal=false, $id=false){
		parent::init($cal, $id);
		$this->setLength(20);
	}


 	function displayType(){
		if(!$this->_loaded){
			$this->reload();
		}
		return "SMALL TEXT";
	}

	//////////////////////////////////////////////////////////////////
	//  load_options ($prefix, $my_array)				//
	//  input: the prefix of the variable				//
	//	   the array of variables, in format of $_POST or $_GET	//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//////////////////////////////////////////////////////////////////
	function load_options($prefix, $my_array){
		// noop
	}

	//////////////////////////////////////////////////////////////////
	//  load_form_value ($prefix, $my_array)			//
	//  input: the prefix of the variable				//
	//	   the array of variables, in format of $_POST or $_GET	//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//////////////////////////////////////////////////////////////////
	function load_form_value($prefix, $my_array){
		$obj = $this->toGUI($prefix);
		if($obj->loadFormValue($my_array)){
			$this->set_value($obj->getValue());
			return true;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  input_value ()						//
	//  input: none							//
	//  output: the value to go into the input_options function	//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//////////////////////////////////////////////////////////////////
	function input_value(){
		return $this->_value;

	}


	//////////////////////////////////////////////////////////////////
	//  input_options ($prefix, $skin)			//
	//  input: the name to give variable				//
	//	   the value for input, in format of $_POST or $_GET	//
	//	   the skin to write it in				//
	//  output: the html for the input box to get value for new	//
	//	    field						//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//////////////////////////////////////////////////////////////////
	function input_options($name, $skin){
		return array();
	}

	//////////////////////////////////////////////////////////////////
	//  style_options()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: an array describing the possible styles of this	//
	//		field						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function style_options(){
		return array();
	}


	//////////////////////////////////////////////////////////////////
	//  to_add()							//
	//  input: none							//
	//  output: an array with all the field values in it indexed	//
	//	    by parameter name					//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//								//
	//////////////////////////////////////////////////////////////////
	function to_add(){
		return array("type" => $this->type(),
			     "value" => $this->value(),
			     "size" => $this->size(),
			     "style" => $this->style(),
			     "MYSQL_TYPE" => "TEXT");
	}

	//////////////////////////////////////////////////////////////////
	//  sizeable()							//
	//  input: none							//
	//  output: true if this field can be sized. false otherwise.	//
	//								//
	//  precondition:						//
	//	object must have been initialized			//
	//								//
	//  postcondition:						//
	//	this object is loaded with the value			//
	//								//
	//////////////////////////////////////////////////////////////////
	function sizeable(){
		return false;
	}


	function setLength($len){
		if(!is_integer($len)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer");
		}
		$this->len = $len;
	}


	//////////////////////////////////////////////////////////////////
	//  toHTML($skin)						//
	//	the prefix should be unique to this field in the form	//
	//	in which it is used. the resulting html will have the	//
	//	name of [$prefix][field_name].				//
	//	also, $skin must be a valid skin object already set to	//
	//	the appropriate layer.					//
	//								//
	//	regarding $value, input must be of the following:	//
	//	format for specific field types.			//
	//	 date: "yyyy-mm-dd"					//
	//	 time: "hh:mm"  (24 hour format)			//
	//	 select: the value of one of the options		//
	//	         or value of none of the options to force	//
	//		    no value and override possible default	//
	//	 text: any string					//
	//	 multi-select: an array of values			//
	//	 checkbox: 1 or 0					//
	//								//
	//--------------------------------------------------------------//
	//  input: a skin to print this field				//
	//  output: string html form input				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//	object must have vars set				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function toHTML($prefix, $skin, $override=false){
		$value = $this->value();
		if($override){
			$override = htmlspecialchars($override, ENT_QUOTES);
		}

		$value = htmlspecialchars($this->value(), ENT_QUOTES);
		if($override){
			$value=$override;
		}

		$input = $skin->input("name='$prefix" . $this->field() . "' value='" . $value . "' size='" . $this->len . "'");
		return $input;
	}

	function toGUI($prefix, $override = false){
		$value = (string)$this->value();
		if($override){
			$value=$override;
		}

		$text = new SmallTextInput();
		$text->setValue($value);
		$text->setName($prefix . $this->field());
		$text->setSize($this->len);
		$text->getStyle()->setBorderWidth(1);
		$text->getStyle()->setBorderStyle("solid");
		$text->getStyle()->setBorderColor("black");
		return $text;
	}


	function toHiddenHTML($prefix, $override=false){
		$value = $this->value();
		if($override){
			$override = htmlspecialchars($override, ENT_QUOTES);
		}

		$value = htmlspecialchars($this->value(), ENT_QUOTES);
		if($override){
			$value=$override;
		}

		$input = "<input type='hidden' name='$prefix" . $this->field() . "' value='" . $value . "'>";
		return $input;
	}
}


?>