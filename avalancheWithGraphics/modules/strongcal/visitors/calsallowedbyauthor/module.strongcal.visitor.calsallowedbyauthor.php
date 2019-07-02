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
class module_strongcal_visitor_calsallowedbyauthor implements module_strongcal_visitor {

	private $avalanche;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
	}
	
	
	private $author;

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
		if($visitor->name() == "calsbyauthor"){
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
		$final = "";
		$usergroups = $this->avalanche->getAllUsergroupsFor($this->_author);
		for($i=0;$i<count($usergroups);$i++){
			if(strlen($final)>0){
				$final .= " OR ";
			}
			$final .= "usergroup = '" . $usergroups[$i]->getId() . "'";
		}

		$result = connectTo("strongcal_permissions", $final);


		$can_read = 0;
		$field = "add_calendar";
		while($myrow = mysql_fetch_array($result)){
			if($myrow[$field] != 0 && $myrow[$field] > $can_read){
				$can_read = $myrow[$field];
			}else
			if($myrow[$field] == -1){
				return -1;
			}
		}

		if($temp_login){
			$this->avalanche->logOut();
		}


		return $can_read;
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a calendar				//
	//////////////////////////////////////////////////////////////////
	function calendarCase($calendar){
		trigger_error("Visitor \"calsallowedbyauthor\" can only be executed on Aurora, not on a Calendar.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on an event				//
	//////////////////////////////////////////////////////////////////
	function eventCase($event){
		trigger_error("Visitor \"calsallowedbyauthor\" can only be executed on Aurora, not on an Event.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a field				//
	//////////////////////////////////////////////////////////////////
	function fieldCase($field){
		trigger_error("Visitor \"calsallowedbyauthor\" can only be executed on Aurora, not on a Field.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  case to be executed on a recur				//
	//////////////////////////////////////////////////////////////////
	function recurCase($recur){
		trigger_error("Visitor \"calsallowedbyauthor\" can only be executed on Aurora, not on a Recur.", E_USER_ERROR);
	}

	//////////////////////////////////////////////////////////////////
	//  returns the name of this visitor				//
	//  (the name of the folder its in)				//
	//////////////////////////////////////////////////////////////////
	function name(){
		return "calsallowedbyauthor";
	}
}

?>