<?

abstract class Avalanche_TestCase extends TestCase { 

   public function setUp(){
	global $avalanche;

	$sql = "SOURCE " . ROOT . APPPATH . INCLUDEPATH . "testfiles/test_data.sql";
	$result = mysql_query($sql);
	if(mysql_error()){
		echo mysql_error() . "<br>";
	}

	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
   }

   public function tearDown(){
	global $avalanche;
	$avalanche->logOut();
   }


};
?>