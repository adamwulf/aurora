<?

class TestNotifier_main_module extends Abstract_Avalanche_TestCase {

	public function test_add_and_delete_task(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assert(is_object($notifier->getNotification($notification->getId())), "i can get the notification");
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	public function test_notification_item(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		$strongcal = $avalanche->getModule("strongcal");
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assertEquals($notification->item(), module_notifier_notification::$ITEM_EVENT, "i can get the notification");
		$this->assertEquals($notification->item(module_notifier_notification::$ITEM_TASK), module_notifier_notification::$ITEM_TASK, "i can get the notification");
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	public function test_notification_contact(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		$strongcal = $avalanche->getModule("strongcal");
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assertEquals($notification->contactBy(), module_notifier_notification::$CONTACT_NONE, "the contact defaults to none");
		$this->assertEquals($notification->contactBy(module_notifier_notification::$CONTACT_EMAIL), module_notifier_notification::$CONTACT_EMAIL, "i can change the contact");
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	public function test_notification_action(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		$strongcal = $avalanche->getModule("strongcal");
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assertEquals($notification->action(), module_notifier_notification::$ACTION_ADDED, "i can get the notification");
		$this->assertEquals($notification->action(module_notifier_notification::$ACTION_EDITED), module_notifier_notification::$ACTION_EDITED, "i can get the notification");
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	public function test_notification_all_calendars(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		$strongcal = $avalanche->getModule("strongcal");
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assert(!$notification->allCalendarsHuh(), "the notification defaults to not all calendars");
		$this->assert($notification->allCalendarsHuh(true), "i changed the notification to be for all calendars");
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	public function test_notification_calendars(){
		global $avalanche;
		$notifier = $avalanche->getModule("notifier");
		$strongcal = $avalanche->getModule("strongcal");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 1){
			throw new Exception("not enough calendars in strongcal to run test");
		}
		
		$cal = $cals[0]["calendar"];		
		
		$notification = $notifier->addNotificationFor($avalanche->loggedInHuh());
		$this->assert(is_object($notification), "the notification is an object");
		$this->assert(is_array($notification->getCalendars()), "the notification has an array of calendars");
		$this->assertEquals(count($notification->getCalendars()), 0, "the notification does not apply to any calendars");
		// add a calendar
		$notification->addCalendar($cal);
		$this->assert(is_array($notification->getCalendars()), "the notification has an array of calendars");
		$this->assertEquals(count($notification->getCalendars()), 1, "the notification applies to one calendars");
		// reload
		$notification->reload();
		$this->assert(is_array($notification->getCalendars()), "the notification has an array of calendars");
		$this->assertEquals(count($notification->getCalendars()), 1, "the notification applies to one calendars");
		// remove the calendar
		$notification->removeCalendar($cal);
		$this->assert(is_array($notification->getCalendars()), "the notification has an array of calendars");
		$this->assertEquals(count($notification->getCalendars()), 0, "the notification does not apply to any calendars");
		// reload
		$notification->reload();
		$this->assert(is_array($notification->getCalendars()), "the notification has an array of calendars");
		$this->assertEquals(count($notification->getCalendars()), 0, "the notification does not apply to any calendars");
		
		$this->assert($notifier->deleteNotification($notification->getId()), "the notification was deleted");
	}
	
	
};

?>