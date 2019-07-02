<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.template.php							//
//----------------------------------------------------------------------//
//  initializes the module's class object and adds it to avalanches	//
//  module list								//
//									//
//									//
//  NOTE: filename must be of format module.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.template.php							//
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


//Syntax - module classes should always start with module_ followed by the module's install folder (name)
abstract class module_template{
	//////////////////////////////////////////////////////////////////
	//			PRIVATE VARIABLES			// 
	//	do not directly reference these variables		// 
	protected $_name;						//
	protected $_version;						//
	protected $_desc;						//
	protected $_folder;						//
	protected $_copyright;						//
	protected $_author;						//
	protected $_date;						//
	//								// 
	//////////////////////////////////////////////////////////////////


	function name() { 
	//////////////////////////////////////////////////////////////////
	//  name()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's name				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the name of this object				//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_name;
	} 

	function version(){
	//////////////////////////////////////////////////////////////////
	//  version()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's version			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the version of this object			//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_version;
	}

	function desc(){
	//////////////////////////////////////////////////////////////////
	//  desc()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's description			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the description of this object			//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_desc;
	}

	function folder(){
	//////////////////////////////////////////////////////////////////
	//  folder()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's install folder		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the installation folder of this object		//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_folder;
	}

	function copyright(){
	//////////////////////////////////////////////////////////////////
	//  copyright()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's copyright information	//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the copyright information of this object	//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_copyright;
	}

	function author(){
	//////////////////////////////////////////////////////////////////
	//  author()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's author			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the author of this object			//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_author;
	}

	function date(){
	//////////////////////////////////////////////////////////////////
	//  date()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this module's last updated date		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the date this file was last updated		//
	//								//
	//--------------------------------------------------------------//
	//  THIS FUNCTION DOES NOT NEED TO BE REDEFINED.		//
	//////////////////////////////////////////////////////////////////
		return $this->_date;
	}

	private $_avalanche;
	public function avalanche(){
		return $this->_avalanche;
	}
	
	function __construct($avalanche){
	//////////////////////////////////////////////////////////////////
	//  init()							//
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
	//	I.E.	module_template::init();			//
	//////////////////////////////////////////////////////////////////
		$this->_avalanche = $avalanche;
		$this->_name = "Incomplete Module";	
		$this->_version = "1.0.0";	
		$this->_desc = "This module has not redefined init()";	
		$this->_folder = "";
		$this->_copyright = "";
		$this->_author = "";
		$this->_date = "";
	}


	// runs the visitor on module case
	function execute($visitor){
		return $visitor->visit($this);
	}

	//////////////////////////////////////////////////////////////////
	//								//
	//	ABOVE ARE FUNCTIONS PERTAINING TO THIS			//
	//		FILE'S CREATION AND OWNER			//
	//								//
	//////////////////////////////////////////////////////////////////


	function enableUser($userid){
	//////////////////////////////////////////////////////////////////
	//  enableUser()						//
	//--------------------------------------------------------------//
	//  input: $userid - the user id to enable from this module	//
	//  output: boolean - true if the user has been			//
	//			 successfully enabled			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $userid has been deleted				//
	//								//
	//  called everytime a user is deleted from the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}




	function disableUser($userid){
	//////////////////////////////////////////////////////////////////
	//  disableUser()						//
	//--------------------------------------------------------------//
	//  input: $userid - the user id to disable from this module	//
	//  output: boolean - true if the user has been			//
	//			 successfully disabled			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $userid has been deleted				//
	//								//
	//  called everytime a user is deleted from the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}


	function deleteUser($userid){
	//////////////////////////////////////////////////////////////////
	//  deleteUser()						//
	//--------------------------------------------------------------//
	//  input: $userid - the user id to delete from this module	//
	//  output: boolean - true if the user has been			//
	//			 successfully deleted			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $userid has been deleted				//
	//								//
	//  called everytime a user is deleted from the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
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
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	usergroup $usergroupid has been deleted			//
	//								//
	//  called everytime a usergroup is deleted from the system	//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}

	function addUser($userid){
	//////////////////////////////////////////////////////////////////
	//  addUser()							//
	//--------------------------------------------------------------//
	//  input: $userid   - the user to add to this module		//
	//  output: boolean  - true if the user has been		//
	//			 successfully added			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $userid has been added				//
	//								//
	//  called everytime a new user is added to the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}

	function addUsergroup($usergroupid){
	//////////////////////////////////////////////////////////////////
	//  addUser()							//
	//--------------------------------------------------------------//
	//  input: $usergroup - the usergroup to add to this module	//
	//  output: boolean   - true if the user has been		//
	//			 successfully added			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $usergroupid has been added			//
	//								//
	//  called everytime a new usergroup is added to the system	//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}

	function permissions(){
	//////////////////////////////////////////////////////////////////
	//  permissions()						//
	//--------------------------------------------------------------//
	//  output: boolean   - an array of permissions for this module	//
	//	eg array("can_email" => "Allow the user to send		//  
	//				 outgoing email.")		//
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
		return true;
	}

	function updatepermissions($permissions){
	//////////////////////////////////////////////////////////////////
	//  addUser()							//
	//--------------------------------------------------------------//
	//  input: $permissions - an array of permissions for this	//
	//		module.						//
	//		eg. array("can_email" => '1')			//
	//  output: boolean   - true if the permissions have been	//
	//			 successfully updated			//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user permissions are updated for this module		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}


	function userLoggedIn($username, $valid=true){
	//////////////////////////////////////////////////////////////////
	//  userLoggedIn()						//
	//--------------------------------------------------------------//
	//  input: $username - the user who just logged in		//
	//  input: $valid - true if login succeeded			//
	//  		false if login failed				//
	//  output: boolean - true if the user has been			//
	//			 successfully processed	for login	//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $username has been processed for login		//
	//								//
	//  called everytime a user logs into the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}

	function userLoggedOut($username, $valid=true){
	//////////////////////////////////////////////////////////////////
	//  userLoggedIn()						//
	//--------------------------------------------------------------//
	//  input: $username - the user who just logged out		//
	//  input: $valid - true if logout succeeded			//
	//  		false if logout failed				//
	//  output: boolean - true if the user has been			//
	//			 successfully processed	for logout	//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	user $username has been processed for logout		//
	//								//
	//  called everytime a user logs out of the system		//
	//								//
	//--------------------------------------------------------------//
	//  * THIS FUNCTION MUST BE REDEFINED *				//
	//////////////////////////////////////////////////////////////////
		return true;
	}
	
	
	// called during the cron job
	abstract function cron();
} 

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>