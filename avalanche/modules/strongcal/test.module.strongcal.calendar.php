<?
Class TestAurora_calendar extends Abstract_Avalanche_TestCase {

   private $CST = -6;

   public function setUp(){
	global $avalanche;
	Abstract_Avalanche_TestCase::setUp();

	$sql = "DELETE FROM " . PREFIX . "strongcal_cal_38";
	$result = mysql_query($sql);
	if(mysql_error()){
		throw new DatabaseException(mysql_error());
	}

	$strongcal = $avalanche->getModule("strongcal");
	$strongcal->timezone($this->CST);
   }

   public function test_TestCase_init(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");
	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");
   }


   public function test_load_calendar(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(is_int($cal->getId()), "the calendar id is a number");
	$this->assertEquals($cal->getId(), 38, "the calenar id is 38");
   }

   public function test_new_field_with_bad_name(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$check = $fm->getField(CHECKBOX_INPUT);

	$field_properties = $check->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_\\check";

	$this->assert(!$cal->addField($field_properties), "calendar could not ad a checkbox due to bad syntax for field name");

	$check = $cal->getField("my_\\check");

	$this->assert(!is_object($check), "calendar didn't added a checkbox");
   }

/*
 * begin tests for checkbox
 */

   public function test_new_checkbox_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$check = $fm->getField(CHECKBOX_INPUT);

	$field_properties = $check->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_check";

	$this->assert($cal->addField($field_properties), "calendar added a checkbox");

	$check = $cal->getField("my_check");

	$this->assert(is_object($check), "calendar added a checkbox");
	$this->assert($cal->dropField("my_check"), "the checkbox was dropped from the calendar");
   }

   public function test_new_checkbox_change_value(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$check = $fm->getField(CHECKBOX_INPUT);

	$field_properties = $check->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_check";

	$this->assert($cal->addField($field_properties), "calendar added a checkbox");

	$check = $cal->getField("my_check");

	$this->assert(is_object($check), "calendar added a checkbox");
	$this->assertEquals($check->value(), "0", "the checkbox is unchecked");

	$check->set_value($check->value());

	$this->assertEquals($check->value(), "0", "the checkbox is still unchecked");
	$this->assert($cal->dropField("my_check"), "the checkbox was dropped from the calendar");
   }

   public function test_new_checkbox_change_value_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$check = $fm->getField(CHECKBOX_INPUT);

	$field_properties = $check->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_check";

	$this->assert($cal->addField($field_properties), "calendar added a checkbox");

	$check = $cal->getField("my_check");

	$this->assert(is_object($check), "calendar added a checkbox");
	$this->assertEquals($check->value(), "0", "the checkbox is unchecked");

	$check->set_value(1);

	// reload it, to make sure changes are made to database in backend
	$check = $cal->getField("my_check");
	$this->assertEquals($check->value(), "1", "the checkbox is checked");
	$this->assert($cal->dropField("my_check"), "the checkbox was dropped from the calendar");
   }

