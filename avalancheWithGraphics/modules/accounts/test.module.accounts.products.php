<?

Class TestAccountsProducts extends Abstract_Avalanche_TestCase { 

   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
   }

   public function testGetProducts(){
	   global $avalanche;
	   
	   $accounts = $avalanche->getModule("accounts");
	   $prods = $accounts->getProducts();
	   $this->assertEquals(count($prods), 8, "there are 8 products");
	   
	   foreach($prods as $prod){
		   $this->assertTrue(is_object($prod), "each product is an object");
		   if(is_object($prod)){
			   $this->assertTrue($prod instanceof AccountProduct, "each is a AccountProduct");
		   }
		   $this->assertTrue(is_object($accounts->getProduct($prod->getId())), "i can get a single product");
	   }
   }

   public function testProduct(){
	   global $avalanche;
	   
	   $accounts = $avalanche->getModule("accounts");
	   $prod = $accounts->getProduct(1);

	   $old_desc = $prod->description();
	   $this->assertEquals($prod->description(), "Price per month for 1 user", "the product has no email");
	   $this->assertEquals($prod->description("this is a description"), "this is a description", "the product info is correct");
	   $this->assertEquals($prod->description($old_desc), $old_desc, "the product info is correct");

	   $old_price = 4.99;
	   $this->assertEquals($prod->pricePerMonth(), $old_price, "the product has no email");
	   $this->assertEquals($prod->pricePerMonth(2.66), 2.66, "the product info is correct");
	   $this->assertEquals($prod->pricePerMonth($old_price), $old_price, "the product info is correct");

	   $old_price = 4.99;
	   $this->assertEquals($prod->pricePerUser(), $old_price, "the product has no email");
	   $this->assertEquals($prod->pricePerUser(2.66), 2.66, "the product info is correct");
	   $this->assertEquals($prod->pricePerUser($old_price), $old_price, "the product info is correct");

	   $old_users = 1;
	   $this->assertEquals($prod->users(), $old_users, "the product has no email");
	   $this->assertEquals($prod->users(2), 2, "the product info is correct");
	   $this->assertEquals($prod->users($old_users), $old_users, "the product info is correct");
   }
};

?>