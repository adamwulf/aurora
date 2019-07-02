<?
Class TestAurora_field_date extends Abstract_Avalanche_TestCase { 

   public function test_new_date() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assert(is_object($date_obj), "the date field is an object" );
   }

   public function test_new_date_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->prompt(), "date: ", "the date field has prompt \"date: \"" );
   }

   public function test_new_date_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->display_value(), date("Y-m-d",$strongcal->localtimestamp()), "the date value to display defaults to today's date" );
   }

   public function test_new_date_value() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->value(), "0000-00-001", "the date value defaults to '0000-00-001'" );
   }

   public function test_new_date_to_gui() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");
	$date_obj->set_value("2004-17-09");
	$date_obj->set_field("mydate");
	$date_obj = $date_obj->toGUI("pre_");
 
	$this->assertEquals($date_obj->getName(), "pre_mydate", "the gui object is initialized with the correct name");
	$this->assertEquals($date_obj->getValue(), "2004-17-09", "the gui object has the right data");
   }

   public function test_new_date_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->type(), DATE_INPUT, "the date if is of type DATE_INPUT" );
   }

   public function test_new_date_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->displayType(), "DATE", "the date if has displaytype \"DATE\"" );
   }

   public function test_new_date_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField("date");

	$this->assertEquals($date_obj->style(), 0, "the date if has displaytype \"DATE\"" );

	$date_obj->set_style(1);
	$this->assertEquals($date_obj->style(), 1, "the date if has displaytype \"DATE\"" );

	$date_obj->set_style(2);
	$this->assertEquals($date_obj->style(), 1, "the date if has displaytype \"DATE\"" );
   }

   public function test_new_date_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$date_obj->set_value("1993-05-11");

	$this->assertEquals($date_obj->value(), "1993-05-11", "the date is 1993-05-11" );

	$fake_form_data = array("prefix__day" => "01",
				"prefix__month" => "12",
				"prefix__year" => "1997");

	$this->assert($date_obj->load_form_value("prefix_", $fake_form_data), "the date loaded the form data");

	$this->assertEquals($date_obj->value(), "1997-12-01", "the date has been updated to 1997-12-01" );
   }
   
   public function test_new_date_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$date_obj->set_value("1993-05-11");

	$this->assertEquals($date_obj->value(), "1993-05-11", "the date is 1993-05-11" );

	$fake_form_data = array("prefix__day" => "01",
				"prefix__month" => "12",
				"prefix__year" => "1997");

	$this->assert(!$date_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the data rejected the form data");

	$this->assertEquals($date_obj->value(), "1993-05-11", "the date is still 1993-05-11" );
   }
};


?>