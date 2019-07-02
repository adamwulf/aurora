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
class module_strongcal_field_select extends module_strongcal_field {

	protected $_drop_down;


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
		$this->initDropDown();
		if(is_object($cal) && $id){
			$this->_calendar = $cal;
			if(is_array($id)){
				$this->_id 	   = $id['id'];
				$this->_prompt     = $id['prompt'];
				$this->_field      = $id['field'];
				$this->_type       = $id['type'];
				if(get_magic_quotes_runtime()){
					$this->_value      = stripslashes($id['value']);
				}else{
					$this->_value      = $id['value'];
				}
				$this->_form_order = $id['form_order'];
				$this->_removeable = $id['removeable'];
				$this->_size       = $id['size'];
				$this->_style       = $id['style'];
				$this->_ics       = $id['ics'];
				$this->fix_value();
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "drop down: ";
			$this->_field      = false;
			$this->_type       = "select";
			$this->_value      = "";
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = 10;
			$this->_style       = 0;
			$this->_ics       = false;
			$this->_loaded = true;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  load_event($id)						//
	//--------------------------------------------------------------//
	//  input: the id of the event to load				//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function load_event($id){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId();
			$my_id = $this->getId();
	               	$sql = "SELECT * FROM $tablename WHERE id = '$id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$my_row = mysql_fetch_array($result);
				$this->set_value($my_row[$this->field()]);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * initialize the form field
	 */
	function initDropDown(){
		$this->_drop_down = new DropDownInput();
		$this->_drop_down->setName((string)$this->field());
	}




	//////////////////////////////////////////////////////////////////
	//  fix_value()							//
	//	parses the fields value in to what will be displayed,	//
	//	what will be shown, and what will be selected		//
	//	(only for select boxes and multiple select boxes	//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function fix_value(){
		$value = $this->_value;
		$value_list = explode ("\n", $value);
		$this->initDropDown();
		if(count($value_list) % 3 == 0){
			for($i=0;$i<count($value_list);$i+=3){
				$to_show = htmlspecialchars($value_list[$i], ENT_QUOTES);
				$to_show = str_replace(" ", "&nbsp;", $to_show);
				$value_list[$i+1] = trim($value_list[$i+1]);
				$value_list[$i+2] = trim($value_list[$i+2]);
				$new_opt = new DropDownOption(trim($to_show), $value_list[$i+1]);
				$new_opt->setSelected((bool) $value_list[$i+2]);
				$this->_drop_down->addOption($new_opt);
			}
		}
	}


	//////////////////////////////////////////////////////////////////
	//  reload()							//
	//  loads the values for this field				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function reload(){
		module_strongcal_field::reload();
		$this->fix_value();
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
		if($this->style()){
			return "RADIO BUTTONS";
		}else{
			return "DROP DOWN";
		}
	}


	//////////////////////////////////////////////////////////////////
	//  value()							//
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
	function value(){
		$temp = "";
		if(!$this->_loaded){
			$this->reload();
		}
		if(!is_object($this->_calendar) ||
		   $this->field() == "start_date" ||
		   $this->field() == "start_time" ||
		   $this->field() == "end_date" ||
		   $this->field() == "end_time" ||
		   $this->_calendar->canReadEntries()){
			   return $this->_value;
		   }else{
			   return false;
		   }
	}

