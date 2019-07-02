<?

Class TestAccounts extends Abstract_Avalanche_TestCase { 

   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
   }

   public function test_get_accounts(){
	global $avalanche;
	
	$acctmod = $avalanche->getModule("accounts");
	
	$accounts = $acctmod->getAccounts();
	$this->assertEquals(count($accounts), 0, "there are 0 accounts in the database");
   }
   
   public function test_names(){
	global $avalanche;
	$acctmod = $avalanche->getModule("accounts");
	$names = array("honor",
			"billy",
			"123",
			"asdf",
			"1lk1j4",
			"lj134lj5");
	foreach($names as $name){
		$this->assert($acctmod->checkName($name), "the name \"$name\" should be valid");
	}
	$names = array("ho nor",
			"b%@A",
			"12()3",
			"a@sdf",
			"1l.k1j4",
			"lj1314\4flj5");
	foreach($names as $name){
		$this->assert(!$acctmod->checkName($name), "the name \"$name\" should be invalid");
	}
   }


   public function test_add_account(){
	global $avalanche;
	$acctmod = $avalanche->getModule("accounts");
	$this->assert(is_string($acctmod->addAccount("testx", "null@inversiondesigns.com", "My Site!", "inversiondesigns.com")), "account created");

	$accounts = $acctmod->getAccounts();
	$this->assertEquals(count($accounts), 1, "there is one accounts in the database");
   }
   
   public function test_delete_account(){
	global $avalanche;
	try{
		$acctmod = $avalanche->getModule("accounts");
		$this->assertTrue($acctmod->deleteAccount("testx"), "account deleted");
		$accounts = $acctmod->getAccounts();
		$this->assertEquals(count($accounts), 0, "there are zero accounts in the database");
	}catch(AccountNotFoundException $e){
		$this->fail($e->getMessage());
	}
   }
};

?>