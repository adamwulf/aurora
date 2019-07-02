<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.export.php						//
//----------------------------------------------------------------------//
//  sub class for the strongcal module. this class exports information	//
//  to a .ics file							//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.export.php						//
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
//////////////////////////////////////////////////////////////////
//								//
// 	DEFINE CONSTANTS FOR EXPORTER				//
//								//
//////////////////////////////////////////////////////////////////

define("V_CALENDAR_START",	"BEGIN:VCALENDAR");
define("V_CALENDAR_PRODID",	"PRODID");
define("V_CALENDAR_VERSION",	"VERSION");
define("V_CALENDAR_END",	"END:VCALENDAR");
define("V_CALENDAR_NAME",	"X-WR-CALNAME");
define("V_CALENDAR_METHOD",	"METHOD");
define("I_CAL_TIMEZONE",	"X-WR-TIMEZONE");
define("VALUE_TEXT",		"VALUE=TEXT");
define("V_EVENT_START",		"BEGIN:VEVENT");
define("V_EVENT_END",		"END:VEVENT");
define("DESCRIPTION",		"DESCRIPTION");
define("ENC_PRINTABLE",		";ENCODING=QUOTED-PRINTABLE");
define("ENC_7BIT",		";ENCODING=7-Bit");
define("ENC_8BIT",		";ENCODING=8-Bit");
define("ENC_BASE64",		";ENCODING=BASE64");
//define(ENC_PRINTABLE_ENDL",	"=0D=0A=\r\n");
define("ENC_PRINTABLE_ENDL",	"\\n");
/* 
 * this is only for use in wrapping long lines,
 * not for multi-line values.
 */
define("ENDL",			"\r\n");
define("TEXT_LINE_WRAPPER",	"\r\n ");
define("FIELD_DILIMITER",		";");
define("PROPERTY_VALUE_DILIMITER",":");
define("I_CAL_EVENT_TZ_FIELD",	"TZID");
define("EVENT_FIELD_ATTACH",	"ATTACH");
define("EVENT_FIELD_CATEGORIES","CATEGORIES");
define("EVENT_FIELD_DCREATED",	"DCREATED");
define("EVENT_FIELD_DESCRIPTION","DESCRIPTION");
define("EVENT_FIELD_LOCATION",	"LOCATION");
define("EVENT_FIELD_RESOURCES",	"RESOURCES");
define("EVENT_FIELD_STATUS",	"STATUS");
define("EVENT_FIELD_DTCOMPLETE","DTCOMPLETE");
define("EVENT_FIELD_DTDUE",	"DTDUE");
define("EVENT_FIELD_DTSTART",	"DTSTART");
define("EVENT_FIELD_DTEND",	"DTEND");
define("EVENT_FIELD_SUMMARY",	"SUMMARY");
define("EVENT_FIELD_URL",	"URL");
define("EVENT_FIELD_UID",	"UID");
define("EVENT_FIELD_EXTENSION",	"X-AUR-");
define("V_CALENDAR_RELCALID",	"X-WR-RELCALID");


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////





//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////         export          //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this export class defines an export class which will export	//
// information to a ics file.					//
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
class module_strongcal_export {

	private $_prodid;
	private $_version;
	private $_events;
	private $_name;
	private $_download_type;
	private $_timezone;
	private $_cal_name;

	//////////////////////////////////////////////////////////////////
	//  init()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function init(){
		$this->_prodid = "-//Inversion Designs//Aurora Calendar 0.9b//EN";
		$this->_version = "1.0";
		$this->_events = array();
		$this->_name = EVENT_FIELD_EXTENSION . "Calendar";
		$this->_download_type = "file";
		$this->_timezone = 0;
		$this->_cal_name = "Aurora Export Calendar";
	}


	//////////////////////////////////////////////////////////////////
	//  addEvent()							//
	//--------------------------------------------------------------//
	//  input: a strongcal event object				//
	//  output: none						//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function addEvent($event){
		$this->_events[] = $event;
	}

	function name($name){
		$this->_name = $name;
	}

	function timezone($time){
		$this->_timezone = $time;
	}

	function download_type($type){
		$this->_download_type = $type;
	}

	function cal_name($name){
		$this->_cal_name = $name;
	}

	function format_timezone(){
		return "US/Central";
	}

	//////////////////////////////////////////////////////////////////
	//  export()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string of the .ics file contents			//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function export(){
		$file = "";
		$file .= V_CALENDAR_START;
		$file .= ENDL;
