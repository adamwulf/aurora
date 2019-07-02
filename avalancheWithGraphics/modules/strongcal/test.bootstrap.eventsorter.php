<?

class TestAurora_bootstrap_eventsorter extends Abstract_Avalanche_TestCase { 

  private $cal_ids = array(38);

  private $start_times = array("12:15", 
			       "23:00",
			       "05:30",
			       "22:00",
			       "15:00",
			       "15:00");

  private $end_times   = array("13:00", 
			       "23:30",
			       "06:30",
			       "23:00",
			       "16:00",
			       "15:30");

  public function setUp(){
    global $avalanche;
    Abstract_Avalanche_TestCase::setUp();
    $strongcal = $avalanche->getModule("strongcal");
    $strongcal->timezone(-6);
    $data = false;
    $runner = new module_bootstrap_runner();
    $runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
    $data = $runner->run($data);
    $cals = $data->data();
    foreach($cals as $cal){
	  if($cal->getId() != 38){
	     	    $strongcal->removeCalendar($cal->getId());
	  }
    }
    $this->cal_ids[] = $strongcal->addCalendar("new calendar 1");
    $this->cal_ids[] = $strongcal->addCalendar("new calendar 2");	
    
    $data = false;
    $runner = new module_bootstrap_runner();
    $runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
    $data = $runner->run($data);
    $cals = $data->data();
    $this->assertEquals(count($cals), 3, "there are 3 calendar");

    for($i=0;$i < 2*count($cals);$i+=2){
      $cal = $cals[$i/2];
      $this->assert(is_object($cal), "the item in the array is an object (hopefully a calendar)");
      $this->assert($cal instanceof module_strongcal_calendar, "the object should be a calendar");
      
      // add the first event to each calendar
      $event = $cal->addEvent();
      $event->setValue("start_date", "2004-05-01");
      $event->setValue("end_date",   "2004-05-01");
      $event->setValue("start_time", $this->start_times[$i]);
      $event->setValue("end_time",   $this->end_times[$i]);
      
      // add the second event to each calendar
      $event = $cal->addEvent();
      $event->setValue("start_date", "2004-04-30");
      $event->setValue("end_date",   "2004-04-30");
      $event->setValue("start_time", $this->start_times[$i+1]);
      $event->setValue("end_time",   $this->end_times[$i+1]);
    }
  }

  public function tearDown(){
    global $avalanche;
    $strongcal = $avalanche->getModule("strongcal");

    Abstract_Avalanche_TestCase::tearDown();
  }


  // sometimes this test fails... but for no reason? b/c it passes on the next run...
  // i dunno what the prolem is...
   public function test_get_all_on_date(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	$data = false; // send in false as the default value
	//	$data = new module_bootstrap_data($data, "send in false, the default value to get a list of all calendars");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-05-01"));
	$runner->add(new module_bootstrap_strongcal_eventsorter());
	$data = $runner->run($data);
	$event_list = $data->data();

	$this->assertEquals(count($event_list), 3, "there are 3 events on 2004-05-01");

	$comp = new StrongcalEventComparator();
	for($i=0;$i<count($event_list)-1;$i++){
	  $this->assert($comp->compare($event_list[$i], $event_list[$i+1]) < 0, "the list is sorted");
	}
    }

   
   public function test_get_all_on_dates(){
	global $avalanche;

	$strongcal = $avalanche->getModule("strongcal");

	$data = false;
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$runner->add(new module_bootstrap_strongcal_eventlist("2004-04-29", "2004-05-02"));
	$runner->add(new module_bootstrap_strongcal_eventsorter());
	$data = $runner->run($data);
	$event_list = $data->data();
	
	$this->assertEquals(count($event_list), 6, "there is 1 event between 2004-04-29 and 2004-05-02");

	$comp = new StrongcalEventComparator();
	for($i=0;$i<count($event_list)-1;$i++){
	  $this->assert($comp->compare($event_list[$i], $event_list[$i+1]) < 0, "the list is sorted");
	}
   } 
   
   
};

?>