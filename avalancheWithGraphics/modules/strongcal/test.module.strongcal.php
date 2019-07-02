<?
Class TestAurora extends Abstract_Avalanche_TestCase { 

   public function test_included_classes(){
	$class_array =    array("StrongcalEventComparator",
				"module_strongcal_calendar",
				"module_strongcal_event",
				"module_strongcal_fieldmanager",
				"module_strongcal_field",
				"module_strongcal_recurrance",
				"module_strongcal_validation",
				"module_bootstrap_strongcal_main_loader",
				"module_bootstrap_strongcal_eventlist",
				"module_bootstrap_strongcal_default_view",
				"module_bootstrap_strongcal_monthview_gui",
				"module_bootstrap_strongcal_calendarlist");
	for($i=0;$i<count($class_array);$i++){
		$class = $class_array[$i];
		$this->assert(class_exists($class), "the class $class exists");
	}
   }

   public function test_timezone(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$strongcal->timezone(-6);
	$strongcal->reload();
	$this->assertEquals($strongcal->timezone(), (double) -6, "the timezone has been changed to -6");

	$strongcal->timezone(3);
	$strongcal->reload();
	$this->assertEquals($strongcal->timezone(), (double) 3, "the timezone has been changed to 3");
   }

   public function test_add_remove_calendar(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$cal_id = $strongcal->addCalendar("new calenar");
	
	$this->assert(is_int($cal_id), "adding a calendar returned an integer id");

	$strongcal->reload();

	$cal = $strongcal->getCalendarFromDb($cal_id);
	$this->assert(is_object($cal), "the calendar is an object");
	$this->assert($cal instanceof module_strongcal_calendar, "the calendar object is a module_strongcal_calendar");
	$this->assertEquals($cal->getId(), $cal_id, "the calendar id is correct");

	$strongcal->removeCalendar($cal_id);

	$strongcal->reload();

	$cal = $strongcal->getCalendarFromDb($cal_id);
	$this->assertEquals($cal, false, "the calendar has been removed");
   }

   public function test_add_del_listener(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$original = count($strongcal->getListeners());
	$list = new TestAuroraListenerHelper();
	$this->assertEquals(count($strongcal->getListeners()), $original, "there are $original listeners");
	$strongcal->addListener($list);
	$this->assertEquals(count($strongcal->getListeners()), $original+1, "there are " . ($original+1) . " listeners");
	$strongcal->removeListener($list);
	$this->assertEquals(count($strongcal->getListeners()), $original, "there are $original listeners");
   }

   public function test_listener_pattern(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$list = new TestAuroraListenerHelper();
	$strongcal->addListener($list);
	$cal_id = $strongcal->addCalendar("cal name");
	$cal = $strongcal->getCalendarFromDb($cal_id);
	
	$event = $cal->addEvent();
	$event->getField("title")->set_value("asfdasfasdf");
	// i can even edit it twice, as long as i send in the exact same value
	// since i'm editing, i'm not technically changing the value the second time so
	// the event doesn't fire again
	$event->getField("title")->set_value("asfdasfasdf");
	$cal->removeEvent($event->getId());
	$strongcal->removeCalendar($cal->getId());
	
	$this->assertEquals($list->calendarAddedCount(), 1, "only 1 calendar has been added");
	$this->assertEquals($list->calendarDeletedCount(), 1, "only 1 calendar has been deleted");
	$this->assertEquals($list->eventAddedCount(), 1, "only 1 event has been added");
	$this->assertEquals($list->eventEditedCount(), 1, "only 1 event has been edited");
	$this->assertEquals($list->eventDeletedCount(), 1, "only 1 event has been deleted");
	
	
	$strongcal->removeListener($list);
   }
   
   public function test_listener_recurring_event(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");
	$list = new TestAuroraListenerHelper();
	$strongcal->addListener($list);
	$cal_id = $strongcal->addCalendar("cal name");
	$cal = $strongcal->getCalendarFromDb($cal_id);
	$event = $cal->addEvent();
	$recur = $cal->getNewRecurrancePattern();

	$recur->setEndType(RECUR_END_BY, "2007-03-16");
	$recur->setStartDate("2004-11-14");
	
	/* recur every 2nd wednesday of march */
	$recur->setToYearly(RECUR_YEARLY_DOW, 2, 3, 3);

	$cal->reload();
	$recur = $cal->getRecur($recur->getId());

	$event->returnRecurrance($recur);
	$event->reload();

	$this->assertEquals($list->calendarAddedCount(), 1, "only 1 calendar has been added");
	$this->assertEquals($list->calendarDeletedCount(), 0, "only 0 calendar has been deleted");
	$this->assertEquals($list->eventAddedCount(), 1, "only 1 event has been added");
	// twice (once for resetting start date, and once for resetting end date)
	$this->assertEquals($list->eventEditedCount(), 2, "only 1 event has been edited");
	$this->assertEquals($list->eventDeletedCount(), 0, "only 1 event has been deleted");
   }

};

class TestAuroraListenerHelper implements module_strongcal_listener{
	private $cal_add_count = 0;
	private $cal_del_count = 0;
	private $event_add_count = 0;
	private $event_edit_count = 0;
	private $event_del_count = 0;
	private $attendee_add_count = 0;
	private $attendee_del_count = 0;
	
	// called when a calendar is added
	function calendarAdded($cal_id){
		$this->cal_add_count++;
	}	
	
	// called when a calendar is deleted
	function calendarDeleted($cal_id){
		$this->cal_del_count++;
	}
	
	// called when an event is added
	function eventAdded($cal_id, $event_id){
		$this->event_add_count++;
	}
	
	// called when an event is deleted
	function eventEdited($cal_id, $event_id){
		$this->event_edit_count++;
	}
	
	// called when an event is deleted
	function eventDeleted($cal_id, $event_id){
		$this->event_del_count++;
	}
	
	// called when an attendee is added
	function attendeeAdded($cal_id, $event_id, $user_id){
		$this->attendee_add_count++;
	}
	
	// called when an attendee is removed
	function attendeeDeleted($cal_id, $event_id, $user_id){
		$this->attendee_del_count++;
	}
	
	function calendarAddedCount(){
		return $this->cal_add_count;
	}
	function calendarDeletedCount(){
		return $this->cal_del_count;
	}
	function eventAddedCount(){
		return $this->event_add_count;
	}
	function eventEditedCount(){
		return $this->event_edit_count;
	}
	function eventDeletedCount(){
		return $this->event_del_count;
	}
	function attendeeAddedCount(){
		return $this->attendee_add_count;
	}
	function attendeeDeletedCount(){
		return $this->attendee_d_count;
	}
}



?>