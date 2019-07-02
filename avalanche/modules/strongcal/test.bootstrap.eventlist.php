<?

class TestAurora_bootstrap_eventlist extends Abstract_Avalanche_TestCase { 

  private $cal_ids = array(38);

  public function setUp(){
    global $avalanche;
    Abstract_Avalanche_TestCase::setUp();
    $strongcal = $avalanche->getModule("strongcal");
    $this->cal_ids[] = $strongcal->addCalendar("new calendar 1");
    $this->cal_ids[] = $strongcal->addCalendar("new calendar 2");	
    
    $data = false;
    new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
    $runner = new module_bootstrap_runner();
    $runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
    $data = $runner->run($data);
    
    $cals = $data->data();
    for($i=0;$i < count($cals);$i++){
      $cal = $cals[$i];
      $this->assert(is_object($cal), "the item in the array is an object (hopefully a calendar)");
      $this->assert($cal instanceof module_strongcal_calendar, "the object should be a calendar");
      
      // add the first event to each calendar
      $event = $cal->addEvent();
      $event->setValue("start_date", "2004-05-01");
      $event->setValue("end_date",   "2004-05-01");
      $event->setValue("start_time", "02:00");
      $event->setValue("end_time",   "03:00");
      $event->setTimezone($strongcal->timezone());
      
      // add the second event to each calendar
      $event = $cal->addEvent();
      $event->setValue("start_date", "2004-04-30");
      $event->setValue("end_date",   "2004-04-30");
      $event->setValue("start_time", "22:00");
      $event->setValue("end_time",   "23:30");
      $event->setTimezone($strongcal->timezone());
    }
  }

  public function tearDown(){
    global $avalanche;
    $strongcal = $avalanche->getModule("strongcal");

    Abstract_Avalanche_TestCase::tearDown();
  }


   public function test_get_all_possible1(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	$data = false; // send in false as the default value
	//	$data = new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-05-01"));
	$data = $runner->run($data);
	$event_list = $data->data();

	$this->assertEquals(count($event_list), 3, "there are 3 events on 2004-05-01");
    }

   public function test_get_limit(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	// add some more events
	$data = false;
	new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$data = $runner->run($data);
	
	$cals = $data->data();
	for($i=0;$i < count($cals);$i++){
	  $cal = $cals[$i];
	  $this->assert(is_object($cal), "the item in the array is an object (hopefully a calendar)");
	  $this->assert($cal instanceof module_strongcal_calendar, "the object should be a calendar");
	  
	  // add the first event to each calendar
	  $event = $cal->addEvent();
	  $event->setValue("start_date", "2004-05-01");
	  $event->setValue("end_date",   "2004-05-01");
	  $event->setValue("start_time", "02:00");
	  $event->setValue("end_time",   "03:00");
	  $event->setTimezone($strongcal->timezone());
	  
	  // add the second event to each calendar
	  $event = $cal->addEvent();
	  $event->setValue("start_date", "2004-04-30");
	  $event->setValue("end_date",   "2004-04-30");
	  $event->setValue("start_time", "22:00");
	  $event->setValue("end_time",   "23:30");
	  $event->setTimezone($strongcal->timezone());
	}
	// done adding more events





	$data = false; // send in false as the default value
	//	$data = new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-05-01", 1));
	$data = $runner->run($data);
	$event_list = $data->data();

	$this->assertEquals(count($event_list), 3, "there are at least 3 events on 2004-05-01");
    }

   
   public function test_get_all_possible2(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	$data = false; // send in false as the default value
	//	$data = new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-04-30", "2004-05-02"));
	$data = $runner->run($data);
	$event_list = $data->data();

	$this->assertEquals(count($event_list), 6, "there are 6 events between 2004-04-30 and 2004-05-01");
    }

   
   public function test_get_specific(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	$data = array(38);
	$data = new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-05-01"));
	$data = $runner->run($data);
	$event_list = $data->data();
	
	$this->assertEquals(count($event_list), 1, "there is 1 event on 2004-05-01");
   } 
   
   
};

?>