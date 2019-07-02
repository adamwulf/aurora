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
include_once ROOT . APPPATH . MODULES . "notifier/submodule.notification.php";

$fileloader = $this->getModule("fileloader");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "notifier/bootstraps/");

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
class module_notifier extends module_template implements module_strongcal_listener, module_taskman_listener{
	
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
	private $avalanche;
	private $notification_cache;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_name = "Inversion Notifier";
		$this->_version = "1.0.0";	
		$this->_desc = "Notifier for Inversion Designs.";	
		$this->_folder = "notifier";
		$this->_copyright = "Copyright 2004 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "11-30-04";
		$this->notification_cache = new HashTable();
		
		$strongcal = $this->avalanche->getModule("strongcal");
		$strongcal->addListener($this);

		$taskman = $this->avalanche->getModule("taskman");
		$taskman->addListener($this);
	}

	function addNotificationFor($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "notifier_notifications (`user_id`) VALUES ('$user_id')";
		$result = $this->avalanche->mysql_query($sql);
		$notification = new module_notifier_notification($this->avalanche, mysql_insert_id());
		$this->notification_cache->put($notification->getId(), $notification);
		return $notification;
	}
	
	function getNotificationsFor($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";
		$sql = "SELECT * FROM $notification_table WHERE user_id = '$user_id'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = array();
		while($myrow = mysql_fetch_array($result)){
			if(is_object($this->notification_cache->get((int)$myrow["id"]))){
				$ret[] = $this->notification_cache->get((int)$myrow["id"]);
			}else{
				$notification = new module_notifier_notification($this->avalanche, (int)$myrow["id"]);
				$this->notification_cache->put($notification->getId(), $notification);
				$ret[] = $notification;
			}
		}
		return $ret;
	}
	
	function getNotification($notification_id){
		if(!is_int($notification_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";
		$sql = "SELECT * FROM $notification_table WHERE id = '$notification_id'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			if(is_object($this->notification_cache->get((int)$myrow["id"]))){
				$ret = $this->notification_cache->get((int)$myrow["id"]);
			}else{
				$ret = new module_notifier_notification($this->avalanche, (int)$myrow["id"]);
				$this->notification_cache->put($ret->getId(), $ret);
			}
			return $ret;
		}
	}
	
	function deleteNotification($notification_id){
		$note = $this->getNotification($notification_id);
		if($note->canWrite()){
			$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";
			$sql = "DELETE FROM $notification_table WHERE id = '$notification_id'";
			$result = $this->avalanche->mysql_query($sql);
			
			$this->notification_cache->clear($notification_id);
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
		$user_id = (int)($this->avalanche->loggedInHuh() ? $this->avalanche->loggedInHuh() : $this->avalanche->getVar("USER"));
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);

		$title = "New Event!";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|$cal_id|%' OR all_calendars='1') AND item='" . module_notifier_notification::$ITEM_EVENT . "' AND action='" . module_notifier_notification::$ACTION_ADDED . "'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has added the event \"" . $event->getDisplayValue("title") . "\" to the " . $cal->name() . " calendar.";
				}else{
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has added an event to the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
			}
		}
	}
	
	// notifies that an event has been edited
	function eventEdited($cal_id, $event_id){
		$user_id = (int)($this->avalanche->loggedInHuh() ? $this->avalanche->loggedInHuh() : $this->avalanche->getVar("USER"));
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);

		$title = "Event Edited";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|$cal_id|%' OR all_calendars='1') AND item='" . module_notifier_notification::$ITEM_EVENT . "' AND action='" . module_notifier_notification::$ACTION_EDITED . "'";
		$result = $this->avalanche->mysql_query($sql);
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has edited the event \"" . $event->getDisplayValue("title") . "\" in the " . $cal->name() . " calendar.";
				}else{
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has edited an event in the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
			}
		}
	}
	
	public function eventDeleted($cal_id, $event_id){
		$user_id = (int)($this->avalanche->loggedInHuh() ? $this->avalanche->loggedInHuh() : $this->avalanche->getVar("USER"));
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$cal = $strongcal->getCalendarFromDb($cal_id);
		$event = $cal->getEvent($event_id);

		$title = "Event Deleted";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|$cal_id|%' OR all_calendars='1') AND item='" . module_notifier_notification::$ITEM_EVENT . "' AND action='" . module_notifier_notification::$ACTION_DELETED . "'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has deleted the event \"" . $event->getDisplayValue("title") . "\" from the " . $cal->name() . " calendar.";
				}else{
					$body = $this->avalanche->getModule("os")->getUsername($user_id) . " has deleted the event from the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
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
	
	// notifies that an attendee has been added
	function attendeeAdded($cal_id, $event_id, $user_id){
		// noop
	}
	
	// notifies that an attendee has been deleted
	function attendeeDeleted($cal_id, $event_id, $user_id){
		// noop
	}

	//////////////////////////////////////////////////////////
	// implement module_taskman_listener			//
	//////////////////////////////////////////////////////////
	// notifies that an task has been added
	function taskAdded($task_id){
		$user_id = (int)($this->avalanche->loggedInHuh() ? $this->avalanche->loggedInHuh() : $this->avalanche->getVar("USER"));
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		$os = $this->avalanche->getModule("os");
		$task = $taskman->getTask($task_id);
		$cal = $strongcal->getCalendarFromDb($task->calId());

		$title = "New Task!";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|" . $task->calId() . "|%' || all_calendars = '1') AND item='" . module_notifier_notification::$ITEM_TASK . "' AND action='" . module_notifier_notification::$ACTION_ADDED . "'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $os->getUsername($user_id) . " has added the task \"" . $task->title() . "\" to the " . $cal->name() . " calendar.";
				}else{
					$body = $os->getUsername($user_id) . " has added a task to the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
			}
		}
	}
	
	// notifies that an task has been deleted
	function taskDeleted($task_id){
		$user_id = (int)($this->avalanche->loggedInHuh() ? $this->avalanche->loggedInHuh() : $this->avalanche->getVar("USER"));
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		$os = $this->avalanche->getModule("os");
		$task = $taskman->getTask($task_id);
		$cal = $strongcal->getCalendarFromDb($task->calId());

		$title = "Task Deleted";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|" . $task->calId() . "|%' || all_calendars = '1') AND item='" . module_notifier_notification::$ITEM_TASK . "' AND action='" . module_notifier_notification::$ACTION_DELETED . "'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $os->getUsername($user_id) . " has deleted the task \"" . $task->title() . "\" from the " . $cal->name() . " calendar.";
				}else{
					$body = $os->getUsername($user_id) . " has deleted a task from the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
			}
		}
	}
	
	// notifies that an task has it's status changed
	function taskStatusChanged($task_id, $comment=false){
		$user_id = (int)($this->avalanche->getActiveUser());
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		$os = $this->avalanche->getModule("os");
		$task = $taskman->getTask($task_id);
		$cal = $strongcal->getCalendarFromDb($task->calId());

		$title = "Task Status Change";

		
		if($task->status() == module_taskman_task::$STATUS_COMPLETED){
			$extra_sql = " OR action='" . module_notifier_notification::$ACTION_COMPLETED . "'";
		}else
		if($task->status() == module_taskman_task::$STATUS_DELEGATED){
			$extra_sql = " OR action='" . module_notifier_notification::$ACTION_DELEGATED . "'";
		}else
		if($task->status() == module_taskman_task::$STATUS_CANCELLED){
			$extra_sql = " OR action='" . module_notifier_notification::$ACTION_CANCELLED . "'";
		}else{
			$extra_sql = "";
		}
		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|" . $task->calId() . "|%' || all_calendars = '1') AND item='" . module_notifier_notification::$ITEM_TASK . "' AND (action='" . module_notifier_notification::$ACTION_STATUS . "' $extra_sql)";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				$send_message = true;
				if($note->action() == module_notifier_notification::$ACTION_CANCELLED){
					$title = "Task Cancelled";
					if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
						if(strlen($comment)){
							$comment = " with comment \"$comment\"";
						}
						$body = $os->getUsername($user_id) . " cancelled \"" . $task->title() . "\" in the " . $cal->name() . " cal $comment.";
					}else{
						$body = $os->getUsername($user_id) . " cancelled a task in the " . $cal->name() . " calendar.";
					}
				}else
				if($note->action() == module_notifier_notification::$ACTION_COMPLETED){
					$title = "Task Completed";
					if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
						if(strlen($comment)){
							$comment = " with comment \"$comment\"";
						}
						$body = $os->getUsername($user_id) . " completed \"" . $task->title() . "\" in the " . $cal->name() . " cal $comment.";
					}else{
						$body = $os->getUsername($user_id) . " completed a task in the " . $cal->name() . " calendar.";
					}
				}else
				if($note->action() == module_notifier_notification::$ACTION_DELEGATED && $task->delegatedTo() == $note->userId()){
					$title = "Task Delegated to You";
					if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
						if(strlen($comment)){
							$comment = " with comment \"$comment\"";
						}
						$body = $os->getUsername($user_id) . " delegated \"" . $task->title() . "\" in the " . $cal->name() . " cal to you$comment.";
					}else{
						$body = $os->getUsername($user_id) . " delegated a task in the " . $cal->name() . " calendar to you.";
					}
				}else if($note->action() == module_notifier_notification::$ACTION_STATUS){
					if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
						$status = "\"" . $this->getReadableStatus($task) . "\"";
						if(strlen($comment)){
							$comment = " with comment \"$comment\"";
						}
						$body = $os->getUsername($user_id) . " changed task status of \"" . $task->title() . "\" in the " . $cal->name() . " cal to $status" . $comment . ".";
					}else{
						$body = $os->getUsername($user_id) . " changed a task status in the " . $cal->name() . " calendar.";
					}
				}else{
					// the note could refer to a 'please contact if delegated to me' when it was really
					// delegated to somebody else
					$send_message = false;
				}
				if($send_message){
					$note->contact($this->avalanche->getUser(-1), $title, $body);
				}
			}
		}
	}
	
	// notifies that an task has it's status changed
	function taskEdited($task_id){
		$user_id = (int)$this->avalanche->getActiveUser();
		$notification_table = $this->avalanche->PREFIX() . $this->folder() . "_notifications";

		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		$os = $this->avalanche->getModule("os");
		$task = $taskman->getTask($task_id);
		$cal = $strongcal->getCalendarFromDb($task->calId());

		$title = "Task Edited";

		$sql = "SELECT * FROM $notification_table WHERE (calendars LIKE '%|" . $task->calId() . "|%' || all_calendars = '1') AND item='" . module_notifier_notification::$ITEM_TASK . "' AND action='" . module_notifier_notification::$ACTION_EDITED . "'";
		$result = $this->avalanche->mysql_query($sql);
		
		$ret = false;
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$note = $this->getNotification($id);
			if($note->userId() != $user_id && $cal->canReadName($this->avalanche->getAllUsergroupsFor($note->userId()))){
				if($cal->canReadEntries($this->avalanche->getAllUsergroupsFor($note->userId()))){
					$body = $os->getUsername($user_id) . " edited \"" . $task->title() . "\" in the " . $cal->name() . " calendar.";
				}else{
					$body = $os->getUsername($user_id) . " edited a task in the " . $cal->name() . " calendar.";
				}
				$note->contact($this->avalanche->getUser(-1), $title, $body);
			}
		}
	}
	
	private function getReadableStatus($task){
		$os = $this->avalanche->getModule("os");
		if($task->status() == module_taskman_task::$STATUS_ACCEPTED){
			return "Accepted";
		}else
		if($task->status() == module_taskman_task::$STATUS_NEEDS_ACTION){
			return "Needs Action";
		}else
		if($task->status() == module_taskman_task::$STATUS_DECLINED){
			return "Declined";
		}else
		if($task->status() == module_taskman_task::$STATUS_COMPLETED){
			return "Completed";
		}else
		if($task->status() == module_taskman_task::$STATUS_DELEGATED){
			return "Delegated to " . $os->getUsername($task->delegatedTo());;
		}
		return "Unknown";
	}


	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
		// noop
	}
	
	function deleteUser($user_id){
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_notifications` WHERE `user_id`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
	}
	
} 

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>