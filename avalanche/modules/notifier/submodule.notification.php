<?


class module_notifier_notification{

	//contact_by static values
	public static $CONTACT_EMAIL 		= 1;
	public static $CONTACT_SMS	 	= 2;
	public static $CONTACT_MESSAGE		= 4;
	public static $CONTACT_NONE		= 0;
	public static $CONTACT_EMAIL_SMS	= 3;

	// item static values
	public static $ITEM_EVENT 		= 0;
	public static $ITEM_TASK	 	= 1;
	public static $ITEM_COMMENT		= 2;

	// action static values
	public static $ACTION_ADDED 		= 0;
	public static $ACTION_EDITED	 	= 1;
	public static $ACTION_DELETED		= 2;
	public static $ACTION_STATUS		= 3;
	public static $ACTION_DELEGATED		= 4;
	public static $ACTION_COMPLETED		= 5;
	public static $ACTION_CANCELLED		= 6;

	// this notification's id
	private $id;

	// the notifier module
	private $notifier;

	// bool if notification is loaded or not
	private $loaded;


	// creates a new notification
	private $avalanche;
	public function __construct($avalanche, $id){
		$this->sent_users_cache = new HashTable();
		$this->all = false;
		$this->calendars = array();
		$this->loaded = false;
		$this->avalanche = $avalanche;
		$this->notifier = $this->avalanche->getModule("notifier");
		if(!is_int($id)){
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be an int");
		}
		$this->id = $id;
	}

	// returns the id of this notification
	public function getId(){
		return $this->id;
	}


	private $user_id;
	private $contact_by;
	private $item;
	private $action;
	private $calendars;
	private $all;
	private function load(){
		if(!$this->loaded){
			$this->sent_users_cache = new HashTable();
			$notification_id = $this->getId();
			// gets raw data from mysql
			$notification_table = $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications";
			$status_table = $this->avalanche->PREFIX() . $this->notifier->folder() . "_status_history";
			$sql = "SELECT * FROM $notification_table WHERE `id` = '$notification_id'";
			$result = $this->avalanche->mysql_query($sql);

			if($myrow = mysql_fetch_array($result)){
				$this->user_id = (int) $myrow["user_id"];
				$this->contact_by = (int) $myrow["contact_by"];
				$this->item = (int) $myrow["item"];
				$this->action = (int) $myrow["action"];
				$this->calendars = $myrow["calendars"];
				$this->all = $myrow["all_calendars"];
				$this->parseCalendars();
			}else{
				throw new Exception("no notification found with id = $notification_id");
			}
			$this->loaded = true;
		}
	}

	private function parseCalendars(){
		$cal_ids = array();
		// get ids of calendars
		if(strlen($this->calendars)){
			// format is |##||###||#||#||#|
			// chop off the first |
			$str = substr($this->calendars, 1);
			while(strlen($str)){
				$pos = strpos($str, "|");
				$cal_ids[] = (int) substr($str, 0, $pos);
				$str = substr($str, $pos+2);
			}
		}
		// get each cal from id
		$this->calendars = array();
		foreach($cal_ids as $id){
			$cal = $this->avalanche->getModule("strongcal")->getCalendarFromDb($id);
			if(is_object($cal)){
				$this->calendars[] = $cal;
			}
		}
	}

