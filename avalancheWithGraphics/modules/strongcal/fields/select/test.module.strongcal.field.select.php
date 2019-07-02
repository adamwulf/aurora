<?

Class TestAurora_field_select extends Abstract_Avalanche_TestCase { 

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


   public function test_new_select() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assert(is_object($select_obj), "the select field is an object" );
   }

   public function test_new_select_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assertEquals($select_obj->prompt(), "drop down: ", "the select field has prompt \"drop down: \"" );
   }

   public function test_new_select_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assertEquals($select_obj->display_value(), "", "the select value to display blank by default" );
   }

   public function test_new_select_value() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");
	$this->assertEquals($select_obj->value(), "", "the select value defaults to ''" );

	$select_obj->set_value("option1\noption2\n1");
	$this->assertEquals($select_obj->value(), "option1\noption2\n1", "the select value has been set to 'option1\\noption2\\n1'" );

	set_magic_quotes_runtime (1);
	$select_obj->set_value("option4\noption5\n0");
	$this->assertEquals($select_obj->value(), "option4\noption5\n0", "the select value has been set to 'option4\noption5\n0' with magic quotes runtime" );

	set_magic_quotes_runtime (0);
	$select_obj->set_value("option3\noption2\n1");
	$this->assertEquals($select_obj->value(), "option3\noption2\n1", "the select value has been set to 'option3\noption2\n1' without magic quotes runtime" );
   }

   public function test_new_select_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assertEquals($select_obj->type(), SELECT_INPUT, "the text if is of type SELECT_INPUT" );
   }

   public function test_new_select_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assertEquals($select_obj->displayType(), "DROP DOWN", "the select if has displaytype \"DROP DOWNS\"" );
   }

   public function test_new_select_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField("select");

	$this->assertEquals($select_obj->style(), 0, "the select has 0 as the default style" );

	$select_obj->set_style(1);
	$this->assertEquals($select_obj->style(), 1, "the select should be able to change to style 1" );

	$select_obj->set_style(2);
	$this->assertEquals($select_obj->style(), 1, "the select should not be able to change style to style 2" );
   }

   public function test_new_select_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$select_obj->set_value("first sample\nsample data\n\nsecond\nother\n1");

	$this->assertEquals($select_obj->value(), "first sample\nsample data\n\nsecond\nother\n1", "the select is 'first sample'" );

	$fake_form_data = array("prefix_" => "sample data");
        $this->assert($select_obj->load_form_value("prefix_", $fake_form_data), "the select updated from the form value");
        $this->assertEquals($select_obj->value(), "first sample\nsample data\n1\nsecond\nother\n", "the select has been updated to 'first\&nbsp;sample\\nsample data\\n1\\nsecond\\nother\\n'" );

	$fake_form_data = array("prefix_" => "other");
        $this->assert($select_obj->load_form_value("prefix_", $fake_form_data), "the select updated from the form value");
        $this->assertEquals($select_obj->value(), "first sample\nsample data\n\nsecond\nother\n1", "the select has been updated to 'first\&nbsp;sample\\nsample data\\n\\nsecond\\nother\\n1'" );

   }
   
   public function test_new_select_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$select_obj->set_value("first sample");

	$this->assertEquals($select_obj->value(), "first sample", "the select is 'first sample'" );

	$fake_form_data = array("prefix_" => "my sample form input\n\r with line breaks\r\n");

	$this->assert(!$select_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the select rejected the form data");

	$this->assertEquals($select_obj->value(), "first sample", "the select value is still 'first sample'" );
   }


   public function test_select_sending_value_back_and_forth() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$select_obj->set_value("first sample\nsample data\n\nsecond\nother\n1");

	$this->assertEquals($select_obj->value(), "first sample\nsample data\n\nsecond\nother\n1", "the select is 'first sample\\nsample data\\n\\nsecond\\nother\\n1'" );

	$fake_form_data = array("prefix_" => "other");

	$this->assert($select_obj->load_form_value("prefix_", $fake_form_data), "the select accepts the form data");

	$this->assertEquals($select_obj->value(), "first sample\nsample data\n\nsecond\nother\n1", "the select value is still 'first sample\\nsample data\\n\\nsecond\\nother\\n1'" );

	$select_obj->set_value($select_obj->value());
	$select_obj->set_value($select_obj->value());
	$select_obj->set_value($select_obj->value());
	$select_obj->set_value($select_obj->value());
	$this->assertEquals($select_obj->value(), "first sample\nsample data\n\nsecond\nother\n1", "the select is 'first sample\\nsample data\\n\\nsecond\\nother\\n1'" );
   }
};

?>