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
class module_strongcal_field_check extends module_strongcal_field {

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
				$this->_style       = $id['style'];
				$this->_ics       = $id['ics'];
				$this->_loaded = true;
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "checkbox: ";
			$this->_field      = false;
			$this->_type       = "check";
			$this->_value      = "0";
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = 10;
			$this->_style       = false;
			$this->_ics       = false;
			$this->_loaded = true;
		}
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
			$this->load();
		}
		return "CHECKBOX";
	}


	//////////////////////////////////////////////////////////////////
	//  display_value($cal_id=false, $event_id=false)						//
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
	function display_value($cal_id=false, $event_id=false){
		if(!$this->_loaded){
			$this->load();
		}
		if($this->_value)
			return "YES";
		else
			return "NO";
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
		$name = $prefix . $this->field();
		if(isset($my_array[$name])){
			$val = $my_array[$name];
			$this->_value = (string) $val;
			return true;
		}else{
			//noop
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
			$value = htmlspecialchars($this->value(), ENT_QUOTES);
			if($override || !$override && $this->_value){
				$checked="CHECKED";
			}else{
				$checked="";
			}
			if($override){
				$value = $override;
			}
			$input = $skin->check("name='$prefix" . $this->field() . "' value='YES' $checked");
			return $input;
	}


	function toHiddenHTML($prefix, $override=false){
			$value = $this->value();
			if($override || !$override && $this->_value){
				$value="YES";
			}else{
				$value="";
			}
			if($override){
				$value = $override;
			}
			$input = "<input type='hidden' name='$prefix" . $this->field() . "' value='$value'>";
			return $input;
	}

	function toGUI($prefix){
		$value = $this->value();
			
		$check = new CheckInput("");
		$check->setName($prefix . $this->field());
		$check->setValue("YES");
		if($value){
			$check->setChecked(true);
		}
		
		
		
		return $check;
		//throw new Exception("This has not yet been defined");
	}
}


?>