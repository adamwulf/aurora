<?

Class TestAccountsTransactions extends Abstract_Avalanche_TestCase { 

   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
   }

   public function testNewTransaction(){
	   global $avalanche;
	   $accounts = $avalanche->getModule("accounts");
	   
	   $trans = new AccountTransaction($avalanche);
	   $this->assertTrue($trans > 0, " the transaction id is greater than 0");
	   
	   $this->assertFalse($trans->processed(), "the transaction is not processed");
	   $this->assertTrue($trans->processed(true), "the transaction is now processed");
	   $this->assertTrue($trans->processed(), "the transaction is processed");
	   
	   $this->assertFalse($trans->pending(), "the transaction is not pending");
	   $this->assertTrue($trans->pending(true), "the transaction is now pending");
	   $this->assertTrue($trans->pending(), "the transaction is pending");
	   
	   $this->assertEquals($trans->email(), "", "the transaction has no email");
	   $this->assertEquals($trans->email("awulf@ev1.net"), "awulf@ev1.net", "the transaction info is correct");
	   $this->assertEquals($trans->email(), "awulf@ev1.net", "the transaction info is correct");
	   
	   $this->assertEquals($trans->phone(), "", "the transaction has no email");
	   $this->assertEquals($trans->phone("832-928-3396"), "832-928-3396", "the transaction info is correct");
	   $this->assertEquals($trans->phone(), "832-928-3396", "the transaction info is correct");
	   
	   $this->assertEquals($trans->country(), "", "the transaction has no email");
	   $this->assertEquals($trans->country("USA"), "USA", "the transaction info is correct");
	   $this->assertEquals($trans->country(), "USA", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->state(), "", "the transaction has no email");
	   $this->assertEquals($trans->state("TX"), "TX", "the transaction info is correct");
	   $this->assertEquals($trans->state(), "TX", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->zip(), "", "the transaction has no email");
	   $this->assertEquals($trans->zip("77429"), "77429", "the transaction info is correct");
	   $this->assertEquals($trans->zip(), "77429", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->street(), "", "the transaction has no email");
	   $this->assertEquals($trans->street("11842 Hickory Hill"), "11842 Hickory Hill", "the transaction info is correct");
	   $this->assertEquals($trans->street(), "11842 Hickory Hill", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->name(), "", "the transaction has no email");
	   $this->assertEquals($trans->name("Adam Wulf"), "Adam Wulf", "the transaction info is correct");
	   $this->assertEquals($trans->name(), "Adam Wulf", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->quantity(), 0, "the transaction has no email");
	   $this->assertEquals($trans->quantity(5), 5, "the transaction info is correct");
	   $this->assertEquals($trans->quantity(), 5, "the transaction info is correct");
	   
 	   $this->assertEquals($trans->purchasedOn(), "0000-00-00 00:00:00", "the transaction has no email");
	   $this->assertEquals($trans->purchasedOn("2004-09-23 12:45:12"), "2004-09-23 12:45:12", "the transaction info is correct");
	   $this->assertEquals($trans->purchasedOn(), "2004-09-23 12:45:12", "the transaction info is correct");

 	   $this->assertEquals($trans->orderId(), "", "the transaction has no email");
	   $this->assertEquals($trans->orderId("asdf1234"), "asdf1234", "the transaction info is correct");
	   $this->assertEquals($trans->orderId(), "asdf1234", "the transaction info is correct");

	   $prod = $accounts->getProduct(1);
	   $this->assertEquals($trans->product(), false, "the transaction has no email");
	   $this->assertTrue($trans->product($prod) instanceof AccountProduct, "the transaction info is correct");
	   $this->assertTrue($trans->product() instanceof AccountProduct, "the transaction info is correct");

 	   $this->assertEquals($trans->city(), "", "the transaction has no email");
	   $this->assertEquals($trans->city("houston"), "houston", "the transaction info is correct");
	   $this->assertEquals($trans->city(), "houston", "the transaction info is correct");
	   
 	   $this->assertEquals($trans->total(), 0, "the transaction has no email");
	   $this->assertEquals($trans->total(4.99), 4.99, "the transaction info is correct");
	   $this->assertEquals($trans->total(), 4.99, "the transaction info is correct");

	   $trans = new AccountTransaction($avalanche, $trans->getId());
	   
	   $this->assertTrue($trans > 0, " the transaction id is greater than 0");
	   $this->assertTrue($trans->pending(), "the transaction is pending");
	   $this->assertTrue($trans->processed(), "the transaction is processed");
	   $this->assertEquals($trans->email(), "awulf@ev1.net", "the transaction info is correct");
	   $this->assertEquals($trans->phone(), "832-928-3396", "the transaction info is correct");
	   $this->assertEquals($trans->country(), "USA", "the transaction info is correct");
	   $this->assertEquals($trans->state(), "TX", "the transaction info is correct");
	   $this->assertEquals($trans->zip(), "77429", "the transaction info is correct");
	   $this->assertEquals($trans->street(), "11842 Hickory Hill", "the transaction info is correct");
	   $this->assertEquals($trans->name(), "Adam Wulf", "the transaction info is correct");
	   $this->assertEquals($trans->quantity(), 5, "the transaction info is correct");
	   $this->assertEquals($trans->purchasedOn(), "2004-09-23 12:45:12", "the transaction info is correct");
	   $this->assertEquals($trans->orderId(), "asdf1234", "the transaction info is correct");
	   $this->assertEquals($trans->city(), "houston", "the transaction info is correct");
	   $this->assertTrue($trans->product() instanceof AccountProduct, "the transaction info is correct");
	   $this->assertEquals($trans->total(), 4.99, "the transaction info is correct");
   }

};

?>