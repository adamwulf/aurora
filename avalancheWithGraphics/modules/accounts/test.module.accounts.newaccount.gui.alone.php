<?

Class TestAccountsNewAccountGui extends WebTestCase { 
   private $test_name = "testx";
   private $test_password;
  
   public function setUp(){
	global $avalanche;
	// clear the cache of sent letters
	$avalanche->getMailMan()->reset();
	
	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
	$accounts = $avalanche->getModule("accounts");
	$sql = "DELETE FROM " . PREFIX . "accounts_transactions";
	$avalanche->mysql_query($sql);
	try{
		$accounts->getAccount($this->test_name);
	}catch(AccountNotFoundException $e){
		$this->getBrowser()->setConnectionTimeout(120);
		$this->test_password = $accounts->addAccount($this->test_name, "null@inversiondesigns.com", "My Site's Awesome\!", "inversiondesigns.com");
	}
   }
   
   private $methods_tested_so_far = 0;
   public function tearDown(){
	   global $avalanche;
	   $this->methods_tested_so_far++;
	   $ms = get_class_methods(get_class($this));
	   $method_count = 0;
	   foreach($ms as $m){
		   if(strpos($m, "test") === 0){
			   $method_count++;
		   }
	   }
	   if($method_count == $this->methods_tested_so_far){
		$accounts = $avalanche->getModule("accounts");
		$this->assertTrue($accounts->deleteAccount($this->test_name), "the account has been deleted");
	   }
	   $avalanche->logOut();
   }

   public function test_get_test_account(){
	global $avalanche;
	$accounts = $avalanche->getModule("accounts");
	$account = $accounts->getAccount($this->test_name);
	$this->assertTrue(is_object($account), "the account is an object");
   }

    function testTitle(){
        $this->get("http://" . $this->test_name . ".inversiondesigns.com/");
        $this->assertTitle("Aurora - My Site's Awesome\!");
    }

    function testUsers(){
	global $avalanche;
	$accounts = $avalanche->getModule("accounts");
	$account = $accounts->getAccount($this->test_name);
	$this->assertTrue($account->maxUsers() === 100, "the account allows up to 100 users");
    }

    function testMonthsSoFar(){
	global $avalanche;
	$accounts = $avalanche->getModule("accounts");
	$account = $accounts->getAccount($this->test_name);
	$this->assertTrue((int)round($account->monthsSoFar(),0) === 1, "the account allows 1 month by default");
    }

   public function testNewTransaction(){
	   global $avalanche;
	   $accounts = $avalanche->getModule("accounts");
	   $account = $accounts->getAccount($this->test_name);
	
	   $this->assertTrue(count($account->getTransactions()) === 0, "there are no transactions");

	   $trans = new AccountTransaction($avalanche);
	   $this->assertTrue($trans > 0, " the transaction id is greater than 0");
	   $this->assertTrue($trans->account() === false, "the transaction has no account");
	   $this->assertTrue($trans->account($account) instanceof module_accounts_account, "the transaction info is correct");
	   $this->assertTrue($trans->account() instanceof module_accounts_account, "the transaction info is correct");


 	   $trans = new AccountTransaction($avalanche, $trans->getId());
	   $this->assertTrue($trans->account() instanceof module_accounts_account, "the transaction info is correct");

	   $this->assertTrue(count($account->getTransactions()) === 1, "there are no transactions");
   }
   
    function testAccountManagementTitle(){
        $this->get("http://www.inversiondesigns.com/index.php?page=members&testserver=1&account=" . $this->test_name);
        $this->assertTitle("Inversion Designs&apos; Aurora - Account Management");
    }
    
    
    function testAccountManagementLogin(){
        $this->get("http://www.inversiondesigns.com/index.php?page=members&testserver=1&account=" . $this->test_name);
	$this->assertField("account", $this->test_name);
	$this->assertField("username", "");
	$this->assertField("password", "");
	$this->assertField("testserver", "1");
	$this->assertTrue($this->setField("account", $this->test_name));
	$this->assertTrue($this->setField("username", $this->test_name));
	$this->assertTrue($this->setField("password", $this->test_password));
	$this->assertTrue($this->clickSubmit("Log In"));
	$this->assertWantedText($this->test_name . ".inversiondesigns.com");
	$this->assertWantedText("logout");
    }
    
    function testAccountAutoEmails(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$accounts = $avalanche->getModule("accounts");
	$account = $accounts->getAccount($this->test_name);
	// ensure the account is loaded
	$account->name();
	$body = $avalanche->execute(new AccountsCommunicationVisitor($avalanche));
	$mailman = $avalanche->getMailMan();
	$letters = $mailman->getLetters();
	$this->assertTrue(count($letters) == 0, "no letters have been sent");
	
	// hack to change added_on date to yesterday
	// otherwise we'd have to actuallly add an account and wait a day to test it...
	// make the account added on yesterday
	$offset = - 26 * 60 * 60;
	$added = new DateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+$offset));
	$sql = "UPDATE " . $avalanche->PREFIX() . "accounts SET `added_on` = '" . ($added->toString()) . "' WHERE `id` = '" . $account->getId() . "'";
	$avalanche->mysql_query($sql);
	// reload the account so that it takes the new added_on date
	$account->reload();
	
	//
	// now we check for the very first welcome email
	//
	$body = $avalanche->execute(new AccountsCommunicationVisitor($avalanche));
	$mailman = $avalanche->getMailMan();
	$letters = $mailman->getLetters();
	
	// 2 letters are sent. 1 to me, and 1 to the account.
	$this->assertTrue(count($letters) == 1, "one letter has been sent");
	
	$this->assertTrue($letters[0]["subject"] == "Welcome to Aurora Calendar!", "the subject is right");
	$this->assertTrue($letters[0]["to"] == $account->email(), "the mail was sent to the right person");
	// done checking for first welcome email



	// hack to change expire_on date to tomorrow
	// otherwise we'd have to actuallly add an account and wait a day to test it...
	// make the account added today (so no more emails for welcome/tips)
	$offset = 0;
	$added = new DateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+$offset));
	$sql = "UPDATE " . $avalanche->PREFIX() . "accounts SET `added_on` = '" . ($added->toString()) . "' WHERE `id` = '" . $account->getId() . "'";
	$avalanche->mysql_query($sql);
	// make the account expire tomorrow
	$offset = 26 * 60 * 60;
	$expire = new DateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+$offset));
	$sql = "UPDATE " . $avalanche->PREFIX() . "accounts SET `expires_on` = '" . ($expire->toString()) . "' WHERE `id` = '" . $account->getId() . "'";
	$avalanche->mysql_query($sql);
	// reload the account so that it takes the new added_on date
	$account->reload();
	
	//
	// now we check for the warning about expire email
	//
	$avalanche->getMailMan()->reset();
	$body = $avalanche->execute(new AccountsCommunicationVisitor($avalanche));
	$mailman = $avalanche->getMailMan();
	$letters = $mailman->getLetters();
	$this->assertTrue(count($letters) == 1, "one letter has been sent");
	
	$this->assertTrue($letters[0]["subject"] == "Uh Oh! Your Aurora Calendar account is about to expire!", "the subject is right");
	$this->assertTrue($letters[0]["to"] == $account->email(), "the mail was sent to the right person");
	// done checking for first welcome email
    }
};

?>