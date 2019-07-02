<?


Class TestAccountsCalculator extends Abstract_Avalanche_TestCase { 

   public function setUp(){
	global $avalanche;
	$avalanche->getModule("accounts");
	Mock::generate('module_accounts_account');
	Abstract_Avalanche_TestCase::setUp();
   }

   public function testNewCalculatorForDemo(){
	   global $avalanche;
	   $accounts = $avalanche->getModule("accounts");
	   $strongcal = $avalanche->getModule("strongcal");
	   $os = $avalanche->getModule("os");
	   
	   
	   // now create a bogus account object
	   $mock_account = new Mockmodule_accounts_account($this);
	   $mock_account->setReturnValue("getAvalanche",	$avalanche);
	   $mock_account->setReturnValue("name",		"testx");
	   $mock_account->setReturnValue("discount",		10);
	   $mock_account->setReturnValue("disabled",		false);
	   $mock_account->setReturnValue("maxUsers",		100);
	   $mock_account->setReturnValue("isDemo",		true);
	   $mock_account->setReturnValue("getMonthsLeft",	1);
	   $mock_account->setReturnValue("findCurrentProduct", $accounts->getProduct(1));
	   
	   $this->assertTrue(is_object($mock_account), "the mock is an object");
	   
	   // create a transaction
	   $trans = new AccountTransaction($avalanche);
	   $trans->account($mock_account);
	   $calc  = new TransactionCalculator($avalanche, $trans);
	   $calc->setToDefault();
	   
	   $this->assertTrue($calc->isUpdate(), "this will 'upgrade' time already purchased");
	   $this->assertEquals($trans->users(), count($avalanche->getAllUsers())-1, "users is set correct");
	   $this->assertEquals($trans->quantity(), 12, "default is set for a year");
	   $this->assertEquals($trans->product()->users(), 1, "the default product is for 1 user");
	   $this->assertTrue($calc->applyDiscountHuh(), "a discount is in order");
	   
	   $trans->quantity(11);
	   
	   $this->assertFalse($calc->applyDiscountHuh(), "a discount is not in order");
	   
	   $this->assertEquals($calc->calculateTotal(), ((4.99+4.99)*11), "it only costs 4.99*11");

	   $trans->quantity(11);
	   $trans->users(5);
	   $calc->setToOptimum();
	   
	   $this->assertFalse($calc->applyDiscountHuh(), "a discount is not in order");
	   
	   $this->assertEquals((string)$calc->calculateTotal(),(string) (14.99*11), "it only costs 14.99*11");
	   
   }

   
   public function testNewCalculatorForNonDemo(){
	   global $avalanche;
	   $accounts = $avalanche->getModule("accounts");
	   $strongcal = $avalanche->getModule("strongcal");
	   $os = $avalanche->getModule("os");
	   
	   
	   // now create a bogus account object
	   $mock_account = new Mockmodule_accounts_account($this);
	   $mock_account->setReturnValue("getAvalanche",	$avalanche);
	   $mock_account->setReturnValue("name",		"testx");
	   $mock_account->setReturnValue("discount",		10);
	   $mock_account->setReturnValue("disabled",		false);
	   // set maxUsers to 2, b/c i have an admin user and a phpunit user
	   // on this avalanche...
	   $mock_account->setReturnValue("maxUsers",		2);
	   $mock_account->setReturnValue("isDemo",		false);
	   $mock_account->setReturnValue("getMonthsLeft",	2);
	   $mock_account->setReturnValue("findCurrentProduct", $accounts->getProduct(1));
	   
	   $this->assertTrue(is_object($mock_account), "the mock is an object");
	   
	   // create a transaction
	   $trans = new AccountTransaction($avalanche);
	   $trans->account($mock_account);
	   $calc  = new TransactionCalculator($avalanche, $trans);
	   $calc->setToDefault();
	   
	   $this->assertTrue($calc->isUpdate(), "this will 'upgrade' time already purchased");
	   $this->assertEquals($trans->users(), count($avalanche->getAllUsers())-1, "users is set correct");
	   $this->assertEquals($trans->quantity(), 12, "default is set for a year");
	   $this->assertEquals($trans->product()->users(), 1, "the default product is for 1 user");
	   $this->assertTrue($calc->applyDiscountHuh(), "a discount is in order");
	   
	   $trans->quantity(10);
	   
	   $this->assertEquals((string)$calc->calculateTotal(),(string) ((4.99+4.99)*10), "it only costs 4.99*11");
	   $this->assertFalse($calc->applyDiscountHuh(), "a discount is not in order");
	   
	   $trans->quantity(10);
	   $trans->users(5);
	   $calc->setToOptimum();
	   
	   $this->assertEquals((string)$calc->calculateTotal(),(string) round(.9*((14.99)*10 + (14.99-(4.99+4.99))*2),2), "they get the discount");
	   $this->assertTrue($calc->applyDiscountHuh(), "a discount is in order");
   }

};

?>