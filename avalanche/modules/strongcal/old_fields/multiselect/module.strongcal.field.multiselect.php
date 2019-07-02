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
class module_strongcal_field_multiselect extends module_strongcal_field {

	/**
	 * replaces all \n and \r with the empty string
	 */
	private function trim($str){
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "", $str);
		return $str;
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
				$this->fix_value();
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "multiselect: ";
			$this->_field      = false;
			$this->_type       = "multiselect";
			$this->_value      = array(array("display" => "Option 1",
							 "value"   => "Option 1",
							 "selected" => false),
						   array("display" => "Option 2",
							 "value"   => "Option 2",
							 "selected" => false),
						   array("display" => "Option 3",
							 "value"   => "Option 3",
							 "selected" => true),
						   array("display" => "Option 4",
							 "value"   => "Option 4",
							 "selected" => false),
						   array("display" => "Option 5",
							 "value"   => "Option 5",
							 "selected" => false));
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = 3;
			$this->_style       = false;
			$this->_ics       = false;
			$this->_loaded = true;
		}
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
		$this->_value = array();
		for($i=0;$i<count($value_list);$i+=3){
			$to_show = htmlspecialchars($value_list[$i], ENT_QUOTES);
			$to_show = str_replace(" ", "&nbsp;", $to_show);
			$this->_value[] = array("display" => trim($to_show), "value" => $value_list[$i+1], "selected" => trim($value_list[$i+2]));
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
				$this->_value = $my_row[$this->field()];
				$this->fix_value();
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  load()							//
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
	function load(){
		if(is_object($cal)){
			$this->_loaded = true;
			$result = connectTo("strongcal_cal_" . $this->_calendar->getId() . "_fields", "id = '" . $this->_id . "'");
			while($myrow = mysql_fetch_array($result)){
				$this->_prompt     = $myrow['prompt'];
				$this->_field      = $myrow['field'];
				$this->_type       = $myrow['type'];
				$this->_value      = $myrow['value'];
				$this->_form_order = $myrow['form_order'];
				$this->_removeable = $myrow['removeable'];
				$this->_size       = $myrow['size'];
				$this->_style       = $myrow['style'];
				$this->_ics       = $myrow['ics'];
			}
		}
		$this->fix_value();
	}



	//////////////////////////////////////////////////////////////////
	//  getId()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getId(){
		return $this->_id;
	}

	//////////////////////////////////////////////////////////////////
	//  getCal()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the calendar to which this field belongs		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getCal(){
		return $this->_calendar;
	}


	//////////////////////////////////////////////////////////////////
	//  prompt()							//
	//	returns the prompt for this field.			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the prompt of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function prompt(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_prompt;
	}


	//////////////////////////////////////////////////////////////////
	//  ics()							//
	//	returns the export rule for this field.			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the export rule of this field			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function ics(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_ics;
	}


	//////////////////////////////////////////////////////////////////
	//  field()							//
	//	returns the name of this field.				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the field of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function field(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_field;
	}


	//////////////////////////////////////////////////////////////////
	//  type()							//
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
	function type(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_type;
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
		if($this->style()){
			return "CHECKBOXES";
		}else{
			return "MULTI-SELECT";
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
		if(!$this->_loaded){
			$this->load();
		}
		for($i=0;$i<count($this->_value);$i++){
			$temp .= $this->trim($this->_value[$i]["display"]) . "\n";
			$temp .= $this->trim($this->_value[$i]["value"]) . "\n";
			$temp .= $this->_value[$i]["selected"] . "\n";
		}

		return substr($temp,0,strlen($temp)-1);
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
		$ret = "";
		for($i=0;$i<count($this->_value);$i++){
			if($this->_value[$i]["selected"]){
				if($ret){
					$ret .= "\n";
				}
				$ret .= trim($this->_value[$i]["display"]);
			}
		}
		return $ret;
	}

	//////////////////////////////////////////////////////////////////
	//  form_order()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the form_order of this field			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function form_order(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_form_order;
	}


	//////////////////////////////////////////////////////////////////
	//  removeable()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the removeable of this field			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function removeable(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_removeable;
	}

	//////////////////////////////////////////////////////////////////
	//  size()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the removeable of this field			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function size(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_size;
	}

	//////////////////////////////////////////////////////////////////
	//  style()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the style of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function style(){
		if(!$this->_loaded){
			$this->load();
		}
		return $this->_style;
	}


	//////////////////////////////////////////////////////////////////
	//  set_prompt($val)						//
	//--------------------------------------------------------------//
	//  input: the prompt of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_prompt($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
	               	$sql = "UPDATE $tablename SET prompt = '$val' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_prompt = $val;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_ics($val)						//
	//--------------------------------------------------------------//
	//  input: the export rule of this field			//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_ics($val){
		if(is_object($this->_calendar)){
			if(($val == ICS_DTCOMPLETED ||
			    $val == ICS_DTDUE) &&
			    !($this->type() == DATE_INPUT ||
			      $this->type() == TIME_INPUT)){
	
			/* if it's not a date or time field, then
			 * it can't be exported to a datetime ics field
			 */
				return false;
			}

			$calendar = $this->_calendar;
			if($calendar->isRemoveable($this->field())){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
				$my_id = $this->getId();
	                	$sql = "UPDATE $tablename SET ics = '$val' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_ics = $val;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_field($val)						//
	//--------------------------------------------------------------//
	//  input: the field of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_field($val){
		if(is_object($this->_calendars)){
			$calendar = $this->_calendar;
			if($calendar->isRemoveable($this->field())){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
				$my_id = $this->getId();
	                	$sql = "UPDATE $tablename SET field = '$val' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_field = $val;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			$this->_field = $val;
			return true;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_type($val)						//
	//--------------------------------------------------------------//
	//  input: the type of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_type($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			if($calendar->isRemoveable($this->field())){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
				$my_id = $this->getId();
	                	$sql = "UPDATE $tablename SET type = '$val' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_type = $val;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
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
	function set_value($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
	               	$sql = "UPDATE $tablename SET value = '$val' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_value = $val;
				$this->fix_value();
				return true;
			}else{
				return false;
			}
		}else{
			if(is_string($val)){
				$this->_value = $val;
				$this->fix_value();
			}else
			if(is_array($val)){
				$this->_value = $val;
			}else{
				trigger_error($this->type() . "::set_value(\$val) requires a string or array as input.", E_USER_WARNING);
			}
			return true;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_form_order($val)					//
	//--------------------------------------------------------------//
	//  input: the form_order of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_form_order($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			if($calendar->isRemoveable($this->field())){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
				$my_id = $this->getId();
	                	$sql = "UPDATE $tablename SET form_order = '$val' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_form_order = $val;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_size($val)						//
	//--------------------------------------------------------------//
	//  input: the form_order of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_size($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
	               	$sql = "UPDATE $tablename SET size = '$val' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_size = $val;
				return true;
			}else{
				return false;
			}
		}else{
			$this->_size = $val;
			return true;

		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_style($val)						//
	//--------------------------------------------------------------//
	//  input: the style of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_style($val){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
	               	$sql = "UPDATE $tablename SET style = '$val' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_style = $val;
				return true;
			}else{
				return false;
			}
		}else{
			$this->_style = $val;
			return true;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_removeable($val)					//
	//--------------------------------------------------------------//
	//  input: the removeable of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_removeable($val){
		return false;
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
		$vals = $my_array[$name];
		for($j=0;$j<count($this->_value);$j++){
			$this->_value[$j]["selected"] = false;
		}
		for($i=0;$i<count($vals);$i++){
			for($j=0;$j<count($this->_value);$j++){
				if($this->trim($vals[$i]) == $this->trim($this->_value[$j]["value"])){
					$this->_value[$j]["selected"] = true;
				}
			}
		}
		return true;
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
		$this->_value = array();
		for($i=0;$i<count($vals);$i++){
			$this->_value[] = array("display"  => $this->trim($vals[$i]),
						"value"    => $this->trim($vals[$i]),
						"selected" => false);
		}
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
			$this->load();
		}
		for($i=0;$i<count($this->_value);$i++){
			$temp .= $this->trim($this->_value[$i]["display"]) . "\n";
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
				      "display" => "Multi-select Box"),
				array("value" => 1,
				      "display" => "Checkboxes"));
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
		return true;
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
		$value = $this->_value;

		$select = "";
		$my_array = $value;

		if(!$override){
			$override = array();
		}

			$select = "";

			if($this->style() == FIELD_NO_STYLE){
				for($i=0;$i<count($my_array);$i++){
					if(count($override) && in_array($my_array[$i]["value"], $override)){
						// if the user specified a value
						$select .= $skin->option($my_array[$i]["value"], $my_array[$i]["display"], "SELECTED");
					}else if(!count($override) && $my_array[$i]["selected"]){
						$select .= $skin->option($my_array[$i]["value"], $my_array[$i]["display"], "SELECTED");
					}else{
						$select .= $skin->option($my_array[$i]["value"], $my_array[$i]["display"]);
					}
					
				}
				$input = $skin->select($select, "name='$prefix" . $this->field() . "[]' size='" . $this->size() . "' MULTIPLE");
			}else{
				for($i=0;$i<count($my_array);$i++){
					if(in_array($my_array[$i], $override)){
						$checked = "CHECKED";
					}else{
						if(!count($override) && $my_array[$i]["selected"]){
							$checked = "CHECKED";
						}else{
							$selected = "";
						}
					}
					$sample .= $skin->check("value='" . $my_array[$i]["value"] . "' name='$prefix" . $this->field() . "[]' $checked") . $my_array[$i]["display"] . "<br>";
				}
				$input = $sample;
			}
			return $input;

		return $input;
	}

	function toGUI($prefix){
		throw new Exception("This has not yet been defined");
	}
}


?>