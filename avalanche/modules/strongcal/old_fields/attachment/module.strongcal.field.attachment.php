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
class module_strongcal_field_attachment extends module_strongcal_field {

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
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "attachment: ";
			$this->_field      = false;
			$this->_type       = "attachment";
			$this->_value      = false;
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = 10;
			$this->_style       = false;
			$this->_ics       = false;
			$this->_loaded = true;
		}
		$this->_path = $this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/fields/";
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
		return "ATTACHMENT";
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
		return $this->_value;
	}

	//////////////////////////////////////////////////////////////////
	//  display_value()						//
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
		if($this->_value){

			$val = $this->_value;

			$strpos = strpos($val, "\n");
			$overwrite = substr($val, 0, $strpos);
			$val = substr($val, $strpos+1);

			$strpos = strpos($val, "\n");
			$filename = substr($val, 0, $strpos);
			$val = substr($val, $strpos+1);

			$strpos = strpos($val, "\n");
			$mime_type = substr($val, 0, $strpos);
			$val = substr($val, $strpos+1);

			$strpos = strpos($val, "\n");
			$size = substr($val, 0, $strpos);
			$val = substr($val, $strpos+1);


			$size = intval($size/1000) . "kb";

			if($val){
				return "<a target='download_frame' href='" . $this->_path . "attachment/getattachment.php?cal_id=" . $cal_id . "&event_id=$event_id&field=" . $this->field() . "'>$filename</a> ($size)";
			}else{
				return "";
			}
		}else{
			return "";
		}
		
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
		$strpos = strpos($val, "\n");
		$overwrite = substr($val, 0, $strpos);

		if($overwrite){
			if(is_object($this->_calendar)){
				$calendar = $this->_calendar;
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
				$my_id = $this->getId();
	               		$sql = "UPDATE $tablename SET value = '" . $val . "' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_value = $val;
					return true;
				}else{
					return false;
				}
			}else{
				$this->_value = $val;
				return true;
			}
		}else{
			return false;
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
			return false;
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
		$overwrite = $name . "_overwrite";
		$filename = $my_array[$name]['name'];
		$type = $my_array[$name]['type'];
		$size = $my_array[$name]['size'];
		$val = $my_array[$name]['tmp_name'];
		$overwrite = $my_array[$overwrite];


		if(get_magic_quotes_runtime ()){
			$file_data = stripslashes(@file_get_contents($val));
		}else{
			$file_data = @file_get_contents($val);
		}

		if($overwrite){
			$this->_value = $overwrite . "\n" . $filename . "\n" . $type . "\n" . $size . "\n" . $file_data;
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
	//  input_options ($prefix, $value, $skin)			//
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
		if(get_magic_quotes_gpc()){
			return array("type" => $this->type(),
			     "value" => addslashes($this->value()),
			     "size" => $this->size(),
			     "style" => $this->style(),
			     "MYSQL_TYPE" => "LONGBLOB");
		}else{
			return array("type" => $this->type(),
			     "value" => $this->value(),
			     "size" => $this->size(),
			     "style" => $this->style(),
			     "MYSQL_TYPE" => "BLOB");
		}
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
		$buffer = $avalanche->getSkin("buffer");
		$value = $this->value();

		if($override){
			$override = htmlspecialchars($override, ENT_QUOTES);
		}

		$value = htmlspecialchars($this->value(), ENT_QUOTES);
		if($override){
			$value=$override;
		}
		$input = $skin->input("type='file' name='$prefix" . $this->field() . "' size='17'");

		$val = $this->_value;

		$strpos = strpos($val, "\n");
		$overwrite = substr($val, 0, $strpos);
		$val = substr($val, $strpos+1);

		$strpos = strpos($val, "\n");
		$filename = substr($val, 0, $strpos);
		$val = substr($val, $strpos+1);

		$strpos = strpos($val, "\n");
		$mime_type = substr($val, 0, $strpos);
		$val = substr($val, $strpos+1);

		$strpos = strpos($val, "\n");
		$size = substr($val, 0, $strpos);
		$val = substr($val, $strpos+1);

		$size = intval($size / 1000) . "kb";

		if($val){
			$input = $skin->font($skin->check("type='hidden' name='" . $prefix . $this->field() . "_overwrite' value='1'") . " Overwrite current file:<br> (" . $filename . " " . $size . ")<br>") . $input;
		}else{
			$input .= $skin->input("type='hidden' name='" . $prefix . $this->field() . "_overwrite' value='1'");
		}



		return $input;
	}
	
	function toGUI($prefix){
		throw new Exception("This has not yet been defined");
	}
}


?>