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


// DEPENDANT ON OS
try{
	$os = $this->getModule("os");
	if(!is_object($os)){
		throw new ClassDefNotFoundException("module_os");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"OS\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"OS\" exiting.";
	exit;
}


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


// DEPENDANT ON FILELOADER
try{
	$fileloader = $this->getModule("fileloader");
	if(!is_object($fileloader)){
		throw new ClassDefNotFoundException("module_fileloader");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Aurora cannot include dependancy \"FILELOADER\" exiting.", E_USER_ERROR);
	echo "Aurora cannot include dependancy \"FILELOADER\" exiting.";
	exit;
}

$fileloader = $this->getModule("fileloader");

$fileloader->include_recursive(ROOT . APPPATH . MODULES . "strongcal/bootstraps/");

// STRONGCAL INCLUDES
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.listener.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.constants.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.fields.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.fieldmanager.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.validation.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.recurrance.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.attendee.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.event.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.attendeeComparator.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.eventAddedComparator.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.calendarAddedComparator.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.eventComparator.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.commentComparator.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.calendar.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.export.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.visitor.php";
include ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.visitormanager.php";

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  MAIN STRONGCAL MODULE  //////////////////////////
///////////////                         //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)

class module_strongcal extends module_template implements avalanche_interface_os{
	//////////////////////////////////////////////////////////////////
	//			PRIVATE VARIABLES			//
	//	do not directly reference these variables		//
	protected $_name;							//
	protected $_version;							//
	protected $_desc;							//
	protected $_folder;							//
	protected $_copyright;						//
	protected $_author;							//
	protected $_date;							//

	private $_field_manager;
	private $_timezone;
	// an array representing which calendars are selected.
	// only selected calendars are in the array.
	private $_selected;
	// the list of calendars we've cached
	private $_calendars;
	// the runtime Id for the sprocket
	private $_runtimeId;
	// the array of listeners
	private $_listeners;
	//								//
	//////////////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// for avalanche_interface_sprocket				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// visitor pattern						//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////

	//standard visitor pattern
	function execute($visitor){
		return $visitor->moduleCase($this);
	}



	//////////////////////////////////////////////////////////////////
	//  select($cal)						//
	//	tags the calendar as selected				//
	//--------------------------------------------------------------//
	//  input: $cal - the id of the calendar data to initialze to	//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function select($cal){

		if(is_object($cal)){
			$cal = $cal->getId();
		}

		$selected = $this->getUserVar("selected_calendars");
		$selected = explode(",", $selected);
		$val = "";
		$duplicate = false;
		for($i=0;$i<count($selected);$i++){
			if(strlen($selected[$i])){
				if($cal != $selected[$i]){
					if($val){
						$val .= ",";
					}
					$val .= $selected[$i];
				}else{
					$duplicate = true;
				}
			}
		}
		if($val){
			$val .= ",";
		}
		$val .= $cal;

		$this->setUserVar("selected_calendars", $val);
	}


	//////////////////////////////////////////////////////////////////
	//  unselect($cal)						//
	//	tags the calendar as unselected				//
	//--------------------------------------------------------------//
	//  input: $cal - the id of the calendar data to initialze to	//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function unselect($cal){

		if(is_object($cal)){
			$cal = $cal->getId();
		}

		$selected = $this->getUserVar("selected_calendars");
		$selected = explode(",", $selected);
		$val = "";
		$my_index = false;
		for($i=0;$i<count($selected);$i++){
			if($val){
				$val .= ",";
			}
			if($cal != $selected[$i]){
				$val .= $selected[$i];
			}else{
				$my_index = $i;
			}
		}


		$this->setUserVar("selected_calendars", $val);
	}


	//////////////////////////////////////////////////////////////////
	//  selected($cal)						//
	//	returns if the calendar is selected			//
	//--------------------------------------------------------------//
	//  input: $cal - the id of the calendar data to initialze to	//
	//  output: void						//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function selected($cal){

		if(is_object($cal)){
			$cal = $cal->getId();
		}

		$selected = $this->getUserVar("selected_calendars");
		$selected = explode(",", $selected);
		$duplicate = false;
		for($i=0;$i<count($selected);$i++){
			if($cal == $selected[$i]){
				$duplicate = true;
			}
		}
		return $duplicate;
	}


	public function avalanche(){
		return $this->avalanche;
	}

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
		$this->user_preference_cache = new HashTable();
		$this->user_has_preferences = new HashTable();
		$this->_runtimeId = 1;
		if(isset($_COOKIE[$this->avalanche->PREFIX() . 'selected_calendars'])){
			$this->_selected = explode(",", $_COOKIE[$this->avalanche->PREFIX() . 'selected_calendars']);
		}else{
			$this->_selected = array();
		}
		$this->_name = "Aurora";
		$this->_version = "1.0.0";
		$this->_desc = "Calendar Module.";
		$this->_folder = "strongcal";
		$this->_copyright = "Copyright 2003 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "01-07-03";
		$this->_field_manager = new module_strongcal_fieldmanager($this->avalanche);
		$this->_visitor_manager = new module_strongcal_visitormanager($this->avalanche);
		$this->_timezone = $this->getUserVar("timezone");
		$this->_calendars = new HashTable();

		$timezone = $this->timezone();
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$localyear  = gmdate("Y", time());
		$localmonth = gmdate("m", time());
		$localday = gmdate("d", time());
		$localhour = gmdate("H", time()) + $hour_offset + date("I", mktime(0,0,0,$localmonth, $localday, $localyear)); // includes daylight savings!
		$localminute = gmdate("i", time()) + $min_offset;
		$localsecond = gmdate("s", time());
		$localtimestamp = mktime($localhour, $localminute, $localsecond, $localmonth, $localday, $localyear);

		$this->_localtimestamp = $localtimestamp;
		$this->_listeners = array();
	}



	//////////////////////////////////////////////////////////////////
	//  reload()							//
	//	the module has reloaded from database			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none				 		//
	//								//
	//////////////////////////////////////////////////////////////////
	function reload(){
		$this->_field_manager = new module_strongcal_fieldmanager($this->avalanche);
		$this->_visitor_manager = new module_strongcal_visitormanager($this->avalanche);
		$this->_timezone = $this->getUserVar("timezone");
		$this->_calendars = new HashTable();
	}



	//////////////////////////////////////////////////////////////////
	//  timezone()							//
	//	returns the timezone of this calendar			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the timezone of this calendar			//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function timezone($timezone=false){
		if($timezone === false){
			return (double)$this->_timezone;
		}else{
			$this->setUserVar("timezone", $timezone);
			$this->_timezone = (integer)$this->getUserVar("timezone");
			return true;
		}
	}


	// puts timezone into effect
	function adjust($date, $time, $timezone = false){
		if($timezone === false){
			$timezone = $this->timezone();
		}
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$year  = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day   = substr($date, 8, 2);
		$hour  = substr($time, 0, 2);
		$min   = substr($time, 3, 2);
		$sec   = substr($time, 6, 2);
		$stamp = mktime($hour + $hour_offset, $min + $min_offset, $sec, $month, $day, $year);
		$date = @date("Y-m-d", $stamp);
		$time = @date("H:i:s", $stamp);
		return array("date" => $date, "time" => $time);
	}

	// takes timezone effect off
	function adjust_back($date, $time, $timezone = false){
		if($timezone === false){
			$timezone = $this->timezone();
		}
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$year  = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day   = substr($date, 8, 2);
		$hour  = substr($time, 0, 2);
		$min   = substr($time, 3, 2);
		$sec   = substr($time, 6, 2);
		$stamp = mktime($hour - $hour_offset, $min - $min_offset, $sec, $month, $day, $year);
		$date = @date("Y-m-d", $stamp);
		$time = @date("H:i:s", $stamp);
		return array("date" => $date, "time" => $time);
	}


	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// returns the timestamp for the users local time		//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function localtimestamp(){
		return $this->_localtimestamp;
	}

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// returns the timestamp for gmt				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function gmttimestamp(){
		$timezone = $this->timezone();
		$stamp = $this->localtimestamp();
		$dt = date("H:i:s", $stamp);
		$dd = date("Y-m-d", $stamp);;
		$d = $this->adjust_back($dd, $dt, $timezone);
		$dd = $d["date"];
		$dt = $d["time"];
		return mktime(substr($dt, 0, 2), substr($dt, 3, 2), substr($dt, 6, 2), substr($dd, 5, 2), substr($dd, 8, 2), substr($dd, 0, 4));
	}


	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// returns the contents of a user var				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function getUserVar($var, $user_id=false){
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}else if(!is_int($user_id)){
			throw new IllegalArgumentException("2nd optional argument to " . __METHOD__ . " must be an int");
		}
		$table = $this->avalanche->PREFIX() . "strongcal_preferences";
		if($this->avalanche->loggedInHuh()){
			if(!is_object($this->user_preference_cache->get($user_id))){
				$this->user_preference_cache->put($user_id, new HashTable());
				$sql = "SELECT * FROM $table WHERE user_id='$user_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($myrow = mysql_fetch_array($result)){
					$this->user_preference_cache->get($user_id)->put("highlight", $myrow["highlight"]);
					$this->user_preference_cache->get($user_id)->put("timezone",  $myrow["timezone"]);
					$this->user_preference_cache->get($user_id)->put("selected_calendars", $myrow["selected_calendars"]);
					$this->user_preference_cache->get($user_id)->put("day_start", $myrow["day_start"]);
					$this->user_preference_cache->get($user_id)->put("day_end", $myrow["day_end"]);
					$this->user_has_preferences->put($user_id, true);
				}else{
					// get value for SYSTEM
					$sql = "SELECT * FROM $table WHERE user_id='-1'";
					$result = $this->avalanche->mysql_query($sql);
					if($myrow = mysql_fetch_array($result)){
						if(isset($myrow["highlight"]))
							$this->user_preference_cache->get($user_id)->put("highlight", $myrow["highlight"]);
						if(isset($myrow["timezone"]))
							$this->user_preference_cache->get($user_id)->put("timezone",  $myrow["timezone"]);
						if(isset($myrow["selected_calendars"]))
							$this->user_preference_cache->get($user_id)->put("selected_calendars", $myrow["selected_calendars"]);
						if(isset($myrow["day_start"]))
							$this->user_preference_cache->get($user_id)->put("day_start", substr($myrow["day_start"],0,5));
						if(isset($myrow["day_end"]))
							$this->user_preference_cache->get($user_id)->put("day_end", substr($myrow["day_end"],0,5));
					}else{
						throw new Exception("cannot load preference for SYSTEM");
					}
				}
			}
			if($this->user_preference_cache->get($user_id)->get($var)){
				return $this->user_preference_cache->get($user_id)->get($var);
			}
		}else{
			if(isset($_COOKIE[$var])){
				return $_COOKIE[$var];
			}else{
				// get value for SYSTEM
				$sql = "SELECT $var FROM $table WHERE user_id='-1'";
				$result = $this->avalanche->mysql_query($sql);
				if($myrow = mysql_fetch_array($result)){
					return $myrow[$var];
				}else{
					throw new Exception("cannot load preference for SYSTEM");
				}
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// sets the contents of a user var				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	private $user_preference_cache;
	private $user_has_preferences;
	function setUserVar($var, $val, $user_id=false){
		if($user_id === false){
			$user_id = $this->avalanche->getActiveUser();
		}else if(!is_int($user_id)){
			throw new IllegalArgumentException("3rd optional argument to " . __METHOD__ . " must be an integer");
		}
		if($this->avalanche->loggedInHuh()){
			$table = $this->avalanche->PREFIX() . "strongcal_preferences";
			$ans = false;
			if(!$this->user_has_preferences->get($user_id)){
				$sql = "SELECT COUNT(*) AS count FROM $table WHERE user_id='$user_id'";
				$result = $this->avalanche->mysql_query($sql);
				$myrow = mysql_fetch_array($result);
				$ans = $myrow["count"];
				$this->user_preference_cache->put($user_id, new HashTable());
				$this->user_has_preferences->put($user_id, true);
			}else{
				$ans = is_object($this->user_preference_cache->get($user_id));
			}
			if($ans){
				$sql = "UPDATE $table SET $var='$val' WHERE user_id='$user_id'";
				$this->avalanche->mysql_query($sql);
			}else{
				$sql = "INSERT INTO $table (user_id,$var) VALUES ('$user_id','$val')";
				$this->avalanche->mysql_query($sql);
			}
			$this->user_preference_cache->get($user_id)->put($var, $val);
		}else{
			$this->avalanche->setCookie($var, $val);
		}
	}

	function ipOk($ip = false, $ips=false, $bans = false){

		if(!$ips){
			$ips = $this->getVar("ip_filter");
		}
		if($ips){
			$ips = explode("\n", $ips);
		}else{
			$ips = array();
		}

		if(!$bans){
			$bans = $this->getVar("ip_ban");
		}
		if($bans){
			$bans = explode("\n", $bans);
		}else{
			$bans = array();
		}

		$ip_ok = false;
		if(!$ip){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
			$value = $ip;
			$my_area_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$my_pre_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$my_post_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$my_last_val = trim(substr($value,0));

		if(!count($ips)){
			$ip_ok = true;
		}

		for($i=0;$i<count($ips);$i++){
			$value = $ips[$i];
			$area_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$pre_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$post_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$last_val = trim(substr($value,0));
			if(($area_val == $my_area_val || !ereg("^([0-9])+$", $area_val)) &&
			   ($pre_val  == $my_pre_val  || !ereg("^([0-9])+$", $pre_val)) &&
			   ($post_val == $my_post_val || !ereg("^([0-9])+$", $post_val)) &&
			   ($last_val == $my_last_val || !ereg("^([0-9])+$", $last_val))){
				$ip_ok = true;
			}
		}

		for($i=0;$i<count($bans);$i++){
			$value = $bans[$i];
			$area_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$pre_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$post_val = trim(substr($value,0,strpos($value, ".")));
			$value = substr($value, strpos($value, ".")+1);
			$last_val = trim(substr($value,0));
			if(($area_val == $my_area_val || !ereg("^([0-9])+$", $area_val)) &&
			   ($pre_val  == $my_pre_val  || !ereg("^([0-9])+$", $pre_val)) &&
			   ($post_val == $my_post_val || !ereg("^([0-9])+$", $post_val)) &&
			   ($last_val == $my_last_val || !ereg("^([0-9])+$", $last_val))){
				$ip_ok = false;
			}
		}
		return $ip_ok;
	}


	// cache of the variable list
	private $var_list = false;

	function getVar($var){
		// if the var list has not been cached... go find it
		if($this->var_list === false){
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_varlist";
			$result = $this->avalanche->mysql_query($sql);
			$this->var_list = array();
			while($row = mysql_fetch_array($result)){
				if(get_magic_quotes_runtime()){
					$row['val'] = stripslashes($row['val']);
					$row['dflt'] = stripslashes($row['dflt']);
				}
				$row[$var] = array("val" => $row["val"], "dflt" => $row["dflt"]);
			}
		}
		if(isset($this->var_list[$var]) && is_array($this->var_list[$var])){
			return $this->var_list[$var]["val"];
		}else{
			return false;
		}
	}

	function getVarDefault($var){
		// if the var list has not been cached... go find it
		if($this->var_list === false){
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_varlist";
			$result = $this->avalanche->mysql_query($sql);
			$this->var_list = array();
			while($row = mysql_fetch_array($result)){
				if(get_magic_quotes_runtime()){
					$row['val'] = stripslashes($row['val']);
					$row['dflt'] = stripslashes($row['dflt']);
				}
				$row[$var] = array("val" => $row["val"], "dflt" => $row["dflt"]);
			}
		}
		if(isset($this->var_list[$var]) && is_array($this->var_list[$var])){
			return $this->var_list[$var]["dflt"];
		}else{
			return false;
		}
	}

	function setVar($var, $val){
		if($this->ipOk()){
			$this->var_list = false;
			$val = addslashes($val);
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_varlist SET val='$val' WHERE var='$var'";
			$result = $this->avalanche->mysql_query($sql);
			if(mysql_affected_rows()){
				return true;
			}else{
				return false;
			}
		}
	}

	function setVarDefault($var, $val){
		if($this->ipOk()){
			$this->var_list = false;
			$val = addslashes($val);
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_varlist SET dflt='$val' WHERE var='$var'";
			$result = $this->avalanche->mysql_query($sql);
			if(mysql_affected_rows()){
				return true;
			}else{
				return false;
			}
		}
	}





	//////////////////////////////////////////////////////////////////
	//								//
	//	ABOVE ARE FUNCTIONS PERTAINING TO THIS			//
	//		FILE'S CREATION AND OWNER			//
	//								//
	//////////////////////////////////////////////////////////////////


	function fieldManager(){
		return $this->_field_manager;
	}


	function visitorManager(){
		return $this->_visitor_manager;
	}

	function addUsergroup($usergroupid, $verify){
	//////////////////////////////////////////////////////////////////
	//  addUser()							//
	//--------------------------------------------------------------//
	//  input: $usergroup - the usergroup to add to this module	//
	//  input: $verify    - true if the usergroup was successfully	//
	//		      - added to avalanche, false otherwise	//
	//  output: boolean   - true if the user has been		//
	//			 successfully added			//
	//								//
	//  called everytime a new usergroup is added to the system	//
	//								//
	//////////////////////////////////////////////////////////////////
		if($verify){
			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . $this->folder() . "_permissions (`usergroup`) VALUES ('$usergroupid')";
        		$result = $this->avalanche->mysql_query($sql);

			return $result;
		}else{
			return false;
		}
	}

	function deleteUsergroup($usergroupid){
	//////////////////////////////////////////////////////////////////
	//  deleteUsergroup()						//
	//--------------------------------------------------------------//
	//  input: $usergroupid - the usergroup id to delete		//
	//			  from this module			//
	//  output: boolean - true if the usergroup has been		//
	//			  successfully deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
		$sql = "DELETE FROM " . $this->avalanche->PREFIX() . $this->folder() . "_permissions WHERE `usergroup` = '$usergroupid'";
       		$result = $this->avalanche->mysql_query($sql);

		$sql = "SELECT id FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_calendars`";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_cal_" . $id . "_fields` WHERE `usergroup`='" . $usergroupid . "'";
			$this->avalanche->mysql_query($sql);
		}
		return $result;
	}


	function deleteUser($user_id){
		$sql = "DELETE FROM " . $this->avalanche->PREFIX() . $this->folder() . "_permissions WHERE `usergroup` = '" . (-$user_id) . "'";
       		$result = $this->avalanche->mysql_query($sql);

		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_attendees` WHERE `user_id`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		// clear user preference cache
		$this->user_preference_cache->put($user_id, false);
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_preferences` WHERE `user_id`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);

		$sql = "SELECT id FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_calendars` WHERE `author`='" . $user_id . "'";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$this->_removeCalendar($id);
		}
		$sql = "SELECT id FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_calendars` WHERE 1";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			$id = (int) $myrow["id"];
			$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_cal_$id` WHERE `author`='" . $user_id . "'";
			$this->avalanche->mysql_query($sql);
			$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_cal_" . $id . "_comments` WHERE `author`='" . $user_id . "'";
			$this->avalanche->mysql_query($sql);
			$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . $this->folder() . "_cal_" . $id . "_fields` WHERE `user`='" . $user_id . "'";
			$this->avalanche->mysql_query($sql);
		}
		return true;
	}


	//////////////////////////////////////////////////////////////////
	//  getCalendarList()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id and names of installed calendars		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
	function getCalendarList(){
		$ret = array();
		if($this->ipOk()){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_calendars WHERE 1 ORDER BY name";
			$result = $this->avalanche->mysql_query($sql);
			$rows = array();
			$count = 0;
			while($myrow = mysql_fetch_array($result)){
				$count++;
				$id = $myrow['id'];
				$calendar = $this->_getCalendarFromDb($id, $myrow);
				if(is_object($calendar) && $calendar->canReadName()){
					$ret[] = array( "id" => $myrow['id'],
						"color" => $myrow['color'],
						"author" => $myrow['author'],
						"public" => $myrow['public'],
						"calendar" => $calendar);
				}
			}
		}
		return $ret;
	}

	// returns a list of all calendars that match all of the given terms (separated by " ")
	function getAllCalendarsMatching($text){
		$ret = array();
		$text = addslashes($text);

		$like = "1 ";
		$texts = explode(" ", $text);
		foreach($texts as $text){
			$like .= "AND ((name LIKE '%$text%') OR (description LIKE '%$text%'))";
		}
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_calendars WHERE $like";
	        $result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException(mysql_error());
		}
	        while ($myrow = mysql_fetch_array($result)) {
			$cal = $this->_getCalendarFromDb((int)$myrow["id"], $myrow);
			if($cal->canReadName()){
				$ret[] = $cal;
			}
	        }
	        return $ret;
	}

	//////////////////////////////////////////////////////////////////
	//  getCalendarListEssentials()					//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id and names of installed calendars		//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
	function getCalendarListEssentials(){
		$ret = array();
		if($this->ipOk()){
			$user_id = $this->avalanche->getActiveUser();

			$cals = "";
			$sql = "SELECT DISTINCT cal_id FROM `" . $this->avalanche->PREFIX() . "strongcal_attendees` WHERE user_id='$user_id'";
			$result = $this->avalanche->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				$cals .= " OR ";
				$cals .= " `" . $this->avalanche->PREFIX() . "strongcal_calendars`.id='" . $myrow["cal_id"] . "'";
			}

			$usergroups = $this->avalanche->getAllUsergroupsFor($user_id);
			$usergroup_sql = "0";
			$usergroup_sql .= $cals;
			for($i=0;$i<count($usergroups);$i++){
				$usergroup_sql .= " OR ";
				$usergroup_sql .= "`" . $this->avalanche->PREFIX() . "strongcal_permissions`.usergroup = '" . $usergroups[$i]->getId() . "'";
			}

			$sql = "SELECT `" . $this->avalanche->PREFIX() . "strongcal_calendars`.id AS cal_id,
				`" . $this->avalanche->PREFIX() . "strongcal_calendars`.name,
				`" . $this->avalanche->PREFIX() . "strongcal_calendars`.color,
				`" . $this->avalanche->PREFIX() . "strongcal_calendars`.author,
				`" . $this->avalanche->PREFIX() . "strongcal_calendars`.public,
				`" . $this->avalanche->PREFIX() . "strongcal_permissions`.*
				FROM
				`" . $this->avalanche->PREFIX() . "strongcal_calendars`,
				`" . $this->avalanche->PREFIX() . "strongcal_permissions`
				WHERE
				$usergroup_sql ORDER BY `" . $this->avalanche->PREFIX() . "strongcal_calendars`.name";
			$result = $this->avalanche->mysql_query($sql);
			$ret = array();
			$cals = array();
			while($myrow = mysql_fetch_array($result)){
				$cal_id = $myrow['cal_id'];
				$field = "cal_" . $cal_id . "_name";
				if(($myrow[$field] == "r" || $myrow[$field] == "rw") && $myrow['public'] || $this->avalanche->loggedInHuh() == $myrow['author']){
					if(!in_array($cal_id, $cals)){
						$cals[] = $cal_id;
						$ret[] = array(  "id" => $myrow['cal_id'],
								 "name" => $myrow['name'],
								 "author" => $myrow['author'],
								 "public" => $myrow['public'],
								 "color" => $myrow['color']);
					}
				}
			}
		}
		return $ret;
	}


	//////////////////////////////////////////////////////////////////
	//  getCalendarFromDb($id)					//
	//--------------------------------------------------------------//
	//  input: $id - which calendar to get				//
	//  output: creates a calendar object initialized with the data	//
	//          from calendar $id					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
	function getCalendarFromDb($id){
		if($id && $this->ipOk()){
			$val = $this->_calendars->get($id);
			if(is_object($val)){
				return $val;
			}
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_calendars WHERE id='$id'";
			$result = $this->avalanche->mysql_query($sql);
			if($row = mysql_fetch_array($result)){
				$ret = $this->_getCalendarFromDb($id, $row);
				if($ret->canReadName()){
					return $ret;
				}else{
					// i'm not allowed to see the calendar
					return false;
				}
			}else{
				// the calendar is not in the database
				return false;
			}
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  getCalendarFromDb($id)					//
	//--------------------------------------------------------------//
	//  input: $id - which calendar to get				//
	//  output: creates a calendar object initialized with the data	//
	//          from calendar $id					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
	private function _getCalendarFromDb($id, $row){
		$ret = false;
		if(is_object($this->_calendars->get($id))){
			$ret = $this->_calendars->get($id);
		}else{
			$ret = new module_strongcal_calendar($id, $this->avalanche, $row);
			$this->_calendars->put($id, $ret);
		}

		return $ret;
	}

	//////////////////////////////////////////////////////////////////
	//  addCalendar($name_of_cal, $group)				//
	//--------------------------------------------------------------//
	//  input: $name_of_cal - the name of the the calendar		//
	//  group: $group - the id of the group to default as admin	//
	//  output: true if the calendar was successfully created,	//
	//		false otherwise					//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//////////////////////////////////////////////////////////////////
	function addCalendar($name_of_cal, $group=false){
		if(strlen($name_of_cal) == 0){
			throw new IllegalArgumentException("Cannot create a calendar without a name");
		}else
		if(!$this->canAddCalendar()){
			throw new Exception("You do not have permission to add calendars");
		}else
		if(strlen($name_of_cal) > 0 && $this->ipOk()){
			$datetime = date("Y-m-d H:i:s", $this->gmttimestamp());
			$tablename = $this->avalanche->PREFIX() . "strongcal_calendars";
			$sql = "INSERT INTO `$tablename` (`id`,`name`,`color`,`author`,`public`,`added_on`) VALUES ('','" . addslashes($name_of_cal) . "','#FFFFFF', '" . $this->avalanche->loggedInHuh() . "', '', '$datetime')";
	        	$result = $this->avalanche->mysql_query($sql);

			if(mysql_error()){
				throw new DatabaseException("trying to add calendar, but mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$newid = mysql_insert_id();


			/*
			 * create all the tables for that calendar
			 */

			$sql_table = "CREATE TABLE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . " (
					  id mediumint(9) NOT NULL auto_increment,
					  author tinytext NOT NULL, recur_id mediumint(9) NOT NULL default '0',
					  `added_on` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
					  `all_day` TINYINT DEFAULT '0' NOT NULL,
					  `has_comments` MEDIUMINT DEFAULT '0' NOT NULL,
					  start_date date NOT NULL default '0000-00-00',
					  end_date date NOT NULL default '0000-00-00',
					  title text NOT NULL,
					  start_time time NOT NULL default '00:00:00',
					  end_time time NOT NULL default '00:00:00',
					  description text NOT NULL,
					  priority text NOT NULL,
					  PRIMARY KEY  (id)) TYPE=MyISAM;";

	        	$result = $this->avalanche->mysql_query($sql_table);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql_table = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "` ADD INDEX(`start_time`);";
	        	$result = $this->avalanche->mysql_query($sql_table);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql_table = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "` ADD INDEX(`end_time`);";
	        	$result = $this->avalanche->mysql_query($sql_table);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql_table = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "` ADD INDEX(`start_date`);";
	        	$result = $this->avalanche->mysql_query($sql_table);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql_table = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "` ADD INDEX(`end_date`);";
	        	$result = $this->avalanche->mysql_query($sql_table);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql_fields = "CREATE TABLE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields (
					  id mediumint(9) NOT NULL auto_increment,
					  prompt text NOT NULL,
					  field text NOT NULL,
					  type text NOT NULL,
					  value text NOT NULL,
					  size mediumint(9) NOT NULL default '0',
					  style mediumint(9) NOT NULL default '0',
					  valid tinyint(4) NOT NULL default '0',
					  form_order tinyint(4) NOT NULL default '0',
					  user tinyint(4) NOT NULL default '0',
					  usergroup tinyint(4) NOT NULL default '0',
					  removeable tinyint(4) NOT NULL default '1',
					  ics mediumint(9) NOT NULL default '0',
					  PRIMARY KEY  (id)) TYPE=MyISAM;";
	        	$result = $this->avalanche->mysql_query($sql_fields);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}


			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (1, 'Start Date:', 'start_date', 'date', '0000-00-001', 0, 0, 0, 1, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (2, 'Start Time:', 'start_time', 'time', '01:001', 15, 0, 0, 2, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (3, 'End Date:', 'end_date', 'date', '0000-00-001', 0, 0, 0, 3, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (4, 'End Time:', 'end_time', 'time', '01:001', 15, 0, 0, 4, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (5, 'Title:', 'title', 'text', '', 0, 0, 0, 5, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (6, 'Description:', 'description', 'largetext', '', 0, 0, 0, 6, 0, 0, 0, 0);";
		       	$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_fields VALUES (7, 'Priority:', 'priority', 'select', 'High\\nHigh\\n\\nNormal\\nNormal\\n1\\nLow\\nLow\\n', 0, 0, 0, 7, 0, 0, 0, 0);";
			$result = $this->avalanche->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}


			$sql_recur = "CREATE TABLE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_recur (
			  id mediumint(9) NOT NULL auto_increment,
			  start_time time NOT NULL default '00:00:00',
			  end_time time NOT NULL default '00:00:00',
			  start_date date NOT NULL default '0000-00-00',
			  end_type mediumint(9) NOT NULL default '0',
			  end_after mediumint(9) NOT NULL default '0',
			  end_date date NOT NULL default '0000-00-00',
			  recur_type mediumtext NOT NULL,
			  day_count mediumint(9) NOT NULL default '0',
			  week_count mediumint(9) NOT NULL default '0',
			  week_days text NOT NULL,
			  month_type tinyint(4) NOT NULL default '0',
			  month_day mediumint(9) NOT NULL default '0',
			  month_week mediumint(9) NOT NULL default '0',
			  month_weekday mediumint(9) NOT NULL default '0',
			  month_months mediumint(9) NOT NULL default '0',
			  year_type tinyint(4) NOT NULL default '0',
			  year_m mediumint(9) NOT NULL default '0',
			  year_day mediumint(9) NOT NULL default '0',
			  year_week mediumint(9) NOT NULL default '0',
			  year_weekday mediumint(9) NOT NULL default '0',
			  last_entry_date date NOT NULL default '0000-00-00',
			  PRIMARY KEY  (id)
			) TYPE=MyISAM;";
		       	$result = $this->avalanche->mysql_query($sql_recur);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}




			$sql_varlist = "CREATE TABLE " . $this->avalanche->PREFIX() . "strongcal_cal_" . $newid . "_varlist (
			  id mediumint(9) NOT NULL auto_increment,
			  var text NOT NULL,
			  val text NOT NULL,
			  dflt text NOT NULL,
			  PRIMARY KEY  (id)
			) TYPE=MyISAM;";
		       	$result = $this->avalanche->mysql_query($sql_varlist);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$field = "cal_" . $newid . "_entry";
			$sql_permissions = "ALTER TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_permissions` ADD `$field` TINYTEXT NOT NULL";
		       	$result = $this->avalanche->mysql_query($sql_permissions);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$field = "cal_" . $newid . "_field";
			$sql_permissions = "ALTER TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_permissions` ADD `$field` TINYTEXT NOT NULL";
		       	$result = $this->avalanche->mysql_query($sql_permissions);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$field = "cal_" . $newid . "_validation";
			$sql_permissions = "ALTER TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_permissions` ADD `$field` TINYTEXT NOT NULL";
		       	$result = $this->avalanche->mysql_query($sql_permissions);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$field = "cal_" . $newid . "_name";
			$sql_permissions = "ALTER TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_permissions` ADD `$field` TINYTEXT NOT NULL";
		       	$result = $this->avalanche->mysql_query($sql_permissions);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$field = "cal_" . $newid . "_comments";
			$sql_permissions = "ALTER TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_permissions` ADD `$field` TINYTEXT NOT NULL";
		       	$result = $this->avalanche->mysql_query($sql_permissions);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			if($group){
				$cal = "cal_" . $newid . "_name";
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_permissions SET $cal = 'rw' WHERE usergroup='$group'";
				$result= $this->avalanche->mysql_query($sql);
				if(mysql_error()){
					throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
				}
			}

			$sql_comments = "CREATE TABLE `" . $this->avalanche->PREFIX() . $this->folder() . "_cal_" . $newid . "_comments` (
			`id` MEDIUMINT NOT NULL AUTO_INCREMENT,
			`event_id` MEDIUMINT NOT NULL ,
			`author` MEDIUMINT NOT NULL ,
			`post_date` DATETIME NOT NULL ,
			`title` TINYTEXT NOT NULL ,
			`body` MEDIUMTEXT NOT NULL ,
			PRIMARY KEY ( `id` )
			);";
		       	$result = $this->avalanche->mysql_query($sql_comments);
			if(mysql_error()){
				throw new DatabaseException("trying to add calendar id # $newid, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
			}

			$this->calendarAdded((int)$newid);
			return (int) $newid;
		}else{
			return false;
		}
	}

	private function _removeCalendar($id){
		// get the calendar to ensure it's real
		$cal = $this->getCalendarFromDb($id);
		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . "strongcal_calendars` WHERE `id` = '$id'";
		$result = $this->avalanche->mysql_query($sql);

		$sql = "DELETE FROM `" . $this->avalanche->PREFIX() . "strongcal_attendees` WHERE `cal_id` = '$id'";
		$result = $this->avalanche->mysql_query($sql);

		$sql = "DROP TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $id . "`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "DROP TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $id . "_fields`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "DROP TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $id . "_recur`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "DROP TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $id . "_varlist`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "DROP TABLE `" . $this->avalanche->PREFIX() . "strongcal_cal_" . $id . "_comments`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_permissions` DROP `cal_" . $id . "_entry`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_permissions` DROP `cal_" . $id . "_field`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_permissions` DROP `cal_" . $id . "_validation`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_permissions` DROP `cal_" . $id . "_name`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$sql = "ALTER TABLE `" . $this->avalanche->PREFIX() . "strongcal_permissions` DROP `cal_" . $id . "_comments`";
		$result = $this->avalanche->mysql_query($sql);
		if(mysql_error()){
			throw new DatabaseException("trying to remove calendar id # $id, mysql returned with: " . mysql_error() . " with sql command \"$sql\"");
		}

		$this->_calendars->clear($id);
		$this->calendarDeleted((int)$id);
		return true;
	}

	public function removeCalendar($id){
		$cal = $this->getCalendarFromDb($id);
		if($this->ipOk() && $cal->canWriteName()){
			return $this->_removeCalendar($id);
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  canAddCalendar()						//
	//--------------------------------------------------------------//
	//  output: boolean   - returns 1/true if the logged in user	//
	//			can add calendars, 0/false otherwise.	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	none							//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
	function canAddCalendar($usergroups = false){
		if(!$this->ipOk()){
			return false;
		}


		$final = "";
		if(!is_array($usergroups) || count($usergroups) == 0){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		for($i=0;$i<count($usergroups);$i++){
			if(strlen($final)>0){
				$final .= " OR ";
			}
			$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
		$result = $this->avalanche->mysql_query($sql);

		$can_read = 0;
		$field = "add_calendar";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] != 0 && $myrow[$field] > $can_read && $can_read != -1){
				$can_read = $myrow[$field];
			}else
			if($myrow[$field] == -1){
				$can_read = -1;
			}
		}

		$vm = $this->visitorManager();
		$calsbyauthor = $vm->getVisitor("calsbyauthor");
		$calsbyauthor->init($this->avalanche->loggedInHuh());
		$cals = $this->execute($calsbyauthor);

		if($can_read == -1 ||
		   $can_read > $cals){
			return true;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////
	//  canDeleteCalendar()						//
	//--------------------------------------------------------------//
	//  output: boolean   - returns 1/true if the logged in user	//
	//			can add calendars, 0/false otherwise.	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	none							//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
	function canDeleteCalendar(){
		if(!$this->ipOk()){
			return false;
		}

		if(!is_array($usergroups) || count($usergroups) == 0){
			$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
		}
		for($i=0;$i<count($usergroups);$i++){
			if(strlen($final)>0){
				$final .= " OR ";
			}
			$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
		}

		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE " . $final;
		$result = $this->avalanche->mysql_query($sql);


		$can_read = false;
		$field = "delete_calendar";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] == 1){
				$can_read = true;
			}
		}

		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  canChangePermissions()					//
	//--------------------------------------------------------------//
	//  output: boolean   - returns 1/true if the logged in user	//
	//			can add calendars, 0/false otherwise.	//
	//								//
	//  precondition:						//
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	none							//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
	function canChangePermissions(){
		if(!$this->ipOk()){
			return false;
		}

		$usergroups = $this->avalanche->getAllUsergroupsFor($this->avalanche->getActiveUser());
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
		$field = "change_permissions";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] == 1){
				$can_read = true;
			}
		}

		return $can_read;
	}


	//////////////////////////////////////////////////////////////////
	//  updatePermission($permission, $value, $group)		//
	//--------------------------------------------------------------//
	//								//
	//////////////////////////////////////////////////////////////////
	function updatePermission($permission, $value, $group){
		$value = $value + 0;
		if($this->ipOk() &&
		   ($this->canChangePermissions()) &&
		   ($permission == "change_permissions" ||
		    $permission == "add_calendar" ||
		    $permission == "delete_calendar") &&
		   ($value == 1 ||
		    $value == 0 ||
		    (is_integer($value) && $permission == "add_calendar") ||
		    $value === true ||
		    $value === false)){
			if($permission == "change_permissions"){
				/* we have to make sure at least one person
				 * has rw access to this permission.
				 * so that means that 2 people must have it to change.
				 */
				$cal = $permission;
				$sql = "SELECT usergroup FROM " . $this->avalanche->PREFIX() . "strongcal_permissions WHERE `$cal`='1'";
				$result= $this->avalanche->mysql_query($sql);
				$groups = array();
				while($myrow = mysql_fetch_array($result)){
					$groups[] = $myrow["usergroup"];
				}
				if(count($groups) == 1 &&
				   $groups[0] == $group){
					return false;
				}
			}
			$cal = $permission;
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "strongcal_permissions SET $cal = '$value' WHERE usergroup='$group'";
			$result= $this->avalanche->mysql_query($sql);
			if($result){
				return true;
			}
		}
		return false;
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
				$new_list[] = $l;
			}
		}
		$this->_listeners = $new_list;
	}

	public function getListeners(){
		return $this->_listeners;
	}

	// notifies listeners that an event has been added
	public function eventAdded($cal_id, $event_id){
		if(!is_int($cal_id) || !is_int($event_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->eventAdded($cal_id, $event_id);
		}
	}

	// notifies listeners that an event has been deleted
	public function eventDeleted($cal_id, $event_id){
		if(!is_int($cal_id) || !is_int($event_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->eventDeleted($cal_id, $event_id);
		}
	}

	// notifies listeners that an event has been edited
	public function eventEdited($cal_id, $event_id){
		if(!is_int($cal_id) || !is_int($event_id)){
			throw new IllegalArgumentException("arguments to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->eventEdited($cal_id, $event_id);
		}
	}

	// notifies listeners that an attendee has been deleted
	public function attendeeAdded($cal_id, $event_id, $user_id){
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(!is_int($event_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->attendeeAdded($cal_id, $event_id, $user_id);
		}
	}

	// notifies listeners that an attendee has been deleted
	public function attendeeDeleted($cal_id, $event_id, $user_id){
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(!is_int($event_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->attendeeDeleted($cal_id, $event_id, $user_id);
		}
	}

	// notifies listeners that a calendar has been deleted
	private function calendarAdded($cal_id){
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->calendarAdded($cal_id);
		}
	}

	// notifies listeners that a calendar has been deleted
	private function calendarDeleted($cal_id){
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		foreach($this->_listeners as $l){
			$l->calendarDeleted($cal_id);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
	}


	function userLoggedIn($username, $valid=true){
		$cals = $this->_calendars->enum();
		foreach($cals as $c){
			$c->reload();
			$c->clearCache();
		}
		return true;
	}

	function userLoggedOut($username, $valid=true){
		$cals = $this->_calendars->enum();
		foreach($cals as $c){
			$c->reload();
			$c->clearCache();
		}
		return true;
	}


}

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>