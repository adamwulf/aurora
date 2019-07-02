<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.php						//
//----------------------------------------------------------------------//
//  initializes the module's class object and adds it to avalanches	//
//  module list								//
//									//
//									//
//  NOTE: filename must be of format module.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.php						//
//----------------------------------------------------------------------//
//									//
//  This is an abstract module. All modules for avalanche must extend	//
//	this class.							//
//									//
//  NOTE: ALL MODULES WILL BE INCLUDE *INSIDE* OF THE avalanche'S MAIN	//
//	CLASS. SO REFER ANY FUNCTION CALLS THAT ARE *OUTSIDE* OF YOUR	//
//	CLASS TO avalanche BY USING *THIS->functionhere*		//
//									//
//////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                        ///////////////////////////
///////////////  STRONGCAL SUB-MODULE  ///////////////////////////
///////////////       calendar         ///////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
class module_strongcal_calendar {

	// the author of this calendar
	private $_author;

	// 1 if this calendar is public, 0 otherwise
	private $_public;

	// the name of this calendar
	private $_name;

	// the description for this calendar
	private $_description;

	// the color of this calendar
	private $_color;

	// the array of fields for this calendar
	private $_fields;

	// the array of validations for this calendar
	private $_validations;

	// the sql where clause for validations
	private $_sql_validations;

	// the sql where clause for validations
	private $_sql_to_validate;

	// the sql where clause for events which i have validated, but are not completely validated
	private $_sql_validated;

	// the array of events for this calendar
	private $_events;

	// the array of events for this calendar that need to be validated
	private $_to_validate_events;

	// the array of events for this calendar that need to be validated by a user/group other than me
	private $_validated_events;

	// the array of events indexed by date for this calendar
	private $_events_date;

	// a hash table of events by id
	private $_hash_events;

	// the id of this calendar
	private $_id;

	// a list of all loaded recuring objects for the loaded events
	private $_recur_objects;

	// the starting date to load events on
	private $_start_date;

	// the ending date to load events to (instead of end_after)
	private $_end_date;

	// the number of events to load (instead of end_date)
	private $_end_after;

	// $_is_serialized
	private $_is_serialized;

	// $_tag
	private $_tag;

	// $_get_essentials_on
	// buffered result from getEssentialsOn($date, $limit=false);
	private $_get_essentials_on;

	// true if this calendar has any validation rules
	private $_has_validation;

	//////////////////////////////////////////////////////////////////
	//  is_serialized()						//
	//////////////////////////////////////////////////////////////////
	function is_serialized(){
		return $this->_is_serialized;
	}

