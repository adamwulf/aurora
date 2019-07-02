<?

class TestTaskMan_main_module extends Abstract_Avalanche_TestCase {
	
	public function test_get_categories(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 0, "there are no categories");
	}
	
	public function test_add_category(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 0, "there are no categories");
		
		$strongcal = $avalanche->getModule("strongcal");
		$cals = $strongcal->getCalendarList();
		if(count($cals) == 0){
			throw new Exception("cannot finish taskman test. there are no calendars in strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$this->assert(is_object($taskman->addCategory($cal, "new cat")), "adding a task returned an object");
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 1, "there is one category");
		$this->assertEquals($cats[0]->name(), "new cat", "the category name is correct");
		$this->assertEquals($cats[0]->calId(), $cal->getId(), "the category cal_id is correct");
		
		$taskman->deleteCategory($cats[0]->getId());
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 0, "there are no categories");
	}
	
	public function test_add_multiple_categories(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 0, "there are no categories");
		
		$strongcal = $avalanche->getModule("strongcal");
		$cal_id = $strongcal->addCalendar("new calenar");
		
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 2){
			throw new Exception("cannot finish taskman test. there are less than 2 calendars in strongcal");
		}
		$cal1 = $cals[0]["calendar"];
		$cal2 = $cals[1]["calendar"];
		
		$this->assert(is_object($taskman->addCategory($cal1, "new cat")), "adding a task returned an object");
		$this->assert(is_object($taskman->addCategory($cal2, "new cat")), "adding a task returned an object");
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 2, "there are two categories");
		
		$cats = $taskman->getCategories($cal1);
		$this->assertEquals(count($cats), 1, "there is one category for this calendar");
		
		
		$cats = $taskman->getCategories();
		$taskman->deleteCategory($cats[0]->getId());
		$taskman->deleteCategory($cats[1]->getId());
		$cats = $taskman->getCategories();
		$this->assertEquals(count($cats), 0, "there are no categories");

		$strongcal->removeCalendar($cal_id);
	}
	
	public function test_match_datetime_format(){
		// test some valid dates (both format and actual date)
		$this->assert(module_taskman_task::isDateTime("2004-09-24 20:13:45"));
		$this->assert(module_taskman_task::isDateTime("2000-04-23 24:00:00"));
		$this->assert(module_taskman_task::isDateTime("1965-12-04 04:53:25"));
		$this->assert(module_taskman_task::isDateTime("1954-11-19 02:30:05"));

		// test to make sure the date is the only thing in the string
		$this->assert(!module_taskman_task::isDateTime("2004-09-24 20:13:45more"));
		$this->assert(!module_taskman_task::isDateTime("more2004-09-24 20:13:45"));
		$this->assert(!module_taskman_task::isDateTime("more2004-09-24 20:13:45more"));
		
		// test bogus and null strings
		$this->assert(!module_taskman_task::isDateTime(""));
		$this->assert(!module_taskman_task::isDateTime("3245avsq4124"));
		
		// test valid format, but incorrect dates
		$this->assert(!module_taskman_task::isDateTime("1854-11-19 02:30:05"));
		$this->assert(!module_taskman_task::isDateTime("2004-13-24 20:13:45"));
		$this->assert(!module_taskman_task::isDateTime("2000-04-32 24:00:00"));
		$this->assert(!module_taskman_task::isDateTime("1965-12-04 34:53:25"));
		$this->assert(!module_taskman_task::isDateTime("1954-11-19 02:70:05"));
		$this->assert(!module_taskman_task::isDateTime("1954-11-19 02:00:65"));
	}
	
	public function test_add_and_delete_task(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$strongcal = $avalanche->getModule("strongcal");
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 1){
			throw new Exception("cannot finish taskman test. there are less than 1 calendars in strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$task = $taskman->addTask($cal, array("summary" => "get the task done!",
					      "due" => "2004-09-28 20:15:00",
					      "priority" => 2,
					      "description" => "this is a longer description"));
		$this->assert($taskman->deleteTask($task->getId()), "the task was deleted");
	}

	public function test_everything_for_single_task(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$strongcal = $avalanche->getModule("strongcal");
		$gmtime = $strongcal->gmttimestamp();
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 1){
			throw new Exception("cannot finish taskman test. there are less than 1 calendars in strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$task = $taskman->addTask($cal, array("summary" => "get the task done!",
					      "due" => "2004-09-28 20:15:00",
					      "priority" => module_taskman_task::$PRIORITY_NORMAL,
					      "description" => "this is a longer description"));
		$this->assertEquals($task->completed(), "0000-00-00 00:00:00", "completion date is right");
		// i have to sleep here, b/c getting the correct status depends on the stamp
		$task->status(module_taskman_task::$STATUS_COMPLETED);
		
		$this->assertEquals(count($task->history()), 2, "the history is the correct length (only 2)");

		// test after immediate add
		$this->assertEquals($task->author(), $avalanche->loggedInHuh(), "the author is right");
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED, "the status is right");
		$this->assertEquals($task->title(), "get the task done!", "the title is right");
		$this->assertEquals($task->due(), "2004-09-28 20:15:00", "the due date is right");
		// check to make sure completion date was filled in
		$this->assertEquals($task->completed(), $taskman->adjustFromGMT(date("Y-m-d H:i:s", $gmtime)));
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_NORMAL);
		$this->assertEquals($task->description(), "this is a longer description");
		
		$this->assertEquals(count($task->history()), 2, "the history is the correct length (only 2)");

		$task->title("new title");
		$task->due("2004-10-28 20:15:00");
		$task->priority(module_taskman_task::$PRIORITY_HIGH);
		$task->description("a different text");

		// test after reload
		$task->reload();
		$this->assertEquals($task->author(), $avalanche->loggedInHuh());
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED);
		$this->assertEquals($task->title(), "new title");
		$this->assertEquals($task->due(), "2004-10-28 20:15:00", "due date is right");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_HIGH);
		$this->assertEquals($task->description(), "a different text");
		
		// test when asking taskman
		$tasks = $taskman->getTasks();
		
		$this->assertEquals(count($tasks), 1, "there is one task in the list");
		$task = $tasks[0];
		
		// add categories and add the task to the category
		$cat = $taskman->addCategory($cal, "new cat");
		
		// make sure it's not linked to any categories
		$this->assertEquals(count($task->getCategories()), 0, "the task is not linked");
		// link the task
		$task->linkTo($cat);	
		// make sure it's linked
		$this->assertEquals(count($task->getCategories()), 1, "the task is linked");
		$cats = $task->getCategories();
		$this->assert(is_object($cats[0]), "the task is not linked");
		$this->assertEquals($cats[0]->name(), "new cat", "the name of the category is correct");
		
		// unlink the task
		$task->unlinkto($cat);
		$this->assertEquals(count($task->getCategories()), 0, "the task is not linked");
		
		// delete the category
		$taskman->deleteCategory($cat->getId());
		
		
		// check history of status etc.
		$this->assertEquals($task->author(), $avalanche->loggedInHuh());
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED);
		$this->assertEquals($task->title(), "new title");
		$this->assertEquals($task->due(), "2004-10-28 20:15:00");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_HIGH);
		$this->assertEquals($task->description(), "a different text");
		
		$history = $task->history();
		
		$this->assertEquals(count($task->history()), 2, "the history is the correct length (only 2)");

		$this->assertEquals($history[0]["status"], module_taskman_task::$STATUS_COMPLETED, "the history is correct (complete)");
		$this->assertEquals($history[1]["status"], module_taskman_task::$STATUS_DEFAULT, "the history is correct (need action)");

		$this->assertEquals($task->assignedTo(), $avalanche->loggedInHuh(), "it's assigned to the logged in user");
		$this->assertEquals($task->delegatedTo(), $avalanche->loggedInHuh(), "it's delegated to the logged in user");
		$task->status(module_taskman_task::$STATUS_DELEGATED, 1);					      
		$this->assertEquals($task->delegatedTo(), 1, "it's delegated to user 1");
		$this->assertEquals($task->assignedTo(), $avalanche->loggedInHuh(), "still assigned to logged in user");
		$task->status(module_taskman_task::$STATUS_ACCEPTED);					      
		$this->assertEquals($task->delegatedTo(), 1, "it's delegated to user 1");
		$this->assertEquals($task->assignedTo(), 1, "still assigned to user 1");
		
		$this->assert($taskman->deleteTask($task->getId()), "the task was deleted");
	}

	public function test_cancelled_status(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$strongcal = $avalanche->getModule("strongcal");
		$gmtime = $strongcal->gmttimestamp();
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 1){
			throw new Exception("cannot finish taskman test. there are less than 1 calendars in strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$task = $taskman->addTask($cal, array("summary" => "get the task done!",
					      "due" => "2004-09-28 20:15:00",
					      "priority" => module_taskman_task::$PRIORITY_NORMAL,
					      "description" => "this is a longer description"));

					      // i have to sleep here, b/c getting the correct status depends on the stamp
		$task->status(module_taskman_task::$STATUS_CANCELLED);
		
		$this->assertEquals(count($task->history()), 2, "the history is the correct length (only 2)");

		// test after immediate add
		$this->assertEquals($task->author(), $avalanche->loggedInHuh(), "the author is right");
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_CANCELLED, "the status is right");
		$this->assertEquals($task->title(), "get the task done!", "the title is right");
		$this->assertEquals($task->due(), "2004-09-28 20:15:00", "the due date is right");
		// check to make sure completion date was filled in
		$this->assertEquals($task->cancelled(), $taskman->adjustFromGMT(date("Y-m-d H:i:s", $gmtime)));
		$this->assertEquals($task->completed(), "0000-00-00 00:00:00");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_NORMAL);
		$this->assertEquals($task->description(), "this is a longer description");

		// test after reload
		$task->reload();
		$this->assertEquals($task->author(), $avalanche->loggedInHuh());
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_CANCELLED);
		$this->assertEquals($task->title(), "get the task done!");
		$this->assertEquals($task->due(), "2004-09-28 20:15:00", "due date is right");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_NORMAL);
		$this->assertEquals($task->description(), "this is a longer description");
		
		$task->status(module_taskman_task::$STATUS_NEEDS_ACTION);
		$this->assertEquals($task->cancelled(), $taskman->adjustFromGMT(date("Y-m-d H:i:s", $gmtime)));

		// test when asking taskman
		$tasks = $taskman->getTasks();
		
		$this->assertEquals(count($tasks), 1, "there is one task in the list");
		$task = $tasks[0];
		$this->assert($taskman->deleteTask($task->getId()), "the task was deleted");
	}


	public function test_everything_for_two_tasks(){
		global $avalanche;
		$taskman = $avalanche->getModule("taskman");
		$strongcal = $avalanche->getModule("strongcal");
		$bootstrap = $avalanche->getModule("bootstrap");
		$gmtime = $strongcal->gmttimestamp();
		$cals = $strongcal->getCalendarList();
		if(count($cals) < 1){
			throw new Exception("cannot finish taskman test. there are less than 1 calendars in strongcal");
		}
		$cal = $cals[0]["calendar"];
		
		$task = $taskman->addTask($cal, array("summary" => "get the task done!",
					      "due" => "2004-09-28 20:15:00",
					      "priority" => module_taskman_task::$PRIORITY_NORMAL,
					      "description" => "this is a longer description"));

		$task2 = $taskman->addTask($cal, array("summary" => "the second task!",
					      "due" => "2004-10-28 20:15:00",
					      "priority" => module_taskman_task::$PRIORITY_HIGH,
					      "description" => "this is a different task than the other task"));

		$this->assertEquals($task->completed(), "0000-00-00 00:00:00");

		// i have to sleep here, b/c getting the correct status depends on the stamp
		$task->status(module_taskman_task::$STATUS_COMPLETED);					      

		// test after immediate add
		$this->assertEquals($task->author(), $avalanche->loggedInHuh());
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED);
		$this->assertEquals($task->title(), "get the task done!");
		$this->assertEquals($task->due(), "2004-09-28 20:15:00");
		// check to make sure completion date was filled in
		$this->assertEquals($task->completed(), $taskman->adjustFromGMT(date("Y-m-d H:i:s", $gmtime)));
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_NORMAL);
		$this->assertEquals($task->description(), "this is a longer description");
		
		$task->title("new title");
		$task->due("2004-10-28 20:15:00");
		$task->priority(module_taskman_task::$PRIORITY_HIGH);
		$task->description("a different text");

		// test after reload
		$task->reload();
		$this->assertEquals($task->author(), $avalanche->loggedInHuh());
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED);
		$this->assertEquals($task->title(), "new title");
		$this->assertEquals($task->due(), "2004-10-28 20:15:00", "due date is right");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_HIGH);
		$this->assertEquals($task->description(), "a different text");
		
		// test when asking taskman
		$tasks = $taskman->getTasks();
		
		$this->assertEquals(count($tasks), 2, "there is one task in the list");
		
		// test task 0 for category support
		// add categories and add the task to the category
		$data = new module_bootstrap_data($tasks, "send in list of tasks to sort");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add(new module_bootstrap_taskman_tasksorter());
		$data = $runner->run($data);
		$tasks = array_reverse($data->data());

		$task = $tasks[0];
		$task1 = $tasks[0];
		$task2 = $tasks[1];
		$cat = $taskman->addCategory($cal, "new cat");
		
		// make sure it's not linked to any categories
		$this->assertEquals(count($task1->getCategories()), 0, "the task is not linked");
		$this->assertEquals(count($task2->getCategories()), 0, "the task is not linked");
		// link the task
		$task1->linkTo($cat);	
		// make sure it's linked
		$this->assertEquals(count($task1->getCategories()), 1, "the task is linked");
		$this->assertEquals(count($task2->getCategories()), 0, "the task is not linked");
		$cats = $task1->getCategories();
		$this->assert(is_object($cats[0]), "the task is not linked");
		$this->assertEquals($cats[0]->name(), "new cat", "the name of the category is correct");
		
		// unlink the task
		$task1->unlinkto($cat);
		$this->assertEquals(count($task1->getCategories()), 0, "the task is not linked");
		$this->assertEquals(count($task2->getCategories()), 0, "the task is not linked");
		
		// delete the category
		$taskman->deleteCategory($cat->getId());
		
		
		// make sure fields are correct
		$this->assertEquals($task->author(), $avalanche->loggedInHuh(), "author is right");
		$this->assertEquals($task->status(), module_taskman_task::$STATUS_COMPLETED, "status is right");
		$this->assertEquals($task->title(), "new title", "title is right");
		$this->assertEquals($task->due(), "2004-10-28 20:15:00", "due date is right");
		$this->assertEquals($task->priority(), module_taskman_task::$PRIORITY_HIGH, "priority is right");
		$this->assertEquals($task->description(), "a different text", "description is right");
		
		$history = $task->history();
		
		$this->assertEquals(count($history), 2, "the history is the correct length");
		$this->assertEquals($history[0]["status"], module_taskman_task::$STATUS_COMPLETED, "the history is the correct length");
		$this->assertEquals($history[1]["status"], module_taskman_task::$STATUS_DEFAULT, "the history is the correct length");

		$this->assertEquals($task->assignedTo(), $avalanche->loggedInHuh(), "it's assigned to the logged in user");
		$this->assertEquals($task->delegatedTo(), $avalanche->loggedInHuh(), "it's delegated to the logged in user");
		$task->status(module_taskman_task::$STATUS_DELEGATED, 1);					      
		$this->assertEquals($task->delegatedTo(), 1, "it's delegated to user 1");
		$this->assertEquals($task->assignedTo(), $avalanche->loggedInHuh(), "still assigned to logged in user");
		$task->status(module_taskman_task::$STATUS_ACCEPTED);					      
		$this->assertEquals($task->delegatedTo(), 1, "it's delegated to user 1");
		$this->assertEquals($task->assignedTo(), 1, "still assigned to user 1");
		
		$this->assert($taskman->deleteTask($task->getId()), "the task was deleted");


		// test to make sure 2nd task didn't change
		$task2->reload();
		$this->assertEquals($task2->author(), $avalanche->loggedInHuh(), "author is correct");
		$this->assertEquals($task2->status(), module_taskman_task::$STATUS_DEFAULT, "status is correct");
		$this->assertEquals($task2->title(), "the second task!", "title is correct");
		$this->assertEquals($task2->due(), "2004-10-28 20:15:00", "due date is correct");
		// check to make sure completion date was filled in
		$this->assertEquals($task2->completed(), "0000-00-00 00:00:00", "completion date is correct");
		$this->assertEquals($task2->priority(), module_taskman_task::$PRIORITY_HIGH, "priority is correct");
		$this->assertEquals($task2->description(), "this is a different task than the other task", "description is correct");
		$this->assert($taskman->deleteTask($task2->getId()), "the task was deleted");
	}
};


?>