	function trim($str){
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "", $str);
		return $str;
	}

	//////////////////////////////////////////////////////////////////
	//  display_value($cal_id=false, $event_id=false)		//
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
			$this->reload();
		}
		if(!is_object($this->_calendar) ||
		   $this->field() == "start_date" ||
		   $this->field() == "start_time" ||
		   $this->field() == "end_date" ||
		   $this->field() == "end_time" ||
		   $this->_calendar->canReadEntries()){
			   $ret = "";
			   $opts = $this->_drop_down->getOptions();
			   foreach($opts as $opt){
				   if($opt->isSelected()){
					   if(strlen($ret)){
						   $ret .= "\n";
					   }
					   $ret .= $opt->getDisplay();
				   }
			   }
			   return $ret;
		   }else{
			   return false;
		   }
	}



	//////////////////////////////////////////////////////////////////
	//  set_value($val)						//
	//--------------------------------------------------------------//
	//  input: the value of this field				//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_value($val, $passive=false){
		$ret = module_strongcal_field::set_value($val, $passive);
		$this->fix_value();
		return $ret;
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
		$new_value = "";
		$name = $prefix . $this->field();
		$old_value = explode("\n", $this->_value);
		if(isset($my_array[$name])){
			$text = new SmallTextInput();
			$text->setName($name);
			$text->loadFormValue($my_array);
			$val = $text->getValue();
			for($i=0;$i<count($old_value);$i+=3){
				$str = $old_value[$i+1];
				$new_value .= $old_value[$i] . "\n";
				$new_value .= $str . "\n";
				if($val == $str){
					$new_value .= "1\n";
				}else{
					$new_value .= "\n";
				}
			}
			if(substr($new_value, (strlen($new_value)-1), 1) == "\n"){
				/* to chop off the last \n */
				$new_value = substr($new_value, 0, strlen($new_value)-1);
			}
			$this->set_value($new_value);

			return true;
		}else{
			return false;
		}
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
		$name = $prefix . $this->field();
		$val = $my_array[$name];
		$vals = @explode("\n", $val);
		$this->_value = "";
		for($i=0;$i<count($vals);$i++){
			$this->_value .= $this->trim($vals[$i]) . "\n";
			$this->_value .= $this->trim($vals[$i]) . "\n";
			$this->_value .= "\n";
		}
		$this->_value = substr($this->_value, strlen($this->_value)-1);
		$this->set_value($this->_value);
	}




	//////////////////////////////////////////////////////////////////
	//  input_options ($prefix, $skin)				//
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
		return array(
				array("prompt" => "Type each option on a separate line below:",
				      "input" => $skin->textarea($this->input_value(), "name='$name' cols='25' rows='5'")));
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
		if(!$this->_loaded){
			$this->reload();
		}
		$opts = $this->_drop_down->getOptions();
		foreach($opts as $opt){
			$temp .= $opt->getDisplay() . "\n";
		}

		return substr($temp,0,strlen($temp)-1);

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
		return array(
				array("value" => 0,
				      "display" => "Drop Down"),
				array("value" => 1,
				      "display" => "Radio Buttons"));
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
		$value = $this->_arrayed_value;

		$select = "";
		$my_array = $value;

		if($override){
			$my_array = @explode("\n", $override);
			$val = array();
			for($i=0;$i<count($my_array);$i+=3){
				$val[] = array("display" => trim($my_array[$i]),
						"value" => $my_array[$i+1],
						"selected" => $my_array[$i+2]);
			}
			$my_array = $val;
		}

		if($this->style() == FIELD_NO_STYLE){
			for($i=0;$i<count($my_array);$i++){
				$to_show = $my_array[$i]["display"];
				$to_value = trim($my_array[$i]["value"]);
				$selected = $my_array[$i]["selected"];
				if($selected){
					$selected = "SELECTED";
				}
				$select .= $skin->option($to_value, $to_show, $selected);
			}
			$input = $skin->select($select, "name='$prefix" . $this->field() . "'");
		}else{
			for($i=0;$i<count($my_array);$i++){
				$to_show = $my_array[$i]["display"];
				$to_value = trim($my_array[$i]["value"]);
				$selected = $my_array[$i]["selected"];
				if($selected){
					$selected = "CHECKED";
				}else{
					$selected = "";
				}
				$sample .= $skin->radio("name='$prefix" . $this->field() . "' value='" . $to_value . "' $selected") . $to_show . "<br>";
					}
			$input = $sample;
		}

		return $input;
	}


	function toHiddenHTML($prefix, $override=false){
		$value = $this->_arrayed_value;

		$select = "";
		$my_array = $value;

		if($override){
			$my_array = @explode("\n", $override);
			$val = array();
			for($i=0;$i<count($my_array);$i+=3){
				$val[] = array("display" => trim($my_array[$i]),
						"value" => $my_array[$i+1],
						"selected" => $my_array[$i+2]);
			}
			$my_array = $val;
		}

		for($i=0;$i<count($my_array);$i++){
			$selected = $my_array[$i]["selected"];
			if($selected){
				$str = $my_array[$i]["value"];
			}
		}
		$input = "<input type='hidden' name='$prefix" . $this->field() . "' value='$str'>";

		return $input;
	}

	function toGUI($prefix){
		$this->_drop_down->setName($prefix . $this->field());
		return $this->_drop_down;
	}
}


?>