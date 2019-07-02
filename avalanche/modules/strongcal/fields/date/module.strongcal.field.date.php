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
class module_strongcal_field_date extends module_strongcal_field {

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
				$this->_value      = substr($id['value'], 0, 10);
				$this->_form_order = $id['form_order'];
				$this->_removeable = $id['removeable'];
				$this->_size       = $id['size'];
				$this->_style       = (int) $id['style'];
				$this->_ics       = $id['ics'];
				$this->_current = substr($id['value'], 10);
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
		}else{
			$this->_id 	   = false;
			$this->_prompt     = "date: ";
			$this->_field      = false;
			$this->_type       = "date";
			$this->_value      = "0000-00-00";
			$this->_form_order = false;
			$this->_removeable = false;
			$this->_size       = false;
			$this->_style      = 0;
			$this->_ics        = false;
			$this->_current    = 1;
			$this->_loaded     = true;
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
			$this->reload();
		}
		return "DATE";
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
			$this->reload();
		}
		return $this->_value . $this->_current;
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
	function display_value($cal_id = false, $event_id = false){
		$strongcal = $this->avalanche->getModule("strongcal");

		if(!$this->_loaded){
			$this->reload();
		}
		if($this->_current == "1"){
			return date("Y-m-d", $strongcal->localtimestamp());
		}else{
			if(is_object($this->_event) && $this->field() == "start_date"){
				if(!$this->_event->isAllDay()){
					$sd = $this->value();
					$st = $this->_event->getValue("start_time");
					$s = $strongcal->adjust($sd, $st, $strongcal->timezone());
					return $s["date"];
				}else{
					return $this->value();
				}
			}else
			if(is_object($this->_event) && $this->field() == "end_date"){
				if(!$this->_event->isAllDay()){
					$ed = $this->value();
					$et = $this->_event->getValue("end_time");
					$e = $strongcal->adjust($ed, $et, $strongcal->timezone());
					return $e["date"];
				}else{
					return $this->value();
				}
			}else{
				return substr($this->_value, 0, 10);
			}
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
		$name = $prefix . "_default_type";
		$this->_current = $my_array[$name];
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
		$obj = $this->getComponent($prefix);
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
		return $this->_current;

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
		if($this->_current){
			$selected = "CHECKED";
		}else{
			$selected = "";
		}
		$default = array("prompt" => "Default date is today: ", "input" => $skin->radio("name='" . $prefix . "_default_type' value='1' $selected"));
		if(!$this->_current){
			$selected = "CHECKED";
		}else{
			$selected = "";
		}
		$select = array("prompt" => "Select date from below: ", "input" => $skin->radio("name='" . $prefix . "_default_type' value='0' $selected"));

		return array($default, $select);
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
				array("value" => 0, "display" => "Text Inputs"),
				array("value" => 1, "display" => "Drop Downs"));
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
		if(is_object($this->_calendar)){
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
	//  reload()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////

	function reload(){
		module_strongcal_field::reload();
		$val = $this->_value;
		$this->_value = substr($val, 0, 10);
		$this->_current = substr($val, 10);
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
		if(module_strongcal_field::set_value($val, $passive)){
			$this->_value = substr($val, 0, 10);
			$this->_current = substr($val, 10);
			return true;
		}else{
			return false;
		}
	}



	//////////////////////////////////////////////////////////////////
	//  to_add()							//
	//  input: none							//
	//  output: an array with all the field values in it indexed	//
	//	    by parameter name					//
	//	    used when adding a field to a calendar. it should	//
	//	    contain all info for the field to be initialized to	//
	//	    on recreate.					//
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
			     "MYSQL_TYPE" => "DATE");
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

		$strongcal = $this->avalanche->getModule("strongcal");
		$localtimestamp = $strongcal->localtimestamp();
		$buffer = $this->avalanche->getSkin("buffer");

		if($this->_current && !is_object($this->_calendar)){
			$value = date("Y-m-d", $localtimestamp);
		}

		if($override){
			$value = $override;
		}else{
			$value = $this->display_value();
		}


		if(!$value){
			$value = $this->display_value();
		}

		if($value == "0000-00-00"){
			$value = date("Y-m-d", $localtimestamp);
		}

		$day_val = substr($value,8,2);
		$month_val = substr($value,5,2);
		$year_val = substr($value,0,4);
		$timestamp = mktime(0,0,0,$month_val,$day_val,$year_val);
		if(!checkdate ( @date("m", $timestamp), @date("d", $timestamp), @date("Y", $timestamp))){
			$value = date("Y-m-d", $localtimestamp);
		}


			$var_prefix = $prefix . $this->field();
			if($this->style() == 1){
				$select = "";
				$day_val = substr($value,8,2);
				for($i=1;$i<32;$i++){
					if($i<10){
						$i = "0" . $i;
					}
					if($i == substr($value,8,2)){
						$selected = "SELECTED";
					}else{
						$selected = "";
					}
					$select .= $skin->option($i,  $i, $selected) . "\n";
				}
				$day = $skin->select($select, "name='$prefix" . $this->field() . "_day' onChange=\"this.form." . $var_prefix . "_dow.value=day_of_the_week(this.form." . $var_prefix . "_day.options[this.form." . $var_prefix . "_day.selectedIndex].value, this.form." . $var_prefix . "_month.options[this.form." . $var_prefix . "_month.selectedIndex].value, this.form." . $var_prefix . "_year.options[this.form." . $var_prefix . "_year.selectedIndex].value)\"");

				$select = "";
				$month_val = substr($value,5,2);
				for($i=1;$i<13;$i++){
					if($i<10){
						$i = "0" . $i;
					}
					if($i == substr($value,5,2)){
						$selected = "SELECTED";
					}else{
						$selected = "";
					}
					$select .= $skin->option($i, $i, $selected) . "\n";
				}
				$month = $skin->select($select, "name='$prefix" . $this->field() . "_month' onChange=\"this.form." . $var_prefix . "_dow.value=day_of_the_week(this.form." . $var_prefix . "_day.options[this.form." . $var_prefix . "_day.selectedIndex].value, this.form." . $var_prefix . "_month.options[this.form." . $var_prefix . "_month.selectedIndex].value, this.form." . $var_prefix . "_year.options[this.form." . $var_prefix . "_year.selectedIndex].value)\"");

				$select = "";
				$start = date("Y");
				$year_val = substr($value,0,4);
				for($i=0;$i<6;$i++){
					$j = $start + $i;
					if($j == substr($value,0,4)){
						$selected = "SELECTED";
					}else{
						$selected = "";
					}
					$select .= $skin->option($j, $j, $selected) . "\n";
				}
				$year = $skin->select($select, "name='$prefix" . $this->field() . "_year' onChange=\"this.form." . $var_prefix . "_dow.value=day_of_the_week(this.form." . $var_prefix . "_day.options[this.form." . $var_prefix . "_day.selectedIndex].value, this.form." . $var_prefix . "_month.options[this.form." . $var_prefix . "_month.selectedIndex].value, this.form." . $var_prefix . "_year.options[this.form." . $var_prefix . "_year.selectedIndex].value)\"");


				$dow = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
				$dow = $skin->input("name='$prefix" . $this->field() . "_dow' id='$prefix" . $this->field() . "_dow' value='" . $dow[date("w",mktime(0,0,0,$month_val,$day_val,$year_val))] . "' READONLY size='2' maxlength='2' tabindex='-1'");

				return ($month . "-" . $day . "-" . $year . "&nbsp;" . $dow);
			}else{
				$day_val = substr($value,8,2);
				$month_val = substr($value,5,2);
				$year_val = substr($value,0,4);


				$hidden = $skin->input("type='hidden' name='input_type' value='" . DATE_INPUT . "'");
				$month  = "<input name='$prefix" . $this->field() . "_month' value='$month_val' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2' onChange=\"this.form." . $var_prefix . "_dow.value=getDOW(new Date(this.form." . $var_prefix . "_year.value, this.form." . $var_prefix . "_month.value-1, this.form." . $var_prefix . "_day.value,0,0,0).getDay())\">";
				$day = "<input name='$prefix" . $this->field() . "_day' value='$day_val' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2' onChange=\"this.form." . $var_prefix . "_dow.value=getDOW(new Date(this.form." . $var_prefix . "_year.value, this.form." . $var_prefix . "_month.value-1, this.form." . $var_prefix . "_day.value,0,0,0).getDay());\">";
				$year  = "<input name='$prefix" . $this->field() . "_year' value='$year_val' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='4' maxlength='4' onChange=\"this.form." . $var_prefix . "_dow.value=getDOW(new Date(this.form." . $var_prefix . "_year.value, this.form." . $var_prefix . "_month.value-1, this.form." . $var_prefix . "_day.value,0,0,0).getDay())\">";
				$dow = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
				$dow = "<input name='$prefix" . $this->field() . "_dow' id='$prefix" . $this->field() . "_dow' value='" . $dow[date("w",mktime(0,0,0,$month_val,$day_val,$year_val))] . "' READONLY size='2' maxlength='2' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' tabindex='-1'>";
				$date = $buffer->table($buffer->tr($buffer->td($skin->font($month . "/" . $day . "/" . $year . "&nbsp;&nbsp;" . $dow))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'");

				return $date;
			}
	}

	private function getComponent($prefix){
		$date = new DateInput($this->value());
		$date->setName($prefix . $this->field());
		return $date;
	}

	function toGui($prefix, $override = false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$localtimestamp = $strongcal->localtimestamp();
		if($this->_current && !is_object($this->_calendar)){
			$value = date("Y-m-d", $localtimestamp);
		}

		if($override){
			$value = $override;
		}else{
			$value = $this->display_value();
		}

		if(!$value){
			$value = $this->display_value();
		}

		if($value == "0000-00-00"){
			$value = date("Y-m-d", $localtimestamp);
		}

		$day_val = substr($value,8,2);
		$month_val = substr($value,5,2);
		$year_val = substr($value,0,4);
		$timestamp = mktime(0,0,0,$month_val,$day_val,$year_val);
		if(!checkdate ( @date("m", $timestamp), @date("d", $timestamp), @date("Y", $timestamp))){
			$value = date("Y-m-d", $localtimestamp);
		}

		$date = new DateInput($value);
		$date->setName($prefix . $this->field());
		return $date;
	}



	function toHiddenHTML($prefix, $override=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$localtimestamp = $strongcal->localtimestamp();

		if($this->_current && !is_object($this->_calendar)){
			$value = date("Y-m-d", $localtimestamp);
		}

		if($override){
			$value = $override;
		}else{
			$value = $this->display_value();
		}


		if(!$value){
			$value = $this->display_value();
		}

		if($value == "0000-00-00"){
			$value = date("Y-m-d", $localtimestamp);
		}

		$day_val = substr($value,8,2);
		$month_val = substr($value,5,2);
		$year_val = substr($value,0,4);
		$timestamp = mktime(0,0,0,$month_val,$day_val,$year_val);
		if(!checkdate ( @date("m", $timestamp), @date("d", $timestamp), @date("Y", $timestamp))){
			$value = date("Y-m-d", $localtimestamp);
		}


		$var_prefix = $prefix . $this->field();
		$day_val = substr($value,8,2);
		$day = "<input type='hidden' name='$prefix" . $this->field() . "_day' value='$day_val'>";

		$month_val = substr($value,5,2);
		$month = "<input type='hidden' name='$prefix" . $this->field() . "_month' value='$month_val'>";

		$year_val = substr($value,0,4);
		$year = "<input type='hidden' name='$prefix" . $this->field() . "_year' value='$year_val'>";

		return ($month . $day . $year);
	}
}


?>