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
class module_strongcal_field_url extends module_strongcal_field {

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
		if(is_object($cal) && $id){
			$this->_calendar = $cal;
			if(is_array($id)){
				$this->_id 	   = $id['id'];
				$this->_prompt     = $id['prompt'];
				$this->_field      = $id['field'];
				$this->_type       = $id['type'];
				$this->_value      = $id['value'];
				$this->_form_order = $id['form_order'];
				$this->_removeable = $id['removeable'];
				$this->_size       = $id['size'];
				$this->_style       = (int) $id['style'];
				$this->_ics       = $id['ics'];
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "url: ";
			$this->_field      = "";
			$this->_type       = "url";
			$this->_value      = "";
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = 15;
			$this->_style      = 0;
			$this->_ics        = false;
			$this->_loaded     = true;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  display_value()		//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the value of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function display_value(){
		$strongcal = $this->avalanche->getModule("strongcal");

		if(!$this->_loaded){
			$this->reload();
		}
		
		return $this->value();
	}

	//////////////////////////////////////////////////////////////////
	//  displayType()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the type of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function displayType(){
		if(!$this->_loaded){
			$this->reload();
		}
		return "URL";
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
		$name = $prefix . "_default_type";
		if(isset($my_array[$name])){
			$current = $my_array[$name];
		}
		$this->_value = substr($this->value,5,1) . $current;
		
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
	function input_options($prefix, $skin){
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
			     "value" => addslashes($this->value()),
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
		if(!is_object($skin)){
			throw new IllegalArgumentException("2nd argument to " . __METHOD__ . " must be a skin object");
		}
		
		$buffer = $this->avalanche->getSkin("buffer");
		$value = $this->value();
		if($override){
			$value = $override;
		}
		$values = explode("\n", $value);
		$text = trim($values[0]);
		$link = trim($values[1]);

		$text_inp  = "<input name='$prefix" . $this->field() . "_text' value='$text' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2'>";
		$link_inp  = "<input name='$prefix" . $this->field() . "_url' value='$link' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2'>";

		$input = $buffer->table($buffer->tr($buffer->td(
					$buffer->table($buffer->tr($buffer->td($skin->font("Link Text"))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'")))
					.
					$buffer->tr($buffer->td(
					$buffer->table($buffer->tr($buffer->td($skin->font($text_inp))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'")))
					.
					$buffer->tr($buffer->td(
					$buffer->table($buffer->tr($buffer->td($skin->font("Link URL"))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'")))
					.
					$buffer->tr($buffer->td(
					$buffer->table($buffer->tr($buffer->td($skin->font($link_inp))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'")))
			 , "cellpadding='0' cellspacing='0' border='0'");
		return $input;
	}

	function toGui($prefix, $override = false){
		$value = $this->display_value();
		if(!is_string($value)){
			$value = "\n";
		}
		if($override !== false){
			$value = $override;
		}
		
		$url = new URLInput($value);
		$url->setName($prefix . $this->field());
		
		return $url;
	}

	function toHiddenHTML($prefix, $override=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$value = $this->value();
		if($override){
			$value = $override;
		}
		
		$values = explode("\n", $value);
		$text = trim($values[0]);
		$link = trim($values[1]);

		if($override){
			$value = $override;
		}

		$text_inp  = "<input type='hidden' name='$prefix" . $this->field() . "_text' value='$text'>";
		$link_inp  = "<input type='hidden' name='$prefix" . $this->field() . "_url' value='$link'>";


		$input = $text_inp . $link_inp;
		return $input;

	}
}


?>