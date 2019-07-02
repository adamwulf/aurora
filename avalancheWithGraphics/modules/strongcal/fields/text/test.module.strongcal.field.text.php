<?
Class TestAurora_field_text extends Abstract_Avalanche_TestCase { 

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

   public function test_new_text() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assert(is_object($text_obj), "the text field is an object" );
   }

   public function test_new_text_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->prompt(), "text input: ", "the text field has prompt \"text input: \"" );
   }

   public function test_new_text_field() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->field(), "", "the text field has field \"\"" );

	$text_obj->set_field("foo");

	$this->assertEquals($text_obj->field(), "foo", "the text field has field \"\"" );
   }

   public function test_new_text_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->display_value(), "", "the text value to display blank by default" );
   }

   public function test_new_text_value() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");
	$this->assertEquals($text_obj->value(), "", "the text value defaults to ''" );

	$text_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($text_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the text value has been set to '\' can \" you \n\r \" believe \\\' it?!~~'" );

	set_magic_quotes_runtime (1);
	$text_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($text_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the text value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' with magic quotes runtime" );

	set_magic_quotes_runtime (0);
	$text_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~");
	$this->assertEquals($text_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the text value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' without magic quotes runtime" );
   }

   public function test_new_text_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->type(), SMALL_TEXT_INPUT, "the text if is of type SMALL_TEXT_INPUT" );
   }

   public function test_new_text_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->displayType(), "SMALL TEXT", "the text if has displaytype \"SMALL TEXT\"" );
   }

   public function test_new_text_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField("text");

	$this->assertEquals($text_obj->style(), 0, "the text has 0 as the default style" );

	$text_obj->set_style(1);
	$this->assertEquals($text_obj->style(), 0, "the text cannot change style" );

	$text_obj->set_style(2);
	$this->assertEquals($text_obj->style(), 0, "the text cannot change style" );
   }

   public function test_new_text_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField(SMALL_TEXT_INPUT);

	$text_obj->set_value("first sample");

	$this->assertEquals($text_obj->value(), "first sample", "the text is 'first sample'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");
        $this->assert($text_obj->load_form_value("prefix_", $fake_form_data), "the text updated from the form value");
        $this->assertEquals($text_obj->value(), "my sample form input\n\r with line breaks\r\n", "the text has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");
        $this->assert($text_obj->load_form_value("prefix_", $fake_form_data), "the text updated from the form value");
        $this->assertEquals($text_obj->value(), "my sample form input\n\r with line breaks\r\n", "the text has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );

   }
   
   public function test_new_text_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField(SMALL_TEXT_INPUT);

	$text_obj->set_value("first sample");

	$this->assertEquals($text_obj->value(), "first sample", "the text is 'first sample'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");

	$this->assert(!$text_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the text rejected the form data");

	$this->assertEquals($text_obj->value(), "first sample", "the text value is still 'first sample'" );
   }
};

?>