	//////////////////////////////////////////////////////////////////
	//  seraial_id()						//
	//	returns the id of this calendar				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function serial_id($id=false){
		if($id){
			$this->_serial_id = $id;
		}
		return $this->_serial_id;
	}

	//////////////////////////////////////////////////////////////////
	//  tag()							//
	//	returns (and sets) the tag on this calendar		//
	//	a tag can be any random information stored temporarily  //
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function tag($tag=false){
		if($tag){
			$this->_tag = $tag;
		}
		return $this->_tag;
	}


	//////////////////////////////////////////////////////////////////
	//  getId()							//
	//	returns the id of this calendar				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getId(){
		return (int) $this->_id;
	}

	//////////////////////////////////////////////////////////////////
	//  sleep()							//
	//	this function is called automatically by php when the	//
	//	calendar is serialized					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//////////////////////////////////////////////////////////////////
	function sleep(){
		if(!$this->_is_serialized){
			$this->_serial_can["canReadEntries"]      = $this->canReadEntries();
			$this->_serial_can["canWriteEntries"]     = $this->canWriteEntries();
			$this->_serial_can["canReadFields"]       = $this->canReadFields();
			$this->_serial_can["canWriteFields"]      = $this->canWriteFields();
			$this->_serial_can["canReadValidations"]  = $this->canReadValidations();
			$this->_serial_can["canWriteValidations"] = $this->canWriteValidations();
			$this->_serial_can["canReadName"]         = $this->canReadNameStrict();
			$this->_serial_can["canWriteName"]        = $this->canWriteName();
			$this->_serial_can["canReadComments"]     = $this->canReadComments();
			$this->_serial_can["canWriteComments"]    = $this->canWriteComments();
			$this->_is_serialized = true;

			for($i=0;$i<count($this->_fields);$i++){
				$this->_fields[$i]->sleep();
			}
			for($i=0;$i<count($this->_events);$i++){
				$this->_events[$i]->sleep();
			}
		}
		return;
	}

	//////////////////////////////////////////////////////////////////
	//  start_date($date=false)					//
	//	sets the beginning date of this calendar		//
	//	the calendar will load events starting at this date	//
	//--------------------------------------------------------------//
	//  input: $date - the start date for this calendar		//
	//  output: the start date for this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function start_date($date=false){
		if($date){
			$strongcal = $this->avalanche->getModule("strongcal");
			$datestamp = mktime(0, 0, 0, substr($date,5,2), substr($date,8,2), substr($date,0,4));
			$date = date("Y-m-d", $datestamp);
			$this->_start_date = $date;
		}
		return $this->_start_date;
	}


	//////////////////////////////////////////////////////////////////
	//  end_date($date=false)					//
	//	sets the beginning date of this calendar		//
	//	the calendar will load events ending at this date	//
	//--------------------------------------------------------------//
	//  input: $date - the end date for this calendar		//
	//  output: the end date for this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function end_date($date=false){
		if($date){
			$strongcal = $this->avalanche->getModule("strongcal");
			$datestamp = mktime(0, 0, 0, substr($date,5,2), substr($date,8,2), substr($date,0,4));
			$date = date("Y-m-d", $datestamp);
			$this->_end_date = $date;
			$this->_end_after = false;
		}
		return $this->_end_date;
	}

	//////////////////////////////////////////////////////////////////
	//  end_after($date=false)					//
	//	sets the beginning date of this calendar		//
	//	the calendar will load events ending at this date	//
	//--------------------------------------------------------------//
	//  input: $date - the end date for this calendar		//
	//  output: the end date for this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function end_after($num=false){
		if($num){
			$this->_end_after = $num;
			$this->_end_date = false;
		}
		return $this->_end_after;
	}


	//////////////////////////////////////////////////////////////////
	//  color()							//
	//	returns the color of this calendar			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the name of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function color($color=false){
		if(!$color){
			return $this->_color;
		}else{
			$calendar = $this;
			if($calendar->canWriteName()){
				$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
				$my_id = $this->getId();
                		$sql = "UPDATE $tablename SET color = '$color' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_color = $color;
					return $this->_color;
				}else{
					return $this->_color;
				}
			}else{
				return $this->_color;
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//  author()							//
	//	returns the author of this calendar			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the author of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function author($author=false){
		if(!$author){
			return (int)$this->_author;
		}else{
			$calendar = $this;
			if($calendar->canWriteName()){
				$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
				$my_id = $this->getId();
                		$sql = "UPDATE $tablename SET author = '$author' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_author = $author;
					return (int)$this->_author;
				}else{
					return (int)$this->_author;
				}
			}else{
				return (int)$this->_author;
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//  author()							//
	//	returns the author of this calendar			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the author of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function addedOn(){
		return $this->_added_on;
	}

	//////////////////////////////////////////////////////////////////
	//  isPublic()							//
	//	returns the public of this calendar			//
	//--------------------------------------------------------------//
	//  input: whether the calendar should be public		//
	//		1 for yes					//
	//		0 for no					//
	//  output: the public of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function isPublic($public="temp"){
		if($public==="temp"){
			return $this->_public;
		}else if(is_bool($public)){
			$calendar = $this;
			if($this->avalanche->loggedInHuh() == $calendar->author()){
				$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
				$my_id = $this->getId();
                		$sql = "UPDATE $tablename SET public = '$public' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_public = $public;
					return $this->_public;
				}else{
					return $this->_public;
				}
			}else{
				return $this->_public;
			}
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be boolean");
		}
	}

	//////////////////////////////////////////////////////////////////
	//  name()							//
	//	returns the name of this calendar			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the name of this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function name($name=false){
		if(!$name){
			if($this->canReadName()){
				return $this->_name;
			}else{
				return "";
			}
		}else{
			$calendar = $this;
			if($calendar->canWriteName()){
				if(strlen($name) > 0){
					$sql_name = addslashes($name);
					$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
					$my_id = $this->getId();
					$sql = "UPDATE $tablename SET name = '$sql_name' WHERE id = '$my_id'";
					$result = $this->avalanche->mysql_query($sql);
					if($result){
						$this->_name = $name;
						return $this->_name;
					}else{
						return $this->name();
					}
				}else{
					return $this->name();
				}
			}else{
				if($this->canReadName()){
					return $this->_name;
				}else{
					return "";
				}
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//  description()						//
	//	returns the description of this calendar		//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the description of this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function description($description=false){
		if(!$description){
			if($this->canReadName()){
				return $this->_description;
			}else{
				return "";
			}
		}else{
			$calendar = $this;
			if($calendar->canWriteName()){
				$sql_description = addslashes($description);
				$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
				$my_id = $this->getId();
                		$sql = "UPDATE $tablename SET description = '$sql_description' WHERE id = '$my_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_description = $description;
					return $this->_description;
				}else{
					if($this->canReadName()){
						return $this->_description;
					}else{
						return "";
					}
				}
			}else{
				if($this->canReadName()){
					return $this->_description;
				}else{
					return "";
				}
			}
		}
	}



	//////////////////////////////////////////////////////////////////
	//  fields()							//
	//	returns the array of fields for this calendar		//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the array of fields objects in this calendar	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function fields(){
		return $this->_fields;
	}

	//////////////////////////////////////////////////////////////////
	//  validations()						//
	//	returns the array of validations for this calendar	//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the array of validation objects in this calendar	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function validations(){
		if($this->canReadName() && $this->canReadValidations()){
			return $this->_validations;
		}else{
			return array();
		}
	}

	//////////////////////////////////////////////////////////////////
	//  events()							//
	//	returns a list of events for this calendar		//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the event objects in this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function events(){
		if($this->canReadName()){
			return $this->_events;
		}else{
			return array();
		}
	}

	//////////////////////////////////////////////////////////////////
	//  eventsToValidate()						//
	//	returns a list of events for this calendar		//
	//	that need to be validated				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the event objects in this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function eventsToValidate(){
		if($this->canReadName() && $this->canReadValidations()){
			return $this->_to_validate_events;
		}else{
			return array();
		}
	}

	//////////////////////////////////////////////////////////////////
	//  eventsToValidateByOthers()					//
	//	returns a list of events for this calendar		//
	//	that need to be validated				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the event objects in this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function eventsToValidateByOthers(){
		if($this->canReadName() && $this->canReadValidations()){
			return $this->_validated_events;
		}else{
			return array();
		}
	}

	//////////////////////////////////////////////////////////////////
	//  hasFieldHuh($field)						//
	//	returns whether or not this calendar contains the	//
	//	input field						//
	//--------------------------------------------------------------//
	//  input: $field - a field object				//
	//  output: boolean - true if field is in this calendar		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function hasFieldHuh($field){
		$field_list = $this->fields();
		if(is_object($field)){
			$field = $field->field();
		}
		for($i=0;$i<count($field_list) && $this->canReadName();$i++){
			if(is_string($field) && $field_list[$i]->field() == $field){
				return true;
			}
		}
		return false;
	}



	//////////////////////////////////////////////////////////////////
	//  __construct($id)						//
	//	initialize the calendar to the given calendar id	//
	//--------------------------------------------------------------//
	//  input: $id - the id of the calendar data to initialze to	//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	private $avalanche;
	function __construct($id, $avalanche, $myrow=false){
		$this->avalanche = $avalanche;
		$this->avalanche->setActive();
		if(!$myrow){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_calendars WHERE id='$id'";
			$result = $this->avalanche->mysql_query($sql);
			$myrow = mysql_fetch_array($result);
		}


		$ok = true;
		$this->_name = $myrow['name'];
		$this->_description = $myrow['description'];
		$this->_color = $myrow['color'];
		$this->_author = $myrow['author'];
		$this->_public = $myrow['public'];
		$this->_added_on = $myrow['added_on'];

		// need to init fields to proper array values.
		$this->_serial_id = false;
		$this->_id = $id;
		$this->_fields = array();
		$this->_start_date = "0000-00-01";
		$this->_end_date = false;
		$this->_end_after = false;
		$this->_events_date = array();
		$this->_is_serialized = false;

		$this->_is_attendee = new HashTable();
		$this->_hash_events = new HashTable();
		$this->permission_cache = new HashTable();

		$this->loadFieldList();
		if($this->canReadName()){
			$this->loadValidationList();
			$this->loadEventList();
			$this->loadToValidate();
			$this->loadValidated();
		}

		$this->_serial_can["canReadEntries"]      = $this->canReadEntries();
		$this->_serial_can["canWriteEntries"]     = $this->canWriteEntries();
		$this->_serial_can["canReadFields"]       = $this->canReadFields();
		$this->_serial_can["canWriteFields"]      = $this->canWriteFields();
		$this->_serial_can["canReadValidations"]  = $this->canReadValidations();
		$this->_serial_can["canWriteValidations"] = $this->canWriteValidations();
		$this->_serial_can["canReadName"]         = $this->canReadNameStrict();
		$this->_serial_can["canWriteName"]        = $this->canWriteName();
		$this->_serial_can["canReadComments"]     = $this->canReadComments();
		$this->_serial_can["canWriteComments"]    = $this->canWriteComments();
	}



	//////////////////////////////////////////////////////////////////
	//  reload()							//
	//	reloads this calendar with the current event list	//
	//--------------------------------------------------------------//
	//  input: void							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads field objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function reload(){
		$this->_is_attendee = new HashTable();
		$this->_recur_objects = array();
		$this->_hash_events = new HashTable();
		$this->permission_cache = new HashTable();
		$this->loadFieldList();
		$this->loadValidationList();
		$this->loadEventList();
		$this->loadToValidate();
		$this->loadValidated();
	}


	// very expensive!
	// resetting this cache will slow the hell out of this thing
	public function clearCache(){
		if(isset($this->_serial_can["canReadName"])) unset($this->_serial_can["canReadName"]);
		if(isset($this->_serial_can["canWriteName"])) unset($this->_serial_can["canWriteName"]);
		if(isset($this->_serial_can["canReadComments"])) unset($this->_serial_can["canReadComments"]);
		if(isset($this->_serial_can["canWriteComments"])) unset($this->_serial_can["canWriteComments"]);
		if(isset($this->_serial_can["canReadEntries"])) unset($this->_serial_can["canReadEntries"]);
		if(isset($this->_serial_can["canWriteEntries"])) unset($this->_serial_can["canWriteEntries"]);
		if(isset($this->_serial_can["canReadValidations"])) unset($this->_serial_can["canReadValidations"]);
		if(isset($this->_serial_can["canWriteValidations"])) unset($this->_serial_can["canWriteValidations"]);
		if(isset($this->_serial_can["canReadFields"])) unset($this->_serial_can["canReadFields"]);
		if(isset($this->_serial_can["canWriteFields"])) unset($this->_serial_can["canWriteFields"]);
	}


	//////////////////////////////////////////////////////////////////
	//  loadFieldList()						//
	//	loads the field list for this calendar upon		//
	//	called during initialization				//
	//--------------------------------------------------------------//
	//  input: void							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads field objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function loadFieldList(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$this->_fields = array();
		$ret = array();
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_fields WHERE valid='0' ORDER BY form_order, id ASC";
		$result = $this->avalanche->mysql_query($sql);
		if($result){
			while($myrow = mysql_fetch_array($result)){
				$new_field = $fm->getField($myrow["type"]);
				if(is_object($new_field)){
					$new_field->init($this, $myrow);
//					$new_field->calendar($this);
//					$new_field->setId($myrow['id']);
					$ret[] = $new_field;
				}else{
					trigger_error("Error in calendar \"" . $this->name() . "\". cannot load field type: " . $myrow["type"], E_USER_WARNING);

				}
			}
			$this->_fields = $ret;
			return true;
		}else{
			return false;
		}
	}



	function hasValidation(){
		return $this->_has_validation;
	}

	//////////////////////////////////////////////////////////////////
	//  loadValidationList()					//
	//	loads the Validation list for this calendar upon	//
	//	called during initialization				//
	//--------------------------------------------------------------//
	//  input: void							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads Validation objects into this calendar		//
	//////////////////////////////////////////////////////////////////
	function loadValidationList(){
		$this->_validations = array();
		$this->_sql_validations = "";
		$this->_sql_to_validate = "";
		$this->_sql_validated = "";
		if(!$this->canReadName()){
			return;
		}

		/*******************************************************************/
		/*******************************************************************/
		/****************   don't worry about this   ***********************/
		/**************   we don't do validation now   *********************/
		/*******************************************************************/
		return true;


		$ret = array();
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_fields WHERE valid='1' ORDER BY id ASC";
		$result = $this->avalanche->mysql_query($sql);
		if($result){
			$temp_validated = "0";
			$this->_has_validation = false;
			while($myrow = mysql_fetch_array($result)){
				$this->_has_validation = true;
				$new_validation = new module_strongcal_validation($this->avalanche);
				$this->_sql_validations .= "`" . $myrow['field'] . "` = '1' AND ";
				if($myrow['user'] == $this->avalanche->loggedInHuh() ||
					$this->avalanche->userInGroupHuh($this->avalanche->loggedInHuh(), $myrow['usergroup'])){
					$this->_sql_to_validate .= "`" . $myrow['field'] . "` = '0' OR ";
					$this->_sql_validated .= "`" . $myrow['field'] . "` = '1' AND ";
				}else{
					$temp_validated .= " OR ";
					$temp_validated .= "`" . $myrow['field'] . "` = '0'";
				}
				$new_validation->init($this, $myrow);
				$ret[] = $new_validation;
			}
			$temp_validated = "(" . $temp_validated . ")";
			$this->_sql_validated = $this->_sql_validated . $temp_validated;
			$this->_sql_validated = "(" . $this->_sql_validated . ") AND ";

			$this->_sql_to_validate .= "0";
			$this->_sql_to_validate = "(" . $this->_sql_to_validate . ") AND ";
			$this->_validations = $ret;
			return true;
		}else{
			return false;
		}
	}



	// returns an event object from for the specified row
	// in the calendar table.
	// the event object is pulled from cache when available
	// or added to cache as case may be
	private function getEventFromRow($myrow){
		if(!is_array($myrow)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an event array");
		}
		if(is_object($this->_hash_events->get((int) $myrow["id"]))){
			$new_event = $this->_hash_events->get((int) $myrow["id"]);
		}else{
			$new_event = new module_strongcal_event($this->avalanche, $this, $myrow);
			$this->_hash_events->put($new_event->getId(), $new_event);
			$this->indexEventByDate($new_event);
		}
		return $new_event;
	}




	// won't return more than 200
	function getEventsAfter($datetime, $limit=200){
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE added_on > '$datetime' LIMIT $limit";
		$result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysql_fetch_array($result)){
			  $ret[] = $this->getEventFromRow($myrow);
		}
		return $ret;
	}

	//////////////////////////////////////////////////////////////////
	//  loadEventList()						//
	//	loads the event list for this calendar			//
	//--------------------------------------------------------------//
	//  input: 							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads event objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function loadEventList(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$this->_events = array();
		if(!$this->canReadName()){
			return;
		}


		$ret = array();
		$start_date = $this->_start_date;
		if($this->_start_date > "0000-99-99"){
		  $sdatestamp = mktime(-$strongcal->timezone(), 0, 0, substr($start_date,5,2), substr($start_date,8,2)-1, substr($start_date,0,4));
		  $start_date = date("Y-m-d", $sdatestamp);
		  $start_time = date("H:i:00", $sdatestamp);
		}else{
			return;
		}

		if($this->_end_date > "0000-99-99"){
			$datestamp = mktime(-$strongcal->timezone(), 0, 0, substr($this->_end_date,5,2), substr($this->_end_date,8,2)+1, substr($this->_end_date,0,4));
			$end_date = date("Y-m-d", $datestamp);
			$end_time = date("H:i:00", $datestamp);
		}else if(is_numeric($this->_end_after)){
		  // set the end date to 2 days after our adjusted start date
		  $edatestamp = mktime(-$strongcal->timezone(), 0, 0, substr($start_date,5,2), substr($start_date,8,2)+2, substr($start_date,0,4));
		  $end_date = date("Y-m-d", $edatestamp);
		  $end_time = date("H:i:00", $edatestamp);

		  $edatestamp = mktime(0, 0, 0, substr($start_date,5,2), substr($start_date,8,2)+2, substr($start_date,0,4));
		  $all_day_end_date = date("Y-m-d", $edatestamp);
		}else{
			return;
		}

		if($this->_end_date){
		  $sql = $this->_sql_validations . " ((start_date <= '" . $end_date . "' AND end_date >= '" . $start_date . "' AND all_day = '0') OR start_date <= '" . $this->_end_date . "' AND end_date >= '" . $this->_start_date . "' AND all_day = '1') ORDER BY start_date, start_time, end_date, end_time, id ASC";
		}else
		if($this->_end_after){
		  $sql = $this->_sql_validations . " ((start_date <= '" . $end_date . "' AND end_date >= '" . $start_date . "' AND all_day = '0') OR start_date <= '" . $all_day_end_date . "' AND end_date >= '" . $this->_start_date . "' AND all_day = '1') ORDER BY start_date, start_time, end_date, end_time, id ASC";
		}else{
		  $sql = $this->_sql_validations . " ((start_date <= '0000-00-01' AND end_date >= '0000-00-00' AND all_day = '0') OR start_date <= '0000-00-01' AND end_date >= '0000-00-00' AND all_day = '1') ORDER BY start_date, start_time, end_date, end_time, id ASC";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE $sql";

		$result = $this->avalanche->mysql_query($sql);

		if($result){
		  $count = 0;
			while($myrow = mysql_fetch_array($result)){
				$new_event = false;
				if(!$myrow["all_day"]){
					$s = $strongcal->adjust($myrow["start_date"], $myrow["start_time"]);
					$e = $strongcal->adjust($myrow["end_date"], $myrow["end_time"]);

					if($this->_end_date &&
					   ($e["date"] < $this->_start_date ||
						$s["date"] > $this->_end_date)
					   ||
					   $this->_end_after &&
					   ($count >= $this->_end_after ||
					   $e["date"] < $this->_start_date ||
						$s["date"] > $this->_start_date)){
						// since sql can only filter out events by the day, and the timezone
						// for the user can affect the day the event is on,
						// we have to filter out false positives.
					}else{
					  $new_event = $this->getEventFromRow($myrow);
					}
				}else{
				  $new_event = $this->getEventFromRow($myrow);
				}
				if(is_object($new_event)){
					if($this->canReadNameStrict() || $this->canReadEvent($new_event->getId(), $this->avalanche->getActiveUser())){
					  $count++;
					  $ret[] = $new_event;
					}
				}
			}
			$this->_events = $ret;
			return true;
		}else{
			return false;
		}
	}


	// returns a list of all events matching the given text
	function getAllEventsMatching($text, $limit=200){
		$texts = explode(" ", $text);
		$rightquery = "1 ";
		$fields = $this->fields();
		foreach($texts as $text){
			$middle = "0";
			foreach($fields as $field){
				if($field->type() == "text" || $field->type() == "largetext"){
					$middle .= " OR " . $field->field() . " LIKE " . "'%$text%'";
				}
			}
			$rightquery .= "AND ($middle) ";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE " . $rightquery . " LIMIT $limit";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException(mysql_error());
		}
		$event_results = array();
		while($myrow = mysql_fetch_array($result)){
			$obj = $this->getEvent($myrow["id"], $myrow);
			if(is_object($obj)){
				$event_results[] = $obj;
			}
		}
		return $event_results;
	}


	// returns a list of all events matching the given text
	function getAllCommentsMatching($text){
		$comment_results = array();
		if($this->canReadComments()){
			$texts = explode(" ", $text);
			$rightquery = "1 ";
			$fields = array("title", "body");
			foreach($texts as $text){
				$middle = "0";
				foreach($fields as $field){
					$middle .= " OR " . $field . " LIKE " . "'%$text%'";
				}
				$rightquery .= "AND ($middle) ";
			}

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_comments WHERE " . $rightquery;
			$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException(mysql_error());
			}
			while($myrow = mysql_fetch_array($result)){
				$myrow["cal_id"] = $this->getId();
				$myrow["date"] = $myrow["post_date"];
				$comment_results[] = $myrow;
			}
		}
		return $comment_results;
	}


	//////////////////////////////////////////////////////////////////
	//  loadToValidate()						//
	//	loads the event list for this calendar			//
	//	that still need to be validated				//
	//--------------------------------------------------------------//
	//  input: 							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads event objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function loadToValidate(){
		$this->_to_validate_events = array();
		if(!$this->canReadName()){
			return;
		}

		if($this->hasValidation()){
			$ret = array();
			if($this->_sql_to_validate){
				$sql = $this->_sql_to_validate . " 1 ORDER BY start_date, start_time, end_date, end_time, id ASC";
			}else{
				$sql = " 0 ORDER BY start_date, start_time, end_date, end_time, id ASC";
			}

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE $sql";

			$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					$obj = $this->getEvent($myrow["id"], $myrow);
					if($obj !== false){
						$ret[] = $obj;
					}
				}
				$this->_to_validate_events = $ret;
				return true;
			}else{
				return false;
			}
		}
		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  loadValidated()						//
	//	loads the event list for this calendar			//
	//	that still need to be validated by a user/group other	//
	//	than me.						//
	//--------------------------------------------------------------//
	//  input: 							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads event objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function loadValidated(){
		$this->_validated_events = array();
		if(!$this->canReadName()){
			return;
		}

		if($this->hasValidation()){
			$ret = array();
			if($this->_sql_validated){
				$sql = $this->_sql_validated . " 1 ORDER BY start_date, start_time, end_date, end_time, id ASC";
			}else{
				$sql = " 0 ORDER BY start_date, start_time, end_date, end_time, id ASC";
			}

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE $sql";

			$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					$obj = $this->getEvent($myrow["id"], $myrow);
					if($obj !== false){
						$ret[] = $obj;
					}
				}
				$this->_validated_events = $ret;
				return true;
			}else{
				return false;
			}
		}
		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  indexEventByDate($event)					//
	//	loads the event list for this calendar			//
	//--------------------------------------------------------------//
	//  input: 							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads event objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function indexEventByDate($event){
		$start_date = $event->getDisplayValue("start_date");
		$end_date = $event->getDisplayValue("end_date");
		if($start_date == $end_date){
			$this->_events_date[$start_date][] = $event;
		}else{
			$day   = substr($start_date,8,2);
			$month = substr($start_date,5,2);
			$year  = substr($start_date,0,4);
			$start_stamp = mktime(0,0,0,$month,$day,$year);

			$day   = substr($end_date,8,2);
			$month = substr($end_date,5,2);
			$year  = substr($end_date,0,4);
			$end_stamp = mktime(0,0,0,$month,$day,$year);

			while(date("Y-m-d", $start_stamp) <= date("Y-m-d", $end_stamp)){
				$start_date = date("Y-m-d", $start_stamp);
				$this->_events_date[$start_date][] = $event;
				$start_stamp = strtotime("+1 day", $start_stamp);
			}
		}
	}


	//////////////////////////////////////////////////////////////////
	//  removeFromDateIndex($event)					//
	//	loads the event list for this calendar			//
	//--------------------------------------------------------------//
	//  input: 							//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	loads event objects into this calendar			//
	//////////////////////////////////////////////////////////////////
	function removeFromDateIndex($event){
		$start_date = $event->getValue("start_date");
		$end_date = $event->getValue("end_date");


		if($start_date == $end_date){
			if(isset($this->_events_date[$start_date]) && is_array($this->_events_date[$start_date])){
				$list = $this->_events_date[$start_date];
				for($i=0;$i<count($list);$i++){
					if($list[$i]->getId() == $event->getId()){
						array_splice($this->_events_date[$start_date], $i, 1);
					}
				}
			}
		}else{
			$day   = substr($start_date,8,2);
			$month = substr($start_date,5,2);
			$year  = substr($start_date,0,4);
			$start_stamp = mktime(0,0,0,$month,$day,$year);

			$day   = substr($end_date,8,2);
			$month = substr($end_date,5,2);
			$year  = substr($end_date,0,4);
			$end_stamp = mktime(0,0,0,$month,$day,$year);

			while(date("Y-m-d", $start_stamp) <= date("Y-m-d", $end_date)){
				$start_date = date("Y-m-d", $start_stamp);
				for($i=0;$i<count($list);$i++){
					if($list[$i]->getId() == $event->getId()){
						array_splice($this->_events_date[$start_date], $i, 1);
						$i=count($list);
					}
				}
				$start_stamp = strtotime("+1 day", $start_stamp);
			}
		}
	}


	//////////////////////////////////////////////////////////////////
	//  countEvents()						//
	//	counts the number of events in the calendar		//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countEvents(){
		$events = array();
		$ret = array();

		if($this->canReadName()){
			$sql = "SELECT COUNT(*) AS total FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId();
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $myrow["total"];
				}
				return 0;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  countComments()						//
	//	counts the number of events in the calendar		//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countComments(){
		$events = array();
		$ret = array();

		if($this->canReadComments() && $this->canReadName()){
		$sql = "SELECT COUNT(*) AS total FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_comments";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $myrow["total"];
				}
				return 0;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getLastCommentAdded()					//
	//--------------------------------------------------------------//
	//  input: true if comment must be authored by me		//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getLastCommentAdded($me = false){
		$events = array();
		$ret = array();

		if($this->canReadComments() && $this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_comments $where ORDER BY post_date DESC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				if($myrow = mysql_fetch_array($result)){
					return $myrow;
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getFirstCommentAdded()					//
	//--------------------------------------------------------------//
	//  input: true if comment must be authored by me		//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getFirstCommentAdded($me = false){
		$events = array();
		$ret = array();

		if($this->canReadComments() && $this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_comments $where ORDER BY post_date ASC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $myrow;
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getLastEventAdded()						//
	//	counts the number of events the logged in user has	//
	//	entered.						//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getLastEventAdded($me = false){
		$events = array();
		$ret = array();

		if($this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " $where ORDER BY added_on DESC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				if($myrow = mysql_fetch_array($result)){
					return $this->getEvent($myrow['id'], $myrow);
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getFirstEventAdded()					//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getFirstEventAdded($me = false){
		$events = array();
		$ret = array();

		if($this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " $where ORDER BY added_on ASC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $this->getEvent($myrow['id'], $myrow);
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getLastEvent()						//
	//	counts the number of events the logged in user has	//
	//	entered.						//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getLastEvent($me = false){
		$events = array();
		$ret = array();

		if($this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " $where ORDER BY end_date DESC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				if($myrow = mysql_fetch_array($result)){
					return $this->getEvent($myrow['id'], $myrow);
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getFirstEvent()						//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getFirstEvent($me = false){
		$events = array();
		$ret = array();

		if($this->canReadName()){

		if($me){
			$user = $this->avalanche->loggedInHuh();
			if(!$user){
				$user = $this->avalanche->getVar("USER");
			}
			$where = "WHERE author='" . $user . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " $where ORDER BY start_date ASC";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $this->getEvent($myrow['id'], $myrow);
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  countAuthoredEvents()					//
	//	counts the number of events the logged in user has	//
	//	entered.						//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countAuthoredEvents(){
		$events = array();
		$ret = array();

		if($this->canReadName()){
		$user = $this->avalanche->loggedInHuh();
		if(!$user){
			$user = $this->avalanche->getVar("USER");
		}
		$sql = "SELECT author, COUNT(*) AS total FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE author='" . $user . "' GROUP BY author";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $myrow;
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}



	//////////////////////////////////////////////////////////////////
	//  countAuthoredComments()					//
	//	counts the number of comments the logged in user has	//
	//	entered.						//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countAuthoredComments(){
		$events = array();
		$ret = array();

		if($this->canReadName()){
		$user = $this->avalanche->loggedInHuh();
		if(!$user){
			$user = $this->avalanche->getVar("USER");
		}

		$sql = "SELECT author, COUNT(*) AS total FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . "_comments WHERE author='" . $user . "' GROUP BY author";
		$result = $this->avalanche->mysql_query($sql);
			if($result){
				while($myrow = mysql_fetch_array($result)){
					return $myrow;
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  countEventsOn($date)					//
	//	counts the events that occur on this day. 		//
	//	NEEDS TO COUNT EVENTS THAT START EARLIER AND END	//
	//	LATER THAN DATE... JUST MAKE SURE IT COUNTS RIGHT...	//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countEventsOn($date){
		$events = array();
		$ret = array();

		$sql = $this->_sql_validations . " start_date <= '" . $date . "' AND end_date >= '" . $date . "' ORDER BY start_date, start_time, end_date, end_time, id ASC";

		$sql = "SELECT COUNT(*) AS count FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE $sql";

		if(!$this->is_serialized()){
			if($this->canReadName()){
				$result = $this->avalanche->mysql_query($sql);
				if($result && $myrow = mysql_fetch_array($result)){
					return $myrow['count'];
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			$result = $this->avalanche->mysql_query($sql);
			if($result && $myrow = mysql_fetch_array($result)){
				return $myrow['count'];
			}else{
				return false;
			}
		}
	}


	function adjust_date($date, $adjust){
		$year  = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day   = substr($date, 8, 2);
		return date("Y-m-d", mktime(0,0,0, $month, $day + $adjust, $year));
	}

	//////////////////////////////////////////////////////////////////
	//  getEssentialsOn($date)					//
	// returns an array with title, start/end_time, and end_date	//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getEssentialsOn($date, $limit=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$events = array();
		$ret = array();


		$start_date = $this->adjust_date($date, -1);
		$end_date = $this->adjust_date($date, 1);
		if(!$this->is_serialized()){
			if($this->canReadName()){
				if(isset($this->_get_essentials_on[$date][$limit]) && count($this->_get_essentials_on[$date][$limit])){
					/*
					 * return buffered value;
					 */
					return $this->_get_essentials_on[$date][$limit];
				}
				if($limit){
					$limit = "LIMIT $limit";
				}else{
					$limit = "";
				}

				$sql = "SELECT id, recur_id, title, description, start_date, start_time, end_time, end_date, priority FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE " . $this->_sql_validations . "start_date <= '$end_date' AND end_date >= '$start_date' ORDER BY start_time, end_date, end_time ASC $limit";
				$result = $this->avalanche->mysql_query($sql);
				$sd_field = $fm->getField("date");
				$ed_field = $fm->getField("date");
				$st_field = $fm->getField("time");
				$et_field = $fm->getField("time");
				$title_field = $fm->getField("text");
				$desc_field = $fm->getField("largetext");
				$priority_field = $fm->getField("select");
				if($result){
					while($myrow = mysql_fetch_array($result)){

						// load values into field so we can get the right formatting

						$sd_field->set_value(stripslashes($myrow["start_date"]));
						$ed_field->set_value(stripslashes($myrow["end_date"]));
						$st_field->set_value(stripslashes($myrow["start_time"]));
						$et_field->set_value(stripslashes($myrow["end_time"]));
						$title_field->set_value(stripslashes($myrow["title"]));
						$desc_field->set_value(stripslashes($myrow["description"]));
						$priority_field->set_value(stripslashes($myrow["priority"]));

						$start_times = $strongcal->adjust($sd_field->display_value(), $st_field->display_value());
						$end_times   = $strongcal->adjust($ed_field->display_value(), $et_field->display_value());

						$myrow["start_date"]  = $start_times["date"];
						$myrow["end_date"]    = $end_times["date"];
						$myrow["start_time"]  = $start_times["time"];
						$myrow["end_time"]    = $end_times["time"];
						$myrow["title"]       = $title_field->display_value();
						$myrow["description"] = $desc_field->display_value();
						$myrow["priority"]    = $priority_field->display_value();

						if(!$this->canReadEntries()){
							$myrow["title"] = "busy";
							$myrow["description"] = "";
							$myrow["priority"] = "";
						}

						if($myrow["start_date"] <= $date && $myrow["end_date"] >= $date){
							$ret[] = array("needs_validate" => false, "event_id" => $myrow['id'],"calendar" => $this,"cal_id" => $this->getId(),"recur_id" => $myrow['recur_id'],"title" => stripslashes($myrow['title']), "description" => stripslashes($myrow['description']), "start_date" => $myrow['start_date'], "start_time" => $myrow['start_time'], "end_time" => $myrow['end_time'], "end_date" => $myrow['end_date'], "priority" => trim($myrow['priority']));
						}
					}
					$this->_get_essentials_on[$date][$limit] = $ret;
					return $ret;
				}else{
					throw new DatabaseException(mysql_error());
				}
			}else{
				return array();
			}
		}else{
			if($this->canReadEntries() && $this->canReadName()){
				$events = $this->events();
				for($i=0;$i<count($events);$i++){
					if(($events[$i]->getValue("start_date") <= $date) &&
					   ($events[$i]->getValue("end_date") >= $date) &&
					   (!$limit || count($ret) < $limit)){
						$recur_id = $events[$i]->stealRecurrance();
						if(is_object($recur_id)){
							$recur_id = $recur_id->getId();
						}else{
							$recur_id = false;
						}
						$ret[] = array("needs_validate" => false, "event_id" => $events[$i]->getId(),"calendar" => $this, "cal_id" => $this->getId(), "recur_id" => $recur_id,"title" => stripslashes($events[$i]->getDisplayValue('title')),"description" => stripslashes($events[$i]->getDisplayValue('description')), "start_date" => $events[$i]->getDisplayValue('start_date'), "start_time" => $events[$i]->getDisplayValue('start_time'), "end_time" => $events[$i]->getDisplayValue('end_time'), "end_date" => $events[$i]->dispaly_value('end_date'), "priority" => trim($events[$i]->getDisplayValue("priority")));
					}
				}
				return $ret;
			}
			return array();
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getNeedyEssentialsOn($date)					//
	// returns an array with title, start/end_time, and end_date	//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getNeedyEssentialsOn($date, $limit=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$events = array();
		$ret = array();

		$start_date = $this->adjust_date($date, -1);
		$end_date = $this->adjust_date($date, 1);
		if(!$this->is_serialized() && $this->canReadEntries() && $this->canReadName() && $this->canReadValidations()){

		if($limit){
			$limit = "LIMIT $limit";
		}else{
			$limit = "";
		}

		if($this->_sql_to_validate){
			$sql = "SELECT id, recur_id, title, description, start_date, start_time, end_time, end_date, priority FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE " . $this->_sql_to_validate . " start_date <= '$end_date' AND end_date >= '$start_date' ORDER BY start_time, end_date, end_time ASC $limit";
		}else{
			return array();
		}

		$result = $this->avalanche->mysql_query($sql);
			if($result){
				$sd_field = $fm->getField("date");
				$ed_field = $fm->getField("date");
				$st_field = $fm->getField("time");
				$et_field = $fm->getField("time");
				$title_field = $fm->getField("text");
				$desc_field = $fm->getField("largetext");
				$priority_field = $fm->getField("select");
				while($myrow = mysql_fetch_array($result)){

						$sd_field->set_value(stripslashes($myrow["start_date"]));
						$ed_field->set_value(stripslashes($myrow["end_date"]));
						$st_field->set_value(stripslashes($myrow["start_time"]));
						$et_field->set_value(stripslashes($myrow["end_time"]));
						$title_field->set_value(stripslashes($myrow["title"]));
						$desc_field->set_value(stripslashes($myrow["description"]));
						$priority_field->set_value(stripslashes($myrow["priority"]));

						$start_times = $strongcal->adjust($sd_field->display_value(), $st_field->display_value());
						$end_times   = $strongcal->adjust($ed_field->display_value(), $et_field->display_value());

						$myrow["start_date"]  = $start_times["date"];
						$myrow["end_date"]    = $end_times["date"];
						$myrow["start_time"]  = $start_times["time"];
						$myrow["end_time"]    = $end_times["time"];
						$myrow["title"]       = $title_field->display_value();
						$myrow["description"] = $desc_field->display_value();
						$myrow["priority"]    = $priority_field->display_value();

						if(!$this->canReadEntries()){
							$myrow["title"] = "busy";
							$myrow["description"] = "";
							$myrow["priority"] = "";
						}

					if($myrow["start_date"] <= $date && $myrow["end_date"] >= $date)
						$ret[] = array("needs_validate" => true, "event_id" => $myrow['id'],"calendar" => $this,"cal_id" => $this->getId(),"recur_id" => $myrow['recur_id'],"title" => stripslashes($myrow['title']), "description" => stripslashes($myrow['description']), "start_date" => $myrow['start_date'], "start_time" => $myrow['start_time'], "end_time" => $myrow['end_time'], "end_date" => $myrow['end_date'], "priority" => trim($myrow['priority']));
				}
				return $ret;
			}else{
				return array();
			}
		}else{
			return array();
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getValidatedEssentialsOn($date)				//
	// returns an array with title, start/end_time, and end_date	//
	//--------------------------------------------------------------//
	//  input: the date to count the events for			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getValidatedEssentialsOn($date, $limit=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$events = array();
		$ret = array();

		$start_date = $this->adjust_date($date, -1);
		$end_date = $this->adjust_date($date, 1);
		if(!$this->is_serialized() && $this->canReadEntries() && $this->canReadName() && $this->canReadValidations()){

		if($limit){
			$limit = "LIMIT $limit";
		}else{
			$limit = "";
		}

		if($this->_sql_validated){
			$sql = "SELECT id, recur_id, title, description, start_date, start_time, end_time, end_date, priority FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE " . $this->_sql_validated . " start_date <= '$end_date' AND end_date >= '$start_date' ORDER BY start_time, end_date, end_time ASC $limit";
		}else{
			return array();
		}

		$result = $this->avalanche->mysql_query($sql);
			if($result){
				$sd_field = $fm->getField("date");
				$ed_field = $fm->getField("date");
				$st_field = $fm->getField("time");
				$et_field = $fm->getField("time");
				$title_field = $fm->getField("text");
				$desc_field = $fm->getField("largetext");
				$priority_field = $fm->getField("select");
				while($myrow = mysql_fetch_array($result)){

						$sd_field->set_value(stripslashes($myrow["start_date"]));
						$ed_field->set_value(stripslashes($myrow["end_date"]));
						$st_field->set_value(stripslashes($myrow["start_time"]));
						$et_field->set_value(stripslashes($myrow["end_time"]));
						$title_field->set_value(stripslashes($myrow["title"]));
						$desc_field->set_value(stripslashes($myrow["description"]));
						$priority_field->set_value(stripslashes($myrow["priority"]));

						$start_times = $strongcal->adjust($sd_field->display_value(), $st_field->display_value());
						$end_times   = $strongcal->adjust($ed_field->display_value(), $et_field->display_value());

						$myrow["start_date"]  = $start_times["date"];
						$myrow["end_date"]    = $end_times["date"];
						$myrow["start_time"]  = $start_times["time"];
						$myrow["end_time"]    = $end_times["time"];
						$myrow["title"]       = $title_field->display_value();
						$myrow["description"] = $desc_field->display_value();
						$myrow["priority"]    = $priority_field->display_value();

						if(!$this->canReadEntries()){
							$myrow["title"] = "busy";
							$myrow["description"] = "";
							$myrow["priority"] = "";
						}

					if($myrow["start_date"] <= $date && $myrow["end_date"] >= $date)
						$ret[] = array("needs_validate" => true, "event_id" => $myrow['id'],"calendar" => $this,"cal_id" => $this->getId(),"recur_id" => $myrow['recur_id'], "title" => stripslashes($myrow['title']), "description" => stripslashes($myrow['description']), "start_date" => $myrow['start_date'], "start_time" => $myrow['start_time'], "end_time" => $myrow['end_time'], "end_date" => $myrow['end_date'], "priority" => trim($myrow['priority']));
				}
				return $ret;
			}else{
				return array();
			}
		}else{
			return array();
		}
	}




	//////////////////////////////////////////////////////////////////
	//  getEventsOn($date)						//
	//	gets a list of events that occur on this date		//
	//	MAKE SURE IT GETS ALL EVENTS, EVEN EVENTS THAT START	//
	//	ON A DAY EARLIER OR END LATER ETC. JUST MAKE SURE...	//
	//--------------------------------------------------------------//
	//  input: the date to get the events for			//
	//		(gets all events on that day, even events that	//
	//		 start earlier and end later.)			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getEventsOn($date){
		$events = array();
		$ret = array();

		if($this->canReadNameStrict() && isset($this->_events_date[$date])){
			return $this->_events_date[$date];
		}else{
			if(isset($this->_events_date[$date])){
				$ret = array();
				foreach($this->_events_date[$date] as $e){
					if($this->canReadEvent($e->getId(), $this->avalanche->getActiveUser())){
						$ret[] = $e;
					}
				}
				return $ret;
			}else{
				return array();
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getEventsIn($recur)						//
	//	gets a list of events that occur in this series		//
	//--------------------------------------------------------------//
	//  input: the date to get the events for			//
	//		(gets all events on that day, even events that	//
	//		 start earlier and end later.)			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getEventsIn($recur){
		$events = array();
		$ret = array();
		if(!$this->is_serialized() && is_object($recur)){
			$cal = $recur->calendar();
		}else{
			return array();
		}
		if($this->canReadName() && $cal->getId() == $this->getId()){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE recur_id = '" . $recur->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				$obj = $this->getEvent($myrow["id"], $myrow);
				if($obj !== false){
					$ret[] = $obj;
				}
			}
			return $ret;
		}else{
			return array();
		}
	}



	//////////////////////////////////////////////////////////////////
	//  countEventsIn($recur)					//
	//	gets a list of events that occur in this series		//
	//--------------------------------------------------------------//
	//  input: the date to get the events for			//
	//		(gets all events on that day, even events that	//
	//		 start earlier and end later.)			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function countEventsIn($recur){
		$events = array();
		$ret = array();
		if(!$this->is_serialized() && is_object($recur)){
			$cal = $recur->calendar();
		}else{
			return array();
		}
		if($this->canReadName() && $cal->getId() == $this->getId()){
			$sql = "SELECT COUNT(*) AS total FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE recur_id = '" . $recur->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				return $myrow['total'];
			}
			return $ret;
		}else{
			return array();
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getRecur($id)						//
	//--------------------------------------------------------------//
	//  input: the id of the recur object to get			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getRecur($id){
		if($this->canReadName()){
			if(isset($this->_recur_objects[$id]) && is_object($this->_recur_objects[$id])){
				return $this->_recur_objects[$id];
			}else{
				$temp = new module_strongcal_recurrance($this->avalanche, $this, $id);
				$this->_recur_objects[$id] = $temp;
				return $this->_recur_objects[$id];
			}
		}else{
			return false;
		}
	}



	//////////////////////////////////////////////////////////////////
	//  putRecur($id)						//
	//--------------------------------------------------------------//
	//  input: the id of the recur object to get			//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function putRecur($recur){
		if($this->canReadName()){
			if(is_object($recur)){
				$this->_recur_objects[$recur->getId()] = $recur;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getEvent($id)						//
	//--------------------------------------------------------------//
	//  input: the id of the event to get				//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	none							//
	//////////////////////////////////////////////////////////////////
	function getEvent($id, $myrow=false){
		$ret = false;
		if(!$this->is_serialized()){
			if(is_object($this->_hash_events->get((int) $id))){
				$new_event = $this->_hash_events->get((int) $id);
				return $new_event;
			}else{
				$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->getId() . " WHERE id = '$id'";
				if($this->canReadName()){
					$events = $this->events();
					if(is_array($myrow)){
						$new_event = new module_strongcal_event($this->avalanche, $this, $myrow);
						if($this->canReadEvent($new_event->getId(), $this->avalanche->getActiveUser())){
							$this->_hash_events->put($new_event->getId(), $new_event);
							$ret = $new_event;
						}
					}else{
						$result = $this->avalanche->mysql_query($sql);
						if($myrow = mysql_fetch_array($result)){
							for($i=0;$i<count($this->_events);$i++){
								if($this->_events[$i]->getId() == $id){
									return $this->_events[$i];
								}
							}
							$new_event = new module_strongcal_event($this->avalanche, $this, $myrow);
							if($this->canReadEvent($new_event->getId(), $this->avalanche->getActiveUser())){
								$this->_hash_events->put($new_event->getId(), $new_event);
								$ret = $new_event;
							}
						}
					}
					return $ret;
				}else{
					return false;
				}
			}
		}else{
			$events = $this->events();
			for($i=0;$i<count($events);$i++){
				if($events[$i]->getId() == $id){
					return $events[$i];
				}
			}
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  addField($my_array)						//
	//--------------------------------------------------------------//
	//  input: $field - the name of the field			//
	//	   $type - the type of the field, as defined by the	//
	//		   the above constants				//
	//	   $prompt - the prompt for the field, for use in the	//
	//		   add/mod forms				//
	//	   $multiple - true if multiple values can be held	//
	//		   (only applicable to MULITPLE_SELECT_INPUT)	//
	//	   $value - default value for field			//
	//	   $form_order - order it appears on add/mod form	//
	//	   $style - the style of the field (if available)	//
	//  output: boolean if field added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function addField($my_array){
		$field = $my_array["name"];
		if(!ereg('^([[:alnum:]]|[0-9]|[_])+$', $field)){
			return false;
		}
		$prompt = addslashes($my_array["prompt"]);
		$type = $my_array["type"];
		$value = addslashes($my_array["value"]);
		$removeable = 1;
		$size = $my_array["size"];
		$style = $my_array["style"];

		$the_type = $my_array["MYSQL_TYPE"];
		if(!isset($my_array["form_order"]) || !$my_array["form_order"]){
			$form_order = count($this->fields()) + 1;
		}else{
			$form_order = $my_array["form_order"];
		}
		$valid = 0;
		$user = 0;
		$usergroup = 0;
		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
		$sql = "ALTER TABLE `$tablename` ADD `$field` $the_type not null";
		if(!$this->is_serialized() && $this->canWriteFields() && $this->canReadName()){
			$result = $this->avalanche->mysql_query($sql);
		}else{
			return false;
		}

		if($this->canWriteFields() && $this->canReadName()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_fields";
	                $sql = "INSERT INTO `$tablename` (`id`, `prompt`, `field`, `type`, `value`, `valid`, `form_order`, `user`, `usergroup`, `removeable`, `size`, `style`) VALUES ('', '$prompt', '$field', '$type', '$value', '$valid', '$form_order', '$user', '$usergroup', '$removeable', '$size', '$style')";
			$this->avalanche->mysql_query($sql);
			$this->loadFieldList();
			return true;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  dropField($field)						//
	//--------------------------------------------------------------//
	//  input: $field - the name of the field			//
	//		  (not a field object)				//
	//  output: boolean if field added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function dropField($field){
		if(is_object($field)){
			$field = $field->field();
		}
		if(!$this->is_serialized() && $this->isRemoveable($field) && $this->canReadName()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
			$sql = "ALTER TABLE $tablename DROP `$field`";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_fields";
	        	        $sql = "DELETE FROM $tablename WHERE field='$field'";
				$this->avalanche->mysql_query($sql);
				$this->loadFieldList();
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getValidation($field)					//
	//--------------------------------------------------------------//
	//  input: $field - the name of the validation			//
	//  output: field object 					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getvalidation($field){
		$field_list = $this->validations();
		for($i=0;$i<count($field_list) && $this->canReadName();$i++){
			if(is_object($field) && $field->compareTo($field_list[$i])){
				return $field_list[$i];
			}
			if(is_string($field) && $field_list[$i]->field() === $field){
				return $field_list[$i];
			}
		}
		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  isRemoveable($field)					//
	//--------------------------------------------------------------//
	//  input: $field - the name of the field			//
	//		not a field object				//
	//  output: boolean if field added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function isRemoveable($field){
		if(!$this->is_serialized()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_fields";
        	        $sql = "SELECT * FROM $tablename WHERE field='$field' AND removeable='1'";
			$result = $this->avalanche->mysql_query($sql);

			if($myrow = mysql_fetch_array($result) && $this->canWriteFields() && $this->canReadName()){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getField($field)						//
	//--------------------------------------------------------------//
	//  input: $field - the name of the field			//
	//		not a field object				//
	//  output: field object 					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getField($field){
		if(is_object($field)){
			$field = $field->field();
		}
		for($i=0;$i<count($this->_fields);$i++){
			if(is_string($field) && $this->_fields[$i]->field() == $field){
				return $this->_fields[$i];
			}
		}
		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  addValidation($field, $user_group, $val)			//
	//--------------------------------------------------------------//
	//  input: $field - the name of the validation			//
	//	   $user_group - the type of validation			//
	//		VALIDATE_USER or				//
	//		VALIDATE_USERGROUP				//
	//	   $val - the id of the user or group			//
	//  output: boolean if field added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function addValidation($field, $user_group, $val){
		$valid = 1;
		if($user_group == VALIDATE_USER){
			$user = $val;
			$usergroup = 0;
		}else
		if($user_group == VALIDATE_USERGROUP){
			$usergroup = $val;
			$user = 0;
		}else{
			return false;
		}

		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
		$sql = "ALTER TABLE `$tablename` ADD `$field` TINYINT DEFAULT '0' NOT NULL";
		if(!$this->is_serialized() && $this->canWriteValidations() && $this->canReadName()){
			$result = $this->avalanche->mysql_query($sql);
		}else{
			return false;
		}

		$sql = "UPDATE `$tablename` SET `$field` = '1'";
		if($this->canWriteValidations() && $this->canReadName()){
			$result = $this->avalanche->mysql_query($sql);
		}else{
			return false;
		}

		//////////////////////////////////////////////////////////////////
		// NOTE: also, make sure to add default values to all entries	//
		//////////////////////////////////////////////////////////////////

		if($this->canWriteValidations() && $this->canReadName()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_fields";
	                $sql = "INSERT INTO `$tablename` (`id`, `field`, `valid`, `user`, `usergroup`) VALUES ('', '$field', '$valid', '$user', '$usergroup')";
			$this->avalanche->mysql_query($sql);
			$this->loadValidationList();
			return true;
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  dropValidaion($field)					//
	//--------------------------------------------------------------//
	//  input: $field - the name of the validaion			//
	//  output: boolean if validation removed successfully		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function dropValidation($field){
		if(!$this->is_serialized() && $this->canReadName() && $this->canWriteValidations()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
			$sql = "ALTER TABLE $tablename DROP `$field`";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_fields";
	        	        $sql = "DELETE FROM $tablename WHERE field='$field'";
				$this->avalanche->mysql_query($sql);
				$this->loadValidationList();
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	// adds an event and ignores notifying users who request notification on added events
	// uses values from calendar if no data_list is provided
	function addEventSilently($data_list=false){
		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
		$strongcal = $this->avalanche->getModule("strongcal");
		if($this->is_serialized()){
			throw new Exception("cannot add events to serialized calendar");
		}

		$fields = $this->fields();
		if($this->avalanche->loggedInHuh()){
			$user = $this->avalanche->loggedInHuh();
		}else{
			$user = $this->avalanche->getVar("USER");
		}

		if(is_array($data_list)){
			$count = count($data_list);
			$commaHuh = false;
			for($i=0;$i<$count;$i++){
				$field = $data_list[$i]["field"];
				if($this->hasFieldHuh($field, false)){
					$field = $data_list[$i]["field"];
					$value = $data_list[$i]["value"];
					if($field->field() == "start_date"){
						$start_date_index = $i;
						$start_date = $value;
					}
					if($field->field() == "end_date"){
						$end_date_index = $i;
						$end_date = $value;
					}
					if($field->field() == "start_time"){
						$start_time_index = $i;
						$start_time = $value;
					}
					if($field->field() == "end_time"){
						$end_time_index = $i;
						$end_time = $value;
					}
				}
			}

			// the user is sending in display times for the event (ie, times in user timezone)
			// so we need to convert to GMT
			$sd = $data_list[$start_date_index]["value"];
			$st = $data_list[$start_time_index]["value"];
			$s = $strongcal->adjust_back($sd, $st);
			$data_list[$start_date_index]["value"] = $s["date"];
			$data_list[$start_time_index]["value"] = $s["time"];

			$ed = $data_list[$end_date_index]["value"];
			$et = $data_list[$end_time_index]["value"];
			$e = $strongcal->adjust_back($ed, $et);
			$data_list[$end_date_index]["value"] = $e["date"];
			$data_list[$end_time_index]["value"] = $e["time"];
		}else if($data_list === false){
			// noop, just checking types
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of field/value array pairs");
		}

		$vars = "";
		$values = "";
		$commaHuh = false;
		$count = is_array($data_list)?count($data_list):count($fields);
		for($i=0;$i<$count;$i++){
			if(is_array($data_list)){
				$field = $data_list[$i]["field"];
				$value = $data_list[$i]["value"];
			}else{
				$field = $fields[$i];
				$value = $fields[$i]->value();
			}
			$value = addslashes($value);
			// we don't really need strict here.
			if($this->hasFieldHuh($field, false)){
				if(strlen($commaHuh)){
					$vars .= ", ";
				}

				if(strlen($commaHuh)){
					$values .= ", ";
				}

				$vars .= "`" . $field->field() . "`";
				$values .= "'$value'";
			}else
			if(is_string($field) && $field === "recur_id"){
				//could be the recur_id "field," even though it isn't technically a field
				if($commaHuh){
					$vars .= ", ";
				}
				if($commaHuh){
					$values .= ", ";
				}
				$vars .= "`$field`";
				$values .= "'$value'";
			}else{
				// do nothing, we'll just try to add as much as we can.
			}
			$commaHuh = true;
		}

		if($this->avalanche->loggedInHuh()){
			$user = $this->avalanche->loggedInHuh();
		}else{
			$user = $this->avalanche->getVar("USER");
		}

		if($commaHuh){
			$vars .= ", ";
			$values .= ", ";
		}
		$vars .= "`author`";
		$values .= "'$user'";

		$vars .= ", ";
		$values .= ", ";
		$vars .= "`added_on`";
		$values .= "'" . date("Y-m-d H:i:s", $strongcal->gmttimestamp()) . "'";

		$sql = "INSERT INTO `$tablename` ($vars) VALUES ($values)";
		if($this->canWriteEntries() && $this->canReadName()){
			$result = $this->avalanche->mysql_query($sql);
			$new_id = mysql_insert_id();
			$new_event = $this->getEvent($new_id);
			// add the current user as an attendee for the event, if logged in
			if($this->avalanche->loggedInHuh()){
				$attendee = $new_event->addAttendee($this->avalanche->loggedInHuh());
				$attendee->confirm(true);
			}
			$this->_hash_events->put($new_event->getId(), $new_event);
			$this->_events[] = $new_event;
			$this->indexEventByDate($new_event);
		}else{
			throw new Exception("you are not allowed to add events");
		}

		return $new_event;
	}



	//////////////////////////////////////////////////////////////////
	//  addEvent($data_list)					//
	//--------------------------------------------------------------//
	//  input: $data_list - the fields and values for the event	//
	//	   a multidemensional array, the first column is a list //
	//	   of fields, the second is the associated values.	//
	//  output: boolean if event added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function addEvent($data_list=false){
		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
		$strongcal = $this->avalanche->getModule("strongcal");
		if($this->is_serialized()){
			return false;
		}

		$fields = $this->fields();

		$new_event = $this->addEventSilently($data_list);
		if(is_object($new_event)){
			$this->avalanche->getModule("strongcal")->eventAdded($this->getId(), $new_event->getId());
			return $new_event;
		}else{
			throw new Exception("error adding event");
		}
	}


	//////////////////////////////////////////////////////////////////
	//  editSeries($data_list, $recur_obj)				//
	//--------------------------------------------------------------//
	//  input: $data_list - the fields and values for the event	//
	//	   a multidemensional array, the first column is a list //
	//	   of fields, the second is the associated values.	//
	//	   $recur_obj - the recurring object defining the	//
	//	   series						//
	//  output: boolean if event added successfully			//
	//////////////////////////////////////////////////////////////////
	// send in display values! for times!				//
	//////////////////////////////////////////////////////////////////
	function editSeries($data_list, $recur_obj){
		$strongcal = $this->avalanche->getModule("strongcal");
		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
		$count = count($data_list);
		$commaHuh = false;

		$recur_cal = $recur_obj->calendar();
		$loe = $this->getEventsIn($recur_obj);
		for($i=0;$i<count($loe);$i++){
			for($j=0;$j<count($data_list);$j++){
				if($data_list[$j]["field"] == "all_day"){
					if($data_list[$j]["value"]){
						$loe[$i]->setAllDay(true);
					}else{
				$loe[$i]->setAllDay(false);
					}
				}else if($data_list[$j]["field"]->field() == "start_time"){
					$sd = $loe[$i]->getDisplayValue("start_date");
					$st = $data_list[$j]["value"];
					$loe[$i]->setValue("start_date", $sd);
					$loe[$i]->setValue("start_time", $st);
				}else if($data_list[$j]["field"]->field() == "end_time"){
					$ed = $loe[$i]->getDisplayValue("end_date");
					$et = $data_list[$j]["value"];
					$loe[$i]->setValue("end_date", $ed);
					$loe[$i]->setValue("end_time", $et);
				}else if($data_list[$j]["field"]->field() != "start_date" &&
					 $data_list[$j]["field"]->field() != "end_date"){
						 $loe[$i]->setValue($data_list[$j]["field"], $data_list[$j]["value"]);
				}
			}
			if(!$loe[$i]->isAllDay()){
				$loe[$i]->setTimeZone($strongcal->timezone());
			}
		}
		foreach($loe as $e){
			$this->avalanche->getModule("strongcal")->eventEdited($this->getId(), $e->getId());
		}
		return true;

		if(!$this->is_serialized() && $recur_cal->getId() == $this->getId()){
        	        $sql = "SELECT author, start_date, start_time, end_date, end_time FROM $tablename WHERE `recur_id` = '" . $recur_obj->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			if($my_row = mysql_fetch_array($result)){
				$is_author = $this->avalanche->loggedInHuh() == $my_row['author'];
				$start_time_index = -1;
				$end_time_index = -1;
				for($i=0;$i<$count;$i++){
					$field = $data_list[$i]["field"];
					$value = $data_list[$i]["value"];

					$value = addslashes($value);

					// we don't really need strict here.
					if($this->hasFieldHuh($field, false) &&
					   $field->field() != "start_time" &&
					   $field->field() != "end_time" &&
					   $field->field() != "start_date" &&
					   $field->field() != "end_date"){
						if($commaHuh){
							$values .= ", ";
						}

						$values .= "`" . $field->field() . "`='$value'";
						$commaHuh = true;
					}else if($field->field() == "start_time"){
						$start_time_index = $i;
					}else if($field->field() == "end_time"){
						$end_time_index = $i;
					}else{
						// do nothing, we'll just try to edit as much as we can.
					}
				}


				if($this->avalanche->loggedInHuh()){
					$user = $this->avalanche->loggedInHuh();
				}else{
				$user = $this->avalanche->getVar("USER");
				}


		                $sql = "UPDATE $tablename SET $values WHERE `recur_id` = '" . $recur_obj->getId() . "'";


				if($this->canWriteEntries() && $this->canReadName() && $is_author ||
				   $this->canWriteEntries() && $this->canWriteName()){
					$result = $this->avalanche->mysql_query($sql);



					// we didn't update the times for each event yet, so lets do that now.
					$sql = "SELECT `id`, `start_time`, `start_date`, `end_time`, `end_date` FROM $tablename WHERE `recur_id` = '" . $recur_obj->getId() . "'";
					$result = $this->avalanche->mysql_query($sql);
					while($myrow = mysql_fetch_array($result)){
						$id = $myrow["id"];
						$sd = $myrow["start_date"];
						$st = $myrow["start_time"];
						$ed = $myrow["end_date"];
						$et = $myrow["end_time"];
						$s = $strongcal->adjust($sd, $st);
						$sd = $s["date"];
						$st = $s["time"];
						$e = $strongcal->adjust($ed, $et);
						$ed = $e["date"];
						$et = $e["time"];

						$s = $strongcal->adjust_back($sd, $data_list[$start_time_index]["value"]);
						$sd = $s["date"];
						$st = $s["time"];
						$e = $strongcal->adjust_back($ed, $data_list[$end_time_index]["value"]);
						$ed = $e["date"];
						$et = $e["time"];

						$sql = "UPDATE $tablename SET `start_time`='$st', `start_date`='$sd', `end_time`='$et', `end_date`='$ed' WHERE `id`='$id'";
						$result2 = $this->avalanche->mysql_query($sql);
					}
				}else{
					$result = false;
				}

				//get id of new event to return

				if($result){
					foreach($loe as $e){
						$this->avalanche->getModule("strongcal")->eventEdited($this->getId(), $e->getId());
					}
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}



	//////////////////////////////////////////////////////////////////
	//  removeEvent($event_id)					//
	//--------------------------------------------------------------//
	//  input: $event_id - the event's id				//
	//  output: boolean if field added successfully			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function removeEvent($event_id){
		if(!$this->is_serialized() && $this->canWriteEntries() && $this->canReadName()){

			$event = $this->getEvent($event_id);
			if(is_object($event)){
				// notify listeners that the event is being deleted
				$this->avalanche->getModule("strongcal")->eventDeleted($this->getId(), $event->getId());

				$recur = $event->stealRecurrance();

				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId() . "_comments";
				$sql = "DELETE FROM $tablename WHERE event_id='" . $event_id . "'";
				$result = $this->avalanche->mysql_query($sql);

				$tablename = $this->avalanche->PREFIX() . "strongcal_attendees";
				$sql = "DELETE FROM $tablename WHERE cal_id='" . $this->getId() . "' AND event_id='" . $event->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);

				$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $this->getId();
				$sql = "DELETE FROM $tablename WHERE id='" . $event_id . "'";
				$result = $this->avalanche->mysql_query($sql);
			}else{
				return false;
			}
		}else{
			$result = false;
		}
		if($result && mysql_affected_rows()){
			for($i=0;$i<count($this->_events);$i++){
				if($this->_events[$i]->getId() == $event_id){
					$this->removeFromDateIndex($this->_events[$i]);
					array_splice($this->_events, $i, 1);
					$i = count($this->_events);
				}
			}
			return true;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getNewRecurrancePattern()					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: 							//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getNewRecurrancePattern(){
		if(!$this->is_serialized() && $this->canWriteEntries()){
			$temp = new module_strongcal_recurrance($this->avalanche);
			$temp->initFor($this);
		}
		return $temp;
	}


	// a cache for usergroup permissions on this calendar
	// key is the group id
	// value is the array from sql
	private $permission_cache;

	//////////////////////////////////////////////////////////////////
	//  updatePermission($permission, $value, $group)		//
	//--------------------------------------------------------------//
	//								//
	//////////////////////////////////////////////////////////////////
	function updatePermission($permission, $value, $group){
		if(!is_int($group)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " should be the int id of a usergroup");
		}
		if($this->is_serialized()){
			return false;
		}
		if(($this->canWriteName()) &&
		   ($permission == "name" ||
		    $permission == "comments" ||
		    $permission == "entry" ||
		    $permission == "validation" ||
		    $permission == "field") &&
		   ($value == "r" ||
		    $value == "rw" ||
		    $value == "")){

			$this->permission_cache->clear((int)$group);

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE usergroup='$group'";
			$result= $this->avalanche->mysql_query($sql);
			$cal = "cal_" . $this->getId() . "_" . $permission;
			if(mysql_fetch_array($result)){
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_permissions SET $cal = '$value' WHERE usergroup='$group'";
				$result= $this->avalanche->mysql_query($sql);
			}else{
				$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_permissions (`usergroup`,`$cal`) VALUES ('$group','$value')";
				$result = $this->avalanche->mysql_query($sql);
			}
			switch($permission){
				case "name":
					if(isset($this->_serial_can["canReadName"])) unset($this->_serial_can["canReadName"]);
					if(isset($this->_serial_can["canWriteName"])) unset($this->_serial_can["canWriteName"]);
				break;
				case "comments":
					if(isset($this->_serial_can["canReadComments"])) unset($this->_serial_can["canReadComments"]);
					if(isset($this->_serial_can["canWriteComments"])) unset($this->_serial_can["canWriteComments"]);
				break;
				case "entry":
					if(isset($this->_serial_can["canReadEntries"])) unset($this->_serial_can["canReadEntries"]);
					if(isset($this->_serial_can["canWriteEntries"])) unset($this->_serial_can["canWriteEntries"]);
				break;
				case "validation":
					if(isset($this->_serial_can["canReadValidations"])) unset($this->_serial_can["canReadValidations"]);
					if(isset($this->_serial_can["canWriteValidations"])) unset($this->_serial_can["canWriteValidations"]);
				break;
				case "field":
					if(isset($this->_serial_can["canReadFields"])) unset($this->_serial_can["canReadFields"]);
					if(isset($this->_serial_can["canWriteFields"])) unset($this->_serial_can["canWriteFields"]);
				break;
			}
			if($result){
				return true;
			}
		   }else if(!($permission == "name" ||
		    $permission == "comments" ||
		    $permission == "entry" ||
		    $permission == "validation" ||
		    $permission == "field")){
				throw new Exception("permission $permission does not exist. must use \"name\", \"comments\", \"entry\", \"validation\", or \"field\"");
		   }

		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  canReadEntries()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read entries	//
	//	from this calendar					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	private $count = 0;
	function canReadEntries($usergroups = false){
		if(!$this->canReadName($usergroups)){
			return false;
		}
		if($this->canWriteName($usergroups)){
			return true;
		}

		$this->count++;
		$timer = new Timer();
		$timer->start();
		if(isset($this->_serial_can["canReadEntries"]) && !is_array($usergroups)){
			return $this->_serial_can["canReadEntries"];
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}


		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}


		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_entry";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		$timer->stop();

		if(!is_array($usergroups)){
			$this->_serial_can["canReadEntries"] = $can_read;
		}
		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  canReadEvent($event_id, $user_id=false)			//
	//--------------------------------------------------------------//
	// checks if a user can read the specified event in the		//
	// calendar							//
	//////////////////////////////////////////////////////////////////
	function canReadEvent($event_id, $user_id=false){
		if($this->canReadEntries()){
			return true;
		}
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if($this->isAttendee($user_id, $event_id)){
			return true;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteEvent($event_id, $user_id=false)			//
	//--------------------------------------------------------------//
	// checks if a user can read the specified event in the		//
	// calendar							//
	//////////////////////////////////////////////////////////////////
	function canWriteEvent($event_id, $user_id=false){
		$event = $this->getEvent($event_id);
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}
		if($this->canWriteEntries() || $event->author() == $user_id){
			return true;
		}
		return false;
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteEntries()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read entries	//
	//	from this calendar					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canWriteEntries($usergroups = false){
		if(isset($this->_serial_can["canWriteEntries"]) && !is_array($usergroups)){
			return $this->_serial_can["canWriteEntries"];
		}

		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}


		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_entry";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canWriteEntries"] = $can_read;
		}
		return $can_read;
	}




	//////////////////////////////////////////////////////////////////
	//  canReadFields()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read fields	//
	//	from this calendar. should be used in the gui to tell	//
	//	if the user can even see the field modify page.		//
	//	or what fields/field types are on the calendar.		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canReadFields($usergroups = false){
		if(isset($this->_serial_can["canReadFields"]) && !is_array($usergroups)){
			return $this->_serial_can["canReadFields"];
		}

		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_field";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canReadFields"] = $can_read;
		}
		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteFields()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read fields	//
	//	from this calendar					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canWriteFields($usergroups = false){
		if(isset($this->_serial_can["canWriteFields"]) && !is_array($usergroups)){
			return $this->_serial_can["canWriteFields"];
		}

		$this->from_fields = true;
		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}


		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_field";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canWriteFields"] = $can_read;
		}
		return $can_read;
	}


	//////////////////////////////////////////////////////////////////
	//  canReadValidations()					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read validation	//
	//	fields from this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canReadValidations($usergroups = false){
		if(isset($this->_serial_can["canReadValidations"]) && !is_array($usergroups)){
			return $this->_serial_can["canReadValidations"];
		}

		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_validation";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "r" || $myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canReadValidations"] = $can_read;
		}
		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteValidations()					//
	//--------------------------------------------------------------//
	//  input: $usergroups (optional) array of usergroups to check	//
	//  output: boolean if the user is allowed to read validation	//
	//	fields from this calendar				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canWriteValidations($usergroups = false){
		if(isset($this->_serial_can["canWriteValidations"]) && !is_array($usergroups)){
			return $this->_serial_can["canWriteValidations"];
		}


		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
				if(strlen($final)>0){
					$final .= " OR ";
				}
				$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
			}
		}

		$can_read = false;
		$field = "cal_" . $this->getId() . "_validation";

		foreach($usergroups as $u){
			$myrow = $this->permission_cache->get($u->getId());
			if(is_array($myrow)){
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}
		if(strlen($final) > 0){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
				if($myrow[$field] == "rw"){
					$can_read = true;
				}
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canWriteValidations"] = $can_read;
		}
		return $can_read;
	}


	function canReadName($usergroups = false){
		if($this->canReadNameStrict($usergroups)){
			return true;
		}

		if(!is_array($usergroups) && $this->isAttendee($this->avalanche->getActiveUser())){
			return true;
		}
		return false;
	}
	//////////////////////////////////////////////////////////////////
	//  canReadName()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read the		//
	//	calendar name. should be used as if this user is	//
	//	allowed to know the calendar even exists		//
	//	canReadName() should refer to if the user knows the	//
	//	calendar exists while canWriteName() refers to if the	//
	//	user can edit the name of the calendar.			//
	//	from this calendar					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	private function canReadNameStrict($usergroups = false){
		if(isset($this->_serial_can["canReadName"]) && !is_array($usergroups)){
			return $this->_serial_can["canReadName"];
		}


		// this is the line that can cause the crash.
		// if($this->getId() == 45 && isset($this->from_fields)) {echo $this->avalanche->loggedInHuh(false,true);flush();}
		// if($this->getId() == 45 && isset($this->from_fields)) {echo "<-logged";flush();}


		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if($this->isPublic()){


			if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
				$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
			}
			$final = "";
			if(is_array($usergroups)){
				for($i=0;$i<count($usergroups);$i++){
					if(strlen($final)>0){
						$final .= " OR ";
					}
					$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
				}
			}

			$can_read = false;
			$field = "cal_" . $this->getId() . "_name";

			foreach($usergroups as $u){
				$myrow = $this->permission_cache->get($u->getId());
				if(is_array($myrow)){
					if($myrow[$field] == "r" || $myrow[$field] == "rw"){
						$can_read = true;
					}
				}
			}
			if(strlen($final) > 0){
				$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
				$result = $this->avalanche->mysql_query($sql);

				while($myrow = mysql_fetch_array($result)){
					$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
					if($myrow[$field] == "r" || $myrow[$field] == "rw"){
						$can_read = true;
					}
				}
			}
			if(!is_array($usergroups)){
				$this->_serial_can["canReadName"] = $can_read;
			}
			return $can_read;
		}else{
			if(!is_array($usergroups)){
				$this->_serial_can["canReadName"] = false;
			}
			return false;
		}
	}

	// returns true if the user is an attendee to any event in this calendar
	// (if so, then he's granted temporary readname access on this calendar
	// this result is cached until reload is called on the calendar
	private $_is_attendee;
	// initialize cache to an int.
	private function isAttendee($user_id=false, $event_id=false){
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if($event_id === false && $event_id === false){
			if(!is_array($this->_is_attendee->get($user_id))){
				$this->loadAttendee($user_id);
			}
			return (bool)count($this->_is_attendee->get($user_id));
		}else if(is_int($event_id)){
			if(!is_array($this->_is_attendee->get($user_id))){
				$this->loadAttendee($user_id);
			}
			return in_array($event_id, $this->_is_attendee->get($user_id));
		}else{
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an int");
		}
	}

	private function loadAttendee($user_id){
		$table = $this->avalanche->PREFIX() . "strongcal_attendees";
		$sql = "SELECT * FROM $table WHERE cal_id='" . $this->getId() . "' AND user_id='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysql_fetch_array($result)){
			$ret[] = (int)$myrow["event_id"];
		}
		$this->_is_attendee->put($user_id, $ret);
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteName()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to edit the		//
	//	calendar name. 						//
	//	canReadName() should refer to if the user knows the	//
	//	calendar exists while canWriteName() refers to if the	//
	//	user can edit the name of the calendar. (among other	//
	//	admin funcitons) from this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canWriteName($usergroups = false){
		if(isset($this->_serial_can["canWriteName"]) && !is_array($usergroups)){
			return $this->_serial_can["canWriteName"];
		}

		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if($this->isPublic()){

			if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
				$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
			}
			$final = "";
			for($i=0;$i<count($usergroups);$i++){
				if(!is_array($this->permission_cache->get($usergroups[$i]->getId()))){
					if(strlen($final)>0){
						$final .= " OR ";
					}
					$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
				}
			}

			$can_read = false;
			$field = "cal_" . $this->getId() . "_name";

			foreach($usergroups as $u){
				$myrow = $this->permission_cache->get($u->getId());
				if(is_array($myrow)){
					if($myrow[$field] == "rw"){
						$can_read = true;
					}
				}
			}
			if(strlen($final) > 0){
				$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
				$result = $this->avalanche->mysql_query($sql);

				while($myrow = mysql_fetch_array($result)){
					$this->permission_cache->put((int)$myrow["usergroup"], $myrow);
					if($myrow[$field] == "rw"){
						$can_read = true;
					}
				}
			}

			$can_read = ($can_read && $this->isPublic());
			if(!is_array($usergroups)){
				$this->_serial_can["canWriteName"] = $can_read;
			}
			return $can_read;
		}else{
			if(!is_array($usergroups)){
				$this->_serial_can["canWriteName"] = false;
			}
			return false;
		}
	}




	//////////////////////////////////////////////////////////////////
	//  canReadComments()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to read the		//
	//	calendar's event's comments.				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canReadComments($usergroups = false){
		if(isset($this->_serial_can["canReadComments"]) && !is_array($usergroups)){
			return $this->_serial_can["canReadComments"];
		}
		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}

		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(strlen($final)>0){
				$final .= " OR ";
			}
			$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
		}


		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
		$result = $this->avalanche->mysql_query($sql);


		$can_read = false;
		$field = "cal_" . $this->getId() . "_comments";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] == "r" || $myrow[$field] == "rw"){
				$can_read = true;
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canReadComments"] = $can_read;
		}
		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  canWriteComments()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean if the user is allowed to add/edit their	//
	//	comments for a calendar event.				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function canWriteComments($usergroups = false){
		if(isset($this->_serial_can["canWriteComments"]) && !is_array($usergroups)){
			return $this->_serial_can["canWriteComments"];
		}

		if($this->canWriteName($usergroups)){
			return true;
		}
		if(!$this->canReadName($usergroups)){
			return false;
		}

		if($this->author() == $this->avalanche->loggedInHuh() && (!$usergroups || is_array($usergroups) && !count($usergroups))){
			return true;
		}


		if(!is_array($usergroups) || count($usergroups) == 0 || !$this->avalanche->loggedInHuh()){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		$final = "";
		for($i=0;$i<count($usergroups);$i++){
			if(strlen($final)>0){
				$final .= " OR ";
			}
			$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
		$result = $this->avalanche->mysql_query($sql);


		$can_read = false;
		$field = "cal_" . $this->getId() . "_comments";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] == "rw"){
				$can_read = true;
			}
		}

		if(!is_array($usergroups)){
			$this->_serial_can["canWriteComments"] = $can_read;
		}
		return $can_read;
	}


	//standard visitor pattern
	function execute(module_strongcal_visitor $visitor){
		return $visitor->calendarCase($this);
	}

}


?>