/*
 * begin tests for date field
 */

   public function test_new_date_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$this->assertEquals($field_properties["type"], DATE_INPUT, "the date field requests to be added as type \"date\"");

	$this->assert($cal->addField($field_properties), "add a date to a calendar");

	$this->assertEquals($date_obj->displayType(), "DATE", "the date if has displaytype \"DATE\"" );
	$this->assert($cal->dropField("sample"), "the date was dropped from the calendar");
   }

   public function test_new_date_in_calendar_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$cal->addField($field_properties);

	$date_obj = $cal->getField("sample");

	$date_obj->set_value("1993-05-11");

	$this->assertEquals($date_obj->value(), "1993-05-11", "the date is 1993-05-11" );

	$fake_form_data = array("prefix_sample_day" => "01",
				"prefix_sample_month" => "12",
				"prefix_sample_year" => "1997");

	$date_obj->load_form_value("prefix_", $fake_form_data);;

	$this->assertEquals($date_obj->value(), "1997-12-01", "the date has been updated to 1997-12-01" );

	// reload the field from the database
	$cal->reload();
	$date_obj = $cal->getField("sample");

	$this->assertEquals($date_obj->value(), "1997-12-01", "the date has been updated to 1997-12-01" );
	$this->assert($cal->dropField("sample"), "the date was dropped from the calendar");
   }


   public function test_new_date() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_date";

	$this->assert($cal->addField($field_properties), "calendar added a date");

	$check = $cal->getField("my_date");

	$this->assert(is_object($check), "calendar added a date");
	$this->assert($cal->dropField("my_date"), "the date was dropped from the calendar");
   }

   public function test_new_date_change_value(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_date";

	$this->assert($cal->addField($field_properties), "calendar added a date");

	$date_obj = $cal->getField("my_date");

	$this->assert(is_object($date_obj), "calendar added a date field");
	$this->assertEquals($date_obj->value(), "0000-00-001", "the date is today");

	$date_obj->set_value($date_obj->value());

	$this->assertEquals($date_obj->value(), "0000-00-001", "the date is still today");
	$this->assert($cal->dropField("my_date"), "the date was dropped from the calendar");
   }

   public function test_new_date_change_value_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_date";

	$this->assert($cal->addField($field_properties), "calendar added a date");

	$date_obj = $cal->getField("my_date");

	$this->assert(is_object($date_obj), "calendar added a date field");
	$this->assertEquals($date_obj->value(), "0000-00-001", "the date is today");

	$date_obj->set_value("1997-12-01");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$date_obj = $cal->getField("my_date");
	$this->assertEquals($date_obj->value(), "1997-12-01", "the date is 1997-12-01");
	$this->assertEquals($date_obj->display_value(), "1997-12-01", "the date is displayed as 1997-12-01");
	$this->assert($cal->dropField("my_date"), "the date was dropped from the calendar");
   }

   public function test_new_date_style(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_date";

	$this->assert($cal->addField($field_properties), "calendar added a date");

	$date_obj = $cal->getField("my_date");

	$this->assert(is_object($date_obj), "calendar added a date field");
	$this->assertEquals($date_obj->value(), "0000-00-001", "the date is today");

	$date_obj->set_value("1997-12-01");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$date_obj = $cal->getField("my_date");
	$this->assertEquals($date_obj->value(), "1997-12-01", "the date is 1997-12-01");
	$this->assertEquals($date_obj->display_value(), "1997-12-01", "the date is displayed as 1997-12-01");
	$this->assert($cal->dropField("my_date"), "the date was dropped from the calendar");
   }

   public function test_new_date_change_style_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(DATE_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_date";

	$this->assert($cal->addField($field_properties), "calendar added a date");

	$date_obj = $cal->getField("my_date");

	$this->assert(is_object($date_obj), "calendar added a date field");
	$this->assertEquals($date_obj->style(), 0, "the date field does not have style");
	$this->assertEquals(count($date_obj->style_options()), 2, "the date field has 2 styles");

	$date_obj->set_style(1);

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$date_obj = $cal->getField("my_date");
	$this->assertEquals($date_obj->style(), 1, "the date field has style");

	// try to set the style to an invalid value
	$date_obj->set_style(2);
	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$date_obj = $cal->getField("my_date");
	$this->assertEquals($date_obj->style(), 1, "the date field has style");
	$this->assert($cal->dropField("my_date"), "the date was dropped from the calendar");
   }

   /*
    * tests for largetext
    */
   public function test_new_largetext_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField(LARGE_TEXT_INPUT);

	$field_properties = $largetext_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$this->assertEquals($field_properties["type"], LARGE_TEXT_INPUT, "the largetext field requests to be added as type \"largetext\"");

	$this->assert($cal->addField($field_properties), "add a largetext to a calendar");

	$this->assertEquals($largetext_obj->displayType(), "LARGE TEXT", "the largetext if has displaytype \"LARGE_TEXT\"" );


	// reload the calendar to be sure we're not working with a cached field object
	$cal->reload();
	$largetext_obj = $cal->getField("sample");
	$this->assert(is_object($largetext_obj), "get the recently added field from the calendar");

	set_magic_quotes_runtime (1);
	$cal->reload();
	$this->assert($largetext_obj->set_value("\' caan \" you \n\r \" believe \\\' it?!~~"), "the value for the field (calendarwide default) has been updated successfully");
	$cal->reload();
	$largetext_obj = $cal->getField("sample");
	$this->assertEquals($largetext_obj->calendar()->getId(), $cal->getId(), "the field loads the correct calendar");
	$this->assertEquals($largetext_obj->value(), "\' caan \" you \n\r \" believe \\\' it?!~~", "the largetext value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' with magic quotes runtime" );

	set_magic_quotes_runtime (0);
	$cal->reload();
	$this->assert($largetext_obj->set_value("\' this \" \\ \r is a \r \ different value\\"), "the value for the field (calendarwide default) has been updated successfully");
	$cal->reload();
	$largetext_obj = $cal->getField("sample");
	$this->assertEquals($largetext_obj->calendar()->getId(), $cal->getId(), "the field loads the correct calendar");
	$this->assertEquals($largetext_obj->value(), "\' this \" \\ \r is a \r \ different value\\", "the largetext value has been set to '\' this \" \\ \r is a \r \ different value\\' without magic quotes runtime" );
	$this->assert($cal->dropField("sample"), "the largetext was dropped from the calendar");
   }

   public function test_new_largetext_in_calendar_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$largetext_obj = $fm->getField(LARGE_TEXT_INPUT);

	$field_properties = $largetext_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$cal->addField($field_properties);

	$largetext_obj = $cal->getField("sample");

	$largetext_obj->set_value("sample data");

	$this->assertEquals($largetext_obj->value(), "sample data", "the largetext is 'sample data'" );

	$fake_form_data = array("prefix_sample" => "my sample form input\n\r with line breaks\r\n");

	$largetext_obj->load_form_value("prefix_", $fake_form_data);;


	// reload the field from the database
	$cal->reload();
	$largetext_obj = $cal->getField("sample");

	$this->assertEquals($largetext_obj->value(), "my sample form input\n\r with line breaks\r\n", "the largetext has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );
	$this->assert($cal->dropField("sample"), "the largetext was dropped from the calendar");
   }

   public function test_new_largetext() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(LARGE_TEXT_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_largetext";

	$this->assert($cal->addField($field_properties), "calendar added a largetext");

	$check = $cal->getField("my_largetext");

	$this->assert(is_object($check), "calendar added a largetext");
	$this->assert($cal->dropField("my_largetext"), "the largetext was dropped from the calendar");
   }

   public function test_new_largetext_change_value(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(LARGE_TEXT_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_largetext";

	$this->assert($cal->addField($field_properties), "calendar added a largetext");

	$date_obj = $cal->getField("my_largetext");

	$this->assert(is_object($date_obj), "calendar added a largetext field");
	$this->assertEquals($date_obj->value(), "", "the largetext value is ''");

	$date_obj->set_value($date_obj->value());

	$this->assertEquals($date_obj->value(), "", "the largetext value is ''");
	$this->assert($cal->dropField("my_largetext"), "the largetext was dropped from the calendar");
   }

   public function test_new_largetext_change_value_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$date_obj = $fm->getField(LARGE_TEXT_INPUT);

	$field_properties = $date_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_largetext";

	$this->assert($cal->addField($field_properties), "calendar added a largetext");

	$date_obj = $cal->getField("my_largetext");

	$this->assert(is_object($date_obj), "calendar added a largetext field");
	$this->assertEquals($date_obj->value(), "", "the default largetext value is ''");

	$date_obj->set_value("my new \r \n test \' \" \\ value \\");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$date_obj = $cal->getField("my_largetext");
	$this->assertEquals($date_obj->value(), "my new \r \n test \' \" \\ value \\", "the value is 'my new \r \n test \' \" \\ value \\'");
	$this->assertEquals($date_obj->display_value(), "my new \r \n test \' \" \\ value \\", "the largetextis displayed as 'my new \r \n test \' \" \\ value \\'");
	$this->assert($cal->dropField("my_largetext"), "the largetext was dropped from the calendar");
   }

   /*
    * tests for text field
    */
   public function test_new_text_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField(SMALL_TEXT_INPUT);

	$field_properties = $text_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$this->assertEquals($field_properties["type"], SMALL_TEXT_INPUT, "the text field requests to be added as type \"text\"");

	$this->assert($cal->addField($field_properties), "add a text to a calendar");

	$this->assertEquals($text_obj->displayType(), "SMALL TEXT", "the text if has displaytype \"TEXT\"" );


	// reload the calendar to be sure we're not working with a cached field object
	$cal->reload();
	$text_obj = $cal->getField("sample");
	$this->assert(is_object($text_obj), "get the recently added field from the calendar");

	set_magic_quotes_runtime (1);
	$cal->reload();
	$this->assert($text_obj->set_value("\' can \" you \n\r \" believe \\\' it?!~~"), "the value for the field (calendarwide default) has been updated successfully");
	$cal->reload();
	$text_obj = $cal->getField("sample");
	$this->assertEquals($text_obj->calendar()->getId(), $cal->getId(), "the field loads the correct calendar");
	$this->assertEquals($text_obj->value(), "\' can \" you \n\r \" believe \\\' it?!~~", "the text value has been set to '\' can \" you \n\r \" believe \\\' it?!~~' with magic quotes runtime" );

	set_magic_quotes_runtime (0);
	$cal->reload();
	$this->assert($text_obj->set_value("\' this \" \\ \r is a \r \ different value\\"), "the value for the field (calendarwide default) has been updated successfully");
	$cal->reload();
	$text_obj = $cal->getField("sample");
	$this->assertEquals($text_obj->calendar()->getId(), $cal->getId(), "the field loads the correct calendar");
	$this->assertEquals($text_obj->value(), "\' this \" \\ \r is a \r \ different value\\", "the text value has been set to '\' this \" \\ \r is a \r \ different value\\' without magic quotes runtime" );
	$this->assert($cal->dropField("sample"), "the text was dropped from the calendar");
   }

   public function test_new_text_in_calendar_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$text_obj = $fm->getField(SMALL_TEXT_INPUT);

	$field_properties = $text_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$cal->addField($field_properties);

	$text_obj = $cal->getField("sample");

	$text_obj->set_value("sample data");

	$this->assertEquals($text_obj->value(), "sample data", "the text is 'sample data'" );

	$fake_form_data = array("prefix_sample" => "my sample form input\n\r with line breaks\r\n");

	$text_obj->load_form_value("prefix_", $fake_form_data);;

	$this->assertEquals($text_obj->value(), "my sample form input\n\r with line breaks\r\n", "the text has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );

	// reload the field from the database
	$cal->reload();
	$text_obj = $cal->getField("sample");

	$this->assertEquals($text_obj->value(), "my sample form input\n\r with line breaks\r\n", "the text has been updated to 'my sample form input\\n\\r with line breaks\\r\\n'" );
	$this->assert($cal->dropField("sample"), "the text was dropped from the calendar");
   }
