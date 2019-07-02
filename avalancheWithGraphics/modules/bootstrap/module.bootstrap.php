<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included

//////////////////////////////////////////////////////////////////////////
//									//
//  module.bootstrap.php						//
//----------------------------------------------------------------------//
//  initializes the menu's class object and adds it to avalanches	//
//  menu list								//
//									//
//									//
//  NOTE: filename must be of format menu.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.bootstrap.php						//
//----------------------------------------------------------------------//
//									//
//  This menu shows what errors will be triggered when functions are	//
//	not properly defined.						//
//  This menu will create errors when addUser(), deleteUser(),	//
//	userLoggedIn(), or userLoggedOut() is called.			//
//									//
//////////////////////////////////////////////////////////////////////////

require ROOT . APPPATH . MODULES . "bootstrap/" . "subclass.bootstrap_exception.php";
require ROOT . APPPATH . MODULES . "bootstrap/" . "subclass.bootstrap_data.php";
require ROOT . APPPATH . MODULES . "bootstrap/" . "subclass.bootstrap_module.php";
require ROOT . APPPATH . MODULES . "bootstrap/" . "subclass.bootstrap_runner.php";


//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_bootstrap extends module_template{

	private $_modules;

	private $avalanche;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_name = "Bootstrap!";	
		$this->_version = "1.0.0";	
		$this->_desc = "This Module helps streamline developement by modularizing common tasks.";	
		$this->_folder = "bootstrap";
		$this->_copyright = "Copyright 2002 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "02-27-04";
		$this->_modules = array();
	}

	function newDefaultRunner(){
		return new module_bootstrap_runner();
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