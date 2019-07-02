<?

Class TestGoogleGui extends WebTestCase {

   public function setUp(){

   }

   public function tearDown(){

   }


    function testTitle(){
        $this->get("http://www.google.com/");
        $this->assertTitle("Google");
    }

    function testGoogle(){
	global $avalanche;
	$this->get("http://www.google.com/");
	$this->assertTrue($this->setField("q", "Inversion Designs"), "set query field");
	$this->clickSubmit("Google Search");
	$this->assertTitle("Inversion Designs - Google Search");
	$this->setMaximumRedirects(1);
    }
};

?>