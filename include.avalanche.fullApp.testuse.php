<?php
if(! defined("avalanche_FULLAPP_PHP")){
 define("ROOT", "/local//home/invers/public_html/thewulfs/");
 define("PUBLICHTML", "/local//home/invers/public_html/");
 define("HOSTURL", "http://inversiondesigns.com/thewulfs/");
 define("DOMAIN", "inversiondesigns.com");
 define("APPPATH", "avalanche/");

 //set SECURE to 1 if cookies need to be sent over https connection
 define("SECURE", "0");
 define("INCLUDEPATH", "includes/");
 define("JAVASCRIPT", "javascript/");
 define("MODULES", "modules/");
 define("SKINS", "skins/");
 define("LIBRARY", "library/");
 define("CLASSLOADER", "classloader/");
 define("HOST", "localhost");
 define("ADMIN", "invers");
 define("PASS", "samplepassword");
 define("DATABASENAME", "invers_accttestserver");
 define("PREFIX", "avalanche_");
 if(class_exists("TestCase")){
  require(ROOT . APPPATH . INCLUDEPATH . "include.abstract.test.php");
 }
// include_once ROOT . APPPATH . INCLUDEPATH . "include.bogus.mailman.php";
 include_once ROOT . APPPATH . INCLUDEPATH . "include.php";
 $avalanche->setMailMan(new BogusMailMan());

 $strongcal = $avalanche->getModule("strongcal");
 if(!is_object($strongcal)){
   echo "cannot load strongcal";
   exit;
 }

 if($avalanche->loggedInHuh()){
	 $avalanche->logOut();
 }

 // init_test_data();
}


function init_test_data(){
	$sql_filename = ROOT . APPPATH . INCLUDEPATH . "testfiles/test_data.sql";
	$sqls = file_get_contents($sql_filename);
	$sql = explode(";", $sqls);
	for($i=0;$i<count($sql);$i++){
		$sql[$i] = trim($sql[$i]);
		if(strlen($sql[$i]) > 0){
			mysql_query($sql[$i]);
			if(mysql_error()){
				echo "error: " . $sql[$i];
				throw new DatabaseException(mysql_error());
			}
		}
	}
}
?>