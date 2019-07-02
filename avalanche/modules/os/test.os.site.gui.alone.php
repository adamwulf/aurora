<?

Class TestOsSiteGui extends WebTestCase { 
   	
   public function setUp(){
	   $this->getBrowser()->setConnectionTimeout(120);
   }
   
   public function tearDown(){
	  
   }


    function testTitle(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
    }

    function testFeatures(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Features");
	$this->assertTitle("Inversion Designs&apos; Aurora - Features");
    }

    function testAdvantages(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Advantages");
	$this->assertTitle("Inversion Designs&apos; Aurora - Advantages");
    }

    function testPricing(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Pricing");
	$this->assertTitle("Inversion Designs&apos; Aurora - Pricing");
    }

    function testFreeTrial(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Free Trial");
	$this->assertTitle("Inversion Designs&apos; Aurora - Free Trial");
    }

    function testContact(){
        $this->get("http://www.inversiondesigns.com/");
        $this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Contact");
	$this->assertTitle("Inversion Designs&apos; Aurora - Contact");
    }

    function testFreeTrialCreation(){
	global $avalanche;
	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
	$acctmod = $avalanche->getModule("accounts");
	$this->get("http://www.inversiondesigns.com/");
	$this->assertTitle("Inversion Designs&apos; Aurora - Overview");
	$this->clickLink("Free Trial");
	$this->assertTitle("Inversion Designs&apos; Aurora - Free Trial");
	$this->setField("name", "testx");
	$this->setField("title", "My Awesome Site!");
	$this->assertTrue($this->setField("email", "testbot@inversiondesigns.com"), "set email");
	$this->assertTrue($this->setField("testserver", "1"), "set testserver");
	$this->clickSubmit("Create Account");
	$this->assertTitle("Inversion Designs&apos; Aurora - Free Trial");
	$this->setMaximumRedirects(2);
	if(!$this->clickLink("Continue")){
		$this->fail("can't click link");
		$this->showSource();
	}
	//$this->showSource();
	$this->assertTitle("Aurora - My Awesome Site!");
	// reset the mysql cache
	$avalanche->reset();
	try{
		$this->assertTrue($acctmod->deleteAccount("testx"), "account deleted");
	}catch(AccountNotFoundException $e){
		$this->fail($e->getMessage());
	}
    }
};

?>