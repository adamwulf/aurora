<?
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE |
//		E_CORE_ERROR | E_CORE_WARNING);
function error_handler($errno, $errstr, $errfile, $errline) {
	throw new Exception("$errno: \"$errstr\" in <b>$errfile</b> at <b>$errline</b>");
}
$old_handler = set_error_handler("error_handler", E_ALL);

	include "../include.avalanche.fullApp.php";
	$taskman = $avalanche->getModule("taskman");
	$tasks = $taskman->getTasks();
	foreach($tasks as $t){
		echo $t->title() . "<br>";
	}

?>
