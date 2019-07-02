<?


class module_reminder_reminder{

	public static $TYPE_SIMPLE = 0;
	public static $TYPE_EVENT = 1;
	public static $TYPE_TASK = 2;
	public static $TYPE_EVENT_ATTENDEES = 4;

	// this reminders's id
	private $id;

	// the reminder module
	private $reminder;

	// bool if reminder is loaded or not
	private $loaded;


	// creates a new notification
	private $avalanche;
	public function __construct($avalanche, $id){
		$this->loaded = false;
		$this->avalanche = $avalanche;
		$this->reminder = $this->avalanche->getModule("reminder");
		if(!is_int($id)){
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be an int");
		}
		$this->users = new HashTable();
		$this->id = $id;
	}

	// returns the id of this notification
	public function getId(){
		return $this->id;
	}


	private $users;
	private $author;
	private $subject;
	private $body;
	private $type;
	private $item;
	private $item_calendar;
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $second;
	private $send_on;
	private function load(){
		if(!$this->loaded){
			$this->users = new HashTable();

			$reminder_id = $this->getId();
			// gets raw data from mysql
			$reminder_table = $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders";
			$outbox_table = $this->avalanche->PREFIX() . $this->reminder->folder() . "_outbox";
			$task_table = $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_task";
			$event_table = $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_event";
			$sql = "SELECT * FROM $reminder_table WHERE `id` = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->author = (int) $myrow["author"];
				$this->subject = $myrow["subject"];
				$this->body = $myrow["body"];
				$this->type = (int) $myrow["type"];
				$this->year = (int) $myrow["year"];
				$this->month = (int) $myrow["month"];
				$this->day = (int) $myrow["day"];
				$this->hour = (int) $myrow["hour"];
				$this->minute = (int) $myrow["minute"];
				$this->second = (int) $myrow["second"];
				$this->send_on = $myrow["send_on"];
				$this->sent_on = $myrow["sent_on"];
			}else{
				throw new Exception("no notification found with id = $notification_id");
			}

			$sql = "SELECT * FROM $outbox_table WHERE `reminder_id` = '$reminder_id'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				$id = (int) $myrow["user_id"];
				$this->users->put($id, $id);
			}

