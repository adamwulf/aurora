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
class module_strongcal_field_time extends module_strongcal_field {

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
			$this->_prompt     = "time: ";
			$this->_field      = "";
			$this->_type       = "time";
			$this->_value      = "00:001";
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
	function display_value($cal_id = false, $event_id = false){
		$strongcal = $this->avalanche->getModule("strongcal");

		if(!$this->_loaded){
			$this->reload();
		}


		if(substr($this->_value,5,1) == "1"){
			return date("H:i", $strongcal->localtimestamp());
		}else{
			if(is_object($this->_event) && $this->field() == "start_time"){
				if(!$this->_event->isAllDay()){
					$sd = $this->_event->getValue("start_date");
					$st = $this->value();
					$s = $strongcal->adjust($sd, $st, $strongcal->timezone());
					return substr($s["time"], 0, 5);
				}else{
					return "00:00";
				}
			}else
			if(is_object($this->_event) && $this->field() == "end_time"){
				if(!$this->_event->isAllDay()){
					$ed = $this->_event->getValue("end_date");
					$et = $this->value();
					$e = $strongcal->adjust($ed, $et, $strongcal->timezone());
					return substr($e["time"], 0, 5);
				}else{
					return "23:59";
				}
			}else{
				return substr($this->_value, 0, 5);
			}
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
		return "TIME";
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
		// $hindex = $prefix . $this->field() . "_hour";
		// $mindex = $prefix . $this->field() . "_minute";
		// $aindex = $prefix . $this->field() . "_ampm";
		// if(isset($my_array[$hindex]) && isset($my_array[$mindex]) && isset($my_array[$aindex])){
			// $hour_val = $my_array[$hindex];
			// $minute_val = $my_array[$mindex];
			// $ampm_val = $my_array[$aindex];
			// if(strcasecmp($ampm_val, "AM") == 0){
				// if($hour_val == 12){
					// $hour_val = "00";
				// }
			// }else{
				// if($hour_val != 12){
					// $hour_val += 12;
				// }
			// }
			// $this->set_value($hour_val . ":" . $minute_val);
			// return true;
		// }else{
			// return false;
		// }
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
		return substr($this->_value,5,1);

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
		if(substr($this->_value,5,1) == "1"){
			$selected = "CHECKED";
		}else{
			$selected = "";
		}
		$default = array("prompt" => "Default time is now: ", "input" => $skin->radio("name='" . $prefix . "_default_type' value='1' $selected"));
		if(substr($this->_value,5,1) != "1"){
			$selected = "CHECKED";
		}else{
			$selected = "";
		}
		$select = array("prompt" => "Select time from below: ", "input" => $skin->radio("name='" . $prefix . "_default_type' value='0' $selected"));

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
			     "MYSQL_TYPE" => "TIME");
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

		if(!is_object($skin)){
			throw new IllegalArgumentException("2nd argument to " . __METHOD__ . " must be a skin object");
		}

			$buffer = $this->avalanche->getSkin("buffer");
			$value = $this->display_value();
			if(substr($this->_value,5,1) == "1" && !is_object($this->_calendar)){
				$hour = date("H", $localtimestamp);
				$min  = date("i", $localtimestamp);
			}else{
				$hour = substr($value, 0, 2);
				$min  = substr($value, 3, 2);
			}

			if($override){
				$value = $override;
			}
			$pm = false;
			if($this->style()){
				// change minute value so that it's on a 15 minute mark
				// since drop downs can only hand within 15 minutes.

				$hour   = substr($value,0,2);
				if($hour == 12){
					$pm = true;
				}
				if($hour>12){
					$pm = true;
					$hour -=12;
				}

				$minute = substr($value,3,2);
				$minute = $minute - $minute % 15;
				if($minute < 10){
					$minute = $minute + 0;
					$minute = "0" . $minute;
				}


				$select = "";
				for($i=1;$i<13;$i++){
					if($i<10){
						$i = "0" . $i;
					}else{
						$i = "" . $i;
					}
					if($i == $hour){
						$selected = "SELECTED";
					}else{
						$selected = "";
					}
					$select .= $skin->option($i, $i, $selected) . "\n";
				}
				$hour = $skin->select($select, "name='$prefix" . $this->field() . "_hour'");

				$select = "";
				for($i=0;$i<60;$i+=$this->size()){
					if($i<10){
					$i = "0" . $i;
					}
					if($i == $minute){
						$selected = "SELECTED";
					}else{
						$selected = "";
					}
					$select .= $skin->option($i, $i, $selected) . "\n";
				}
				$minute = $skin->select($select, "name='$prefix" . $this->field() . "_minute'");


				if(!$pm){
					$selected = "SELECTED";
				}else{
					$selected = "";
				}
				$select = $skin->option("AM", "AM", $selected);
				if($pm){
					$selected = "SELECTED";
				}else{
					$selected = "";
				}
				$select .= $skin->option("PM", "PM", $selected);
				$ampm = $skin->select($select, "name='$prefix" . $this->field() . "_ampm'");

				$date = ($hour . ":" . $minute . "&nbsp;" . $ampm);

			}else{
				$min = substr($value,3,2);
				$hour   = substr($value,0,2);

				if($hour >= 12){
					$pm = true;
					$hour -= 12;
				}
				if($hour == 0){
					$hour = 12;
				}

				if($hour < 10){
					$hour = "0" . ($hour + 0);
				}



				$hour_inp    = "<input name='$prefix" . $this->field() . "_hour'   value='$hour' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2'>";
				$minute_inp  = "<input name='$prefix" . $this->field() . "_minute' value='$min' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2'>";


				if($pm){
					$ampm_val = "pm";
				}else{
					$ampm_val = "am";
				}
				$ampm  = "<input name='$prefix" . $this->field() . "_ampm' value='$ampm_val' READONLY onkeypress='var key = 0;if(xDef(event.which) && (event.which != 0)){key = event.which;}else{key = event.keyCode;} if(String.fromCharCode(key).toLowerCase() == \"a\") this.value=\"am\"; else if(String.fromCharCode(key).toLowerCase() == \"p\") this.value=\"pm\"; this.select();' onClick='this.select()' style='" . $skin->form_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 0px;' size='2' maxlength='2'>";



				$date = $buffer->table($buffer->tr($buffer->td($buffer->table($buffer->tr($buffer->td($skin->font($hour_inp . ":" . $minute_inp . " " . $ampm))), "cellpadding='0' cellspacing='0' style='" . $skin->form_border_style() . "  BACKGROUND-COLOR: " . $skin->form_color() . "; border-width: 1px;'"))), "cellpadding='0' cellspacing='0' border='0'");
			}
		return $date;
	}

