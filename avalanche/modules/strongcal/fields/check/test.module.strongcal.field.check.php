<?
Class TestAurora_field_check extends Abstract_Avalanche_TestCase { 

   public function test_new_checkbox() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assert(is_object($check), "the check box is an object" );
	$this->assertEquals($check->prompt(), "checkbox: ", "the check box is an object" );
   }

   public function test_new_checkbox_prompt() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assertEquals($check->prompt(), "checkbox: ", "the checkbox has prompt \"checkbox: \"" );
   }

   public function test_new_checkbox_display() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assertEquals($check->display_value(), "NO", "the checkbox value to display defaults to 'NO'" );
   }

   public function test_new_checkbox_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assertEquals($check->value(), "0", "the checkbox value defaults to '0'" );
   }

   public function test_new_checkbox_type() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assertEquals($check->type(), CHECKBOX_INPUT, "the checkbox if is of type CHECKBOX_INPUT" );
   }

   public function test_new_checkbox_displaytype() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check = $fm->getField("check");

	$this->assertEquals($check->displayType(), "CHECKBOX", "the checkbox if has displaytype \"CHECKBOX\"" );
   }

   public function test_new_largetext_style() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$fm = $strongcal->fieldManager();
	$check_obj = $fm->getField("check");

	$this->assertEquals($check_obj->style(), 0, "the largetext has 0 as the default style" );

	$check_obj->set_style(1);
	$this->assertEquals($check_obj->style(), 0, "the largetext cannot change style" );

	$check_obj->set_style(2);
	$this->assertEquals($check_obj->style(), 0, "the largetext cannot change style" );
   }

   public function test_new_check_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$check_obj = $fm->getField(CHECKBOX_INPUT);

	$check_obj->set_value(0);

	$this->assertEquals($check_obj->value(), "0", "the checkbox is not checked" );

	$fake_form_data = array("prefix_" => "1");

	$this->assert($check_obj->load_form_value("prefix_", $fake_form_data), "the checkbox updated from the form value");

	$this->assertEquals($check_obj->value(), "1", "the checkbox is checked" );
   }
   
   public function test_new_check_loading_missing_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(CHECKBOX_INPUT);

	$date_obj->set_value(0);

	$this->assertEquals($date_obj->value(), "0", "the checkbox is not checked" );

	$fake_form_data = array("prefix_" => "1");

	$this->assert(!$date_obj->load_form_value("incorrect_prefix_", $fake_form_data), "the checkbox rejected the form data");

	$this->assertEquals($date_obj->value(), "0", "the checkbox is still not checked" );
   }
};


?>