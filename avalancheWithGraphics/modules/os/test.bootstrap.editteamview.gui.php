<?

class test_bootstrap_edit_team_screen extends Abstract_Avalanche_TestCase { 

   public function test_edit_team_page_no_id(){
	global $avalanche;
	
	try{
		$data = array();
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_editteamview_gui($avalanche, new Document()));
		$runner->run($data);
		fail("should have thrown IllegalArgumentException");
	}catch(IllegalArgumentException $e){
		$this->pass("everything is fine");		
	}
    }
    
   public function test_edit_team_page(){
	global $avalanche;
	
	$data = array("team_id" => 1);
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_editteamview_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
   public function test_edit_team_page_with_id(){
	global $avalanche;
	
	$data = array("view" => "manage_teams",
			"subview" => "edit_team",
			"team_id" => "1");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_editteamview_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_edit_team_page_with_submit(){
	global $avalanche;
	
	$group = $avalanche->addUsergroup(avalanche_usergroup::$PERSONAL, "groupname", "desc", "key");
	$group = $avalanche->getUsergroup($group);
	try{
		$data = array("submit" => "1",
				"team_id" => $group->getId(),
				"name" => "boo",
				"description" => "desc here",
				"keywords" => "schwaat?");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_editteamview_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have been redirected after adding team");
	}catch(RedirectException $e){
		// they redirected us to the view page, that's good
	}
	
	$group = $avalanche->getUsergroup($group->getId());
	$this->assertEquals($group->name(), "boo", "name is right");
	$this->assertEquals($group->description(), "desc here", "description is right");
	$this->assertEquals($group->keywords(), "schwaat?", "keywords are right");
    }
};


?>