	function toGui($prefix, $override = false){
			$value = $this->display_value();
			if($override){
				$value = $override;
			}
			$time = new TimeInput($value);
			$time->setName($prefix . $this->field());
			return $time;
	}

	function toHiddenHTML($prefix, $override=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$localtimestamp = $strongcal->localtimestamp();
		$buffer = $this->avalanche->getSkin("buffer");
		$value = $this->display_value();
		if(substr($this->_value,5,1) == "1" && !is_object($this->_calendar)){
			$hour = date("H", $localtimestamp);
			$min  = date("i", $localtimestamp);
		}else{
			$hour = substr($value, 0, 2);
			$min  = substr($value, 3, 2);
		}

		if($override){
			$value = $override;
		}


		$min = substr($value,3,2);
		$hour   = substr($value,0,2);

		if($hour >= 12){
			$pm = true;
			$hour -= 12;
		}
		if($hour == 0){
			$hour = 12;
		}

		if($hour < 10){
			$hour = "0" . ($hour + 0);
		}



		$hour_inp    = "<input type='hidden' name='$prefix" . $this->field() . "_hour'   value='$hour'>";
		$minute_inp  = "<input type='hidden' name='$prefix" . $this->field() . "_minute' value='$min'>";


		if($pm){
			$ampm_val = "pm";
		}else{
			$ampm_val = "am";
		}
		$ampm  = "<input type='hidden' name='$prefix" . $this->field() . "_ampm' value='$ampm_val'>";


		$time = $hour_inp . $minute_inp . $ampm;
		return $time;

	}
}


?>