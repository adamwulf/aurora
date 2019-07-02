<?
//////////////////////////////////////////////////////////////////////////
//									//
//  submodule.strongcal.event.php					//
//----------------------------------------------------------------------//
//  defines an event in a calendar					//
//									//
//									//
//  NOTE: filename must be of format module.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  submodule.strongcal.event.php					//
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
///////////////   STRONGCAL SUBMODULE   //////////////////////////
///////////////         event           //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this class defines an event. it knows its home calendar and	//
// id, as well as the recurrance pattern to which it belongs,	//
// if any. when the patter is update, it will update mysql with	//
// the appropriate new events. after this update, all event	//
// objects in the series should be re-instantiated.		//
//////////////////////////////////////////////////////////////////

//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_strongcal_event{

	// the calendar to which this is event a part
	private $_calendar;

	// this events id
	private $_id;

	// 1 if this event is all day
	private $_all_day;

	// this events recurrance pattern
	private $_recurrance;

	// the old recurrance, used when updating the rucurrance pattern
	private $_old_recur;

	// a flag if the recurring object is loaded
	private $_recurrance_is_loaded;

	// the id of the recurrance pattern for this event. used for loading recurrance obeject.
	private $_recurrance_id;

	// true if the comments have been loaded
	private $_comments_loaded;

	// an array of all comments
	private $_comments;

	// an hash of all the attendees
	private $_attendees;

	// true if the hash array contains all the attendees
	private $_attendees_loaded;

	// the author of this event
	private $_author;

	// the date this event was added
	private $_added_on;

	// the number of comments for this event
	// so it's > 0 if there are comments
	// so if($this->hasComments()) will work...
	private $_has_comments;

	// random data to store

	function sleep(){
		$this->comments(true);
		$this->_author = $this->avalanche->getUsername($this->_author);
		$this->_calendar->sleep();
	}


	//////////////////////////////////////////////////////////////////
	//  calendar()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the calendar for which this event is a part		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function calendar(){
		return $this->_calendar;
	}


	//////////////////////////////////////////////////////////////////
	//  tag()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the calendar for which this event is a part		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function tag($data = false){
		if($data){
			$this->_tag = $data;
		}
		return $this->_tag;
	}

	//////////////////////////////////////////////////////////////////
	//  author()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the author of this event				//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function author(){
		return (int) $this->_author;
	}


	//////////////////////////////////////////////////////////////////
	//  added_on()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the date this event was added			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function added_on(){
		return $this->_added_on;
	}


	//////////////////////////////////////////////////////////////////
	//  getId()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this event in it's respective calendar	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getId(){
		return (int)$this->_id;
	}


	//////////////////////////////////////////////////////////////////
	//  returnRecurrance($recur)					//
	//--------------------------------------------------------------//
	//  input: the recurrence patter to set this event to		//
	//  output: none						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//	all old event information is lost and the new series	//
	//	of events is created.					//
	//////////////////////////////////////////////////////////////////
	function returnRecurrance($recur){
		if(is_object($recur) || $recur === false){

			if(is_object($this->stealRecurrance())){
				// save the old recurrance, so we know what to delete
				$this->_old_recur = $this->stealRecurrance();
			}
			if(is_object($recur)){
				//update the recurrance series...
				$recur->update();

				//tell the calendar to update it's list too
				$this->_calendar->putRecur($recur);
				$this->_recurrance_id = $recur->getId();
				/* update and make sure my recur id in the table is legit
				 */
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_calendar->getId() . " SET recur_id = '" . $recur->getId() . "' WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}else{
				/* update and make sure my recur id in the table is legit
				 */
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_calendar->getId() . " SET recur_id = '0' WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
				$this->_recurrance_id = false;
			}


			// update our private variable store with the new recurrance
			$this->_recurrance_is_loaded = true;
			$this->_recurrance = $recur;



			// update the events in the calendar to reflect the new series.
			// all old information will be lost
			return $this->update();
		}else{
			return $this;
		}
	}


	function isAllDay(){
		return (bool)$this->_all_day;
	}

	function setAllDay($b){
		if(!is_bool($b)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
		// check if we are allowed to edit this event
		// and check if we even need to
		if($this->calendar()->canWriteEvent($this->getId(), $this->avalanche->getActiveUser()) && $b != $this->isAllDay()){
			$strongcal = $this->avalanche->getModule("strongcal");
			$val = $b ? 1 : 0;
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->calendar()->getId() . " SET all_day='$val' WHERE id='" . $this->getId() . "';";
			$result = $this->avalanche->mysql_query($sql);
			if(mysql_affected_rows()){
				$this->calendar()->removeFromDateIndex($this);
				$this->_all_day = $val;
				if($b){
					// also update so the event is all day on the correct day
					// the gmt time is currently in DB, we're going to overwrite it
					// in localtime so that the all day is the same day of the month
					// for everyone regardless of timezone
					$strongcal = $this->avalanche->getModule("strongcal");
					$sd = $this->getValue("start_date");
					$st = $this->getValue("start_time");
					$s = $strongcal->adjust($sd, $st, $strongcal->timezone());
					$this->setValue("start_date", $s["date"]);
					$this->setValue("start_time", $s["time"]);
					$ed = $this->getValue("end_date");
					$et = $this->getValue("end_time");
					$e = $strongcal->adjust($ed, $et, $strongcal->timezone());
					$this->setValue("end_date", $e["date"]);
					$this->setValue("end_time", $e["time"]);
				}else{
					// also update so the event is all day on the correct day
					// the local time is currently in DB (b/c we changed it
					// when we made it all day), so we're going to overwrite it
					// in gmt so that the event is at the 'correct' time again
					// depending on timezone
					$strongcal = $this->avalanche->getModule("strongcal");
					$sd = $this->getValue("start_date");
					$st = $this->getValue("start_time");
					$s = $strongcal->adjust_back($sd, $st, $strongcal->timezone());
					$this->setValue("start_date", $s["date"]);
					$this->setValue("start_time", $s["time"]);
					$ed = $this->getValue("end_date");
					$et = $this->getValue("end_time");
					$e = $strongcal->adjust_back($ed, $et, $strongcal->timezone());
					$this->setValue("end_date", $e["date"]);
					$this->setValue("end_time", $e["time"]);
				}
				$this->calendar()->indexEventByDate($this);
			}
		}
		return $this->isAllDay();
	}

	//////////////////////////////////////////////////////////////////
	//  stealRecurrance()						//
	//	take a copy of the recurrance pattern.			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the recurrance pattern this event is set to		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function stealRecurrance(){
		if(!$this->_recurrance_is_loaded && $this->_recurrance_id){
			$this->_recurrance = $this->_calendar->getRecur($this->_recurrance_id);
			$this->_recurrance_is_loaded = true;
		}
		return $this->_recurrance;
	}


	//////////////////////////////////////////////////////////////////
	//  values()							//
	//	the array of values for this event			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the array of fields and values for this event	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function values(){
		return $this->_vals;
	}

	//////////////////////////////////////////////////////////////////
	//  display_values()						//
	//	the array of values for this event			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the array of fields and values for this event	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function display_values(){
		return $this->_display_vals;
	}

	//////////////////////////////////////////////////////////////////
	//  fields()							//
	//	the array of fields for this event			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the fields available in this event			//
	//          all may not be used					//
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
			if(is_string($field) && $this->_fields[$i]->field() === $field){
				return $this->_fields[$i];
			}
		}
		return false;
	}

	//////////////////////////////////////////////////////////////////
	//  duration()							//
	//	the duration of this event in php time format		//
	//	(in seconds)						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the duration of this event in seconds		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function duration(){
		$start_time = $this->getValue("start_time");
		$start_date = $this->getValue("start_date");
		$end_time = $this->getValue("end_time");
		$end_date = $this->getValue("end_date");
		$time1 = strtotime("$start_date $start_time");
		$time2 = strtotime("$end_date $end_time");
		return $time2 - $time1;
	}


	//////////////////////////////////////////////////////////////////
	//  setValue($field, $val)					//
	//	deletes old information associated with this field, and	//
	//	adds the new value associated with it. 			//
	//--------------------------------------------------------------//
	//  input: $field - a field object				//
	//	   $val - the value for this field			//
	//	   $skip_mysql - set to true to not update mysql	//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setValue($field_name, $val){
		if(is_object($field_name)){
			$field_name = $field_name->field();
		}

		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$field = $this->getField($field_name);

		$field = $this->getField($field_name);
		$okHuh = $field->set_value($val);


		return $okHuh;
	}

	//////////////////////////////////////////////////////////////////
	//  hasFieldHuh($field)						//
	//	returns true if the field is in the calendar, false	//
	//	otherwise.
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
		return $this->_calendar->hasFieldHuh($field);
	}


	//////////////////////////////////////////////////////////////////
	//  removeField($field)						//
	//	removes the field in this event. does not delete the	//
	//	field from the calendar. only from this event object.	//
	//--------------------------------------------------------------//
	//  input: $field - a field object				//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function removeField($field){
		// this function may need overhaul.
		// there may be an issue with object referencing only
		// a copy of the object, so the copy isn't in the array
		// even though the real thing is...

	}

	//////////////////////////////////////////////////////////////////
	//  getValue($field)						//
	//	returns the value associated with this field object	//
	//	or string.
	//--------------------------------------------------------------//
	//  input: $field - a field object or string			//
	//  output: mixed						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getValue($field){
		if(is_object($field)){
			$fieldname = $field->field();
		}else
		if(is_string($field)){
			$fieldname = $field;
		}else{
			trigger_error("\$field must be either a field object or string in getValue(\$field)", E_USER_ERROR);
		}
		$field_obj = $this->getField($fieldname);

		if($this->_calendar->canReadEvent($this->getId(), $this->avalanche->getActiveUser())){
			if(is_object($field_obj)){
				return $field_obj->value();
			}else{
				throw new Exception("field $fieldname does not exist");
			}
		}else{
			if($fieldname == "title"){
				return "busy";
			}else
			if($fieldname == "start_date"){
				return $field_obj->value();
			}else
			if($fieldname == "end_date"){
				return $field_obj->value();
			}else
			if($fieldname == "start_time"){
				return $field_obj->value();
			}else
			if($fieldname == "end_time"){
				return $field_obj->value();
			}else{
				return "";
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getDisplayValue($field)					//
	//	returns the value associated with this field object	//
	//	or string.
	//--------------------------------------------------------------//
	//  input: $field - a field object or string			//
	//  output: mixed						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getDisplayValue($field){
		if(is_object($field)){
			$fieldname = $field->field();
		}else
		if(is_string($field)){
			$fieldname = $field;
		}else{
			trigger_error("\$field must be either a field object or string in getValue(\$field)", E_USER_ERROR);
		}
		$field_obj = $this->getField($fieldname);

		if($this->_calendar->canReadEvent($this->getId(), $this->avalanche->getActiveUser())){
			if(is_object($field_obj)){
				return $field_obj->display_value();
			}else{
				throw new Exception("field $fieldname does not exist");
			}
		}else{
			if($fieldname == "title"){
				return "busy";
			}else
			if($fieldname == "start_date"){
				return $field_obj->display_value();
			}else
			if($fieldname == "end_date"){
				return $field_obj->display_value();
			}else
			if($fieldname == "start_time"){
				return $field_obj->display_value();
			}else
			if($fieldname == "end_time"){
				return $field_obj->display_value();
			}else{
				return "";
			}
		}
	}


	//////////////////////////////////////////////////////////////////
	//  __construct($cal, $id)					//
	//	itinialize this event and its associated recurrance	//
	//	pattern.						//
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
	private $avalanche;
	function __construct($avalanche, $cal, $id){
		$this->avalanche = $avalanche;
		$strongcal = $this->avalanche->getModule("strongcal");
		$temp_fields = $cal->fields();
		$this->_attendees_loaded = false;
		$this->_attendees = new HashTable();
		if(is_numeric($id)){
			$this->_calendar = $cal;
			$this->_id = $id;
			$this->loadFieldList();
			$this->_recurrance_id = false;
			$this->_comments_loaded = false;

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . " WHERE id = '$id'";
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysqli_fetch_array($result)){
				$field = "recur_id";
				$temp_recur_id = $myrow[$field];
				$this->_recurrance_is_loaded = false;
				$this->_recurrance_id = $temp_recur_id;

				$field = "author";
				$temp_author = $myrow[$field];
				$this->_author = $temp_author;

				$field = "all_day";
				$temp_all_day = $myrow[$field];
				$this->_all_day = $temp_all_day;

				$field = "added_on";
				$temp_date = $myrow[$field];
				$this->_added_on = $temp_date;

				$field = "has_comments";
				$temp_comm = $myrow[$field];
				$this->_has_comments = $temp_comm;
			}
		}else
		if(is_array($id)){
			$myrow = $id;
			$this->_calendar = $cal;
			$this->_id = $myrow['id'];
			$this->loadFieldList($myrow);
			$this->_recurrance_id = $myrow['recur_id'];

			$field = "recur_id";
			$temp_recur_id = $myrow[$field];
			$this->_recurrance_is_loaded = false;
			$this->_recurrance_id = $temp_recur_id;

			$field = "author";
			$temp_author = $myrow[$field];
			$this->_author = $temp_author;

			$field = "all_day";
			$temp_all_day = $myrow[$field];
			$this->_all_day = $temp_all_day;

			$field = "added_on";
			$temp_date = $myrow[$field];
			$this->_added_on = $temp_date;

			$field = "has_comments";
			$temp_comm = $myrow[$field];
			$this->_has_comments = $temp_comm;
		}else{
			trigger_error("\$id must be either an integer or an array in event->init(\$cal,\$id).", E_USER_ERROR);
		}
	}

	public function attendees(){
		if($this->_attendees_loaded){
			return $this->_attendees->enum();
		}else{
			$table = $this->avalanche->PREFIX() . "strongcal_attendees";
			$sql = "SELECT * FROM $table WHERE cal_id='" . $this->calendar()->getId() . "' AND event_id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysqli_fetch_array($result)){
				$attendee = new module_strongcal_attendee($this->avalanche, $this, $myrow);
				$id = (int)$myrow["user_id"];
				if(is_object($this->_attendees->get($id))){
					$this->_attendees->put($id, $attendee);
				}else{
					$this->_attendees->put($id, $attendee);
				}
			}
			$this->_attendees_loaded = true;
			return $this->_attendees->enum();
		}
	}

	public function addAttendee($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an int");
		}
		$this->attendees();
		if(!is_object($this->_attendees->get($user_id)) && ($this->calendar()->canWriteEntries() || $this->author() == $this->avalanche->loggedInHuh())){
			$table = $this->avalanche->PREFIX() . "strongcal_attendees";
			$sql = "INSERT INTO $table (`cal_id`,`event_id`,`user_id`) VALUES ('" . $this->calendar()->getId() . "','" . $this->getId() . "','$user_id')";
			$result = $this->avalanche->mysql_query($sql);
			$id = (int) mysqli_insert_id($this->avalanche->mysqliLink());
			$attendee = new module_strongcal_attendee($this->avalanche, $this, $id);

			// load attendees
			$this->_attendees->put($user_id, $attendee);

			$this->avalanche->getModule("strongcal")->attendeeAdded($this->calendar()->getId(), $this->getId(), $user_id);
		}

		return $this->_attendees->get($user_id);
	}

	public function getAttendee($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an int");
		}
		// load attendees;
		$this->attendees();
		return $this->_attendees->get($user_id);
	}

	public function removeAttendee($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an int");
		}
		if($this->calendar()->canWriteEntries() || $this->author() == $this->avalanche->loggedInHuh()){
			$table = $this->avalanche->PREFIX() . "strongcal_attendees";
			$this->attendees();
			$this->_attendees->clear($user_id);
			$sql = "DELETE FROM $table WHERE user_id='$user_id' AND cal_id='" . $this->calendar()->getId() . "' AND event_id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->avalanche->getModule("strongcal")->attendeeDeleted($this->calendar()->getId(), $this->getId(), $user_id);
			return true;
		}else{
			return false;
		}
	}



	//////////////////////////////////////////////////////////////////
	//  reload()							//
	//  reloads the event from mysql, and recaches values		//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//////////////////////////////////////////////////////////////////
	public function reload(){
		if(is_numeric($this->getId())){
			$this->loadFieldList();
			$this->_recurrance_id = false;
			$this->_comments_loaded = false;
			$this->_attendees_loaded = false;
			$this->_attendees = new HashTable();

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->calendar()->getId() . " WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysqli_fetch_array($result)){
				$field = "recur_id";
				$temp_recur_id = $myrow[$field];
				$this->_recurrance_is_loaded = false;
				$this->_recurrance_id = $temp_recur_id;

				$field = "author";
				$temp_author = $myrow[$field];
				$this->_author = $temp_author;

				$field = "all_day";
				$temp_all_day = $myrow[$field];
				$this->_all_day = $temp_all_day;

				$field = "added_on";
				$temp_date = $myrow[$field];
				$this->_added_on = $temp_date;

				$field = "has_comments";
				$temp_comm = $myrow[$field];
				$this->_has_comments = $temp_comm;
			}
		}
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

	function loadFieldList($myrow=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$fm = $strongcal->fieldManager();
		$this->_fields = array();
		$fields = $this->calendar()->fields();
		foreach($fields as $field){
			$new_field = clone $field;
			if(is_object($new_field)){
				if(is_array($myrow) && isset($myrow[$new_field->field()])){
					$new_field->event($this, $myrow[$new_field->field()]);
				}else{
					$new_field->event($this);
				}
				$ret[] = $new_field;
			}else{
				trigger_error("Error in calendar \"" . $this->name() . "\". cannot load field type: " . $myrow["type"], E_USER_WARNING);

			}
		}
		if(count($ret) == 0){
			throw new Exception("Cannot load field list for event #" . $this->getId() . " in calendar #" . $this->calendar()->getId());
		}
		$this->_fields = $ret;
		return true;
	}


	function hasComments(){
		return $this->_has_comments;
	}

	//////////////////////////////////////////////////////////////////
	//  comments()							//
	//	returns an array with all the comments in it.		//
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
	function comments($sleep = false){
		$cal = $this->calendar();
		if($cal->canReadComments()){
			if(!$this->_comments_loaded){
				$ret = array();
				$event_id = $this->getId();
				$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_comments WHERE event_id = '$event_id' ORDER BY post_date DESC";
				$result = $this->avalanche->mysql_query($sql);
				while($myrow = mysqli_fetch_array($result)){
					$author = $myrow['author'];
					if($sleep){
						$author = $this->avalanche->getUsername($author);
					}
					$ret[] = array("id" => (int)$myrow['id'],
							"author" => (int)$author,
							"date" => $myrow['post_date'],
							"title" => $myrow['title'],
							"body" => $myrow['body'],
							"cal_id" => $cal->getId()
							);
				}
				$this->_comments = $ret;
				$this->_comments_loaded = true;
				return $this->_comments;
			}else{
				return $this->_comments;
			}
		}else{
			return array();
		}
	}

	//////////////////////////////////////////////////////////////////
	//  comment($title, $body, $com_id=false)			//
	//	adds a comment if no id is specified, or modifies a	//
	//	comment if an id is specified				//
	//--------------------------------------------------------------//
	//  input: the title and body of the comment			//
	//  output: boolean if added					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function comment($title, $body, $com_id=false){
		$event_id = $this->getId();
		if($this->avalanche->loggedInHuh()){
			$author = $this->avalanche->loggedInHuh();
		}else{
			$author = $this->avalanche->getVar("USER");
		}
		$post_date = date("Y-m-d H:i:s", $this->avalanche->getModule("strongcal")->gmttimestamp());
		$title = addslashes($title);
		$body = addslashes($body);

		$cal = $this->calendar();

		if($cal->canReadComments()){
			if($com_id){
				// load comments
				$this->comments();
				// get comment cache index
				for($i=0;$i<count($this->_comments); $i++){
					if($this->_comments[$i]['id'] == $com_id){
						$cache_index = $i;
					}
				}

				// if i'm the author of the comment and have permission, or if i'm a calendar admin
				if($this->_comments[$cache_index]["author"] == $this->avalanche->loggedInHuh() && $cal->canWriteComments() ||
				   $cal->canWriteName()){
					/*
					 * update the comment
					 *
					 */
					$ret = array();
					$event_id = $this->getId();
					$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_comments SET event_id='$event_id', title='$title', body='$body' WHERE id='$com_id'";
					$result = $this->avalanche->mysql_query($sql);
					if($result){
						if($this->_comments_loaded){
							$ret = array("id" => $com_id,
									"author" => $author,
									"date" => $post_date,
									"title" => stripslashes($title),
									"body" => stripslashes($body)
									);
							$this->_comments[$cache_index] = $ret;

						}
						return true;
					}else{
						return false;
					}
				   }else{
					   return false;
				   }
			}else{
			/*
			 * new comment
			 *
			 */
				$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_comments (`event_id`, `author`, `post_date`, `title`, `body`) VALUES ('$event_id', '$author','$post_date','$title','$body')";
				$result = $this->avalanche->mysql_query($sql);
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . " SET has_comments='" . ($this->_has_comments+1) . "' WHERE id='$event_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_has_comments += 1;
					if($this->_comments_loaded){
						$ret = array("id" => mysql_insert_id($this->avalanche->mysqliLink()),
								"author" => $author,
								"date" => $post_date,
								"title" => strip_slashes($title),
								"body" => strip_slashes($body)
								);
						$this->_comments[] = $ret;
					}
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}



	//////////////////////////////////////////////////////////////////
	//  removeComment($comment_id)					//
	//	removes the comment					//
	//--------------------------------------------------------------//
	//  input: the id of the comment				//
	//  output: boolean if removed					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function removeComment($comment_id){
		$cal = $this->calendar();

		$ret = array();
		$event_id = $this->getId();
		$sql = "DELETE FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_comments WHERE id='$comment_id'";
		$result = $this->avalanche->mysql_query($sql);
		if($result){
			if($this->_comments_loaded){
				for($i=0;$i<count($this->_comments); $i++){
					if($this->_comments[$i]['id'] == $com_id){
						array_splice($this->_comments, $i, 1);
					}
				}
			}
			return true;
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  validate()							//
	//	validates this event					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: true if success					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function validate(){
		$cal = $this->calendar();

		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId();

		$ret = array();
		$event_id = $this->getId();

		$validations = $cal->validations();
		$sets = "";
		$where = "";
		$found = false;
		$result = false;
		for($i=0;$i<count($validations);$i++){
			if($validations[$i]->user() == $this->avalanche->loggedInHuh() ||
			   $this->avalanche->userInGroupHuh($this->avalanche->loggedInHuh(), $validations[$i]->usergroup())){
				if($found){
					$sets .= ", ";
					$where .= " AND ";
				}
				$sets = "`" . $validations[$i]->field() . "`='1'";
				$where = "`" . $validations[$i]->field() . "`='0'";
				$found = true;
			}
		}

		if($found){
			$where .= " AND ";
		}
		$where .= "`id` = '" . $this->getId() . "'";

		if($sets && $cal->canReadValidations()){
			$sql = "UPDATE $tablename SET $sets WHERE $where";
			$result = $this->avalanche->mysql_query($sql);
		}

		if($result){
			return true;
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  needs_validate()						//
	//	validates this event					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: true if event needs validation			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function needs_validate(){
		$cal = $this->calendar();

		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId();

		$ret = array();
		$event_id = $this->getId();

		$validations = $cal->validations();
		$sets = "";
		$where = "";
		$found = false;
		$result = false;
		for($i=0;$i<count($validations);$i++){
			if($validations[$i]->user() == $this->avalanche->loggedInHuh() ||
			   $this->avalanche->userInGroupHuh($this->avalanche->loggedInHuh(), $validations[$i]->usergroup())){
				if($found){
					$sets .= ", ";
					$where .= " AND ";
				}
				$sets = "`" . $validations[$i]->field() . "`='1'";
				$where = "`" . $validations[$i]->field() . "`='0'";
				$found = true;
			}
		}

		if(!count($validations)){
			return false;
		}

		if($found){
			$where .= " AND ";
		}
		$where .= "`id` = '" . $this->getId() . "'";

		if($sets && $cal->canWriteValidations()){
			$sql = "SELECT * FROM $tablename WHERE $where";
			$result = $this->avalanche->mysql_query($sql);
			if(mysqli_fetch_array($result)){
				return true;
			}else{
				return false;
			}
		}

		return false;
	}


	//////////////////////////////////////////////////////////////////
	//  update()							//
	//	reitinialize this event and its associated recurrance	//
	//	pattern.						//
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
	function update(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $this->_calendar;
		$id = $this->_id;


		if(is_object($this->_old_recur)){
		/*
		 * then we know that there are other events in the series
		 * that need to be deleted
		 */
			$recur_id = $this->_old_recur->getId();
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . " WHERE recur_id = '$recur_id' AND id != '" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysqli_fetch_array($result)){
				$this->_calendar->removeEvent($myrow['id']);
			}
		}



		/*
		 * now that we've deleted the old events, we can input the new
		 * series of events.
		 */


		if(is_object($this->_recurrance)){
			$start_dates = $this->_recurrance->series();

			$start_date = $this->getValue("start_date");
			$start_time = $this->getValue("start_time");
			$start_date  = mktime(substr($start_time, 0, 2),substr($start_time, 3, 2),substr($start_time, 6, 2),
					      substr($start_date, 5,2), substr($start_date, 8,2), substr($start_date, 0,4));
			$end_date = $this->getValue("end_date");
			$end_time = $this->getValue("end_time");
			$end_date  = mktime(substr($end_time, 0, 2),substr($end_time, 3, 2),substr($end_time, 6, 2),
					    substr($end_date, 5,2), substr($end_date, 8,2), substr($end_date, 0,4));

			// find duration in seconds
			$date_duration = $end_date - $start_date;
	                for($i = 0; $i<count($start_dates); $i++){
				$start_date = $start_dates[$i];
				if($i > 0){
					$start_time = $this->getDisplayValue("start_time");
					$calculated_end_date = $start_date;
					$calculated_end_date = mktime (substr($start_time,0,2),substr($start_time,3,2),substr($start_time,6,2),
								substr($calculated_end_date, 5,2), substr($calculated_end_date, 8,2), substr($calculated_end_date, 0,4)) + $date_duration;
					$calculated_end_date = date("Y-m-d", $calculated_end_date);
					$end_date   = $calculated_end_date;

					// send in display dates to _clone...
					$new_event = $this->_clone($start_date, $end_date);
				}else{
					// use GMT time for set_value...
					$st = $this->getDisplayValue("start_time");
					$sd = $start_date;
					$s = $strongcal->adjust_back($sd, $st);
					$start_date = $s["date"];
					$start_time = $s["time"];

					$calculated_end_date = $start_date;
					$calculated_end_date = mktime(substr($start_time,0,2),substr($start_time,3,2),substr($start_time,6,2),
									substr($calculated_end_date, 5,2), substr($calculated_end_date, 8,2), substr($calculated_end_date, 0,4)) + $date_duration;
					$calculated_end_date = date("Y-m-d", $calculated_end_date);
					$end_date   = $calculated_end_date;

					$new_event = $this;
					$new_event->getField("start_date")->set_value($start_date);
					$new_event->getField("end_date")->set_value($end_date);
				}
			}
		}

//		$this = $new_event;
		return $this;
	}


	/* this will take the timezone effect out of the event.
	 * this is useful to call after events have been added with date/time in CST or other timezone
	 * $timezone should be the timezone of the event, not the timezone that the event *should* be, which is GMT
	 */
	function setTimeZone($timezone){
		$strongcal = $this->avalanche->getModule("strongcal");
		$sd = $this->getValue("start_date");
		$st = $this->getValue("start_time");
		$s = $strongcal->adjust_back($sd, $st, $timezone);
		$this->setValue("start_date", $s["date"]);
		$this->setValue("start_time", $s["time"]);

		$ed = $this->getValue("end_date");
		$et = $this->getValue("end_time");
		$e = $strongcal->adjust_back($ed, $et, $timezone);
		$this->setValue("end_date", $e["date"]);
		$this->setValue("end_time", $e["time"]);
	}

	function __clone(){
		throw new Exception("cannot use clone keyword on event objects");
	}





	function _clone($display_start_date=false, $display_end_date=false){
		$strongcal = $this->avalanche->getModule("strongcal");
		if($display_start_date === false){
			$display_start_date = $this->getDisplayValue("start_date");
		}
		if($display_end_date === false){
			$display_end_date = $this->getDisplayValue("end_date");
		}

		if(is_object($this->calendar())){
			$fields = $this->fields();
			$data_list = array();
			foreach($fields as $field){
				if($field->field() == "start_time"){
					$data_list[] = array("field" => $field, "value" => $this->getDisplayValue("start_time"));
				}else if($field->field() == "end_time"){
					$data_list[] = array("field" => $field, "value" => $this->getDisplayValue("end_time"));
				}else if($field->field() == "start_date"){
					$data_list[] = array("field" => $field, "value" => $display_start_date);
				}else if($field->field() == "end_date"){
					$data_list[] = array("field" => $field, "value" => $display_end_date);
				}else{
					$data_list[] = array("field" => $field, "value" => $field->value());
				}
			}

			// i need to send in display times for date/time
			$new_event = $this->calendar()->addEventSilently($data_list);
			$new_event->setAllDay($this->isAllDay());
			if(is_numeric($this->_recurrance_id)){
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $this->calendar()->getId() . " SET recur_id = '" . $this->_recurrance_id . "' WHERE id = '" . $new_event->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$new_event->reload();
		}else{
			throw new Exception("can only clone events with calendar");
		}

		return $new_event;
	}


	//standard visitor pattern
	function execute(module_strongcal_visitor $visitor){
		return $visitor->eventCase($this);
	}


}



?>