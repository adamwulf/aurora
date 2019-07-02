<?php

//*****************************************************************
//**************  USER DEFINED FUNCTIONS BELOW  *******************
//*****************************************************************


function Avalanche_error_handler($errno, $errstr, $errfile, $errline) {
	throw new Exception("<B>PHP ERROR:</B> ".$errstr." <B>in</B> ".$errfile." <B>at line</B> ".$errline . "<br>");
	echo "<B>PHP ERROR:</B> ".$errstr." <B>in</B> ".$errfile." <B>at line</B> ".$errline . "<br>";
}
set_error_handler("Avalanche_error_handler", E_ALL);

    
function include_recursive($dir){
	$theList = array();
	if ($handle = opendir($dir)) {
   		while (false != ($file = readdir($handle))) {
	       	if ($file != "." && $file != "..") {
			if(is_dir($dir . $file)){
				include_recursive($dir . $file . "/");
			}else{
				if(substr($file, 0, 6) == "class." || 
				   substr($file, 0, 8) == "include."){
					include_once $dir . $file;
				}
			}
       		}
	}
	closedir($handle);
	unset($handle); 
	}
}

require_once( "mysql2i-0.8/mysql2i.php");
// include the classloader (used only as a failsafe)
include_recursive(ROOT . APPPATH . CLASSLOADER);
// include the library (include for speed)
include_recursive(ROOT . APPPATH . LIBRARY);
require(ROOT . APPPATH . INCLUDEPATH . "include.interfaces.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.datastructures.php");
require(ROOT . APPPATH . MODULES . "template/module.template.php");
require(ROOT . APPPATH . SKINS . "template/skin.template.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.date.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.users.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.team.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.system.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.module.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.public.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.personal.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.user.usergroups.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.visitormanager.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.string.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.listener.php");
require(ROOT . APPPATH . INCLUDEPATH . "include.avalanche.php");



if(defined("ACCOUNT")){
	$account = ACCOUNT;
}else{
	$account = false;
}
$avalanche = new avalanche_class(ROOT, PUBLICHTML, HOSTURL, DOMAIN, APPPATH, SECURE, INCLUDEPATH, JAVASCRIPT, MODULES, SKINS, LIBRARY, CLASSLOADER, HOST, ADMIN, PASS, DATABASENAME, PREFIX, $account);



$strongcal = $avalanche->getModule("strongcal");
// this speeds up the site considerably...
// $strongcal->getCalendarList();

// make sure the notifier is loaded to keep track of events and such
$notifier = $avalanche->getModule("notifier");
// make sure the reminder is loaded to keep track of events and such
$reminder = $avalanche->getModule("reminder");


?>