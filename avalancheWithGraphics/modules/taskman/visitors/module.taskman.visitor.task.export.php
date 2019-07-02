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
class ExportTaskVisitor extends ExportCalendarVisitor {


	private $task_id;
	function __construct($avalanche, $task_id){
		$this->avalanche = $avalanche;
		$this->task_id = $task_id;
	}
	//////////////////////////////////////////////////////////////////
	//  case to be executed on Aurora				//
	//////////////////////////////////////////////////////////////////
	function moduleCase($calendar){
		$taskman = $this->avalanche->getModule("taskman");
		$id = strlen($this->avalanche->ACCOUNT()) ? $this->avalanche->ACCOUNT() : "www";
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "PRODID:" . $id . "." . $this->avalanche->DOMAIN() . "\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "METHOD: REQUEST\r\n";
		
		$task = $taskman->getTask($this->task_id);
		$output .= $task->execute($this);
		
		$output .= "END:VCALENDAR\r\n";
		return $output;
	}


	//////////////////////////////////////////////////////////////////
	//  returns the name of this visitor				//
	//  (the name of the folder its in)				//
	//////////////////////////////////////////////////////////////////
	function name(){
		return "ExportTaskVisitor";
	}

}

?>