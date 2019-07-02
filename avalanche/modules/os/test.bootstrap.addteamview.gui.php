<?

class test_bootstrap_add_team_screen extends Abstract_Avalanche_TestCase { 

   public function test_add_team_page(){
	global $avalanche;
	
	$data = array();
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_addteamview_gui($avalanche, new Document()));
	$runner->run($data);
    }
    
    
   public function test_add_team_page_with_submit(){
	global $avalanche;
	
	$groups = $avalanche->getAllUsergroups();
	$count = count($groups);
	
	try{
		$data = array("submit" => "1",
				"subview" => "add_team",
				"type" => (string)avalanche_usergroup::$PERSONAL,
				"name" => "boo",
				"description" => "desc here",
				"keywords" => "schwaat?");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_addteamview_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have been redirected after adding team");
	}catch(RedirectException $e){
		$this->pass("they redirected us to the view page, that's good");
	}
	
	$groups = $avalanche->getAllUsergroups();
	$this->assertEquals($count + 1, count($groups), "a group has been added");
	
	$count = 0;
	foreach($groups as $group){
		if($group->name() == "boo"){
			$count++;
			$this->assert($avalanche->deleteUsergroup($group->getId()), "the group has been deleted");
		}
	}
	$this->assertEquals($count, 1, "1 usergroup has been deleted");
    }
};


?>