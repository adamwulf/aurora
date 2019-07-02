<?

class TestAurora_bootstrap_calendarslist extends Abstract_Avalanche_TestCase { 

   public function test_get_all_possible(){
	global $avalanche;
	$strongcal = $avalanche->getModule("strongcal");

	$cal_1_id = $strongcal->addCalendar("new calendar 1");
	$cal_2_id = $strongcal->addCalendar("new calendar 2");	

	$data = false;
	new module_bootstrap_data($data, "the default value to get a list of all calendars");

	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
	$data = $runner->run($data);

	$cal_array = $data->data();
	$this->assertEquals(count($cal_array), 3, "there are 3 calendars in the return array");
	$cal = $cal_array[2];
	$this->assert(is_object($cal), "the calendar that was returned is an object");
	$this->assertEquals($cal->getId(), 38, "the calendar that was loaded is id 38");

	$strongcal->removeCalendar($cal_1_id);
	$strongcal->removeCalendar($cal_2_id);
    }

   public function test_get_specific(){
	global $avalanche;
	
		$data = false;
		new module_bootstrap_data($data, "fake form input that gives a bad module name");

		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_strongcal_calendarlist($avalanche));
		$data = $runner->run($data);

		$cal_array = $data->data();
		$this->assertEquals(count($cal_array), 1, "there is one calendar in the return array");
		$cal = $cal_array[0];
		$this->assert(is_object($cal), "the calendar that was returned is an object");
		$this->assertEquals($cal->getId(), 38, "the calendar that was loaded is id 38");
    }


};


?>