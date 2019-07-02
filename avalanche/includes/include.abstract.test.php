<?
abstract class Abstract_Avalanche_TestCase extends TestCase { 

   public function setUp(){
	global $avalanche;
	$avalanche->logIn(PHP_UNIT_USER, PHP_UNIT_PASS);
	$strongcal = $avalanche->getModule("strongcal");
	$cal_list = $strongcal->getCalendarList();
	foreach($cal_list as $cal){
	  if($cal["calendar"]->getId() != 38){
	     	    $strongcal->removeCalendar($cal["calendar"]->getId());
	  }
	}
	$sql = "DELETE FROM " . PREFIX . "strongcal_cal_38 WHERE 1";
	$avalanche->mysql_query($sql);
	
	$sql = "DELETE FROM " . PREFIX . "taskman_tasks WHERE 1";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "taskman_status_history WHERE 1";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "strongcal_attendees";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "reminder_outbox";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "reminder_relation_event";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "reminder_relation_task";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "reminder_reminders";
	$avalanche->mysql_query($sql);

	$sql = "DELETE FROM " . PREFIX . "accounts_transactions";
	$avalanche->mysql_query($sql);

	$usergroups = $avalanche->getAllUsergroups();
	foreach($usergroups as $group){
		if($group->getId() > 4){
			$avalanche->deleteUsergroup($group->getId());
		}
	}
	
	$users = $avalanche->getAllUsers();
	foreach($users as $user){
		if($user->getId() > 3){
			$avalanche->deleteUser($user->getId());
		}
	}
	
	$strongcal->reload();
   }

   public function tearDown(){
	global $avalanche;
	$avalanche->logOut();
   }


};
?>