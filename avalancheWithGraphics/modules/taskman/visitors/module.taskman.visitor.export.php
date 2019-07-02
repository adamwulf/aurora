<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.visitor.php					//
//----------------------------------------------------------------------//
//  abstract sub class for the strongcal module. this class represents	//
//  a visitor that can be executed on Aurora.				//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.visitor.php					//
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
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////        visitor          //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this field class defines a field that knows its home calendar//
// and own id. when updated, it will update mysql.		//
//////////////////////////////////////////////////////////////////
// THIS CLASS NEEDS UPDATING SO THAT MYSQL WILL UPDATE		//
// APPROPRIATELY WHEN THE FIELD IS RESET.			//
//////////////////////////////////////////////////////////////////
class ExportCalendarVisitor implements module_strongcal_visitor, module_taskman_visitor {


	protected $avalanche;
	private $sdate;
	private $edate;
	function __construct($avalanche, $sdate, $edate=false){
		$this->avalanche = $avalanche;
		$this->sdate = $sdate;
		$this->edate = $edate ? $edate : $this->sdate;
	}

	//////////////////////////////////////////////////////////////////
	//  compareTo($field)						//
	//	set strict equal to true if the calendar must be the	//
	//	same for both recurrance patterns.			//
	//--------------------------------------------------------------//
	//  input: $field - the field to compare to			//
	//								//
	//  output: boolean, true if fields are equal			//
	//								//
	//  precondition:						//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function compareTo($visitor, $strict = true){
		if($visitor->name() == "ExportCalendarVisitor"){
			return true;
		}else{
			return false;
		}
	}


	function init($author){
		$this->_author = $author;
	}


	//////////////////////////////////////////////////////////////////
	//  case to be executed on Aurora				//
	//////////////////////////////////////////////////////////////////
	function moduleCase($calendar){
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$id = strlen($this->avalanche->ACCOUNT()) ? $this->avalanche->ACCOUNT() : "www";
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "PRODID:" . $id . "." . $this->avalanche->DOMAIN() . "\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "METHOD: REQUEST\r\n";

		$data = false;
		$runner = $bootstrap->newDefaultRunner();
		$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
		$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
		$runner->add(new module_bootstrap_strongcal_eventlist($this->sdate, $this->edate));
		$data = $runner->run($data);
		$events = $data->data();
		foreach($events as $event){
			$output .= $event->execute($this);
		}

		$data = false;
		$runner = $bootstrap->newDefaultRunner();
		$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
		$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
		$runner->add(new module_bootstrap_taskman_tasklist($this->avalanche, $this->edate));
		$data = $runner->run($data);
		$tasks = $data->data();
		foreach($tasks as $task){
			$output .= $task->execute($this);
		}

		$output .= "END:VCALENDAR\r\n";
		return $output;
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a calendar				//
	//////////////////////////////////////////////////////////////////
	function calendarCase($calendar){
		trigger_error("Visitor \"calsbyauthor\" can only be executed on Aurora, not on a Calendar.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on an event				//
	//////////////////////////////////////////////////////////////////
	function eventCase($event){
		$os = $this->avalanche->getModule("os");
		$reminder = $this->avalanche->getModule("reminder");
		$id = strlen($this->avalanche->ACCOUNT()) ? $this->avalanche->ACCOUNT() : "www";

		$output = "BEGIN:VEVENT\r\n";
		$name = $os->getUsername($event->author());
		$author = $this->avalanche->getUser($event->author());
		$output .= "ORGANIZER" . ";CN=" . $name . ":MAILTO:" . $author->email() . "\r\n";
		$start = $event->getValue("start_date") . " " . $event->getValue("start_time");
		$start = new MMDateTime($start);
		if($event->isAllDay()){
			$start = ";VALUE=DATE:" . date("Ymd", $start->getTimeStamp());
		}else{
			$start = ":" . date("Ymd\THi00\Z", $start->getTimeStamp());
		}
		$output .= "DTSTART" . $start . "\r\n";
		$end = $event->getValue("end_date") . " " . $event->getValue("end_time");
		$end = new MMDateTime($end);
		if($event->isAllDay()){
			$end = ";VALUE=DATE:" . date("Ymd", $end->getTimeStamp());
		}else{
			$end = ":" . date("Ymd\THi00\Z", $end->getTimeStamp());
		}
		$output .= "DTEND" . $end . "\r\n";
		$output .= "TRANSP:OPAQUE\r\n";
		$output .= "UID:" . $id . "_" . $this->avalanche->DOMAIN() . "_" . $event->calendar()->getId() . "_" . $event->getId() . "\r\n";
		$added = $event->added_on();
		$added = new MMDateTime($added);
		$added = date("Ymd\THi00\Z", $added->getTimeStamp());
		$output .= "DTSTAMP:" . $added . "\r\n";
		$output .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . str_replace('\n', '=0D=0A=',str_replace('\r', '=0D=0A=', $this->quotedPrintableEncode($event->getDisplayValue("description")))) . "\r\n";
		$output .= "SUMMARY;ENCODING=QUOTED-PRINTABLE:" . $this->quotedPrintableEncode($event->getDisplayValue("title")) . "\r\n";
		if(strcasecmp($event->getDisplayValue("priority"), "high") == 0){
			$priority = "1";
		}else if(strcasecmp($event->getDisplayValue("priority"), "high") == 0){
			$priority = "9";
		}else{
			$priority = "5";
		}
		$output .= "PRIORITY:" . $priority . "\r\n";
		$attendees = $event->attendees();
		foreach ($attendees as $attendee) {
			$user = $this->avalanche->getUser($attendee->userId());
			$output .= "ATTENDEE";
			// if($user->getId() == $event->author()){
				// $output .= ";ROLE=CHAIR";
			// }else{
				// $output .= ";ROLE=REQ-PARTICIPANT";
			// }
			$output .= ";CN=" . $os->getUsername($user->getId()) . ":MAILTO:" . $user->email() . "\r\n";
		}
		$output .= "URL:http://" . $id . "." . $this->avalanche->DOMAIN() . "/index.php?view=event&cal_id=" . $event->calendar()->getId() . "&event_id=" . $event->getId() . "\r\n";
		$reminders = $reminder->getRemindersFor($event);
		foreach($reminders as $r){
			$output .= "BEGIN:VALARM\r\n";
			$output .= "ACTION:DISPLAY\r\n";
			$output .= "TRIGGER:-P" . ($r->day() ? ($r->day() . "D") : "") . ($r->hour() ? ($r->hour() . "H") : "") . ($r->minute() ? ($r->minute() . "M") : "") . "\r\n";
			$output .= "DESCRIPTION:" . $this->quotedPrintableEncode($r->body()) . "\r\n";
			$output .= "SUMMARY:" . $this->quotedPrintableEncode($r->subject()) . "\r\n";
			$output .= "END:VALARM\r\n";
		}


		$output .= "END:VEVENT\r\n";
		return $output;
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a field				//
	//////////////////////////////////////////////////////////////////
	function fieldCase($field){
		trigger_error("Visitor \"calsbyauthor\" can only be executed on Aurora, not on a Field.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a recur				//
	//////////////////////////////////////////////////////////////////
	function recurCase($recur){
		trigger_error("Visitor \"calsbyauthor\" can only be executed on Aurora, not on a Recur.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a recur				//
	//////////////////////////////////////////////////////////////////
	function taskCase($task){
		if($task->status() != module_taskman_task::$STATUS_COMPLETED && $task->status() != module_taskman_task::$STATUS_CANCELLED){
			$os = $this->avalanche->getModule("os");
			$taskman = $this->avalanche->getModule("taskman");
			$reminder = $this->avalanche->getModule("reminder");
			$id = strlen($this->avalanche->ACCOUNT()) ? $this->avalanche->ACCOUNT() : "www";
			$output = "BEGIN:VTODO\r\n";

			$name = $os->getUsername($task->assignedTo());
			$assignee = $this->avalanche->getUser($task->assignedTo());
			$output .= "ORGANIZER" . ";CN=" . $name . ":MAILTO:" . $assignee->email() . "\r\n";
			$due = $task->due();
			$due = $taskman->adjustToGMT($due);
			$due = new MMDateTime($due);
			$due = date("Ymd\THi00\Z", $due->getTimeStamp());
			$output .= "DUE:" . $due . "\r\n";
			$output .= "UID:" . $id . "_" . $this->avalanche->DOMAIN() . "_" . $task->getId() . "\r\n";
			$added = $task->createdOn();
			$added = new MMDateTime($added);
			$added = date("Ymd\THi00\Z", $added->getTimeStamp());
			$output .= "DTSTAMP:" . $added . "\r\n";
			$output .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . str_replace('\n', '=0D=0A=',str_replace('\r', '=0D=0A=', $this->quotedPrintableEncode($task->description()))) . "\r\n";
			$output .= "SUMMARY;ENCODING=QUOTED-PRINTABLE:" . $this->quotedPrintableEncode($task->title()) . "\r\n";
			if($task->priority() == module_taskman_task::$PRIORITY_HIGH){
				$priority = "1";
			}else if($task->priority() == module_taskman_task::$PRIORITY_LOW){
				$priority = "9";
			}else{
				$priority = "5";
			}
			$output .= "PRIORITY:" . $priority . "\r\n";
			$output .= "URL:http://" . $id . "." . $this->avalanche->DOMAIN() . "/index.php?view=task&task_id=" . $task->getId() . "\r\n";
			$output .= "STATUS:" . $this->getStatusName($task->status()) . "\r\n";
			if($task->completed() != "0000-00-00 00:00:00"){
				$completed = $task->completed();
				$completed = new MMDateTime($completed);
				$completed = date("Ymd\THi00\Z", $completed->getTimeStamp());
				$output .= "COMPLETED:" . $completed . "\r\n";
			}
			$reminders = $reminder->getRemindersFor($task);
			foreach($reminders as $r){
				$output .= "BEGIN:VALARM\r\n";
				$output .= "ACTION:DISPLAY\r\n";
				$output .= "TRIGGER:-P" . ($r->day() ? ($r->day() . "D") : "") . ($r->hour() ? ($r->hour() . "H") : "") . ($r->minute() ? ($r->minute() . "M") : "") . "\r\n";
				$output .= "DESCRIPTION:" . $this->quotedPrintableEncode($r->body()) . "\r\n";
				$output .= "SUMMARY:" . $this->quotedPrintableEncode($r->subject()) . "\r\n";
				$output .= "END:VALARM\r\n";
			}
			$output .= (string) "END:VTODO\r\n";
			return $output;
		}else{
			return "";
		}
	}

	//////////////////////////////////////////////////////////////////
	//  returns the name of this visitor				//
	//  (the name of the folder its in)				//
	//////////////////////////////////////////////////////////////////
	function name(){
		return "ExportCalendarVisitor";
	}



	/**
	* Encodes a string for QUOTE-PRINTABLE
	*
	* @desc Encodes a string for QUOTE-PRINTABLE
	* @param string $quotprint  String to be encoded
	* @return string  Encodes string
	* @access private
	* @since 1.001 - 2002-10-19
	* @author Harald Huemer <harald.huemer@liwest.at>
	*/
	protected function quotedPrintableEncode($quotprint = '') {
		$quotprint = (string) str_replace('\r\n',chr(13) . chr(10),$quotprint);
		$quotprint = (string) str_replace('\n',chr(13) . chr(10),$quotprint);
		$quotprint = (string) preg_replace("~([\x01-\x1F\x3D\x7F-\xFF])~e", "sprintf('=%02X', ord('\\1'))", $quotprint);
		$quotprint = (string) str_replace('\=0D=0A','=0D=0A',$quotprint);
		return (string) $quotprint;
	} // end function


	protected function getStatusName($status){
		$status_array = array(module_taskman_task::$STATUS_ACCEPTED => "NEEDS-ACTION",
				module_taskman_task::$STATUS_NEEDS_ACTION => "NEEDS-ACTION",
				module_taskman_task::$STATUS_DECLINED => "NEEDS-ACTION",
				module_taskman_task::$STATUS_COMPLETED => "COMPLETED",
				module_taskman_task::$STATUS_DELEGATED => "NEEDS-ACTION",
				module_taskman_task::$STATUS_CANCELLED => "CANCELLED");
		if(isset($status_array[$status])){
			return $status_array[$status];
		}else{
			return "NEEDS-ACTION";
		}
	}
}

?>