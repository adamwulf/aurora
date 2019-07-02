<?
Class TestAurora_field_largetext extends Abstract_Avalanche_TestCase { 

   private $_magic_quotes_runtime;


   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
	$this->_magic_quotes_runtime = get_magic_quotes_runtime();
   }

   public function tearDown(){
	global $avalanche;
	Abstract_Avalanche_TestCase::tearDown();
	set_magic_quotes_runtime($this->_magic_quotes_runtime);
   }


   public function test_new_largetext() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assert(is_object($largetext_obj), "the largetext field is an object" );
	$this->assert($largetext_obj instanceof module_strongcal_field, "the largetext is a module_strongcal_field");
   }

   public function test_new_largetext_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assertEquals($largetext_obj->prompt(), "large text input: ", "the largetext field has prompt \"large text input: \"" );
   }

   public function test_new_largetext_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assertEquals($largetext_obj->display_value(), "", "the largetext value to display blank by default" );
   }

   public function test_new_largetext_value() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");
	$this->assertEquals($largetext_obj->value(), "", "the largetext value defaults to ''" );

	$largetext_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($largetext_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the largetext value has been set to '\' can \" you \n\r \" believe \\\' it?!~~'" );

	set_magic_quotes_runtime (1);
	$largetext_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($largetext_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the largetext value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' with magic quotes runtime" );

	set_magic_quotes_runtime (0);
	$largetext_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($largetext_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the largetext value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' without magic quotes runtime" );
   }

   public function test_new_largetext_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assertEquals($largetext_obj->type(), LARGE_TEXT_INPUT, "the largetext if is of type LARGE_TEXT_INPUT" );
   }

   public function test_new_largetext_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assertEquals($largetext_obj->displayType(), "LARGE TEXT", "the largetext if has displaytype \"LARGE_TEXT\"" );
   }

   public function test_new_largetext_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField("largetext");

	$this->assertEquals($largetext_obj->style(), 0, "the largetext has 0 as the default style" );

	$largetext_obj->set_style(1);
	$this->assertEquals($largetext_obj->style(), 0, "the largetext cannot change style" );

	$largetext_obj->set_style(2);
	$this->assertEquals($largetext_obj->style(), 0, "the largetext cannot change style" );
   }

   public function test_new_largetext_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField(LARGE_TEXT_INPUT);

	$largetext_obj->set_value("first sample");

	$this->assertEquals($largetext_obj->value(), "first sample", "the largetext is 'first sample'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");
        $this->assert($largetext_obj->load_form_value("prefix_", $fake_form_data), "the large text updated from the form value");
        $this->assertEquals($largetext_obj->value(), "my sample form input\n\r with line breaks\r\n", "the largetext has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");
        $this->assert($largetext_obj->load_form_value("prefix_", $fake_form_data), "the large text updated from the form value");
        $this->assertEquals($largetext_obj->value(), "my sample form input\n\r with line breaks\r\n", "the largetext has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );

   }
   
   public function test_new_largetext_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField(LARGE_TEXT_INPUT);

	$largetext_obj->set_value("first sample");

	$this->assertEquals($largetext_obj->value(), "first sample", "the largetext is 'first sample'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");

	$this->assert(!$largetext_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the largetext rejected the form data");

	$this->assertEquals($largetext_obj->value(), "first sample", "the largetext value is still 'first sample'" );
   }
};


?>