			if($this->type == module_reminder_reminder::$TYPE_EVENT || $this->type == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
				$sql = "SELECT * FROM $event_table WHERE `reminder_id` = '$reminder_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($myrow = mysql_fetch_array($result)){
					$cal = $this->avalanche->getModule("strongcal")->getCalendarFromDb((int) $myrow["cal_id"]);
					$this->item = (int) $myrow["event_id"];
					$this->item_calendar = (int) $myrow["cal_id"];
				}else{
					throw new Exception("no item found for notification found with id = $notification_id");
				}
			}else if($this->type == module_reminder_reminder::$TYPE_TASK){
				$sql = "SELECT * FROM $task_table WHERE `reminder_id` = '$reminder_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($myrow = mysql_fetch_array($result)){
					$this->item = (int) $myrow["task_id"];
				}else{
					throw new Exception("no item found for notification found with id = $notification_id");
				}
			}else{
				$this->item = false;
			}
			$this->loaded = true;
		}
	}

	public function addUser($user_id){
		if($this->canWrite()){
			if(!is_int($user_id)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
			}
			$us = $this->getUsers();
			if(!in_array($user_id, $us)){
				$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_outbox` (`reminder_id`,`user_id`) VALUES ('" . $this->getId() . "','" . $user_id . "')";
				$result = $this->avalanche->mysql_query($sql);
				$this->users->put($user_id, $user_id);
			}
			return true;
		}else{
			throw new Exception("you do not have permission to add users to this reminder");
		}
	}

	public function getUsers(){
		$outbox_table = $this->avalanche->PREFIX() . $this->reminder->folder() . "_outbox";
		$sql = "SELECT * FROM $outbox_table WHERE `reminder_id` = '" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);
		$users = array();
		while($myrow = mysql_fetch_array($result)){
			$users[] = (int)$myrow["user_id"];
		}
		return $users;
	}

	public function removeUser($user_id){
		if($this->canWrite()){
			if(!is_int($user_id)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
			}
			$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_outbox` WHERE `reminder_id` = '" . $this->getId() . "' AND `user_id` = '" . $user_id . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->users->clear($user_id);
			return true;
		}else{
			throw new Exception("you do not have permission to remove users to this reminder");
		}
	}

	// return or change the item of the notification
	public function type($type = false, $item=false){
		if($type === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->type;
		}else if(!is_int($type)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($type != module_reminder_reminder::$TYPE_EVENT_ATTENDEES || $type == module_reminder_reminder::$TYPE_EVENT_ATTENDEES && !$item instanceof module_strongcal_event) &&
		         ($type != module_reminder_reminder::$TYPE_EVENT || $type == module_reminder_reminder::$TYPE_EVENT && !$item instanceof module_strongcal_event) &&
			 ($type != module_reminder_reminder::$TYPE_TASK || $type == module_reminder_reminder::$TYPE_TASK && !$item instanceof module_taskman_task) &&
			 ($type != module_reminder_reminder::$TYPE_SIMPLE || $type == module_reminder_reminder::$TYPE_SIMPLE && $item !== false)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either TYPE_EVENT_ATTENDEES, TYPE_EVENT, TYPE_TASK, or TYPE_SIMPLE, with matching second param.");
		}else{
			if($this->canWrite()){
				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_event` WHERE reminder_id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_task` WHERE reminder_id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `type`=\"" . $type . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				if(is_object($item) && $item instanceof module_strongcal_event){
					$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_event` (`reminder_id`,`cal_id`, `event_id`) VALUES ('" . $this->getId() . "','" . $item->calendar()->getId() . "','" . $item->getId() . "')";
					$result = $this->avalanche->mysql_query($sql);
					$this->item = $item->getId();
					$this->item_calendar = $item->calendar()->getId();
				}else if(is_object($item) && $item instanceof module_taskman_task){
					$sql = "INSERT INTO `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_relation_task` (`reminder_id`,`task_id`) VALUES ('" . $this->getId() . "','" . $item->getId() . "')";
					$result = $this->avalanche->mysql_query($sql);
					$this->item = $item->getId();
				}
				$this->type = $type;
				if($type == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
					$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_outbox` WHERE reminder_id='" . $this->getId() . "'";
					$result = $this->avalanche->mysql_query($sql);
					$attendees = $item->attendees();
					foreach($attendees as $attendee){
						$this->addUser($attendee->userId());
					}
				}
				$this->verify();
				return $type;
			}else{
				return $this->type();
			}
		}
	}

	public function item(){
		if(!$this->loaded){
			$this->load();
		}
		if($this->type == module_reminder_reminder::$TYPE_EVENT || $this->type == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
			$event = $this->avalanche->getModule("strongcal")->getCalendarFromDb($this->item_calendar)->getEvent($this->item);
			return $event;
		}else if($this->type == module_reminder_reminder::$TYPE_TASK){
			return $this->avalanche->getModule("taskman")->getTask($this->item);
		}else{
			return false;
		}
	}

	public function author(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->author;
	}

	public function sendOn(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->send_on;
	}

	public function sentOn(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->sent_on;
	}

	public function year($year = 0){
		if($year === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->year;
		}else if(!is_int($year) || $year < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `year`=\"" . $year . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->year = $year;
			$this->verify();
			return $this->year();
		}else{
			return $this->year();
		}
	}

	public function month($month = 0){
		if($month === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->month;
		}else if(!is_int($month) || $month < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `month`=\"" . $month . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->month = $month;
			$this->verify();
			return $this->month();
		}else{
			return $this->month();
		}
	}

	public function day($day = 0){
		if($day === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->day;
		}else if(!is_int($day) || $day < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `day`=\"" . $day . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->day = $day;
			$this->verify();
			return $this->day();
		}else{
			return $this->day();
		}
	}

	public function hour($hour = 0){
		if($hour === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->hour;
		}else if(!is_int($hour) || $hour < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `hour`=\"" . $hour . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->hour = $hour;
			$this->verify();
			return $this->hour();
		}else{
			return $this->hour();
		}
	}

	public function minute($minute = 0){
		if($minute === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->minute;
		}else if(!is_int($minute) || $minute < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `minute`=\"" . $minute . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->minute = $minute;
			$this->verify();
			return $this->minute();
		}else{
			return $this->minute();
		}
	}

	public function second($second = 0){
		if($second === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (int) $this->second;
		}else if(!is_int($second) || $second < 0){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an positive int");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `second`=\"" . $second . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->second = $second;
			$this->verify();
			return $this->second();
		}else{
			return $this->second();
		}
	}

	public function subject($subject = false){
		if($subject === false){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (string) $this->subject;
		}else if(!is_string($subject)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}else if($this->canWrite()){
			$sql_subject = addslashes($subject);
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `subject`=\"" . $sql_subject . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->subject = $subject;
			return $this->subject();
		}else{
			return $this->subject();
		}
	}

	public function body($body = false){
		if($body === false){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return (string) $this->body;
		}else if(!is_string($body)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}else if($this->canWrite()){
			$sql_body = addslashes($body);
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `body`=\"" . $sql_body . "\", `sent_on`=\"0000-00-00 00:00:00\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->body = $body;
			return $this->body();
		}else{
			return $this->body();
		}
	}

	function canWrite(){
		if(!$this->loaded){
			$this->load();
		}
		return ($this->author() == $this->avalanche->loggedInHuh());
	}

	function reload(){
		$this->loaded = false;
	}


	// this function is called internally when the item of the reminder is reset
	// this function makes sure that the sendOn() value is correctly adjusted to
	// the event/task's start/due date/times (is offset of year()/month()/day() hour():minute():second())
	public function verify(){
		$year = $this->year();
		$month = $this->month();
		$day = $this->day();
		$hour = $this->hour();
		$minute = $this->minute();
		$second = $this->second();
		if($this->type() == module_reminder_reminder::$TYPE_EVENT ||
		   $this->type() == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
			$event = $this->item();
			$edate = $event->getValue("start_date") . " " . $event->getValue("start_time");
			$new_date = $this->adjust($edate);
			$this->setSendOn($new_date);
		}else if($this->type() == module_reminder_reminder::$TYPE_TASK){
			$task = $this->item();
			$tdate = $task->due();
			$new_date = $this->adjustTimezone($tdate);
			$new_date = $this->adjust($new_date);
			$this->setSendOn($new_date);
		}else if($this->type() == module_reminder_reminder::$TYPE_SIMPLE){
			$time = mktime($hour, $minute, $second, $month, $day, $year);
			$date = date("Y-m-d H:i:s", $time);
			$this->setSendOn($date);
		}
	}

	// adjusts a datetime from local to GMT time
	private function adjustTimezone($date){
		$strongcal = $this->avalanche->getModule("strongcal");
		$date = explode(" ", $date);
		$d = $date[0];
		$t = $date[1];
		$d = explode("-", $d);
		$t = explode(":", $t);
		$t[0] -= $strongcal->timezone();
		$time = mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
		$date = date("Y-m-d H:i:s", $time);
		return $date;
	}

	// adjusts a datetime to be offset by hour/min/sec/year/month/day etc.
	private function adjust($date){
		$date = explode(" ", $date);
		$d = $date[0];
		$t = $date[1];
		$d = explode("-", $d);
		$t = explode(":", $t);
		$d[0] -= $this->year();
		$d[1] -= $this->month();
		$d[2] -= $this->day();
		$t[0] -= $this->hour();
		$t[1] -= $this->minute();
		$t[2] -= $this->second();
		$time = mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
		$date = date("Y-m-d H:i:s", $time);
		return $date;
	}

	private function ensureLength($str, $len){
		if(!is_string($str)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a string");
		}
		if(!is_int($len)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an int");
		}
		if(strlen($str) > $len){
			return substr($str, 0, $len);
		}
		while(strlen($str) < $len){
			$str = "0" . $str;
		}
		return $str;
	}

	// resets the send on date to match $send and deletes any sent date
	private function setSendOn($send){
		$strongcal = $this->avalanche->getModule("strongcal");
		if($this->canWrite()){
			// if it's not already been sent
			// or if the new send date is after now
			if($this->sentOn() == "0000-00-00 00:00:00" ||
			$send >= date("Y-m-d H:i:s", $strongcal->gmttimestamp())){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `send_on`=\"" . $send . "\", `sent_on`='0000-00-00 00:00:00' WHERE id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->send_on = $send;
				$this->_setSentOn("0000-00-00 00:00:00");
			}
		}
	}

	public function sendReminder(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$send = date("Y-m-d H:i:s", $strongcal->gmttimestamp());

		$users = $this->getUsers();
		foreach($users as $user){
			$user = $this->avalanche->getUser($user);
			$user->contact($this->avalanche->getUser($this->author()), $this->subject(), $this->body());
		}

		$this->_setSentOn($send);
	}

	private function _setSentOn($send){
		$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->reminder->folder() . "_reminders` SET `sent_on`=\"" . $send . "\" WHERE id='" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);
		$this->sent_on = $send;
	}
}
?>
