<?
Class TestAurora_field_time extends Abstract_Avalanche_TestCase { 



   public function test_new_time() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assert(is_object($time_obj), "the time field is an object" );
   }

   public function test_new_time_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->prompt(), "time: ", "the time field has prompt \"time: \"" );
   }

   public function test_new_time_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->display_value(), date("H:i",$strongcal->localtimestamp()), "the time value to display defaults to today's time" );
   }

   public function test_new_time_value() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->value(), "00:001", "the time value defaults to '0000-00-001'" );
   }

   public function test_new_time_to_gui() {
	global $avalanche, $localtimestamp;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");
	$time_obj->set_field("my_time");
	$time_obj->set_value("10:14");
	$time_obj = $time_obj->toGUI("pre_");
	
	$this->assertEquals($time_obj->getName(), "pre_my_time", "the value for the gui object is correct" );
	$this->assertEquals($time_obj->getValue(), "10:14:00", "the value for the gui object is correct" );
   }

   public function test_new_time_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->type(), TIME_INPUT, "the time if is of type TIME_INPUT" );
   }

   public function test_new_time_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->displayType(), "TIME", "the time if has displaytype \"TIME\"" );
   }

   public function test_new_time_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField("time");

	$this->assertEquals($time_obj->style(), 0, "the time if has style \"TIME\"" );

	$time_obj->set_style(1);
	$this->assertEquals($time_obj->style(), 1, "the time if has displaytype \"TIME\"" );

	$time_obj->set_style(2);
	$this->assertEquals($time_obj->style(), 1, "the time if has displaytype \"TIME\"" );
   }

   public function test_new_time_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$time_obj->set_value("05:11");

	$this->assertEquals($time_obj->value(), "05:11", "the time is 5:11" );

	$fake_form_data = array("prefix__hour" => "01",
				"prefix__minute" => "12",
				"prefix__ampm" => "PM");

	$this->assert($time_obj->load_form_value("prefix_", $fake_form_data), "the time loaded the form data");

	$this->assertEquals($time_obj->value(), "13:12:00", "the time has been updated to 13:12" );
   }
   
   public function test_new_time_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$time_obj->set_value("02:25");

	$this->assertEquals($time_obj->value(), "02:25", "the time is 02:25" );

	$fake_form_data = array("prefix__hour" => "01",
				"prefix__minute" => "12",
				"prefix__ampm" => "AM");

	$this->assert(!$time_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the data rejected the form data");

	$this->assertEquals($time_obj->value(), "02:25", "the time is still 02:25" );
   }


};


?>