	// return or change the item of the notification
	public function item($item = false){
		if($item === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->item;
		}else if(!is_int($item)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($item != module_notifier_notification::$ITEM_EVENT) &&
			 ($item != module_notifier_notification::$ITEM_TASK) &&
			 ($item != module_notifier_notification::$ITEM_COMMENT)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either ITEM_EVENT, ITEM_TASK, or ITEM_COMMENT.");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications` SET `item`=\"" . $item . "\" WHERE id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->item = $item;
				return $item;
			}else{
				return $this->item();
			}
		}
	}

	// return or change the contact_by of the notification
	public function contactBy($contact_by = false){
		if($contact_by === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->contact_by;
		}else if(!is_int($contact_by)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($contact_by != module_notifier_notification::$CONTACT_EMAIL) &&
			 ($contact_by != module_notifier_notification::$CONTACT_SMS) &&
			 ($contact_by != module_notifier_notification::$CONTACT_EMAIL_SMS)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either CONTACT_EMAIL, CONTACT_SMS, or CONTACT_EMAIL_SMS.");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications` SET `contact_by`=\"" . $contact_by . "\" WHERE id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->contact_by = $contact_by;
				return $contact_by;
			}else{
				return $this->contactBy();
			}
		}
	}

	// return or change the action of the notification
	public function action($action = false){
		if($action === false){
			if(!$this->loaded){
				$this->load();
			}
			return $this->action;
		}else if(!is_int($action)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be a integer");
		}else if(($action != module_notifier_notification::$ACTION_ADDED) &&
			 ($action != module_notifier_notification::$ACTION_EDITED) &&
			 ($action != module_notifier_notification::$ACTION_STATUS) &&
			 ($action != module_notifier_notification::$ACTION_DELETED) &&
			 ($action != module_notifier_notification::$ACTION_DELEGATED) &&
			 ($action != module_notifier_notification::$ACTION_COMPLETED) &&
			 ($action != module_notifier_notification::$ACTION_CANCELLED)){
			throw new IllegalArgumentException("optional first argument to " . __METHOD__ . " must be either ACTION_ADDED, ACTION_EDITED, ACTION_STATUS, ACTION_DELEGATED, or ACTION_DELETED.");
		}else{
			if($this->canWrite()){
				$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications` SET `action`=\"" . $action . "\" WHERE id='" . $this->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$this->action = $action;
				return $action;
			}else{
				return $this->action();
			}
		}
	}


	public function allCalendarsHuh($allHuh = 0){
		if($allHuh === 0){
			// return our value
			if(!$this->loaded){
				$this->load();
			}
			return $this->all;
		}else if(!is_bool($allHuh)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}else if($this->canWrite()){
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications` SET `all_calendars`=\"" . $allHuh . "\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			$this->all = $allHuh;
			return $this->allCalendarsHuh();
		}else{
			return $this->allCalendarsHuh();
		}
	}

	public function addCalendar($to_add){
		if(!$this->loaded){
			$this->load();
		}
		if(!$this->allCalendarsHuh() && $this->canWrite()){
			$add_ok = true;
			foreach($this->calendars as $cal){
				if($cal->getId() == $to_add->getId()){
					$add_ok = false;
				}
			}
			if($add_ok){
				$this->calendars[] = $to_add;
				$this->storeCalendars();
			}
		}
	}

	private function storeCalendars(){
		if(!$this->loaded){
			$this->load();
		}
		if($this->canWrite()){
			$to_add = "";
			foreach($this->calendars as $cal){
				$to_add .= "|" . $cal->getId() . "|";
			}
			$sql = "UPDATE `" . $this->avalanche->PREFIX() . $this->notifier->folder() . "_notifications` SET `calendars`=\"" . $to_add . "\" WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
		}
	}

	public function getCalendars(){
		if(!$this->loaded){
			$this->load();
		}
		if($this->allCalendarsHuh()){
			$cals = $this->avalanche->getModule("strongcal")->getCalendarList();
			$ret = array();
			foreach($cals as $cal){
				$ret[] = $cal["calendar"];
			}
			return $ret;
		}else{
			return $this->calendars;
		}
	}

	public function removeCalendar($to_rem){
		if(!$this->loaded){
			$this->load();
		}
		if(!$this->allCalendarsHuh() && $this->canWrite()){
			$add_ok = true;
			$new_array = array();
			foreach($this->calendars as $cal){
				if($cal->getId() != $to_rem->getId()){
					$new_array[] = $cal;
				}
			}
			$this->calendars = $new_array;
			$this->storeCalendars();
		}
	}


	// return the owner of this reminder (who we're notifying)
	public function userId(){
		if(!$this->loaded){
			$this->load();
		}
		return $this->user_id;
	}

	function canWrite(){
		if(!$this->loaded){
			$this->load();
		}
		return ($this->userId() == $this->avalanche->loggedInHuh());
	}

	function reload(){
		$this->loaded = false;
	}


	private $sent_users_cache;
	public function contact($from_user, $title, $body){
		if(!is_object($from_user) || !($from_user instanceof avalanche_user)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a user object");
		}
		if(!is_string($title) || strlen($title) == 0){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string of length > 0");
		}
		if(!is_string($body) || strlen($body) == 0){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string of length > 0");
		}
		$user = $this->avalanche->getUser($this->userId());
		if(!$this->sent_users_cache->get($user->getId())){
			$this->sent_users_cache->put($user->getId(),$user);
			if($this->contactBy() == module_notifier_notification::$CONTACT_SMS ||
			   $this->contactBy() == module_notifier_notification::$CONTACT_EMAIL_SMS){
				   $user->contactSMS($from_user, $title, $body);
			}
			if($this->contactBy() == module_notifier_notification::$CONTACT_EMAIL ||
			   $this->contactBy() == module_notifier_notification::$CONTACT_EMAIL_SMS){
				   $user->contactEmail($from_user, $title, $body);
			}
		}else{
			   // don't do anything. i already contacted him...
		}
	}
}
?>
