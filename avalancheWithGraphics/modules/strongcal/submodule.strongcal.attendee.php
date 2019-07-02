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
class module_strongcal_attendee{

	// the event to which this is attendee is attending
	private $_event;

	// this attendee's id
	private $_id;

	// this attendee's user id
	private $_user_id;

	// bool if user has confirmed yet.
	private $_confirm;

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
		return $this->_event->calendar();
	}


	//////////////////////////////////////////////////////////////////
	//  event()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the event for which this event is a part		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function event(){
		return $this->_event;
	}

	//////////////////////////////////////////////////////////////////
	//  userId()							//
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
	function userId(){
		return (int) $this->_user_id;
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
	//  confirm($confirm = false)					//
	//--------------------------------------------------------------//
	//  input: whether the attendee has confirmed his reservation	//
	//  output: none						//
	//								//  
	//////////////////////////////////////////////////////////////////
	function confirm($confirm = 0){
		if($confirm === 0){
			return $this->_confirm;
		}else if(!is_bool($confirm)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a bool");
		}else if($this->avalanche->loggedInHuh() == $this->userId()){
			$table = $this->avalanche->PREFIX() . "strongcal_attendees";
			$sql = "UPDATE $table SET confirm='$confirm' WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_confirm = $confirm;
				return $this->confirm();
			}else{
				return $this->confirm();
			}
		}else{
			throw new Exception("Invalid user trying to confirm attendee");
		}
	}
	//////////////////////////////////////////////////////////////////
	//  __construct($cal, $id)					//
	//	itinialize this attendee				//
	//--------------------------------------------------------------//
	//  input: $cal - the event object to which this attendee	//
	//		  belongs					//
	//  output: the id of this field				//
	//								//  
	//////////////////////////////////////////////////////////////////
	private $avalanche;
	function __construct($avalanche, $event, $id){
		$this->avalanche = $avalanche;
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_object($event)){
			throw new IllegalArgumentException("first argument to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("second argument to " . __METHOD__ . " must be an event object");
		}
		if(!is_int($id) && !is_array($id)){
			throw new IllegalArgumentException("third argument to " . __METHOD__ . " must be an int or array");
		}
		$this->_event = $event;
		if(is_numeric($id)){
			$this->_event = $event;
			$this->_id = $id;

			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_attendees WHERE id = '$id'";
			$result = $this->avalanche->mysql_query($sql);

			while($myrow = mysql_fetch_array($result)){
				$field = "user_id";
				$temp_user = $myrow[$field];
				$this->_user_id = $temp_user;

				$field = "confirm";
				$temp_confirm = $myrow[$field];
				$this->_confirm = $temp_confirm;
			}
		}else
		if(is_array($id)){
			$myrow = $id;
			
			$this->_id = (int)$myrow["id"];
			
			$field = "user_id";
			$temp_user = $myrow[$field];
			$this->_user_id = $temp_user;

			$field = "confirm";
			$temp_confirm = $myrow[$field];
			$this->_confirm = $temp_confirm;
		}
	}
	
	
	public function getAvalanche(){
		return $this->avalanche;
	}
}



?>