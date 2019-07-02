<?
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


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                        ///////////////////////////
///////////////  STRONGCAL SUB-MODULE  ///////////////////////////
///////////////       calendar         ///////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
interface module_strongcal_listener extends avalanche_listener{
	// called when a calendar is added
	function calendarAdded($cal_id);
	
	// called when a calendar is deleted
	function calendarDeleted($cal_id);
	
	// called when an event is added
	function eventAdded($cal_id, $event_id);
	
	// called when an event is deleted
	function eventEdited($cal_id, $event_id);

	// called when an event is deleted
	function eventDeleted($cal_id, $event_id);

	// called when an attendee is added
	function attendeeAdded($cal_id, $event_id, $user_id);
	
	// called when an attendee is added
	function attendeeDeleted($cal_id, $event_id, $user_id);
}

?>