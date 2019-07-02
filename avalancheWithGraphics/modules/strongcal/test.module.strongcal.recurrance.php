<?
Class TestAurora_recurrance extends Abstract_Avalanche_TestCase { 

   private $timezone;

   public function setUp(){
	Abstract_Avalanche_TestCase::setUp();
	$this->timezone = -6;
   }




   public function test_make_new_recur(){
	global $avalanche;
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

	$cal->reload();

	$this->assert(is_object($cal->getRecur($recur->getId())), "the calendar has the new recur in database");
   }

   public function test_make_new_basic_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();


	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-04-03";

	$recur->setEndType(RECUR_END_AFTER, 3);
	$recur->setStartDate($start_date);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_AFTER, $recur->endType(), "the end type is RECUR_END_AFTER");
   }

   public function test_make_new_daily_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-12-14";

	$recur->setEndType(RECUR_END_BY, "2005-01-12");
	$recur->setStartDate($start_date);
	$recur->setToDaily(2);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2004-12-14",
			     "2004-12-16",
			     "2004-12-18",
			     "2004-12-20",
			     "2004-12-22",
			     "2004-12-24",
			     "2004-12-26",
			     "2004-12-28",
			     "2004-12-30",
			     "2005-01-01",
			     "2005-01-03",
			     "2005-01-05",
			     "2005-01-07",
			     "2005-01-09",
			     "2005-01-11");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }

   public function test_make_new_daily_recur_2(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

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
	$recur->setToDaily(2);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2004-05-24",
			     "2004-05-26",
			     "2004-05-28",
			     "2004-05-30");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }


   public function test_make_new_weekly_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-12-14";

	$recur->setEndType(RECUR_END_BY, "2005-01-12");
	$recur->setStartDate($start_date);
	
	$recur->setToWeekly(2, "124");

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
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
   }
   
   public function test_make_new_weekly_recur_2(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

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


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2004-05-24",
			     "2004-05-25",
			     "2004-05-27");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }


   public function test_make_new_monthly_dom_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2005-03-12");
	$recur->setStartDate($start_date);
	
	$recur->setToMonthly(RECUR_MONTHLY_DOM, 2, 10);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2005-01-10",
			     "2005-03-10");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }


   public function test_make_new_monthly_dow_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2005-03-12");
	$recur->setStartDate($start_date);
	
	/* recur every two months on the 2nd monday of each month */
	$recur->setToMonthly(RECUR_MONTHLY_DOW, 1, 2, 1);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2004-12-13",
			     "2005-01-10",
			     "2005-02-14");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }


   public function test_make_new_yearly_dom_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

	// make sure we have a calendar
	$this->assertEquals(count($calendars), 1, "there is one calendar in the test database" );

	$cal = $calendars[0]["calendar"];

	// make sure the calendar loads
	$this->assert(is_object($cal), "the one calendar in the database loads correctly" );

	$recur = $cal->getNewRecurrancePattern();

	$this->assert(is_object($recur), "the new recurrance is an object");
	$this->assert($recur instanceof module_strongcal_recurrance, "the object is a module_strongcal_recurrance object");

	$start_date = "2004-11-14";

	$recur->setEndType(RECUR_END_BY, "2007-03-12");
	$recur->setStartDate($start_date);
	
	/* recur every 5th of march */
	$recur->setToYearly(RECUR_YEARLY_DOM, 5, 3);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());


	$this->assertEquals($start_date, $recur->getProperty("start_date"), "the start date is $start_date");
	$this->assertEquals(RECUR_END_BY, $recur->endType(), "the end type is RECUR_END_BY");
	$series = $recur->series();
	$series_comp = array("2005-03-05",
			     "2006-03-05",
			     "2007-03-05");
	$this->assertEquals(count($series), count($series_comp), "the series contains the correct number of dates");
	for($i=0;$i<count($series);$i++){
		$this->assertEquals($series[$i], $series_comp[$i], "the dates in the series are correct");
	}
   }


   public function test_make_new_yearly_dow_recur(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$calendars = $strongcal->getCalendarList();

	$strongcal->timezone($this->timezone);
	$strongcal->reload();

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
   }



};


?>