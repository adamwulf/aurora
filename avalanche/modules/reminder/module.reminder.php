<?php
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
// DEPENDANT ON BOOTSTRAP
try{
	$bootstrap = $this->getModule("bootstrap");
	if(!is_object($bootstrap)){
		throw new ClassDefNotFoundException("module_bootstrap");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"BOOTSTRAP\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"BOOTSTRAP\" exiting.";
	exit;
}

// DEPENDANT ON STRONGCAL
try{
	$strongcal = $this->getModule("strongcal");
	if(!is_object($strongcal)){
		throw new ClassDefNotFoundException("module_strongcal");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"STRONGCAL\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"STRONGCAL\" exiting.";
	exit;
}

// DEPENDANT ON TASKMAN
try{
	$taskman = $this->getModule("taskman");
	if(!is_object($taskman)){
		throw new ClassDefNotFoundException("module_taskman");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"TASKMAN\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"TASKMAN\" exiting.";
	exit;
}

// include the category class
include_once ROOT . APPPATH . MODULES . "reminder/submodule.reminder.php";

$fileloader = $this->getModule("fileloader");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "reminder/bootstraps/");

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                        ///////////////////////////
///////////////  MAIN NOTIFIER MODULE  ///////////////////////////
///////////////                        ///////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_reminder extends module_template implements module_strongcal_listener, module_taskman_listener {

	//////////////////////////////////////////////////////////////////
	//  __construct()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//	should only be called once				//
	//	(command.php of avalanche will include this		//
	//	   file after installation)				//
	//								//
	//  postcondition:						//
	//  	all variables in this object are initialized		//
	//								//
	//--------------------------------------------------------------//
	//  IF THIS FUNTION IS REDEFINED TO HOLD MORE CODE, BE SURE	//
	//	TO INCLUDE THE PARENT CLASS FUNCTION CALL FOR THE	//
	//	FIRST LINE.						//
	//								//
	//	I.E.	module_strongcal::init();			//
	//////////////////////////////////////////////////////////////////

	public function avalanche(){
		return $this->avalanche;
	}

	private $avalanche;
	private $reminder_cache;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_name = "Inversion Reminder";
		$this->_version = "1.0.0";
		$this->_desc = "Reminder for Inversion Designs.";
		$this->_folder = "reminder";
		$this->_copyright = "Copyright 2004 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "11-30-04";
		$this->reminder_cache = new HashTable();

		$strongcal = $this->avalanche->getModule("strongcal");
		$strongcal->addListener($this);

		$taskman = $this->avalanche->getModule("taskman");
		$taskman->addListener($this);

	}

	function addReminder(){
		if($this->avalanche->loggedInHuh()){
			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . $this->folder() . "_reminders (`author`) VALUES ('" . $this->avalanche->loggedInHuh() . "')";
			$result = $this->avalanche->mysql_query($sql);
			$reminder = new module_reminder_reminder($this->avalanche, mysql_insert_id($this->avalanche->mysqliLink()));
			$reminder->addUser($this->avalanche->getActiveUser());
			$this->reminder_cache->put($reminder->getId(), $reminder);
		}else{
			throw new Exception("You must be logged in to create a reminder");
		}
		return $reminder;
	}

	// get's all reminders this user author'd
	// or for an event, or a task
	function getRemindersFor($user_id){
		$reminder_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_reminders`";
		$outbox_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_outbox`";
		if(!is_int($user_id) && !is_object($user_id) && !$user_id instanceof module_strongcal_event && !$user_id instanceof module_taskman_task){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(is_int($user_id)){
			$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_reminders";
			$sql = "SELECT * FROM $reminder_table WHERE author = '" . $user_id . "'";
			$field = "id";
		}else if($user_id instanceof module_strongcal_event){
			$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_event";
			$sql = "SELECT * FROM $reminder_table WHERE cal_id='" . $user_id->calendar()->getId() . "' AND event_id = '" . $user_id->getId() . "'";
			$field = "reminder_id";
		}else if($user_id instanceof module_taskman_task){
			$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_task";
			$sql = "SELECT * FROM $reminder_table WHERE task_id = '" . $user_id->getId() . "'";
			$field = "reminder_id";
		}
		$result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			if(is_object($this->reminder_cache->get((int)$myrow[$field]))){
				$ret[] = $this->reminder_cache->get((int)$myrow[$field]);
			}else{
				$reminder = new module_reminder_reminder($this->avalanche, (int)$myrow[$field]);
				$this->reminder_cache->put($reminder->getId(), $reminder);
				$ret[] = $reminder;
			}
		}
		return $ret;
	}

	// get's all reminders for a user for an event/task
	function getMyRemindersFor($item, $user_id=false){
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}
		$outbox_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_outbox`";
		if(!is_object($item) && !$item instanceof module_strongcal_event && !$item instanceof module_taskman_task){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an event or task");
		}
		if($item instanceof module_strongcal_event){
			$reminder_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_relation_event`";
			$sql = "SELECT $reminder_table.* FROM $reminder_table, $outbox_table WHERE $reminder_table.cal_id = '" . $item->calendar()->getId() . "' AND $reminder_table.event_id = '" . $item->getId() . "' AND $reminder_table.reminder_id = $outbox_table.reminder_id AND $outbox_table.user_id = '$user_id'";
			$field = "reminder_id";
		}else if($item instanceof module_taskman_task){
			$reminder_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_relation_task`";
			$sql = "SELECT $reminder_table.* FROM $reminder_table, $outbox_table WHERE $reminder_table.task_id = '" . $item->getId() . "' AND $reminder_table.reminder_id = $outbox_table.reminder_id AND $outbox_table.user_id = '$user_id'";
			$field = "reminder_id";
		}
		$result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			if(is_object($this->reminder_cache->get((int)$myrow[$field]))){
				$ret[] = $this->reminder_cache->get((int)$myrow[$field]);
			}else{
				$reminder = new module_reminder_reminder($this->avalanche, (int)$myrow[$field]);
				$this->reminder_cache->put($reminder->getId(), $reminder);
				$ret[] = $reminder;
			}
		}
		return $ret;
	}


	// get's all reminders this user author'd
	// or for an event, or a task
	function getRemindersBefore($datetime){
		$reminder_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_reminders`";
		$outbox_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_outbox`";
		if(!is_string($datetime)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a datetime");
		}
		$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_reminders";
		$sql = "SELECT * FROM $reminder_table WHERE `send_on` <= '" . $datetime . "' AND sent_on = '0000-00-00 00:00:00'";
		$field = "id";
		$result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			if(is_object($this->reminder_cache->get((int)$myrow[$field]))){
				$r = $this->reminder_cache->get((int)$myrow[$field]);
			}else{
				$reminder = new module_reminder_reminder($this->avalanche, (int)$myrow[$field]);
				$this->reminder_cache->put($reminder->getId(), $reminder);
				$r = $reminder;
			}
			if($r->sendOn() <= $datetime){
				$ret[] = $r;
			}
		}
		return $ret;
	}

	function getReminder($reminder_id){
		if(!is_int($reminder_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_reminders";
		$sql = "SELECT * FROM $reminder_table WHERE id = '$reminder_id'";
		$result = $this->avalanche->mysql_query($sql);

		$ret = false;
		while($myrow = mysqli_fetch_array($result)){
			if(is_object($this->reminder_cache->get((int)$myrow["id"]))){
				$ret = $this->reminder_cache->get((int)$myrow["id"]);
			}else{
				$ret = new module_reminder_reminder($this->avalanche, (int)$myrow["id"]);
				$this->reminder_cache->put($ret->getId(), $ret);
			}
			return $ret;
		}
	}

	function deleteReminder($reminder_id){
		$note = $this->getReminder($reminder_id);
		if($note->canWrite()){
			$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_reminders";
			$outbox_table = $this->avalanche->PREFIX() . $this->folder() . "_outbox";
			$task_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_task";
			$event_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_event";
			$sql = "DELETE FROM $reminder_table WHERE id = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);
			$sql = "DELETE FROM $outbox_table WHERE reminder_id = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);
			$sql = "DELETE FROM $task_table WHERE reminder_id = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);
			$sql = "DELETE FROM $event_table WHERE reminder_id = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);

			$this->reminder_cache->clear($reminder_id);
			return true;
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////
	// implement module_strongcal_listener			//
	//////////////////////////////////////////////////////////
	// notifies that an event has been added
	function eventAdded($cal_id, $event_id){
		// noop
	}

	// notifies that an event has been edited
	function eventEdited($cal_id, $event_id){
		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);

		$reminders = $this->getRemindersFor($event);

		foreach($reminders as $reminder){
			$reminder->verify();
		}
	}

	public function eventDeleted($cal_id, $event_id){
		$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_event";
		$sql = "SELECT * FROM $reminder_table WHERE cal_id = '$cal_id' AND event_id = '$event_id'";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysqli_fetch_array($result)){
			$this->deleteReminder((int)$myrow["reminder_id"]);
		}
	}

	// notifies that an attendee has been added
	function attendeeAdded($cal_id, $event_id, $user_id){
		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);
		$reminders = $this->getRemindersFor($event);
		foreach($reminders as $reminder){
			if($reminder->type() == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
				$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->folder() . "_outbox` (`reminder_id`,`user_id`) VALUES ('" . $reminder->getId() . "','" . $user_id . "')";
				$result = $this->avalanche->mysql_query($sql);
				$reminder->reload();
			}
		}
	}

	// notifies that an attendee has been deleted
	function attendeeDeleted($cal_id, $event_id, $user_id){
		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);
		$reminders = $this->getRemindersFor($event);
		foreach($reminders as $reminder){
			if($reminder->type() == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_outbox` WHERE `reminder_id`='" . $reminder->getId() . "' AND `user_id`='" . $user_id . "'";
				$result = $this->avalanche->mysql_query($sql);
				$reminder->reload();
			}
		}
	}

	// notifies that a calendar has been added
	function calendarAdded($cal_id){
		// noop
	}


	// notifies that a calendar has been deleted
	function calendarDeleted($cal_id){
		// noop
	}


	//////////////////////////////////////////////////////////
	// implement module_taskman_listener			//
	//////////////////////////////////////////////////////////
	// called when a task is added
	function taskAdded($task_id){
		// noop
	}

	// called when a task is deleted
	function taskDeleted($task_id){
		$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_relation_task";
		$sql = "SELECT * FROM $reminder_table WHERE task_id = '$task_id'";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysqli_fetch_array($result)){
			$this->deleteReminder((int)$myrow["reminder_id"]);
		}
	}

	// called when a task's status changes
	// $task_id: the task that was changed
	// $comment: the optional comment for change
	function taskStatusChanged($task_id, $comment=false){
		// noop
	}

	// called when a task is edited (including status)
	function taskEdited($task_id){
		$taskman = $this->avalanche->getModule("taskman");
		$task = $taskman->getTask($task_id);

		$reminders = $this->getRemindersFor($task);

		foreach($reminders as $reminder){
			$reminder->verify();
		}
	}


	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$ret = "";
		$reminders = $this->getRemindersBefore(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
		$ret .= "looking for reminders\n";
		foreach($reminders as $r){
			$r->sendReminder();
			$ret .= "sending reminder\n";
		}
	}

	function deleteUser($user_id){
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_outbox` WHERE `user_id`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		$reminder_table = $this->avalanche->PREFIX() . $this->folder() . "_reminders";
		$sql = "DELETE FROM $reminder_table WHERE author = '$user_id'";
		$result = $this->avalanche->mysql_query($sql);

		return true;
	}

}

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>