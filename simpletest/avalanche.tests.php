<?php // -*- mode: html; mmm-classes: html-php -*-

ini_set ("max_execution_time", "60");
include("unit_tester.php");
include('web_tester.php');
include("reporter.php");
include("mock_objects.php");
include("extensions/phpunit_test_case.php");
include("../include.avalanche.fullApp.testuse.php");

define("PHP_UNIT_USER", "phpunit");
define("PHP_UNIT_PASS", "samplepassword");

if (phpversion() >= '4') {
    function SimpleTest_error_handler($errno, $errstr, $errfile, $errline) {
	$e = new Exception();
	echo "<B>PHP ERROR:</B> ".$errstr." <B>in</B> ".$errfile." <B>at line</B> ".$errline . "<br>";
    }
    set_error_handler("SimpleTest_error_handler", E_ALL);
}


function find_test_files($suite, $path, $only=false){
	// if the $path comes in with a trailing /, then chop it off
	if(strrpos($path, "/") != (strlen($path)-1)){
		$path .= "/";
	}
	$ret = array();
	if ($handle = opendir($path)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
			if(is_dir($path . $file)){
				$ret = array_merge($ret, find_test_files($suite, $path . $file, $only));
			}else{
				if((strpos($file, "test.") === 0)
				   && strpos($path . $file, "fullApp") === false
				   && ($only === false && (strpos($file, "alone.") === false)|| (strpos($file, $only) !== false))){
					$ret[] = $path . $file;
				   }
			}
	        }
	    }
	    closedir($handle);
	    unset($handle);
	 }
	 return $ret;
}

$test_files = array();
if(isset($_REQUEST["only"])){
	$suite = new GroupTest("All Tests for: " . $_REQUEST["only"]);
	$test_files = find_test_files($suite, ROOT . APPPATH, $_REQUEST["only"]);
}else{
	$suite = new GroupTest("All Tests For Avalanche");
	$test_files = find_test_files($suite, ROOT . APPPATH);
}

foreach($test_files as $file){
	$suite->addTestFile($file);
}
$suite->run(new VerboseHtmlReporter());

?>