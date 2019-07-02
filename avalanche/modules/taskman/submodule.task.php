<?



class module_taskman_task{

	//status static values
	public static $STATUS_ACCEPTED 		= 1;
	public static $STATUS_NEEDS_ACTION 	= 2;
	public static $STATUS_DEFAULT		= 2;
	public static $STATUS_DECLINED 		= 4;
	public static $STATUS_COMPLETED 	= 5;
	public static $STATUS_DELEGATED 	= 6;
	public static $STATUS_CANCELLED 	= 7;

	// priority static values
	public static $PRIORITY_HIGH 		= 1;
	public static $PRIORITY_NORMAL	 	= 2;
	public static $PRIORITY_LOW		= 3;

	// this task's id
	private $id;
	// this task's calendar id
	private $cal_id;

	// task fields
	private $author;
	private $created_on;
	private $title;
	private $description;
	private $completed_datetime;
	private $due_datetime;
	private $priority;
	private $status;
	private $assigned_to;
	private $delegated_to;

	// an array of category objects to which this task belongs
	private $categories;
	// true if categories are loaded, false otherwise
	private $categories_loaded;
	// true if task data is loaded, false otherwise
	private $loaded;
	// the taskman module object
	private $taskman;

	// the calendar of this task
	private $calendar;

	// creates a new task
	private $avalanche;
	public function __construct($avalanche, $id, $cal_id, $values = false){
		$this->avalanche = $avalanche;
		$this->taskman = $this->avalanche->getModule("taskman");
		$this->categories_loaded = false;
		$this->status_loaded = false;
		if(!is_int($id)){
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be an int");
		}
		$this->id = $id;
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("Second argument to " . __METHOD__ . " must be an int");
		}
		$this->cal_id = $cal_id;
		$this->calendar = false;
		$this->init_values($values);
	}

	// initializes the values of this task
	private function init_values($values){
		if($values === false){
			$this->loaded = false;
		}else if(!is_array($values)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array or string");
		}else{
			$this->loaded = true;
			// load values
			$this->created_on = $values["created_on"];
			$this->author = (int)$values["author"];
			$this->title  = $values["summary"];
			$this->description =$values["description"];
			$this->completed_datetime = $values["completed"];
			$this->due_datetime = $values["due"];
			$this->priority = (int)$values["priority"];
			$this->status = (int)$values["status"];
			$this->assigned_to = (int)$values["assigned_to"];
			$this->delegated_to = (int)$values["delegated_to"];
			$this->modified_by = (int)$values["modified_by"];
			if(!$this->modified_by){
				$this->modified_by = $this->assigned_to;
			}
			$this->cancelled_datetime = $values["cancelled"];
			$this->modified_datetime = $values["modified_on"];

			$strongcal = $this->avalanche->getModule("strongcal");
			$this->calendar = $strongcal->getCalendarFromDb($this->cal_id);
		}
	}

	// tells this task to reload from the db
	public function reload(){
		$this->loaded = false;
		$this->categories_loaded = false;
	}

	// loads in the values of the task from db
	private function load(){
		$data = $this->taskman->getRawData($this->getId());
		$this->init_values($data);
	}


	public function getCategories(){
		if($this->canRead()){
			return $this->taskman->getCategories($this);
		}else{
			return array();
		}
	}

	// takes in a category id
	public function linkTo($cat){
		if(!is_object($cat)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an object");
		}
		return $this->taskman->linkTask($this->getId(), $cat->getId());
	}

	// takes in a category id
	public function unlinkTo($cat){
		if(!is_object($cat)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an object");
		}
		return $this->taskman->unlinkTask($this->getId(), $cat->getId());
	}


	// returns the id of this task
	public function getId(){
		return $this->id;
	}

	// returns the calendar id to which this task belongs
	public function calId(){
		return $this->cal_id;
	}

	// return or change the author
	public function author($author = false){
		if($author === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->author;
		}else if(!is_int($author)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be an int");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `author`=\"" . $author . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->author = $author;
				return $author;
			}else{
				return $this->author();
			}
		}
	}

	// returns the user id to whome this task is assigned
	public function assignedTo(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->assigned_to;
	}

	// returns the user id to whom this event is currently delegated, or the author if none
	public function delegatedTo(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->delegated_to;
	}


	// return or change the completion date
	public function completed($completed = false){
		if($completed === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->taskman->adjustFromGMT($this->completed_datetime);
		}else if(!is_string($completed) || !$this->isDateTime($completed)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string of format YYYY-MM-DD HH:mm:SS");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `completed`=\"" . $completed . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->completed_datetime = $completed;
				return $this->taskman->adjustFromGMT($completed);
			}else{
				return $this->completed();
			}
		}
	}

	// return or change the completion date
	public function cancelled($cancelled = false){
		if($cancelled === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->taskman->adjustFromGMT($this->cancelled_datetime);
		}else if(!is_string($cancelled) || !$this->isDateTime($cancelled)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string of format YYYY-MM-DD HH:mm:SS");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `cancelled`=\"" . $cancelled . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->cancelled_datetime = $cancelled;
				return $this->taskman->adjustFromGMT($cancelled);
			}else{
				return $this->cancelled();
			}
		}
	}

	// return or change the completion date
	public function createdOn(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->taskman->adjustFromGMT($this->created_on);
	}


	// return or change the completion date
	public function modifiedOn($modifiedOn = false){
		if($modifiedOn === false){
			if(!$this->loaded){
				$this->load();
			}
			if($this->modified_datetime != "0000-00-00 00:00:00"){
				return $this->taskman->adjustFromGMT($this->modified_datetime);
			}else{
				return $this->createdOn();
			}
		}else if(!is_string($modifiedOn) || !$this->isDateTime($modifiedOn)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string of format YYYY-MM-DD HH:mm:SS");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `modified_on`=\"" . $modifiedOn . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->modified_datetime = $modifiedOn;
				return $this->taskman->adjustFromGMT($modifiedOn);
			}else{
				return $this->modifiedOn();
			}
		}
	}

	// return or change the due date
	public function due($due = false){
		if($due === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->taskman->adjustFromGMT($this->due_datetime);
		}else if(!is_string($due) || !$this->isDateTime($due)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string of format YYYY-MM-DD HH:mm:SS");
		}else{
			if($this->canWrite()){
				$due = $this->taskman->adjustToGMT($due);
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `due`=\"" . $due . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->due_datetime = $due;
				$this->setModified();
				return $this->taskman->adjustFromGMT($due);
			}else{
				return $this->due();
			}
		}
	}

	// return or change the title
	public function title($title = false){
		if($title === false){
			if(!$this->loaded){
				$this->load();
			}
			if($this->canRead()){
				return $this->title;
			}else{
				return "";
			}
		}else if(!is_string($title)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `summary`=\"" . $title . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error()){
					throw new DatabaseException(mysqli_error());
				}
				$this->title = $title;
				$this->setModified();
				return $title;
			}else{
				return $this->title();
			}
		}
	}

	// return or change the description
	public function description($description = false){
		if($description === false){
			if(!$this->loaded){
				$this->load();
			}
			if($this->canRead()){
				return $this->description;
			}else{
				return "";
			}
		}else if(!is_string($description)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a string");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `description`=\"" . $description . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->description = $description;
				$this->setModified();
				return $description;
			}else{
				return $this->description();
			}
		}
	}

	// return or change who last modified the *status* of this task
	public function modifiedBy($m = false){
		if($m === false){
			if(!$this->loaded){
				$this->load();
			}
			if($this->canRead()){
				return $this->modified_by;
			}else{
				return "";
			}
		}else if(!is_int($m)){
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be a int (user id)");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `modified_by`=\"" . $m . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->modified_by = $m;
				$this->setModified();
				return $m;
			}else{
				return $this->modifiedBy();
			}
		}
	}


	// return or change the status of the task (old status is saved in history)
	// $to_user becomes the comment argument if $status does not equals DELEGATED
	// otherwise, $comment is the comment
	public function status($status = false, $to_user=false, $comment = false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		if($status === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->status;
		}else if(!is_int($status)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($status != module_taskman_task::$STATUS_ACCEPTED) &&
			 ($status != module_taskman_task::$STATUS_NEEDS_ACTION) &&
			 ($status != module_taskman_task::$STATUS_DEFAULT) &&
			 ($status != module_taskman_task::$STATUS_DECLINED) &&
			 ($status != module_taskman_task::$STATUS_COMPLETED) &&
			 ($status != module_taskman_task::$STATUS_CANCELLED) &&
			 ($status != module_taskman_task::$STATUS_DELEGATED)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either STATUS_ACCEPTED, STATUS_NEEDS_ACTION, STATUS_DEFAULT, STATUS_DECLINED, STATUS_COMPLETED, or STATUS_DELEGATED.");
		}else{
			if($this->canWrite() || $this->delegatedTo() == $this->avalanche->loggedInHuh()){
				$to_user_var = "";
				$to_user_val = "";
				$datetime = date("Y-m-d H:i:s", $gmtimestamp);
				if($to_user !== false && is_int($to_user) && $status == module_taskman_task::$STATUS_DELEGATED){
					$to_user_var = ", `to_user_id`";
					$to_user_val = ", '$to_user'";
				}else if($to_user !== false && is_string($to_user) && $status != module_taskman_task::$STATUS_DELEGATED){
					$comment = $to_user;
				}else if($to_user === false){
					// noop
				}else if(!is_int($to_user) && !is_string($to_user)){
					throw new IllegalArgumentException("optional second argument to " . __METHOD__ . " must be an integer or string");
				}

				// update completion time if applicable
				// also, update assignee or delegatee if appropriate
				if($status == module_taskman_task::$STATUS_COMPLETED){
					// update the completion time
					$this->completed($datetime);
					// also update the status
					$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `status`=\"" . $status . "\" WHERE id='" . $this->id . "'";
					$result = $this->avalanche->mysql_query($sql);
				}else if($status == module_taskman_task::$STATUS_DELEGATED){
					$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `status`=\"" . $status . "\", `delegated_to`=\"" . $to_user . "\" WHERE id='" . $this->id . "'";
					$result = $this->avalanche->mysql_query($sql);
					$this->delegated_to = $to_user;
				}else if($status == module_taskman_task::$STATUS_ACCEPTED){
					$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `status`=\"" . $status . "\", `assigned_to`=\"" . $this->delegated_to . "\" WHERE id='" . $this->id . "'";
					$result = $this->avalanche->mysql_query($sql);
					$this->assigned_to = $this->delegated_to;
				}else if($status == module_taskman_task::$STATUS_CANCELLED){
					// update the cancellation time
					$this->cancelled($datetime);
					// also update the status
					$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `status`=\"" . $status . "\" WHERE id='" . $this->id . "'";
					$result = $this->avalanche->mysql_query($sql);
				}else{
					// just update the status
					$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `status`=\"" . $status . "\" WHERE id='" . $this->id . "'";
					$result = $this->avalanche->mysql_query($sql);
				}
				$this->modifiedBy($this->avalanche->getActiveUser());
				// update the status history
				$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_status_history` (`task_id`,`user_id`,`status`, `stamp`$to_user_var, `comment`) VALUES ('" . $this->getId() . "', '" . $this->avalanche->getActiveUser() . "', '$status', '" . $datetime . "'$to_user_val,'" . addslashes($comment) . "')";
				$result = $this->avalanche->mysql_query($sql);
				$this->status = $status;
				// notify of status change
				$this->setModified();
				$this->taskman->taskStatusChanged($this->getId(), $comment);
				return $status;
			}else{
				return $this->status();
			}
		}
	}

	// returns the status history of the task
	public function history(){
		$task_table = $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks";
		$status_table = $this->avalanche->PREFIX() . $this->taskman->folder() . "_status_history";
		$task_id = $this->getId();
		$sql = "SELECT $task_table.id AS task_id, $task_table.author, $status_table.status, $status_table.to_user_id AS assigned_to, $status_table.user_id AS modified_by, $status_table.stamp , $status_table.comment FROM $task_table, $status_table WHERE $task_table.id = $task_id AND $task_table.id = $status_table.task_id ORDER BY $status_table.stamp DESC, $status_table.id DESC";
		$result = $this->avalanche->mysql_query($sql);
		if(mysqli_error($this->avalanche->mysqliLink())){
			throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
		}
		$status = array();
		while($myrow = mysqli_fetch_array($result)){
			// datetime of change
			$myrow["stamp"] = $myrow["stamp"];
			// the author id of the task
			$myrow["author"] = (int) $myrow["author"];
			// the task id
			$myrow["task_id"] = (int) $myrow["task_id"];
			// the id of the user that modified the status
			$myrow["modified_by"] = (int) $myrow["modified_by"];
			// the reciever of the new status (delegatee, assignee, etc)
			$myrow["assigned_to"] = (int) $myrow["assigned_to"];
			// the new status
			$myrow["status"] = (int) $myrow["status"];
			// the comment
			$myrow["comment"] = $myrow["comment"];
			$status[] = $myrow;
		}
		return $status;
	}

	// return or change the status of the task (old status is saved in history)
	public function priority($priority = false){
		if($priority === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->priority;
		}else if(!is_int($priority)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($priority != module_taskman_task::$PRIORITY_HIGH) &&
			 ($priority != module_taskman_task::$PRIORITY_NORMAL) &&
			 ($priority != module_taskman_task::$PRIORITY_LOW)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either PRIORITY_HIGH, PRIORITY_NORMAL, or PRIORITY_LOW.");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->taskman->folder() . "_tasks` SET `priority`=\"" . $priority . "\" WHERE id='" . $this->id . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(mysqli_error($this->avalanche->mysqliLink())){
					throw new DatabaseException(mysqli_error($this->avalanche->mysqliLink()));
				}
				$this->priority = $priority;
				$this->setModified();
				return $priority;
			}else{
				return $this->priority();
			}
		}
	}


	// returns true if the input matches "YYYY-MM-DD HH:MM:SS" format
	public static function isDateTime($var){
		return preg_match("/^(19|20)\d\d-(0[0-9]|1[0-2])-([0-2][0-9]|3[01]) ([01][0-9]|2[0-4]):[0-5][0-9]:[0-5][0-9]\$/", $var);
	}

	// returns true if the logged in user can read this task
	// must be author, assigned, delegated, or read member of calendar
	public function canRead(){
		if(!$this->loaded){
			$this->load();
		}
		return  $this->author() == $this->avalanche->loggedInHuh() ||
			$this->delegatedTo() == $this->avalanche->loggedInHuh() ||
			$this->assignedTo() == $this->avalanche->loggedInHuh() ||
			is_object($this->calendar) && $this->calendar->canReadEntries();
	}

	// returns true if the logged in user can edit this task
	// (must be author, assigned, or admin
	public function canWrite(){
		if(!$this->loaded){
			$this->load();
		}
		return  $this->author() == $this->avalanche->loggedInHuh() ||
			$this->assignedTo() == $this->avalanche->loggedInHuh() ||
			is_object($this->calendar) && $this->calendar->canWriteName();
	}


	// called when a user is deleted from the system. we have to clean up after him.
	function deleteUser($user_id){
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_status_history` WHERE `user_id`='" . $user_id . "' OR `to_user_id`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` WHERE `author`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` SET `delegated_to`='-1' WHERE `delegated_to`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->folder() . "_tasks` SET `assigned_to`='-1' WHERE `assigned_to`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		return true;
	}

	//standard visitor pattern
	function execute(module_taskman_visitor $visitor){
		return $visitor->taskCase($this);
	}



	// notify listeners that i'm edited, and update my modified on date
	protected function setModified(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		$datetime = date("Y-m-d H:i:s", $gmtimestamp);
		$this->modifiedOn($datetime);
		$this->taskman->taskEdited($this->getId());
	}

}
?>
