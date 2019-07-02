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
	if(!is_object($bootstrap)){
		throw new ClassDefNotFoundException("module_strongcal");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"STRONGCAL\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"STRONGCAL\" exiting.";
	exit;
}

// include the category class
include_once ROOT . APPPATH . MODULES . "taskman/class.TaskNotFoundException.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.category.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.task.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.taskman.taskComparator.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.taskman.taskModifiedComparator.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.taskman.taskAddedComparator.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.taskman.listener.php";
include_once ROOT . APPPATH . MODULES . "taskman/submodule.taskman.visitor.php";

$fileloader = $this->getModule("fileloader");

$fileloader->include_recursive(ROOT . APPPATH . MODULES . "taskman/bootstraps/");
include_once ROOT . APPPATH . MODULES . "taskman/visitors/module.taskman.visitor.export.php";
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "taskman/visitors/");

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                  /////////////////////////////////
///////////////  MAIN OS MODULE  /////////////////////////////////
///////////////                  /////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_taskman extends module_template implements module_strongcal_listener{

	private $task_cache;

	// the array of listeners
	private $_listeners;

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
	function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_name = "Inversion Task Manager";
		$this->_version = "1.0.0";
		$this->_desc = "Task Manager for Inversion Designs.";
		$this->_folder = "taskman";
		$this->_copyright = "Copyright 2004 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "09-21-04";
		$this->task_cache = new HashTable();

		$this->_listeners = array();

		$strongcal = $this->avalanche->getModule("strongcal");
		$strongcal->addListener($this);
	}

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	/////////////////////   CATEGORIES   /////////////////////////////
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function getCategories($cal = false){
		$where = "";
		if($cal === false){
			$where = "1";
		}else if(is_object($cal) && $cal instanceof module_strongcal_calendar){
			$where = "cal_id='" . $cal->getId() . "'";
		}else if(is_object($cal) && $cal instanceof module_taskman_task){
			//$cal is a task
			return $this->getCategoriesFor($cal);
			$where = "task_id='" . $cal->getId() . "'";
		}else{
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a strongcal calendar");
		}
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . $this->folder() . "_categories WHERE " . $where . " ORDER BY cal_id, name";
		$result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			$ret[] = new module_taskman_category($this->avalanche, (int)$myrow['id'], (int)$myrow['cal_id'], $myrow['name']);
		}
		return $ret;
	}

	// returns a list of categories for a given task
	private function getCategoriesFor($task){
		$cat_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_categories`";
		$link_table = "`" . $this->avalanche->PREFIX() . $this->folder() . "_catlink`";
		$taskId = $task->getId();
		$sql = "SELECT DISTINCTROW $cat_table.* FROM $cat_table, $link_table WHERE $cat_table.id = $link_table.category_id AND $link_table.task_id = $taskId ORDER BY $cat_table.cal_id, $cat_table.name";
		$result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			$ret[] = new module_taskman_category($this->avalanche, (int)$myrow['id'], (int)$myrow['cal_id'], $myrow['name']);
		}
		return $ret;
	}

	function addCategory(module_strongcal_calendar $calendar, $name){
		$cal_id = $calendar->getId();
		$name = addslashes($name);
		$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->folder() . "_categories` (`cal_id`,`name`) VALUES ('$cal_id', '$name');";
		$this->avalanche->mysql_query($sql);
		$task_id = mysqli_insert_id();
		return new module_taskman_category($this->avalanche, $task_id, $cal_id, $name);
	}

	function deleteCategory($cat_id){
		if(!is_int($cat_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_categories` WHERE id='$cat_id'";
		$this->avalanche->mysql_query($sql);

		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_catlink` WHERE category_id='$cat_id'";
		$this->avalanche->mysql_query($sql);
		return true;
	}

	// adds a category to a task
	function linkTask($task_id, $cat_id){
		$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->folder() . "_catlink` (`task_id`,`category_id`) VALUES ('$task_id', '$cat_id');";
		$this->avalanche->mysql_query($sql);
		return true;
	}

	// removes the category from a task
	function unlinkTask($task_id, $cat_id){
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_catlink` WHERE `task_id`='$task_id' AND `category_id`='$cat_id';";
		$this->avalanche->mysql_query($sql);
		return true;
	}


	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	////////////////////////   TASKS   ///////////////////////////////
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	public function addTask(module_strongcal_calendar $calendar, $values){
		$cal_id = $calendar->getId();
		$user_id = $this->avalanche->loggedInHuh();

		$strongcal = $this->avalanche->getModule("strongcal");
		$gmtime = $strongcal->gmttimestamp();
		$created_on = date("Y-m-d H:i:00", $gmtime);

		if(!isset($values["description"]) || !is_string($values["description"])){
			throw new IllegalArgumentException("values sent into " . __METHOD__ . " must contain the key \"description\" with a string value");
		}
		if(!isset($values["due"]) || !is_string($values["due"])){
			throw new IllegalArgumentException("values sent into " . __METHOD__ . " must contain the key \"due\" with a string value");
		}
		if(!isset($values["priority"]) || !is_int($values["priority"])){
			throw new IllegalArgumentException("values sent into " . __METHOD__ . " must contain the key \"priority\" with an int value");
		}
		if(!isset($values["summary"]) || !is_string($values["summary"])){
			throw new IllegalArgumentException("values sent into " . __METHOD__ . " must contain the key \"summary\" with a string value");
		}

		$description = $values["description"];
		$due = $values["due"];
		$due = $this->adjustToGMT($due);
		$values["due"] = $due;
		$priority = $values["priority"];
		$summary = $values["summary"];

		$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` (`author`,`created_on`,`cal_id`,`completed`,`description`,`due`,`priority`,`summary`,`status`,`delegated_to`,`assigned_to`) VALUES ('$user_id','$created_on','$cal_id', '0000-00-00 00:00:00', '$description', '$due', '$priority', '$summary','" . module_taskman_task::$STATUS_DEFAULT . "','$user_id','$user_id');";
		$this->avalanche->mysql_query($sql);
		// get new task id
		$task_id = mysqli_insert_id();
		// insert history
		$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->folder() . "_status_history` (`task_id`,`user_id`,`status`, `stamp`, `comment`) VALUES ('" . $task_id . "', '" . $this->avalanche->loggedInHuh() . "', '" . module_taskman_task::$STATUS_DEFAULT . "', '" . $created_on . "','')";
		$result = $this->avalanche->mysql_query($sql);

		// fill out rest of data for task
		$values["created_on"] = $created_on;
		$values["author"] = $user_id;
		$values["assigned_to"] = $user_id;
		$values["delegated_to"] = $user_id;
		$values["status"] = module_taskman_task::$STATUS_DEFAULT;
		$values["completed"] = "0000-00-00 00:00:00";
		$values["modified_by"] = $user_id;
		$values["cancelled"] = "0000-00-00 00:00:00";
		$values["modified_on"] = "0000-00-00 00:00:00";
		// create task object
		$task = new module_taskman_task($this->avalanche, $task_id, $cal_id, $values);
		$this->task_cache->put($task->getId(), $task);

		// notify listeners
		$this->taskAdded($task->getId());

		return $task;
	}



	// utility function. does NOT update database.
	// returns new datetime with proper timezone.
	public function adjustToGMT($datetime){
		$strongcal = $this->avalanche->getModule("strongcal");
		if(module_taskman_task::isDateTime($datetime)){
			$strongcal = $this->avalanche->getModule("strongcal");
			$timezone = $strongcal->timezone();
			$dd = $datetime;
			$dt = substr($dd, 11);
			$dd = substr($dd, 0, 10);
			$d = $strongcal->adjust_back($dd, $dt, $timezone);
			$d = $d["date"] . " " . $d["time"];
			return $d;
		}else{
			return $datetime;
		}
	}

	// utility function. does NOT update database.
	// returns new datetime with proper timezone.
	public function adjustFromGMT($datetime){
		if(module_taskman_task::isDateTime($datetime)){
			$strongcal = $this->avalanche->getModule("strongcal");
			$timezone = $strongcal->timezone();
			$dd = $datetime;
			$dt = substr($dd, 11);
			$dd = substr($dd, 0, 10);
			$d = $strongcal->adjust($dd, $dt, $timezone);
			$d = $d["date"] . " " . $d["time"];
			return $d;
		}else{
			return $datetime;
		}
	}

	public function deleteTask($task_id){
		if(!is_int($task_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		try{
			$task = $this->getTask($task_id);
			if($task->canWrite()){

				$this->taskDeleted($task_id);

				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` WHERE id='$task_id'";
				$this->avalanche->mysql_query($sql);

				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_catlink` WHERE task_id='$task_id'";
				$this->avalanche->mysql_query($sql);

				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_status_history` WHERE task_id='$task_id'";
				$this->avalanche->mysql_query($sql);

				$this->task_cache->clear($task_id);
				return true;
			}else{
				return false;
			}
		}catch(TaskNotFoundException $e){
			return false;
		}
	}

	// get all the tasks, or just tasks by this user
	public function getTasks($user_id = false){
		$task_table = $this->avalanche->PREFIX() . $this->folder() . "_tasks";
		$userquery = "1";
		if(is_int($user_id)){
			$userquery = "author='$user_id' OR status='" . module_taskman_task::$STATUS_DELEGATED . "' AND delegated_to='$user_id' OR assigned_to='$user_id'";
		}else if($user_id !== false){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be an int");
		}
		$sql = "SELECT * FROM $task_table WHERE $userquery ORDER BY due, id DESC";
	        $result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			$current_task = $myrow["id"];
			if(is_object($this->task_cache->get((int)$myrow["id"]))){
				$task = $this->task_cache->get((int)$myrow["id"]);
			}else{
				$task = new module_taskman_task($this->avalanche, (int)$myrow["id"], (int)$myrow["cal_id"], $myrow);
				$this->task_cache->put($task->getId(), $task);
			}
			if($task->canRead()){
				$ret[] = $task;
			}
		}
		return $ret;
	}

	// get all the tasks between the start/end datetimes
	public function getTasksBetween($start, $end){
		$task_table = $this->avalanche->PREFIX() . $this->folder() . "_tasks";
		if(!module_taskman_task::isDateTime($start) ||
		   !module_taskman_task::isDateTime($end)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be an int");
		}
		$sql = "SELECT * FROM $task_table WHERE due >= '$start' AND due <= '$end' ORDER BY due, id DESC";
	        $result = $this->avalanche->mysql_query($sql);

		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			$current_task = $myrow["id"];
			if(is_object($this->task_cache->get((int)$myrow["id"]))){
				$task = $this->task_cache->get((int)$myrow["id"]);
			}else{
				$task = new module_taskman_task($this->avalanche, (int)$myrow["id"], (int)$myrow["cal_id"], $myrow);
				$this->task_cache->put($task->getId(), $task);
			}
			$ret[] = $task;
		}
		return $ret;
	}

	// gets all tasks matching the terms
	public function getAllTasksMatching($text){
		$bootstrap = $this->avalanche->getModule("bootstrap");
		// get the calendar list
			$data = false;
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendars = $data->data();
		// end getting the calendar list
		// search for the logged in user.
		$user_id = $this->avalanche->getActiveUser();


		////////////////////////////////////////////////////////////////////
		// get part of query that will return list of all tasks assigned  //
		// to or delegated to the user					  //
		////////////////////////////////////////////////////////////////////
		$task_table = $this->avalanche->PREFIX() . $this->folder() . "_tasks";
		$userquery = "1";
		if(is_int($user_id)){
			$userquery = "author='$user_id' OR status='" . module_taskman_task::$STATUS_DELEGATED . "' AND delegated_to='$user_id' OR assigned_to='$user_id'";
		}else if($user_id !== false){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be an int");
		}
		$userquery = "(" . $userquery . ") ";
		////////////////////////////////////////////////////////////////////
		// get part of query that will return all tasks in calendars that //
		// the user is allowed to see					  //
		////////////////////////////////////////////////////////////////////
		$calquery = "0";
		foreach($calendars as $cal){
			if($cal->canReadEntries()){
				$calquery .= " OR cal_id='" . $cal->getId() . "'";
			}
		}
		$calquery = "(" . $calquery . ") ";
		$leftquery = "(" . $userquery . " OR " . $calquery . ") ";

		////////////////////////////////////////////////////////////////////
		// now get the sql for the search terms				  //
		////////////////////////////////////////////////////////////////////
		$texts = explode(" ", $text);
		$rightquery = "";
		foreach($texts as $text){
			$rightquery .= "AND (description LIKE '%$text%' OR summary LIKE '%$text%') ";
		}

		$sql = "SELECT * FROM $task_table WHERE $leftquery $rightquery ORDER BY id";

		//echo $sql . "<br><br>";

	        $result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysqli_fetch_array($result)){
			$current_task = $myrow["id"];
			if(is_object($this->task_cache->get((int)$myrow["id"]))){
				$task = $this->task_cache->get((int)$myrow["id"]);
			}else{
				$task = new module_taskman_task($this->avalanche, (int)$myrow["id"], (int)$myrow["cal_id"], $myrow);
				$this->task_cache->put($task->getId(), $task);
			}
			$ret[] = $task;
		}
		return $ret;
	}

	public function getTask($task_id){
		$task_table = $this->avalanche->PREFIX() . $this->folder() . "_tasks";
		$userquery = "1";
		if(!is_int($task_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$sql = "SELECT * FROM $task_table WHERE id='$task_id'";
	        $result = $this->avalanche->mysql_query($sql);

		if($myrow = mysqli_fetch_array($result)){
			$current_task = $myrow["id"];
			if(is_object($this->task_cache->get((int)$myrow["id"]))){
				$task = $this->task_cache->get((int)$myrow["id"]);
			}else{
				$task = new module_taskman_task($this->avalanche, (int)$myrow["id"], (int)$myrow["cal_id"], $myrow);
				$this->task_cache->put($task->getId(), $task);
			}
			$ret = $task;
		}else{
			throw new TaskNotFoundException($task_id);
		}
		return $ret;
	}

	// gets raw data from mysql
	function getRawData($task_id){
		$task_table = $this->avalanche->PREFIX() . $this->folder() . "_tasks";
		$status_table = $this->avalanche->PREFIX() . $this->folder() . "_status_history";
		$sql = "SELECT * FROM $task_table WHERE `id` = '$task_id'";
	        $result = $this->avalanche->mysql_query($sql);

		$ret = array();
		if($myrow = mysqli_fetch_array($result)){
			$myrow["id"] = (int) $myrow["id"];
			$myrow["author"] = (int) $myrow["author"];
			$myrow["cal_id"] = (int) $myrow["cal_id"];
			$myrow["priority"] = (int) $myrow["priority"];
			$myrow["status"] = (int) $myrow["status"];
			$myrow["assigned_to"] = (int) $myrow["assigned_to"];
			$myrow["delegated_to"] = (int) $myrow["delegated_to"];
		}else{
			throw new Exception("no task found with id = $task_id");
		}
		return $myrow;
	}

	// clears the task cache
	public function reload(){
		$this->task_cache = new HashTable();
	}

	//////////////////////////////////////////////////////////
	// implement module_strongcal_listener			//
	//////////////////////////////////////////////////////////
	// notifies that an event has been added
	function eventAdded($cal_id, $event_id){
		// noop
	}

	// notifies that an event has been deleted
	function eventDeleted($cal_id, $event_id){
		// noop
	}

	// notifies that an event has been edited
	function eventEdited($cal_id, $event_id){
		// noop
	}

	// notifies that a calendar has been added
	function calendarAdded($cal_id){
		// noop
	}


	// notifies that a calendar has been deleted
	function calendarDeleted($cal_id){
		// delete all tasks with this calendar id
		$sql = "SELECT * FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` WHERE cal_id='$cal_id'";
	        $result = $this->avalanche->mysql_query($sql);
		$query = "0";
		while($myrow = mysqli_fetch_array($result)){
			$query .= " OR task_id='" . $myrow["id"] . "'";
		}

		// delete all tasks
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` WHERE cal_id='$cal_id'";
		$this->avalanche->mysql_query($sql);

		// delete all categories
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_categories` WHERE cal_id='$cal_id'";
		$this->avalanche->mysql_query($sql);

		// delete all links
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_catlink` WHERE $query";
		$this->avalanche->mysql_query($sql);

		// delete all status history
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_status_history` WHERE $query";
		$this->avalanche->mysql_query($sql);
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
	// Listener Pattern					//
	//////////////////////////////////////////////////////////

	// adds a listener
	public function addListener($listener){
		if(!($listener instanceof module_strongcal_listener)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type module_strongcal_listener");
		}
		$this->_listeners[] = $listener;
	}

	// removes a listener
	public function removeListener($listener){
		$new_list = array();
		foreach($this->_listeners as $l){
			if($l != $listener){
				$new_list = $l;
			}
		}
		$this->_listeners = $new_list;
	}

	// notifies listeners that an task has been added
	public function taskAdded($task_id){
		if(!is_int($task_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->taskAdded($task_id);
		}
	}

	// notifies listeners that an task has been deleted
	public function taskDeleted($task_id){
		if(!is_int($task_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->taskDeleted($task_id);
		}
	}

	public function taskEdited($task_id){
		if(!is_int($task_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->taskEdited($task_id);
		}
	}

	public function taskStatusChanged($task_id, $comment=false){
		if(!is_int($task_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->taskStatusChanged($task_id, $comment);
		}
	}




	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
	}
}

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>