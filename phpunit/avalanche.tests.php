<?php // -*- mode: html; mmm-classes: html-php -*-

include("phpunit_test.php");
include("../include.avalanche.fullApp.testuse.php");
// above set $suite to self-test suite

define("PHP_UNIT_USER", "phpunit");
define("PHP_UNIT_PASS", "samplepassword");


$suite = new TestSuite();

include("../avalanche/test.avalanche.fullApp.php");

if(isset($_REQUEST["only"])){
	find_test_files($suite, ROOT . APPPATH, $_REQUEST["only"]);
}else{
	find_test_files($suite, ROOT . APPPATH);
}



echo "<html>";
echo "<head>";
echo "<title>PHP-Unit Results</title>";
echo "<STYLE TYPE=\"text/css\">";
include("stylesheet.css");
echo "</STYLE>";
echo "</head>";
echo "<body>";

$result = new PrettySelfTestResult;
$suite->run($result);
$result->report();

//	$testRunner = new TestRunner();
//$testRunner->run( $suite );

echo "</body>";
?>