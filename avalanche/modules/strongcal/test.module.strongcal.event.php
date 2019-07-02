<?
Class TestAurora_event extends Abstract_Avalanche_TestCase {

   private $timezone;
   // the CST timezone hour offset from GMT
   private $CST = -6;

   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
	$this->timezone = 12;
   }

   public function test_add_event(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");
   }

   public function test_view_event(){
	global $avalanche;


	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");
	$cal = $calendars[0]["calendar"];
	$new_event = $cal->addEvent();


	$data = array("view" => "event");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_eventview_gui($avalanche, new Document(), $cal->getId(), $new_event->getId()));
	$runner->run($data);
	$this->pass("everything is fine");
   }

   public function test_edit_event(){
	global $avalanche;
      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->isPublic(true);
	$groups = $avalanche->getAllUsergroups();
	foreach($groups as $g){
		$cal->updatePermission("name", "r", $g->getId());
		$cal->updatePermission("entry", "rw", $g->getId());
		$cal->updatePermission("comments", "rw", $g->getId());
	}
	$cal->reload();

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");


	$new_event->setValue("title", "asdfasdf");
	$this->assertEquals($new_event->getDisplayValue("title"), "asdfasdf", "the title is correct 1");


	$avalanche->logOut();
	$new_event->setValue("title", "asdf");
	$this->assertEquals($new_event->getDisplayValue("title"), "asdfasdf", "the title is correct 2");
	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
   }

   public function test_edit_event2(){
	global $avalanche;
      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->isPublic(true);
	$groups = $avalanche->getAllUsergroups();
	foreach($groups as $g){
		$cal->updatePermission("name", "r", $g->getId());
		$cal->updatePermission("entry", "r", $g->getId());
		$cal->updatePermission("comments", "r", $g->getId());
	}
	$cal->reload();

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	$new_event->setValue("title", "asdfasdf");
	$this->assertEquals($new_event->getDisplayValue("title"), "asdfasdf", "the title is correct 1");

	$avalanche->logOut();
	$new_event->setValue("title", "asdf");
	$this->assertEquals($new_event->getDisplayValue("title"), "asdfasdf", "the title is correct 2");
	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
   }


   public function test_get_attendees(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();

	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");
	$this->assert($attendees[0]->confirm(), "the attendee is confirmed");
   }


   public function test_alter_attendees(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();

	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");
	$this->assert(!$attendees[0]->confirm(false), "the attendee changed confirmation to false");
	$this->assert(!$attendees[0]->confirm(), "the attendee is not confirmed");
   }

   public function test_remove_attendees(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();

	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");

	$new_event->removeAttendee($attendees[0]->userId());

	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 0, "there is one attendee");

	$new_event->reload();
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 0, "there is one attendee");
   }

   public function test_add_attendees(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");

	$new_event->removeAttendee($attendees[0]->userId());
	$new_event->addAttendee($avalanche->loggedInHuh());

	// now check again, pre reload
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");

	// now check post reload
	$new_event->reload();
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");
   }

   public function test_no_duplicate_attendees(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");

	$new_event->addAttendee($avalanche->loggedInHuh());

	// now check again, pre reload
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");

	// now check post reload
	$new_event->reload();
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assertEquals($attendees[0]->userId(), $avalanche->loggedInHuh(), "the attendee is the logged in user");
   }

   public function test_non_zero_attendee_id(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$new_event = $cal->addEvent();
	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");

	// now get attendees
	$attendees = $new_event->attendees();
	$this->assertEquals(count($attendees), 1, "there is one attendee");
	$this->assert($attendees[0]->getId() != 0, "the attendee id is non zero");

   }


   public function test_add_recursive_event(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

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


	// now make a recurring object

	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2007-03-16");
	$recur->setStartDate($start_date);

	/* recur every 2nd wednesday of march */
	$recur->setToYearly(RECUR_YEARLY_DOW, 2, 3, 3);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2005-03-09",
			     "2006-03-08",
			     "2007-03-14");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $series), "the dates in the series are correct (incorrect date in event: " . $loe[0]->getDisplayValue("start_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $series));
		array_splice($loe, 0);
	}
   }


   public function test_add_recursive_event3(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->CST);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();


	$new_event = $cal->addEvent();
	$new_event->setValue("start_date", "2004-12-25");
	$new_event->setValue("start_time", "04:00:00"); // 10:00p
	$new_event->setValue("end_date", "2004-12-25");
	$new_event->setValue("end_time", "12:00:00"); // 6:00a

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");


	// now make a recurring object
	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-12-24";

	$recur->setEndType(RECUR_END_AFTER, 2);
	$recur->setStartDate($start_date);

	/* recur every 2nd wednesday of march */
	$recur->setToDaily(1);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_AFTER, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$s_series_comp = array("2004-12-24",
			     "2004-12-25");
	$e_series_comp = array("2004-12-25",
			     "2004-12-26");
	$this->assertEquals(count($series), count($s_series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $s_series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $s_series_comp), "the dates in the series are correct (incorrect date in event: " . $loe[0]->getDisplayValue("start_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $s_series_comp));
		$this->assert(in_array ($loe[0]->getDisplayValue("end_date"), $e_series_comp), "the dates in the series are correct (incorrect date in event: " . $loe[0]->getDisplayValue("end_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("end_date"), $e_series_comp));
		array_splice($loe, 0);
	}
   }


   public function test_edit_recursive_event3(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->CST);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();


	$new_event = $cal->addEvent();
	$new_event->setValue("start_date", "2004-12-25");
	$new_event->setValue("start_time", "04:00:00"); // 10:00p
	$new_event->setValue("end_date", "2004-12-25");
	$new_event->setValue("end_time", "12:00:00"); // 6:00a

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");


	// now make a recurring object
	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-12-24";

	$recur->setEndType(RECUR_END_AFTER, 2);
	$recur->setStartDate($start_date);

	/* recur every 2nd wednesday of march */
	$recur->setToDaily(1);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_AFTER, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$s_series_comp = array("2004-12-24",
			     "2004-12-25");
	$e_series_comp = array("2004-12-25",
			     "2004-12-26");
	$this->assertEquals(count($series), count($s_series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $s_series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $s_series_comp), "the dates in the series are correct (incorrect date in event: " . $loe[0]->getDisplayValue("start_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $s_series_comp));
		$this->assert(in_array ($loe[0]->getDisplayValue("end_date"), $e_series_comp), "the dates in the series are correct (incorrect date in event: " . $loe[0]->getDisplayValue("end_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("end_date"), $e_series_comp));
		array_splice($loe, 0);
	}

	// now let's edit the series
	$s_series_comp = array("2004-12-24",
			     "2004-12-25");

	$list = array();
	$list[] = array("field" => $cal->getField("title"),
			"value" => "busta what?");
	$list[] = array("field" => $cal->getField("description"),
			"value" => "what's going on here!");
	$list[] = array("field" => $cal->getField("start_time"),
			"value" => "21:00:00");
	$list[] = array("field" => $cal->getField("end_time"),
			"value" => "07:00:00");

	$cal->editSeries($list, $recur);

	$cal->reload();

	$loe = $cal->getEventsIn($recur);

	for($i=0;$i<count($loe);$i++){
		$this->assertEquals($loe[$i]->getValue("title"), "busta what?", "the title is changed to \"busta what?\"");
		$this->assertEquals($loe[$i]->getValue("description"), "what's going on here!", "the title is changed to \"what's going on here!\"");
		$this->assertEquals($loe[$i]->getValue("start_time"), "03:00:00", "the start time is changed to \"03:00:00\"");
		$this->assertEquals($loe[$i]->getValue("end_time"), "13:00:00", "the start time is changed to \"13:00:00\"");
		$this->assert(in_array($loe[$i]->getDisplayValue("start_date"), $s_series_comp), "the dates in the series are correct");
	}
   }


   public function test_add_recursive_event2(){
	global $avalanche;

      // first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->CST);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();


	$new_event = $cal->addEvent();
	$new_event->setValue("start_time", "19:30");
	$new_event->setValue("end_time", "20:00");
	$new_event->setValue("start_date", "2004-05-24");
	$new_event->setValue("end_date", "2004-05-24");
	$new_event->setTimezone($this->CST);

	$this->assertEquals($new_event->getValue("start_time"), "01:30:00", "the value for the start time is 01:30");
	$this->assertEquals($new_event->getValue("end_time"), "02:00:00", "the value for the end time is 02:00");
	$this->assertEquals($new_event->getDisplayValue("start_time"), "19:30", "the display value for the start time is 19:30");
	$this->assertEquals($new_event->getDisplayValue("end_time"), "20:00", "the display value for the end time is 20:00");

	$this->assertEquals($new_event->getValue("start_date"), "2004-05-25", "the value for the start date is 2004-05-25");
	$this->assertEquals($new_event->getValue("end_date"), "2004-05-25", "the value for the end date is 2004-05-25");
	$this->assertEquals($new_event->getDisplayValue("start_date"), "2004-05-24", "the display value for the start date is 2004-05-24");
	$this->assertEquals($new_event->getDisplayValue("end_date"), "2004-05-24", "the display value for the end date is 2004-05-24");

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");


	// now make a recurring object

	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-05-24";
	$recur->setEndType(RECUR_END_BY, "2004-05-30");
	$recur->setStartDate($start_date);
	$recur->setToWeekly(2, "124");


	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_AFTER");
	$series = $recur->series();
	$series_comp = array("2004-05-24",
			     "2004-05-25",
			     "2004-05-27");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $series), "the dates in the series are correct (" . $loe[0]->getDisplayValue("start_date") . ")");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $series));
		array_splice($loe, 0);
	}
   }




   public function test_add_new_series_to_recursive_event(){
	global $avalanche;

	// first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

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


	// now make a recurring object

	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2007-03-16");
	$recur->setStartDate($start_date);

	/* recur every 2nd wednesday of march */
	$recur->setToYearly(RECUR_YEARLY_DOW, 2, 3, 3);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2005-03-09",
			     "2006-03-08",
			     "2007-03-14");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $series), "the dates in the series are correct");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $series));
		array_splice($loe, 0);
	}

	$recur_old = $recur;

	// now let's add a new recurrance pattern

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-12-14";

	$recur->setEndType(RECUR_END_BY, "2005-01-12");
	$recur->setStartDate($start_date);

	$recur->setToWeekly(2, "124");

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$series = $recur->series();
	$series_comp = array("2004-12-14",
			     "2004-12-16",
			     "2004-12-27",
			     "2004-12-28",
			     "2004-12-30",
			     "2005-01-10",
			     "2005-01-11");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $series), "the dates in the series are correct");
		array_splice($series, array_search ( $loe[0]->getDisplayValue("start_date"), $series));
		array_splice($loe, 0);
	}

	//make sure old recur is empty

	$loe = $cal->getEventsIn($recur_old);
	$this->assertEquals(count($loe), 0, "there are zero events in the old series");
   }



   public function test_edit_series_of_events(){
	global $avalanche;

	// first check strongcal to make sure everything is set up right
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();

	// now add the event
	$new_event = $cal->addEvent();
	// the event spans over a night
	$new_event->setValue("start_date", "2004-08-12");
	$new_event->setValue("start_time", "06:00:00");
	$new_event->setValue("end_date", "2004-08-13");
	$new_event->setValue("end_time", "06:00:00");

	$this->assert(is_object($new_event), "the calendar has added an event successfully");
	$this->assert(($new_event instanceof module_strongcal_event), "the event is of the proper class");


	// now make a recurring object

	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2007-03-16");
	$recur->setStartDate($start_date);

	/* recur every 2nd wednesday of march */
	$recur->setToYearly(RECUR_YEARLY_DOW, 2, 3, 3);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$new_event->returnRecurrance($recur);
	$new_event->reload();


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2005-03-09",
			     "2006-03-08",
			     "2007-03-14");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}

	$loe = $cal->getEventsIn($recur);

	$this->assertEquals(count($loe), count($series), "there are the correct number of events in the series");
	while(count($loe)){
		$this->assertEquals($loe[0]->getValue("start_time"), "06:00:00", "the start time is \"06:00:00\"");
		$this->assert(in_array ($loe[0]->getDisplayValue("start_date"), $series), "the dates in the series are correct");
		array_splice($loe, 0);
	}

	// now let's edit the series

	$list = array();
	$list[] = array("field" => $cal->getField("title"),
			"value" => "busta what?");
	$list[] = array("field" => $cal->getField("description"),
			"value" => "what's going on here!");
	$list[] = array("field" => $cal->getField("start_time"),
			"value" => "02:00:00");

	$cal->editSeries($list, $recur);

	$cal->reload();

	$loe = $cal->getEventsIn($recur);

	for($i=0;$i<count($loe);$i++){
		$this->assertEquals($loe[$i]->getValue("title"), "busta what?", "the title is changed to \"busta what?\"");
		$this->assertEquals($loe[$i]->getValue("description"), "what's going on here!", "the title is changed to \"what's going on here!\"");
		$this->assertEquals($loe[$i]->getValue("start_time"), "14:00:00", "the start time is changed to \"14:00:00\"");
		$this->assert(in_array($loe[$i]->getDisplayValue("start_date"), $series), "the dates in the series are correct");
	}
   }


   function test_event_clone(){
	global $avalanche;

	// first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();
	$cal_fields = $cal->fields();
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );
	$this->assert(count($cal_fields) == 7, "the calendar has 7 fields");

	$fm = $strongcal->fieldManager();

	$event = $cal->addEvent();

	$this->assert(is_object($event), "the calendar has added an event successfully");
	$this->assert(($event instanceof module_strongcal_event), "the event is of the proper class");

	$event->setValue("title", "this is a new title");

	$event->reload();

	$new_event = $event->_clone();

	$new_event->reload();

	$this->assert($new_event->calendar() === $event->calendar(), "the calendars are the same");
	$this->assertEquals($event->getValue("title"), "this is a new title", "the original event's title is the same");
	$this->assertEquals($new_event->getValue("title"), "this is a new title", "the new event's title matches the original");
	$this->assert($new_event->getId() != $event->getId(), "the id's do not match");
   }



   public function test_eventAllDay(){
	global $avalanche;

	// first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();


	$event1 = $cal->addEvent();

	$event1->setValue("start_date", "2004-04-05");
	$event1->setValue("end_date",   "2004-04-06");
	$event1->setValue("start_time", "12:00");
	$event1->setValue("end_time",   "12:00");
	$event1->setValue("title",      "Event 1");

	$this->assertEquals($event1->isAllDay(), 0, "the event is not all day");

	$event1->setAllDay(true);
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$event1->reload();
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");


	$this->assertEquals($event1->getDisplayValue("start_date"), $event1->getValue("start_date"), "the event is all day");
	$this->assertEquals($event1->getDisplayValue("end_date"), $event1->getValue("end_date"), "the event is all day");
	$this->assertEquals($event1->getDisplayValue("start_date"), "2004-04-06", "the event is all day");
	$this->assertEquals($event1->getDisplayValue("end_date"), "2004-04-07", "the event is all day");

	// make sure that the times are adjusted too each time we set it
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");
	$this->assertEquals($event1->isAllDay(), 1, "the event is all day");

	$this->assertEquals($event1->getDisplayValue("start_date"), $event1->getValue("start_date"), "the event is all day");
	$this->assertEquals($event1->getDisplayValue("end_date"), $event1->getValue("end_date"), "the event is all day");
	$this->assertEquals($event1->getDisplayValue("start_date"), "2004-04-06", "the event is all day");
	$this->assertEquals($event1->getDisplayValue("end_date"), "2004-04-07", "the event is all day");

	$this->assertEquals(count($event1->calendar()->getEventsOn("2004-04-06")), 1, "1 event is on that day");

	$event1->setAllDay(false);
	$this->assertEquals($event1->isAllDay(), 0, "the event is all day");

	$this->assert($event1->getDisplayValue("start_date") != $event1->getValue("start_date"), "the event is all day");
	$this->assert($event1->getDisplayValue("end_date") != $event1->getValue("end_date"), "the event is all day");
	$this->assertEquals($event1->getDisplayValue("start_date"), "2004-04-06", "the event is all day");
	$this->assertEquals($event1->getDisplayValue("end_date"), "2004-04-07", "the event is all day");


   }


   public function test_eventComparator1(){
	global $avalanche;

	// first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();


	$event1 = $cal->addEvent();
	$event2 = $cal->addEvent();


	$event1->setValue("start_date", "2004-04-05");
	$event1->setValue("end_date",   "2004-04-06");
	$event1->setValue("start_time", "09:00");
	$event1->setValue("end_time",   "10:00");
	$event1->setValue("title",      "Event 1");

	$event2->setValue("start_date", "2004-04-05");
	$event2->setValue("end_date",   "2004-04-05");
	$event2->setValue("start_time", "09:00");
	$event2->setValue("end_time",   "10:00");
	$event2->setValue("title",      "Event 2");

	$comp = new StrongcalEventComparator();

	$this->assert($comp->compare($event2, $event1) < 0, "event 2 comes before event 1");
	$this->assert($comp->compare($event1, $event2) > 0, "event 2 comes before event 1");

	$this->assert($cal->removeEvent($event1->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event2->getId()), "the event has been removed");
   }


   public function test_eventComparator2(){
	global $avalanche;

	// first add the event
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database");

	$cal = $calendars[0]["calendar"];
	$cal->reload();


	$event1 = $cal->addEvent();
	$event2 = $cal->addEvent();


	$event1->setValue("start_date", "2004-04-05");
	$event1->setValue("end_date",   "2004-04-05");
	$event1->setValue("start_time", "09:00");
	$event1->setValue("end_time",   "10:00");
	$event1->setValue("title",      "Event 1");

	$event2->setValue("start_date", "2004-04-05");
	$event2->setValue("end_date",   "2004-04-05");
	$event2->setValue("start_time", "08:30");
	$event2->setValue("end_time",   "10:00");
	$event2->setValue("title",      "Event 2");

	$comp = new StrongcalEventComparator();

	$this->assert($comp->compare($event2, $event1) < 0, "event 2 comes before event 1");
	$this->assert($comp->compare($event1, $event2) > 0, "event 2 comes before event 1");

	$this->assert($cal->removeEvent($event1->getId()), "the event has been removed");
	$this->assert($cal->removeEvent($event2->getId()), "the event has been removed");
   }
};


?>