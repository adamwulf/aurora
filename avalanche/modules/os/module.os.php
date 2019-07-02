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


include ROOT . APPPATH . MODULES . "os/submodule.os.usergroupComparator.php";
include ROOT . APPPATH . MODULES . "os/visitor.basic.search.php";
include ROOT . APPPATH . MODULES . "os/visitor.search.php";
include ROOT . APPPATH . MODULES . "os/visitor.search.new.php";

$fileloader = $this->getModule("fileloader");

$fileloader->include_recursive(ROOT . APPPATH . MODULES . "os/bootstraps/");


interface avalanche_interface_os{
}

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                  /////////////////////////////////
///////////////  MAIN OS MODULE  /////////////////////////////////
///////////////                  /////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_os  extends module_template{
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
	//								// 
	//////////////////////////////////////////////////////////////////



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
		$this->_name = "Inversion OS";
		$this->_version = "1.0.0";	
		$this->_desc = "Operating System for Inversion Designs.";	
		$this->_folder = "os";
		$this->_copyright = "Copyright 2003 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "01-07-03";
	}

	function getModules(){
		$ret = array();
		for($i = 0; $i < $this->avalanche->getModuleCount(); $i++){
			$temp = $this->avalanche->getModuleAt($i);
			if($temp instanceof avalanche_interface_os){
				$ret[] = $temp;
			}
		}
		return $ret;

//		$strongcal = $this->avalanche->getModule("strongcal");
//		if($strongcal instanceof avalanche_interface_os){
//			return array($strongcal);
//		}else{
//			return array();
//		}
	}
	
	function getUsername($user_id){
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int user id");
		}
		$username = $this->avalanche->getUsername($user_id);
		$name = $this->avalanche->getName($user_id);
		$first = $name["first"];
		$last = $name["last"];
		if(strlen($first) > 0 && strlen($last) > 0){
			$name = $first . " " . $last;
		}else if(strlen($first) > 0){
			$name = $first;
		}else{
			$name = $last;
		}
		
		if(strlen($name) > 0){
			return $name;
		}else{
			return $username;
		}
	}
	
	// returns an array of all users matching these terms	
	function getAllUsersMatching($terms){
		return $this->avalanche->getAllUsersMatching($terms);
	}

	// return an array of all groups matching these terms
	function getAllTeamsMatching($terms){
		return $this->avalanche->getAllTeamsMatching($terms);
	}

	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
		// noop
	}
} 

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>