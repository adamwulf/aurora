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
abstract class module_strongcal_field {

	// the calendar to which this field is a part
	protected $_calendar;

	// the event to which this field is connected
	protected $_event;

	protected $_id;
	protected $_prompt;
	protected $_field;
	protected $_type;
	protected $_value;
	protected $_form_order;
	protected $_removeable;
	protected $_size;
	protected $_style;
	protected $_loaded;
	protected $_ics;


	public function sleep(){
		$this->_calendar->sleep();
	}

	//////////////////////////////////////////////////////////////////
	//  compareTo($field)						//
	//	set strict equal to true if the calendar must be the	//
	//	same for both recurrance patterns.			//
	//--------------------------------------------------------------//
	//  input: $field - the field to compare to			//
	//								//
	//  output: boolean, true if fields are equal			//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	public function compareTo($field, $strict = true){
		if(!$this_>_loaded){
			$this->reload();
		}
		$cal1 = $this->getCal();
		$cal2 = $field->getCal();
		if($this->_prompt     == $field->prompt() &&
		   $this->_field      == $field->field() &&
		   $this->_type       == $field->type() &&
		   $this->_value      == $field->value() &&
		   $this->_form_order == $field->form_order() &&
		   $this->_removeable == $field->removeable() &&
		   $this->_size       == $field->size() &&
		   $this->_style       == $field->style() &&
		   $this->_ics       == $field->ics() &&
		   (!$strict || $strict &&
		    $cal1->getId() == $cal2->getId()
		   )
		  ){
			return true;
		}else{
			return false;
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
	private function load_event($id){
		if(is_object($this->_calendar)){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId();
			$my_id = $this->getId();
	               	$sql = "SELECT * FROM $tablename WHERE id = '$id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$my_row = mysqli_fetch_array($result);
				$this->_value = $my_row[$this->field()];
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	protected $avalanche;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
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
		$this->_loaded = false;
		$this->_event = false;
		$this->_id = false;
		if(is_object($cal) && $id){
			$this->_calendar = $cal;
			if(is_array($id)){
				$this->_loaded = true;
				$this->_id		   = $id['id'];
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
				$this->_style       = (int) $id['style'];
				$this->_ics       = $id['ics'];
			}else{
				$this->_id = $id;
				$this->_loaded = false;
			}
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
	function reload(){
		$this->_loaded = true;
		if(is_object($this->_calendar) && $this->_id !== false){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_calendar->getId() . "_fields WHERE id = '" . $this->_id . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysqli_fetch_array($result)){
				$this->_prompt     = $myrow['prompt'];
				$this->_field      = $myrow['field'];
				$this->_type       = $myrow['type'];
				if(get_magic_quotes_runtime()){
					$this->_value      = stripslashes($myrow['value']);
					echo "reload: " . $this->_value . "<br>";
				}else{
					$this->_value      = $myrow['value'];
				}
				$this->_form_order = $myrow['form_order'];
				$this->_removeable = $myrow['removeable'];
				$this->_size       = $myrow['size'];
				$this->_style       = (int) $myrow['style'];
				$this->_ics       = $myrow['ics'];
			}
		}

		if(is_object($this->_calendar) && is_object($this->_event)){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_calendar->getId() . " WHERE id='" . $this->_event->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysqli_fetch_array($result)){
				$val = $myrow[$this->_field];
				if(get_magic_quotes_runtime()){
					$val = stripslashes($val);
				}
				$this->_value = $val;
			}
		}
	}

	// load in the data from mysql
	function load($myrow){
		$this->_loaded = true;
		$this->_prompt     = $myrow['prompt'];
		$this->_field      = $myrow['field'];
		$this->_type       = $myrow['type'];
		if(get_magic_quotes_runtime()){
			$this->_value      = stripslashes($myrow['value']);
		}else{
			$this->_value      = $myrow['value'];
		}
		$this->_form_order = $myrow['form_order'];
		$this->_removeable = $myrow['removeable'];
		$this->_size       = $myrow['size'];
		$this->_style       = (int) $myrow['style'];
		$this->_ics       = $myrow['ics'];
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
		if(!$this->_id){
			trigger_error("getId() called for field without id.", E_USER_WARNING);
		}else{
			return $this->_id;
		}
	}

	function setId($id=false){
		if($id && !is_object($this->_calendar)){
			trigger_error("setId(\$id=false) called before calendar has been set for this field.", E_USER_WARNING);
		}else{
			$this->_id = $id;
			$this->_loaded = false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  calendar()							//
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
	function calendar($cal=false){
		if(is_object($cal)){
			if(is_object($this->_calendar) && $this->_calendar->getId() != $cal->getId()
			   || !is_object($this->_calendar)){
				$this->_loaded = false;
				$this->_event = false;
				$this->_id = false;
			}
                	$this->_calendar = $cal;
		}else if($cal === false){
                	return $this->_calendar;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a calendar object");
		}
	}

	// $val is an optional value to be sent in
	// if $val is sent in:
	//    the field will not reload
	//    $val is assumed to come straight from db, so
	//    get_magic_quotes_runtime might be applied
	function event($event=false, $val=false){
		if(is_object($event) && !is_object($this->_calendar)){
			throw new Exception("event(\$event) called before calendar has been set for this field.");
		}else
		if(is_object($event)){
			$this->_event = $event;
			if(is_string($val)){
				$this->set_value($val, true);
				$this->_loaded = true;
			}else{
				$this->_loaded = false;
			}
		}else if($event === false){
			return $this->_event;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an event object");
		}
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
			$this->reload();
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
			$this->reload();
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
			$this->reload();
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
			$this->reload();
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
	abstract function displayType();




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
		if(!is_object($this->_calendar) ||
		   $this->field() == "start_date" ||
		   $this->field() == "start_time" ||
		   $this->field() == "end_date" ||
		   $this->field() == "end_time" ||
		   $this->_calendar->canReadEntries() ||
		   is_object($this->_event) && $this->_calendar->canReadEvent($this->_event->getId())){
			return $this->_value;
		}else{
			return false;
		}
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
		   $this->_calendar->canReadEntries() ||
		   is_object($this->_event) && $this->_calendar->canReadEvent($this->_event->getId())){
			return $this->_value;
		}else{
			return false;
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
			$this->reload();
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
			$this->reload();
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
			$this->reload();
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
			$this->reload();
		}
		return (int) $this->_style;
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
			$val = addslashes($val);
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
		// if passive, don't talk to DB
		if($passive){
			$this->_value = (string) $val;
			return true;
		}else
		if(is_object($this->_calendar) && !is_object($this->_event) && $this->_calendar->canWriteFields()){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
			$val_for_sql = addslashes($val);
	        $sql = "UPDATE $tablename SET value = '$val_for_sql' WHERE id = '$my_id'";
	        $result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_value = (string) $val;
				return true;
			}else{
				return false;
			}
		}else
		if(is_object($this->_calendar) && is_object($this->_event) && ($this->_calendar->canWriteName() || $this->_calendar->canWriteEntries() && $this->_event->author() == $this->avalanche->getActiveUser())){
			$calendar = $this->_calendar;
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId();
			$my_id = $this->getId();
			$val_for_sql = addslashes($val);
	               	$sql = "UPDATE $tablename SET " . $this->_field . " = '" . $val_for_sql . "' WHERE id = '" . $this->_event->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			if(mysqli_affected_rows($this->avalanche->mysqliLink()) > 0){
				$this->_value = $val;

				// now we need to notify the visitors
				$strongcal = $this->avalanche->getModule("strongcal");
				$listeners = $strongcal->eventEdited($this->_calendar->getId(), $this->_event->getId());

				return true;
			}else{
				return false;
			}
		}else if(is_object($this->_calendar) || is_object($this->_event)){
			return false;
		}else{
			$this->_value = (string) $val;
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
		$possible_vals = $this->style_options();
		$ok = false;
		for($i=0;$i<count($possible_vals);$i++){
			if($val == $possible_vals[$i]["value"]){
				$ok = true;
			}
		}
		if($ok){
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
		}else{
			return false;
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
	abstract function load_form_value($prefix, $my_array);

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
	abstract function load_options($prefix, $my_array);


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
	abstract function input_options($name, $skin);

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
	abstract function style_options();



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
	abstract function to_add();

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
	abstract function sizeable();


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
	abstract function toHTML($prefix, $skin, $override=false);


	/**
	 * returns a Component representing this field
	 */
	abstract function toGUI($prefix);
}

?>