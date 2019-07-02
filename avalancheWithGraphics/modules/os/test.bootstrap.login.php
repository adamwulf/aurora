<?

class test_bootstrap_login extends Abstract_Avalanche_TestCase {

   public function test_login(){
	global $avalanche;
	$avalanche->logOut();
	$this->assert($avalanche->needLogIn(), "i need to login");
	try{
		$data = array("user" => "phpunit",
			      "pass" => "samplepassword");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new OSLoginBootstrap($avalanche, new Document()));
		$runner->run($data);
		$this->fail("i should've been redirected");
	}catch(RedirectException $e){
		$this->pass("good, i was redirected");
	}

	$this->assert($avalanche->loggedInHuh(), "i'm logged in now");
    }
};


?>