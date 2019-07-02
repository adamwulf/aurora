<?

class TestReminder_main_module extends Abstract_Avalanche_TestCase {

	public function test_add_and_delete_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assert(is_object($reminder->getReminder($remind->getId())), "i can get the reminder");
		$this->assert(!$remind->item(), "the reminder has no item");
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_SIMPLE, "the reminder has a simple type");
		$this->assertEquals($remind->author(), $avalanche->loggedInHuh(), "i am the author");
		$this->assertEquals(count($remind->getUsers()), 1, "there is one user for this reminder");
		$this->assert($remind->canWrite(), "i can write it");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_add_and_delete_users_in_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assert(is_object($reminder->getReminder($remind->getId())), "i can get the reminder");
		$this->assert(!$remind->item(), "the reminder has no item");
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_SIMPLE, "the reminder has a simple type");
		$this->assertEquals($remind->author(), $avalanche->loggedInHuh(), "i am the author");
		$this->assertEquals(count($remind->getUsers()), 1, "there is one user for this reminder");
		$this->assert($remind->removeUser($avalanche->loggedInHuh()), "the user has been removed");
		$this->assertEquals(count($remind->getUsers()), 0, "there is one user for this reminder");
		$remind->reload();
		$this->assertEquals(count($remind->getUsers()), 0, "there is one user for this reminder");
		// i should only be able to add me once...
		$this->assert($remind->addUser($avalanche->loggedInHuh()), "the user has been removed");
		$this->assert($remind->addUser($avalanche->loggedInHuh()), "the user has been removed");
		$this->assertEquals(count($remind->getUsers()), 1, "there is one user for this reminder");
		$remind->reload();
		$this->assertEquals(count($remind->getUsers()), 1, "there is one user for this reminder");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}


	public function test_change_year_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->year(), "the year defaults to 0");
		$this->assertEquals(2004, $remind->year(2004), "i changed the year");
		$this->assertEquals(2004, $remind->year(), "i changed the year");
		$remind->reload();
		$this->assertEquals(2004, $remind->year(), "i changed the year");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_change_month_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->month(), "the month defaults to 0");
		$this->assertEquals(12, $remind->month(12), "i changed the month");
		$this->assertEquals(12, $remind->month(), "i changed the month");
		$remind->reload();
		$this->assertEquals(12, $remind->month(), "i changed the month");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_change_day_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->day(), "the day defaults to 0");
		$this->assertEquals(24, $remind->day(24), "i changed the day");
		$this->assertEquals(24, $remind->day(), "i changed the day");
		$remind->reload();
		$this->assertEquals(24, $remind->day(), "i changed the day");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_change_hour_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->hour(), "the hour defaults to 0");
		$this->assertEquals(11, $remind->hour(11), "i changed the hour");
		$this->assertEquals(11, $remind->hour(), "i changed the hour");
		$remind->reload();
		$this->assertEquals(11, $remind->hour(), "i changed the hour");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_change_minute_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->minute(), "the minute defaults to 0");
		$this->assertEquals(5, $remind->minute(5), "i changed the minute");
		$this->assertEquals(5, $remind->minute(), "i changed the minute");
		$remind->reload();
		$this->assertEquals(5, $remind->minute(), "i changed the minute");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_change_second_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals(0, $remind->second(), "the second defaults to 0");
		$this->assertEquals(53, $remind->second(53), "i changed the second");
		$this->assertEquals(53, $remind->second(), "i changed the second");
		$remind->reload();
		$this->assertEquals(53, $remind->second(), "i changed the second");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}
	
	public function test_change_subject_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals("", $remind->subject(), "the subject defaults to \"\"");
		$this->assertEquals("asdf", $remind->subject("asdf"), "i changed the subject");
		$this->assertEquals("asdf", $remind->subject(), "i changed the subject");
		$remind->reload();
		$this->assertEquals("asdf", $remind->subject(), "i changed the subject");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}
	
	public function test_change_body_reminder(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals("", $remind->body(), "the body defaults to \"\"");
		$this->assertEquals("asdf", $remind->body("asdf"), "i changed the body");
		$this->assertEquals("asdf", $remind->body(), "i changed the body");
		$remind->reload();
		$this->assertEquals("asdf", $remind->body(), "i changed the body");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}
	
	public function test_start_on_simple(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$remind->year(4);
		$remind->month(12);
		$remind->day(30);
		$remind->hour(23);
		$remind->minute(53);
		$remind->second(12);
		$remind->year(2004);
		$this->assertEquals("2004-12-30 23:53:12", $remind->sendOn(), "the start on date is correct");
		$remind->reload();
		$this->assertEquals("2004-12-30 23:53:12", $remind->sendOn(), "the start on date is correct");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_start_on_event(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		$event = $cal->addEvent();
		$event->setValue("start_date", "2004-10-12");
		$event->setValue("start_time", "12:34:23");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$remind->type(module_reminder_reminder::$TYPE_EVENT, $event);
		
		$this->assertEquals("2004-10-12 12:34:23", $remind->sendOn(), "the start on date is correct");
		$remind->reload();
		$this->assertEquals("2004-10-12 12:34:23", $remind->sendOn(), "the start on date is correct");

		$remind->year(4);
		$remind->month(6);
		$remind->day(4);
		$remind->hour(1);
		$remind->minute(3);
		$remind->second(12);

		$this->assertEquals("2000-04-08 11:31:11", $remind->sendOn(), "the start on date is correct");
		$remind->reload();
		$this->assertEquals("2000-04-08 11:31:11", $remind->sendOn(), "the start on date is correct");

		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_start_on_task(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		$strongcal->timezone(-6);
		$taskman = $avalanche->getModule("taskman");
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		$task = $taskman->addTask($cal, array("description" => "", "due" => "2004-10-12 12:34:23", "summary" => "", "priority" => module_taskman_task::$PRIORITY_NORMAL));
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$remind->type(module_reminder_reminder::$TYPE_TASK, $task);
		
		$this->assertEquals("2004-10-12 18:34:23", $remind->sendOn(), "the send on date is correct");
		$remind->reload();
		$this->assertEquals("2004-10-12 18:34:23", $remind->sendOn(), "the send on date is correct");

		$remind->year(4);
		$remind->month(6);
		$remind->day(4);
		$remind->hour(1);
		$remind->minute(3);
		$remind->second(12);

		$this->assertEquals("2000-04-08 17:31:11", $remind->sendOn(), "the start on date is correct");
		$remind->reload();
		$this->assertEquals("2000-04-08 17:31:11", $remind->sendOn(), "the start on date is correct");

		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_sent_on(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		
		$this->assertEquals("0000-00-00 00:00:00", $remind->sentOn(), "the sent on date is correct");
		$remind->sendReminder();
		$this->assertEquals(date("Y-m-d H:i:s", $strongcal->gmttimestamp()), $remind->sentOn(), "the sent on date is correct");
		$remind->reload();
		$this->assertEquals(date("Y-m-d H:i:s", $strongcal->gmttimestamp()), $remind->sentOn(), "the sent on date is correct");

		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}
	
	
	public function test_item_event(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$event = $cal->addEvent();
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals($remind->type(module_reminder_reminder::$TYPE_EVENT, $event),module_reminder_reminder::$TYPE_EVENT, "i changed the type to event");
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_EVENT, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $event->getId(), "the event id is correct");
		$remind->reload();
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_EVENT, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $event->getId(), "the event id is correct");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_item_event_attendees(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$event = $cal->addEvent();
		// remove the default attendee...
		$event->removeAttendee((int)$avalanche->getActiveUser());
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals($remind->type(module_reminder_reminder::$TYPE_EVENT_ATTENDEES, $event),module_reminder_reminder::$TYPE_EVENT_ATTENDEES, "i changed the type to event");
		$this->assert($strongcal->avalanche() === $reminder->avalanche(), "the modules avalanche is the same");
		$this->assert($strongcal === $reminder->avalanche()->getModule("strongcal"), "the strongcal module is the same");
		$this->assert($strongcal->avalanche()->getModule("reminder") === $reminder, "the reminder module is the same");
		$this->assert($event->calendar() === $cal, "the event's calendar is the same as itself");
		$this->assert($event->calendar() === $strongcal->getCalendarFromDb($event->calendar()->getId()), "the calendar is the same as itself");
		$this->assert($event === $event->calendar()->getEvent($event->getId()), "the event is the same as itself");
		$this->assert($remind->item() === $event, "the event and item are exact same");
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_EVENT_ATTENDEES, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $event->getId(), "the event id is correct");
		$remind->reload();
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_EVENT_ATTENDEES, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $event->getId(), "the event id is correct");
		
		$remind->reload();
		$event->addAttendee((int)$avalanche->getVar("USER"));
		$this->assertEquals(count($remind->item()->attendees()), count($remind->getUsers()), "there is 1 user");
		$event->removeAttendee((int)$avalanche->getVar("USER"));

		$this->assertEquals(count($remind->item()->attendees()), count($remind->getUsers()), "there are 0 users");
		$event->addAttendee((int)$avalanche->getVar("USER"));
		$this->assertEquals(count($remind->item()->attendees()), count($remind->getUsers()), "there are 3 users");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

	public function test_item_task(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		$taskman = $avalanche->getModule("taskman");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$task = $taskman->addTask($cal, array("description" => "", "due" => "2004-12-12 08:00:00", "summary" => "", "priority" => module_taskman_task::$PRIORITY_NORMAL));
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$this->assertEquals($remind->type(module_reminder_reminder::$TYPE_TASK, $task),module_reminder_reminder::$TYPE_TASK, "i changed the type to task");
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_TASK, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $task->getId(), "the event id is correct");
		$remind->reload();
		$this->assertEquals($remind->type(), module_reminder_reminder::$TYPE_TASK, "the type is correct");
		$this->assert(is_object($remind->item()), "the item is an object"); 
		$this->assertEquals($remind->item()->getId(), $task->getId(), "the task id is correct");
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
		
		$taskman->deleteTask($task->getId());
	}

	public function test_reminders_for_user(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		
		$remind1 = $reminder->addReminder();
		$remind2 = $reminder->addReminder();
		$this->assert(is_object($remind1), "the reminder is an object");
		$this->assert(is_object($remind2), "the reminder is an object");
		
		$reminds = $reminder->getRemindersFor($avalanche->loggedInHuh());
		$this->assertEquals(count($reminds), 2, "there are 2 reminders for this user");
		
		$this->assert($reminder->deleteReminder($remind1->getId()), "the reminder was deleted");
		$this->assert($reminder->deleteReminder($remind2->getId()), "the reminder was deleted");
	}

	public function test_reminders_for_time(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		$rs = $reminder->getRemindersBefore(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
		$this->assertEquals(count($rs), 0, "there is 1 reminder for this time");
		
		$remind1 = $reminder->addReminder();
		$remind1->year((int)date("Y", $strongcal->gmttimestamp()));
		$remind1->month((int)date("m", $strongcal->gmttimestamp()));
		$remind1->day((int)date("d", $strongcal->gmttimestamp()));
		$remind1->hour((int)date("H", $strongcal->gmttimestamp()));
		$remind1->minute((int)date("i", $strongcal->gmttimestamp()));
		$remind1->second((int)date("s", $strongcal->gmttimestamp()));
		$remind2 = $reminder->addReminder();
		$remind2->year((int)date("Y", $strongcal->gmttimestamp()));
		$remind2->month((int)date("m", $strongcal->gmttimestamp()));
		$remind2->day((int)date("d", $strongcal->gmttimestamp()));
		$remind2->hour((int)date("H", $strongcal->gmttimestamp())+2);
		$remind2->minute((int)date("i", $strongcal->gmttimestamp()));
		$remind2->second((int)date("s", $strongcal->gmttimestamp()));
		$this->assert(is_object($remind1), "the reminder is an object");
		$this->assert(is_object($remind2), "the reminder is an object");
		
		$this->assertTrue($remind1->sendOn() < date("Y-m-d H:i:s", $strongcal->gmttimestamp()+60*60), "the send on date is correct");
		$rs = $reminder->getRemindersBefore(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+60*60));
		$this->assertEquals(count($rs), 1, "there is 1 reminder for this time");
		$rs[0]->sendReminder();
		
		$rs = $reminder->getRemindersBefore(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+60*60));
		$this->assertEquals(count($rs), 0, "there are 0 reminders for this time");

		// reset send time by editing the reminder
		$remind1->hour((int)date("H", $strongcal->gmttimestamp()));
		// set minutes to be after *now* so that it'll actually reset send on time
		$remind1->minute((int)date("i", $strongcal->gmttimestamp()+60*20));
		
		$rs = $reminder->getRemindersBefore(date("Y-m-d H:i:s", $strongcal->gmttimestamp()+60*60));
		$this->assertEquals(count($rs), 1, "there is 1 reminder for this time");
		
		$this->assert($reminder->deleteReminder($remind1->getId()), "the reminder was deleted");
		$this->assert($reminder->deleteReminder($remind2->getId()), "the reminder was deleted");
	}
	
	

	public function test_reminders_for_event(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$event = $cal->addEvent();
		
		$remind1 = $reminder->addReminder();
		$remind2 = $reminder->addReminder();
		$this->assert(is_object($remind1), "the reminder is an object");
		$this->assert(is_object($remind2), "the reminder is an object");
		$remind1->type(module_reminder_reminder::$TYPE_EVENT, $event);
		$remind2->type(module_reminder_reminder::$TYPE_EVENT, $event);
		
		$reminds = $reminder->getRemindersFor($event);
		$this->assertEquals(count($reminds), 2, "there are 2 reminders for this event");
		
		$this->assert($reminder->deleteReminder($remind1->getId()), "the reminder was deleted");
		$this->assert($reminder->deleteReminder($remind2->getId()), "the reminder was deleted");
	}

	public function test_reminders_for_task(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$taskman = $avalanche->getModule("taskman");
		$strongcal = $avalanche->getModule("strongcal");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		$task = $taskman->addTask($cal, array("description" => "", "due" => "2004-12-12 08:00:00", "summary" => "", "priority" => module_taskman_task::$PRIORITY_NORMAL));
		
		$remind1 = $reminder->addReminder();
		$remind2 = $reminder->addReminder();
		$this->assert(is_object($remind1), "the reminder is an object");
		$this->assert(is_object($remind2), "the reminder is an object");
		$remind1->type(module_reminder_reminder::$TYPE_TASK, $task);
		$remind2->type(module_reminder_reminder::$TYPE_TASK, $task);
		
		$reminds = $reminder->getRemindersFor($task);
		$this->assertEquals(count($reminds), 2, "there are 2 reminders for this task");
		
		$this->assert($reminder->deleteReminder($remind1->getId()), "the reminder was deleted");
		$this->assert($reminder->deleteReminder($remind2->getId()), "the reminder was deleted");
		
		$taskman->deleteTask($task->getId());
	}


	public function test_reminders_for_changing_event(){
		global $avalanche;
		$reminder = $avalanche->getModule("reminder");
		$strongcal = $avalanche->getModule("strongcal");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			fail("could not load a calendar from strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$event = $cal->addEvent();
		
		$remind = $reminder->addReminder();
		$this->assert(is_object($remind), "the reminder is an object");
		$remind->type(module_reminder_reminder::$TYPE_EVENT, $event);
		
		$reminds = $reminder->getRemindersFor($event);
		$this->assertEquals(count($reminds), 1, "there is 1 reminders for this event");
		
		// this will throw an exception, b/c reminder->verify isn't complete
		$event->setValue("start_date", "2004-10-10");
		
		$this->assert($reminder->deleteReminder($remind->getId()), "the reminder was deleted");
	}

};

?>