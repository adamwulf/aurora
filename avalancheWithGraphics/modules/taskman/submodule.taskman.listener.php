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
interface module_taskman_listener extends avalanche_listener{
	// called when a task is added
	function taskAdded($task_id);

	// called when a task is deleted
	function taskDeleted($task_id);

	// called when a task's status changes
	// $task_id: the task that was changed
	// $comment: the optional comment for change
	function taskStatusChanged($task_id, $comment=false);
	
	// called when a task is edited (including status)
	function taskEdited($task_id);
}

?>