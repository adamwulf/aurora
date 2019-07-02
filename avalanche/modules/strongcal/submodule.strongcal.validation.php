<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
//----------------------------------------------------------------------//
//  sub class for the strongcal module. this class represents a field	//
//  in a calendar.							//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
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
///////////////         field           //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this field class defines a field that knows its home calendar//
// and own id. when updated, it will update mysql.		//
//////////////////////////////////////////////////////////////////
// THIS CLASS NEEDS UPDATING SO THAT MYSQL WILL UPDATE		//
// APPROPRIATELY WHEN THE FIELD IS RESET.			//
//////////////////////////////////////////////////////////////////
class module_strongcal_validation {

	// the calendar to which this field is a part
	private $_calendar;

	private $_id;
	private $_field;
	private $_user;
	private $_usergroup;

	//////////////////////////////////////////////////////////////////
	//  compareTo($validation)					//
	//	set strict equal to true if the calendar must be the	//
	//	same for both recurrance patterns.			//
	//--------------------------------------------------------------//
	//  input: $validation - the validation to compare to		//
	//								//  
	//  output: boolean, true if validations are equal		//
	//								//  
	//  precondition:						//  
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function compareTo($validation, $strict = true){
		if(!$this_>_loaded){
			$this->load();
		}
		$cal1 = $this->getCal();
		$cal2 = $validation->getCal();
		if($this->_field       == $validation->field() &&
		   $this->_user        == $validation->user() &&
		   $this->_usergroup   == $validation->usergroup() &&
		   (!$strict || $strict && 
		    $cal1->getId() == $cal2->getId()
		   )
		  ){
			return true;
		}else{
			return false;
		}
	}

	private $avalanche;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
	}
	
	
	//////////////////////////////////////////////////////////////////
	//  init($cal, $id)						//
	//--------------------------------------------------------------//
	//  input: $cal - the calendar object to which this validation	//
	//		  belongs					//
	//  output: the id of this field				//
	//								//  
	//  precondition:						//  
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function init($cal, $id){
		$this->_calendar =& $cal;
		if(is_array($id)){
			$this->_id 	   = $id['id'];
			$this->_field      = $id['field'];
			$this->_user       = $id['user'];
			$this->_usergroup  = $id['usergroup'];
			$this->_loaded = true;
		}else{
			$this->_id = $id;
			$this->_loaded = false;
		}
	}

	

	//////////////////////////////////////////////////////////////////
	//  load()							//
	//  loads the values for this field				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function load(){
		$this->_loaded = true;
		$result = connectTo("strongcal_cal_" . $this->_calendar->getId() . "_fields", "id = '" . $this->_id . "'");
		while($myrow = mysql_fetch_array($result)){
			$this->_field      = $myrow['field'];
			$this->_user       = $myrow['user'];
			$this->_usergroup  = $myrow['usergroup'];
		}
	}


	//////////////////////////////////////////////////////////////////
	//  getId()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getId(){
		return $this->_id;
	}

	//////////////////////////////////////////////////////////////////
	//  getCal()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the calendar to which this field belongs		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getCal(){
		return $this->_calendar;
	}


	//////////////////////////////////////////////////////////////////
	//  field()							//
	//	returns the name of this field.				//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the field of this field				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function field(){
		if(!$this_>_loaded){
			$this->load();
		}
		return $this->_field;
	}


	//////////////////////////////////////////////////////////////////
	//  user()							//
	//	returns the uesr of this validation.			//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the user id of this validation			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function user(){
		if(!$this_>_loaded){
			$this->load();
		}
		return $this->_user;
	}


	//////////////////////////////////////////////////////////////////
	//  usergroup()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the usergroup of this validation			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function usergroup(){
		if(!$this_>_loaded){
			$this->load();
		}
		return $this->_usergroup;
	}


	//////////////////////////////////////////////////////////////////
	//  set_field($val)						//
	//--------------------------------------------------------------//
	//  input: the field of this field				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_field($val){
		$calendar = $this->_calendar;
		if($calendar->isRemoveable($this->field()) && $calendar->canWriteValidations()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
                	$sql = "UPDATE $tablename SET field = '$val' WHERE id = '$my_id'";
			$result = runSQL($sql);
			if($result){
				$this->_field = $val;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_user($val)						//
	//--------------------------------------------------------------//
	//  input: the user of this validation				//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_user($val){
		$calendar = $this->_calendar;
		if($calendar->isRemoveable($this->field()) && $calendar->canWriteValidations()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
                	$sql = "UPDATE $tablename SET `user` = '$val', `usergroup` = '0' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_user = $val;
				$this->_usergroup = 0;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  set_usergroup($val)						//
	//--------------------------------------------------------------//
	//  input: the usergroup of this validation			//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//	object must have id set					//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function set_usergroup($val){
		$calendar = $this->_calendar;
		if($calendar->isRemoveable($this->field()) && $calendar->canWriteValidations()){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" .  $calendar->getId() . "_fields";
			$my_id = $this->getId();
                	$sql = "UPDATE $tablename SET `user` = '0', `usergroup` = '$val' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_usergroup = $val;
				$this->_user = 0;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

?>