/*
 * begin tests for time field
 */

   public function test_new_time_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$this->assertEquals($field_properties["type"], TIME_INPUT, "the time field requests to be added as type \"time\"");

	$this->assert($cal->addField($field_properties), "add a time to a calendar");

	$this->assertEquals($time_obj->displayType(), "TIME", "the time if has displaytype \"TIME\"" );
	$this->assert($cal->dropField("sample"), "the text was dropped from the calendar");
   }

   public function test_new_time_in_calendar_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$cal->addField($field_properties);

	$time_obj = $cal->getField("sample");

	$time_obj->set_value("15:11");

	$this->assertEquals($time_obj->value(), "15:11", "the time is 15:11" );

	$fake_form_data = array("prefix_sample_hour" => "01",
				"prefix_sample_minute" => "12",
				"prefix_sample_ampm" => "PM");

	$time_obj->load_form_value("prefix_", $fake_form_data);;

	$this->assertEquals($time_obj->value(), "13:12:00", "the time has been uptimed to 13:12" );


	// reload the field from the database
	$cal->reload();
	$time_obj = $cal->getField("sample");

	$this->assertEquals($time_obj->value(), "13:12:00", "the time is changed to 13:12" );
	$this->assert($cal->dropField("sample"), "the text was dropped from the calendar");
   }


   public function test_new_time() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_time";

	$this->assert($cal->addField($field_properties), "calendar added a time");

	$check = $cal->getField("my_time");

	$this->assert(is_object($check), "calendar added a time");
	$this->assert($cal->dropField("my_time"), "the time was dropped from the calendar");
   }

   public function test_new_time_change_value(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_time";

	$this->assert($cal->addField($field_properties), "calendar added a time");

	$time_obj = $cal->getField("my_time");

	$this->assert(is_object($time_obj), "calendar added a time field");
	$this->assertEquals($time_obj->value(), "00:001", "the time is now");

	$time_obj->set_value($time_obj->value());

	$this->assertEquals($time_obj->value(), "00:001", "the time is still now");
	$this->assert($cal->dropField("my_time"), "the time was dropped from the calendar");
   }

   public function test_new_time_change_value_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_time";

	$this->assert($cal->addField($field_properties), "calendar added a time");

	$time_obj = $cal->getField("my_time");

	$this->assert(is_object($time_obj), "calendar added a time field");
	$this->assertEquals($time_obj->value(), "00:001", "the time is now");

	$time_obj->set_value("19:12");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$time_obj = $cal->getField("my_time");
	$this->assertEquals($time_obj->value(), "19:12", "the time is 19:12");
	$this->assertEquals($time_obj->display_value(), "19:12", "the time is displayed as 19:12");
	$this->assert($cal->dropField("my_time"), "the time was dropped from the calendar");
   }

   public function test_new_time_style(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_time";

	$this->assert($cal->addField($field_properties), "calendar added a time");

	$time_obj = $cal->getField("my_time");

	$this->assert(is_object($time_obj), "calendar added a time field");
	$this->assertEquals($time_obj->value(), "00:001", "the time is today");

	$time_obj->set_value("17:53");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$time_obj = $cal->getField("my_time");
	$this->assertEquals($time_obj->value(), "17:53", "the time is 17:53");
	$this->assertEquals($time_obj->display_value(), "17:53", "the time is displayed as 17:53");
	$this->assert($cal->dropField("my_time"), "the time was dropped from the calendar");
   }

   public function test_new_time_change_style_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$time_obj = $fm->getField(TIME_INPUT);

	$field_properties = $time_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_time";

	$this->assert($cal->addField($field_properties), "calendar added a time");

	$time_obj = $cal->getField("my_time");

	$this->assert(is_object($time_obj), "calendar added a time field");
	$this->assertEquals($time_obj->style(), 0, "the time field does not have style");
	$this->assertEquals(count($time_obj->style_options()), 2, "the time field has 2 styles");

	$time_obj->set_style(1);

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$time_obj = $cal->getField("my_time");
	$this->assertEquals($time_obj->style(), 1, "the time field has style");

	// try to set the style to an invalid value
	$time_obj->set_style(2);
	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$time_obj = $cal->getField("my_time");
	$this->assertEquals($time_obj->style(), 1, "the time field has style");
	$this->assert($cal->dropField("my_time"), "the time was dropped from the calendar");
   }