//		$file .= "TZ:+05" . ENDL;
		$file .= V_CALENDAR_PRODID . PROPERTY_VALUE_DILIMITER . $this->_prodid . ENDL;
		$file .= V_CALENDAR_VERSION . PROPERTY_VALUE_DILIMITER . $this->_version . ENDL;
		$title = str_replace(";", "\;", $this->_cal_name);
		$file .= V_CALENDAR_NAME . FIELD_DILIMITER . VALUE_TEXT . PROPERTY_VALUE_DILIMITER . $title . ENDL;
		$file .= V_CALENDAR_RELCALID . FIELD_DILIMITER . VALUE_TEXT . PROPERTY_VALUE_DILIMITER . $title . ENDL;
		if($this->_download_type == "ical"){
//			$file .= V_CALENDAR_METHOD . PROPERTY_VALUE_DILIMITER . "PUBLISH" . ENDL;
			$file .= I_CAL_TIMEZONE . FIELD_DILIMITER . VALUE_TEXT . PROPERTY_VALUE_DILIMITER . $this->format_timezone() . ENDL;
		}

		for($i=0;$i<count($this->_events);$i++){
			$event = "";
			$dtdue = array("date" => false, "time" => false);
			$dtcomplete = array("date" => false, "time" => false);


			$temp_start = $this->_events[$i]->getValue("start_date");
			$temp_start_year  = substr($temp_start, 0,4);
			$temp_start_month = substr($temp_start, 5,2);
			$temp_start_day   = substr($temp_start, 8,2);
			$temp_start = $this->_events[$i]->getValue("start_time");
			$temp_start_hour   = substr($temp_start, 0,2);
			$temp_start_minute = substr($temp_start, 3,2);
			$start_stamp = mktime($temp_start_hour, $temp_start_minute, 0, $temp_start_month, $temp_start_day, $temp_start_year);
			if(date("I", $start_stamp)){
				$start_stampZ = mktime($temp_start_hour - intval($this->_timezone) - 1, $temp_start_minute, 0, $temp_start_month, $temp_start_day, $temp_start_year);
			}else{
				$start_stampZ = mktime($temp_start_hour - intval($this->_timezone), $temp_start_minute, 0, $temp_start_month, $temp_start_day, $temp_start_year);
			}
			$temp_end = $this->_events[$i]->getValue("end_date");
			$temp_end_year  = substr($temp_end, 0,4);
			$temp_end_month = substr($temp_end, 5,2);
			$temp_end_day   = substr($temp_end, 8,2);
			$temp_end = $this->_events[$i]->getValue("end_time");
			$temp_end_hour   = substr($temp_end, 0,2);
			$temp_end_minute = substr($temp_end, 3,2);
			$end_stamp = mktime($temp_end_hour, $temp_end_minute, 0, $temp_end_month, $temp_end_day, $temp_end_year);
			if(date("I", $end_stamp)){
				$end_stampZ = mktime($temp_end_hour - intval($this->_timezone) - 1, $temp_end_minute, 0, $temp_end_month, $temp_end_day, $temp_end_year);
			}else{
				$end_stampZ = mktime($temp_end_hour - intval($this->_timezone), $temp_end_minute, 0, $temp_end_month, $temp_end_day, $temp_end_year);
			}

			/*
			 * we now have the following variables:
			 * $start_stamp
			 * $start_stampZ
			 * $end_stamp
			 * $end_stampZ
			 *
			 * they represent the time of an event, in local, and gmt stamps.
			 * these will help us format our v-calendar file.
			 */


			$event .= V_EVENT_START . ENDL;

			/*
			 * find the start time of the event
			 * and add it to the ics event
			 *
			 * adjust for timezone if necessary.
			 */
			if($this->_download_type == "outlook"){
				$start_date_time = date("Ymd", $start_stampZ) . "T" . date("Hi", $start_stampZ) . "00Z";
				$event .= EVENT_FIELD_DTSTART . PROPERTY_VALUE_DILIMITER . $start_date_time . ENDL;
			}else
			if($this->_download_type == "ical"){
				$start_date_time = $this->_events[$i]->getValue("start_date") . "T" . $this->_events[$i]->getValue("start_time");
				$start_date_time = str_replace("-", "", $start_date_time);
				$start_date_time = str_replace(":", "", $start_date_time);
				$start_date_time = $start_date_time;
				$event .= EVENT_FIELD_DTSTART . FIELD_DILIMITER . I_CAL_EVENT_TZ_FIELD . "=" . $this->format_timezone() . PROPERTY_VALUE_DILIMITER . $start_date_time . ENDL;
			}else{
				$start_date_time = $this->_events[$i]->getValue("start_date") . "T" . $this->_events[$i]->getValue("start_time");
				$start_date_time = str_replace("-", "", $start_date_time);
				$start_date_time = str_replace(":", "", $start_date_time);
				$start_date_time = $start_date_time;
				$event .= EVENT_FIELD_DTSTART . PROPERTY_VALUE_DILIMITER . $start_date_time . ENDL;
			}

			/*
			 * find the end time of the event
			 * and add it to the ics event
			 *
			 * adjust for timezone if necessary.
			 */
			if($this->_download_type == "outlook"){
				$end_date_time = date("Ymd", $end_stampZ) . "T" . date("Hi", $end_stampZ) . "00Z";
				$event .= EVENT_FIELD_DTEND . PROPERTY_VALUE_DILIMITER . $end_date_time . ENDL;
			}else
//			if($this->_download_type == "ical"){
//				$end_date_time = $this->_events[$i]->getValue("end_date") . "T" . $this->_events[$i]->getValue("end_time");
//				$end_date_time = str_replace("-", "", $end_date_time);
//				$end_date_time = str_replace(":", "", $end_date_time);
//				$end_date_time = $end_date_time;
//				$event .= EVENT_FIELD_DTEND . FIELD_DILIMITER . I_CAL_EVENT_TZ_FIELD . "=" . $this->format_timezone() . PROPERTY_VALUE_DILIMITER . $end_date_time . ENDL;
//			}else{
				$end_date_time = $this->_events[$i]->getValue("end_date") . "T" . $this->_events[$i]->getValue("end_time");
				$end_date_time = str_replace("-", "", $end_date_time);
				$end_date_time = str_replace(":", "", $end_date_time);
				$end_date_time = $end_date_time;
				$event .= EVENT_FIELD_DTEND . PROPERTY_VALUE_DILIMITER . $end_date_time . ENDL;
//			}

			/*
			 * find the created on date of the event
			 * and add it to the ics event
			 */
			$added_on = $this->_events[$i]->added_on();
			$added_on = str_replace("-", "", $added_on);
			$added_on = str_replace(":", "", $added_on);
			$added_on = str_replace(" ", "T", $added_on);
			$added_on = $added_on;
			$event .= EVENT_FIELD_DCREATED . PROPERTY_VALUE_DILIMITER . $added_on . ENDL;


			/*
			 * find the title of the event
			 * and add it to the ics event
			 */
			$title = $this->_events[$i]->getValue("title");
			$title = str_replace(";", "\;", $title);
			$event .= EVENT_FIELD_SUMMARY . PROPERTY_VALUE_DILIMITER . $title . ENDL;



			$cal_id = $this->_events[$i]->calendar();
			$cal_id = $cal_id->getId();
			$event_id = $this->_events[$i]->getId();
			$event .= EVENT_FIELD_UID . PROPERTY_VALUE_DILIMITER . $cal_id . "-" . $event_id . ENDL;

			/* export the cal_id and event_id for later import
			 *
			 */
			$event .= EVENT_FIELD_EXTENSION . "cal_id" . PROPERTY_VALUE_DILIMITER . $cal_id . ENDL;
			$event .= EVENT_FIELD_EXTENSION . "event_id" . PROPERTY_VALUE_DILIMITER . $event_id . ENDL;


			/* this value will append to the description of the event...
			 *
			 */
			$append = "";
			$fields = $this->_events[$i]->fields();
			for($j=0;$j<count($fields);$j++){
				$val = $this->_events[$i]->getValue($fields[$j]->field());
				$real_val = $val;
				$val = str_replace(";", "\;", $val);
				$val = str_replace("\r\n", ENC_PRINTABLE_ENDL, $val);
				$val = str_replace("\r", ENC_PRINTABLE_ENDL, $val);
				$val = str_replace("\n", ENC_PRINTABLE_ENDL, $val);
				$val = trim($val, FIELD_DILIMITER);
				$event .= EVENT_FIELD_EXTENSION . $fields[$j]->field() . PROPERTY_VALUE_DILIMITER . $val . ENDL;

				if($fields[$j]->ics() == ICS_LOCATION){
					$val = $real_val;
					$val = str_replace(";", "\;", $val);
					$val = str_replace("\r\n", "", $val);
					$val = str_replace("\r", "", $val);
					$val = str_replace("\n", "", $val);
					$val = trim($val, FIELD_DILIMITER);
					$event .= EVENT_FIELD_LOCATION . PROPERTY_VALUE_DILIMITER . $val . ENDL;
				}else
				if($fields[$j]->ics() == ICS_RESOURCES){
					$val = $real_val;
					$val = str_replace(";", "\;", $val);
					$val = str_replace("\r\n", FIELD_DILIMITER, $val);
					$val = str_replace("\r", FIELD_DILIMITER, $val);
					$val = str_replace("\n", FIELD_DILIMITER, $val);
					$val = trim($val, FIELD_DILIMITER);
					$event .= EVENT_FIELD_RESOURCES . PROPERTY_VALUE_DILIMITER . $val . ENDL;
				}else
				if($fields[$j]->ics() == ICS_CATEGORY){
					$val = $real_val;
					$val = str_replace(";", "\;", $val);
					$val = str_replace("\r\n", FIELD_DILIMITER, $val);
					$val = str_replace("\r", FIELD_DILIMITER, $val);
					$val = str_replace("\n", FIELD_DILIMITER, $val);
					$val = trim($val, FIELD_DILIMITER);
					$event .= EVENT_FIELD_CATEGORIES . PROPERTY_VALUE_DILIMITER . $val . ENDL;
				}else
				if($fields[$j]->ics() == ICS_STATUS){
					$val = $real_val;
					$val = str_replace(";", "\;", $val);
					$val = str_replace("\r\n", "", $val);
					$val = str_replace("\r", "", $val);
					$val = str_replace("\n", "", $val);
					$val = trim($val, FIELD_DILIMITER);
					$event .= EVENT_FIELD_STATUS . PROPERTY_VALUE_DILIMITER . $val . ENDL;
				}else
				if($fields[$j]->ics() == ICS_DTDUE){
					$val = $real_val;
					if($fields[$j]->type() == DATE_INPUT){
						$val = str_replace("-", "", $val);
						$dtdue["date"] = $val;
					}else
					if($fields[$j]->type() == TIME_INPUT){
						$val = str_replace(":", "", $val);
						$dtdue["time"] = $val;
					}
				}else
				if($fields[$j]->ics() == ICS_DTCOMPLETED){
					$val = $real_val;
					if($fields[$j]->type() == DATE_INPUT){
						$val = str_replace("-", "", $val);
						$dtcomplete["date"] = $val;
					}else
					if($fields[$j]->type() == TIME_INPUT){
						$val = str_replace(":", "", $val);
						$dtcomplete["time"] = $val;
					}
				}else
				if($fields[$j]->ics() == ICS_APPEND){
					$append .= "\r\r" . $fields[$j]->prompt() . "\r" . $real_val;
				}
			}

			/*
			 * find the description of the event
			 * and add it to the ics event
			 */
			$description = $this->_events[$i]->getValue("description") . $append;
			$description = str_replace(";", "\;", $description);
			$description = str_replace("\r\n", ENC_PRINTABLE_ENDL, $description);
			$description = str_replace("\r", ENC_PRINTABLE_ENDL, $description);
			$description = str_replace("\n", ENC_PRINTABLE_ENDL, $description);
			$event .= EVENT_FIELD_DESCRIPTION . PROPERTY_VALUE_DILIMITER . $description . ENDL;


			if($dtcomplete["date"]){
				$val = $dtcomplete["date"];
				if($dtcomplete["time"]){
					$val .= "T" . $dtcomplete["time"];
				}
				$event .= EVENT_FIELD_DTCOMPLETE . PROPERTY_VALUE_DILIMITER . $val . ENDL;
			}
			if($dtdue["date"]){
				$val = $dtdue["date"];
				if($dtdue["time"]){
					$val .= "T" . $dtdue["time"];
				}
				$event .= EVENT_FIELD_DTDUE . PROPERTY_VALUE_DILIMITER . $val . ENDL;
			}


			/* close out the event
			 */
			$event .= V_EVENT_END . ENDL;


			/* now add the event
			 * to the file
			 */
			$file .= $event;
		}

		/* close out the calendar
		 */
		$file .= V_CALENDAR_END . ENDL;


		/* return the file
		 */
		return $file;
	}

	

	//////////////////////////////////////////////////////////////////
	//  toHTML($skin)						//
	//	exports the ics file into html so that it will show up	//
	//	in a browser as it will export to text file		//
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
	function toHTML($skin){
	}
}

?>