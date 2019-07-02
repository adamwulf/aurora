<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
//////////////////////////////////////////////////////////////////////////
//									//
//  module.fileloader.php						//
//----------------------------------------------------------------------//
//  initializes the menu's class object and adds it to avalanches	//
//  menu list								//
//									//
//									//
//  NOTE: filename must be of format menu.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.fileloader.php						//
//----------------------------------------------------------------------//
//									//
//  This menu shows what errors will be triggered when functions are	//
//	not properly defined.						//
//  This menu will create errors when addUser(), deleteUser(),	//
//	userLoggedIn(), or userLoggedOut() is called.			//
//									//
//////////////////////////////////////////////////////////////////////////

//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_fileloader extends module_template{

	private $_modules;

	private $avalanche;
	function __construct($avalanche){
		$this->avalache = $avalanche;
		$this->_name = "FileLoader!";	
		$this->_version = "1.0.0";	
		$this->_desc = "This module can recursively include files.";
		$this->_folder = "fileloader";
		$this->_copyright = "Copyright 2002 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "02-27-04";
		$this->_modules = array();
	}

	function include_recursive($dir){
		$theList = array();
		if ($handle = opendir($dir)) {
    		while (false != ($file = readdir($handle))) {
		       	if ($file != "." && $file != "..") {
				if(is_dir($dir . $file)){
					$this->include_recursive($dir . $file . "/");
				}else{
					include_once $dir . $file;
				}
        		}
		}
		closedir($handle);
		unset($handle); 
		}
	}

	function include_flat($dir){
		$theList = array();
		if ($handle = opendir($dir)) {
    		while (false != ($file = readdir($handle))) {
		       	if ($file != "." && $file != "..") {
				if(is_dir($dir . $file)){
					// noop
				}else{
					include_once $dir . $file;
				}
        		}
		}
		closedir($handle);
		unset($handle); 
		}
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