/*
 * begin tests for select field
 */

   public function test_new_select_in_calendar() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$this->assertEquals($field_properties["type"], SELECT_INPUT, "the select field requests to be added as type \"SELECT\"");

	$this->assert($cal->addField($field_properties), "add a select to a calendar");

	$this->assertEquals($select_obj->displayType(), "DROP DOWN", "the select if has displaytype \"DROP DOWN\"" );
	$this->assert($cal->dropField("sample"), "the select was dropped from the calendar");
   }

   public function test_new_select_in_calendar_loading_form_value() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$calendars = $strongcal->getCalendarList();
	$cal = $calendars[0]["calendar"];

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "sample";

	$cal->addField($field_properties);

	$select_obj = $cal->getField("sample");

	$select_obj->set_value("test3\ntest3_disp\n\ntest4\ntest4_disp\n1");

	$this->assertEquals($select_obj->value(), "test3\ntest3_disp\n\ntest4\ntest4_disp\n1", "the select is 'test3\\ntest3_disp\\n0\\ntest4\\ntest4_disp\\n1'" );

	$fake_form_data = array("prefix_sample" => "test3_disp");

	$select_obj->load_form_value("prefix_", $fake_form_data);;

	$this->assertEquals($select_obj->value(), "test3\ntest3_disp\n1\ntest4\ntest4_disp\n", "the select has been updated to 'test3\\ntest3_disp\n1\\ntest4\\ntest4_disp\\n'" );


	// reload the field from the database
	$cal->reload();
	$select_obj = $cal->getField("sample");

	$this->assertEquals($select_obj->value(), "test3\ntest3_disp\n1\ntest4\ntest4_disp\n", "the select is changed to 'test3\\ntest3_disp\\n1\\ntest4\\ntest4_disp\\n'" );
	$this->assert($cal->dropField("sample"), "the select was dropped from the calendar");
   }


   public function test_new_select() {
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_select";

	$this->assert($cal->addField($field_properties), "calendar added a select");

	$check = $cal->getField("my_select");

	$this->assert(is_object($check), "calendar added a select");
	$this->assert($cal->dropField("my_select"), "the select was dropped from the calendar");
   }

   public function test_new_select_change_value(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_select";

	$this->assert($cal->addField($field_properties), "calendar added a select");

	$select_obj = $cal->getField("my_select");

	$this->assert(is_object($select_obj), "calendar added a select field");
	$this->assertEquals($select_obj->value(), "", "the select is empty");

	$select_obj->set_value($select_obj->value());

	$this->assertEquals($select_obj->value(), "", "the select is still empty");
	$this->assert($cal->dropField("my_select"), "the select was dropped from the calendar");
   }

   public function test_new_select_change_value_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_select";

	$this->assert($cal->addField($field_properties), "calendar added a select");

	$select_obj = $cal->getField("my_select");

	$this->assert(is_object($select_obj), "calendar added a select field");
	$this->assertEquals($select_obj->value(), "", "the select is now");

	$select_obj->set_value("this is <b>what is <i font>displayed\ntest1\n1");
	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$select_obj = $cal->getField("my_select");
	$this->assertEquals($select_obj->value(), "this is <b>what is <i font>displayed\ntest1\n1", "this is <b>what is <i font>displayed\\ntest1\\n1'");
	$this->assertEquals($select_obj->display_value(), str_replace(" ", "&nbsp;", htmlspecialchars("this is <b>what is <i font>displayed")), "the select is displayed as 'this is what is displayed'");
	$this->assert($cal->dropField("my_select"), "the select was dropped from the calendar");
   }

   public function test_new_select_style(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_select";

	$this->assert($cal->addField($field_properties), "calendar added a select");

	$select_obj = $cal->getField("my_select");

	$this->assert(is_object($select_obj), "calendar added a select field");
	$this->assertEquals($select_obj->value(), "", "the select is empty");

	$select_obj->set_value("test1\ntest2\n1");

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$select_obj = $cal->getField("my_select");
	$this->assertEquals($select_obj->value(), "test1\ntest2\n1", "the select is 'test1\\ntest2\\n1'");
	$this->assertEquals($select_obj->display_value(), "test1", "the select is displayed as 'test1'");
	$this->assert($cal->dropField("my_select"), "the select was dropped from the calendar");
   }

   public function test_new_select_change_style_to_new(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();
	$select_obj = $fm->getField(SELECT_INPUT);

	$field_properties = $select_obj->to_add();
	$field_properties["prompt"] = "my very own prompt";
	$field_properties["name"] = "my_select";

	$this->assert($cal->addField($field_properties), "calendar added a select");

	$select_obj = $cal->getField("my_select");

	$this->assert(is_object($select_obj), "calendar added a select field");
	$this->assertEquals($select_obj->style(), 0, "the select field does not have style");
	$this->assertEquals(count($select_obj->style_options()), 2, "the select field has 2 styles");

	$select_obj->set_style(1);

	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$select_obj = $cal->getField("my_select");
	$this->assertEquals($select_obj->style(), 1, "the select field has style");

	// try to set the style to an invalid value
	$select_obj->set_style(2);
	// reload it, to make sure changes are made to database in backend
	$cal->reload();
	$select_obj = $cal->getField("my_select");
	$this->assertEquals($select_obj->style(), 1, "the select field has style");
	$this->assert($cal->dropField("my_select"), "the select was dropped from the calendar");
   }

   public function test_add_event(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();


	$new_event = $cal->addEvent();

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");
	$this->assert($cal->removeEvent($new_event->getId()), "the event has been removed");
   }

   public function test_add_event_check_fields(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();

	$new_event = $cal->addEvent();

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	/* begin new test data from test_add_event */

	$cal_fields = $cal->fields();
	$event_fields = $new_event->fields();
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");
	$this->assertEquals(count($cal_fields), count($event_fields), "the event has the same number of fields as the calendar it belongs to");

	for($i=0;$i<count($cal_fields);$i++){
		if(!($cal_fields[$i] instanceof module_strongcal_field_date ||
		   $cal_fields[$i] instanceof module_strongcal_field_time)){
			$temp_field = $new_event->getField($cal_fields[$i]->field());
			$this->assert(is_object($temp_field), "the field \"" . $cal_fields[$i]->field() . "\" is a field object");
			$this->assertEquals($cal_fields[$i]->value(), $temp_field->value(), "the value for " . $cal_fields[$i]->field() . " is the same for the event and calendar");
		}
	}
	$this->assert($cal->removeEvent($new_event->getId()), "the event has been removed");
   }

   public function test_add_event_check_changed_time_fields(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();

	$new_event = $cal->addEvent();

	$this->assert(is_object($new_event), "the event has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	/* begin new test data from test_add_event */

	$new_event->setValue("start_time", "02:00:00");
	$id = $new_event->getId();

	$cal->reload();
	$new_event = $cal->getEvent($id);
	$field = $fm->getField(TIME_INPUT);
	$field->set_value("02:00:00");
	$cal_field = $cal->getField("start_time");

	$this->assertEquals($new_event->getValue("start_time"), $field->value(), "start time for event has changed");
	$this->assert($cal_field->value() != $new_event->getValue("start_time"), "calendar start time has not changed when event was changed");

	$field = $new_event->getField("start_time");
	$fake_form_data = array("prefix_start_time_hour" => "01",
				"prefix_start_time_minute" => "12",
				"prefix_start_time_ampm" => "PM");
	$field->load_form_value("prefix_", $fake_form_data);
	$cal->reload();
	$new_event = $cal->getEvent($id);
	$field = $fm->getField(TIME_INPUT);
	$field->set_value("13:12:00");

	$this->assertEquals($new_event->getValue("start_time"), $field->value(), "start time for event has changed with form data");
	$this->assert($cal_field->value() != $new_event->getValue("start_time"), "calendar start time has not changed when event was changed");

	$this->assert($cal->removeEvent($new_event->getId()), "the event has been removed");
   }

   public function test_add_event_check_changed_date_fields(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();

	$new_event = $cal->addEvent();

	$this->assert(is_object($new_event), "the event has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	/* begin new test data from test_add_event */

	$this->assert($new_event->setValue("start_date", "2004-12-24"), "the set value when ok");
	$id = $new_event->getId();

	$field = $fm->getField(DATE_INPUT);
	$field->set_value("2004-12-24");


	$this->assertEquals($new_event->getValue("start_date"), $field->value(), "start date for event has changed (pre reload) ");

	$cal->reload();
	$new_event = $cal->getEvent($id);
	$cal_field = $cal->getField("start_date");

	$this->assertEquals($new_event->getValue("start_date"), $field->value(), "start date for event has changed");
	$this->assert($cal_field->value() != $new_event->getValue("start_date"), "calendar start date has not changed when event was changed (1)");

	$field = $new_event->getField("start_date");
	$fake_form_data = array("prefix_start_date_year" => "2003",
				"prefix_start_date_month" => "11",
				"prefix_start_date_day" => "21");
	$field->load_form_value("prefix_", $fake_form_data);
	$cal->reload();
	$new_event = $cal->getEvent($id);
	$field = $fm->getField(TIME_INPUT);
	$field->set_value("2003-11-21");

	$this->assertEquals($new_event->getValue("start_date"), $field->value(), "start date for event has changed with form data");
	$this->assert($cal_field->value() != $new_event->getValue("start_date"), "calendar start date has not changed when event was changed (2)");

	$this->assert($cal->removeEvent($new_event->getId()), "the event has been removed");
   }

   public function test_add_event_check_changed_timezone(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();

	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$fm = $strongcal->fieldManager();

	$new_event = $cal->addEvent();

	$this->assert(is_object($new_event), "the event has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	/* begin new test data from test_add_event */

	$this->assert($new_event->setValue("start_date", "2004-12-24"), "the set date value when ok");
	$this->assert($new_event->setValue("start_time", "18:00:00"), "the set time value when ok");
	$new_event->setTimeZone(-7);
	$id = $new_event->getId();

	$field = $fm->getField(DATE_INPUT);
	$field->set_value("2004-12-25");
	$this->assertEquals($new_event->getValue("start_date"), $field->value(), "start date for event has changed (pre reload) ");

	$field = $fm->getField(TIME_INPUT);
	$field->set_value("01:00:00");
	$this->assertEquals($new_event->getValue("start_time"), $field->value(), "start time for event has changed (pre reload) ");

	$cal->reload();
	$new_event = $cal->getEvent($id);

	$cal_field = $cal->getField("start_date");
	$field = $fm->getField(DATE_INPUT);
	$field->set_value("2004-12-25");
	$this->assertEquals($new_event->getValue("start_date"), $field->value(), "start date for event has changed");
	$this->assert($cal_field->value() != $new_event->getValue("start_date"), "calendar start date has not changed when event was changed");


	$cal_field = $cal->getField("start_time");
	$field = $fm->getField(TIME_INPUT);
	$field->set_value("01:00:00");
	$this->assertEquals($new_event->getValue("start_time"), $field->value(), "start time for event has changed");
	$this->assert($cal_field->value() != $new_event->getValue("start_time"), "calendar start time has not changed when event was changed");

	$this->assert($cal->removeEvent($new_event->getId()), "the event has been removed");
   }


   public function test_set_start_date(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->start_date("2030-01-01");
	$cal->reload();
	$this->assertEquals($cal->start_date(), "2030-01-01", "the calendars start date is set to 2030-01-01");
   }

   public function test_set_end_date(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->end_date("2030-01-01");
	$cal->reload();
	$this->assertEquals($cal->end_date(), "2030-01-01", "the calendars end date is set to 2030-01-01");
   }


   public function test_get_events(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->start_date("2004-05-24");
	$cal->end_date("2030-05-24");
	$cal->reload();
	$events = $cal->events();
	$this->assertEquals(count($events), 0, "there are no events on 2004-05-24");
   }


   public function test_get_events2(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];

	// begin add event
	$event = $cal->addEvent();
	$event->setValue("start_date", "2004-05-24");
	$event->setValue("end_date", "2004-05-24");
	$event->setValue("start_time", "19:30");
	$event->setValue("end_time", "20:30");
	$event->setTimezone($this->CST);
	// end add event

	$cal->start_date("2004-05-24");
	$cal->end_date("2004-05-24");
	$cal->reload();
	$events = $cal->events();
	$this->assertEquals(count($events), 1, "there is 1 event on 2004-05-24");

	$this->assert($cal->removeEvent($event->getId()), "the event has been removed");
   }

   public function test_get_events3(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];

	// begin add 1st event
	$event1 = $cal->addEvent();
	$event1->setValue("start_date", "2004-05-01");
	$event1->setValue("end_date", "2004-05-01");
	$event1->setValue("start_time", "19:30");
	$event1->setValue("end_time", "20:30");
	$event1->setTimezone($this->CST);
	// end add event

	// begin add 2nd event
	$event2 = $cal->addEvent();
	$event2->setValue("start_date", "2004-05-31");
	$event2->setValue("end_date", "2004-05-31");
	$event2->setValue("start_time", "19:30");
	$event2->setValue("end_time", "20:30");
	$event2->setTimezone($this->CST);
	// end add event

	// begin add 3rd out of range event
	$event3 = $cal->addEvent();
	$event3->setValue("start_date", "2004-04-30");
	$event3->setValue("end_date", "2004-04-30");
	$event3->setValue("start_time", "19:30");
	$event3->setValue("end_time", "20:30");
	$event3->setTimezone($this->CST);
	// end add event

	// begin add 4th out of range event
	$event4 = $cal->addEvent();
	$event4->setValue("start_date", "2004-06-01");
	$event4->setValue("end_date", "2004-06-01");
	$event4->setValue("start_time", "01:30");
	$event4->setValue("end_time", "02:30");
	$event4->setTimezone($this->CST);
	// end add event

	// begin add 5th multiday event
	$event5 = $cal->addEvent();
	$event5->setValue("start_date", "2004-04-29");
	$event5->setValue("end_date", "2004-05-01");
	$event5->setValue("start_time", "01:30");
	$event5->setValue("end_time", "02:30");
	$event5->setTimezone($this->CST);
	// end add event

	// begin add 2nd multiday event
	$event6 = $cal->addEvent();
	$event6->setValue("start_date", "2004-05-31");
	$event6->setValue("end_date", "2004-06-01");
	$event6->setValue("start_time", "19:30");
	$event6->setValue("end_time", "20:30");
	$event6->setTimezone($this->CST);
	// end add event

	$cal->start_date("2004-05-01");
	$cal->end_date("2004-05-31");
	$cal->reload();
	$events = $cal->events();
	$this->assertEquals(count($events), 4, "there are 4 events between 2004-05-01 and 2004-05-31");

	$this->assert($cal->removeEvent($event1->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event2->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event3->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event4->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event5->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event6->getId()), "the event has been removed");
   }


   public function test_get_events4(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];

	// begin add 1st event
	$event1 = $cal->addEvent();
	$event1->setValue("start_date", "2004-05-01");
	$event1->setValue("end_date", "2004-05-01");
	$event1->setValue("start_time", "19:30");
	$event1->setValue("end_time", "20:30");
	$event1->setTimezone($this->CST);
	// end add event

	// begin add 2nd event
	$event2 = $cal->addEvent();
	$event2->setValue("start_date", "2004-05-02");
	$event2->setValue("end_date", "2004-05-02");
	$event2->setValue("start_time", "19:30");
	$event2->setValue("end_time", "20:30");
	$event2->setTimezone($this->CST);
	// end add event

	// begin add 3rd out of range event
	$event3 = $cal->addEvent();
	$event3->setValue("start_date", "2004-04-30");
	$event3->setValue("end_date", "2004-04-30");
	$event3->setValue("start_time", "19:30");
	$event3->setValue("end_time", "20:30");
	$event3->setTimezone($this->CST);
	// end add event

	// begin add 4th out of range event
	$event4 = $cal->addEvent();
	$event4->setValue("start_date", "2004-05-01");
	$event4->setValue("end_date", "2004-05-01");
	$event4->setValue("start_time", "01:30");
	$event4->setValue("end_time", "02:30");
	$event4->setTimezone($this->CST);
	// end add event

	// begin add 5th multiday event
	$event5 = $cal->addEvent();
	$event5->setValue("start_date", "2004-04-29");
	$event5->setValue("end_date", "2004-05-01");
	$event5->setValue("start_time", "01:30");
	$event5->setValue("end_time", "02:30");
	$event5->setTimezone($this->CST);
	// end add event

	// begin add 2nd multiday event
	$event6 = $cal->addEvent();
	$event6->setValue("start_date", "2004-05-01");
	$event6->setValue("end_date", "2004-05-03");
	$event6->setValue("start_time", "19:30");
	$event6->setValue("end_time", "20:30");
	$event6->setTimezone($this->CST);
	// end add event

	$cal->start_date("2004-05-01");
	$cal->end_after(3);

	$cal->reload();
	$events = $cal->events();
	$this->assertEquals(count($events), 3, "there are at least events on 2004-05-01");

	$this->assert($cal->removeEvent($event1->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event2->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event3->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event4->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event5->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event6->getId()), "the event has been removed");
   }
};


?>