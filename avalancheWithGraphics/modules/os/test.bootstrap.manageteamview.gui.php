<?

class test_bootstrap_manage_team_screen extends Abstract_Avalanche_TestCase { 

   public function test_manage_team_page_no_id(){
	global $avalanche;
	
	$data = array();
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
   public function test_manage_team_page(){
	global $avalanche;
	
	$data = array("team_id" => 1);
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
   public function test_manage_team_page_for_delete(){
	global $avalanche;
	
	try{
		$data = array("view" => "manage_teams",
				"subview" => "delete_team",
				"team_id" => "17");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("delete group is supposed to redirect");
	}catch(RedirectException $e){
		$this->pass("everything is fine");		
	}
    }
    
    
   public function test_manage_team_page_for_edit(){
	global $avalanche;
	
	$data = array("view" => "manage_teams",
			"subview" => "edit_team",
			"team_id" => "3");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_manage_team_page_for_add(){
	global $avalanche;
	
	$data = array("view" => "manage_teams",
			"subview" => "add_team");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_manage_team_page_for_unlink(){
	global $avalanche;
	$group = $avalanche->addUsergroup("SYSTEM", "asdf", "asdf", "asdf");
	$group = $avalanche->getUsergroup($group);
	$group->linkUser($avalanche->getActiveUser());
	try{
		$data = array("view" => "manage_teams",
				"subview" => "unlink_user",
				"team_id" => $group->getId(),
				"user_id" => $avalanche->getActiveUser());
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have redirected");
	}catch(RedirectException $e){
		$this->pass("everything is fine");		
	}
	$avalanche->deleteUsergroup($group->getId());
    }
    
    
   public function test_manage_team_page_for_link(){
	global $avalanche;
	
	try{
		$data = array("view" => "manage_teams",
				"subview" => "link_user",
				"team_id" => "3",
				"user_id" => "1");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have redirected");
	}catch(RedirectException $e){
		$this->pass("everything is fine");		
	}
    }
    
    
   public function test_manage_team_page_for_members(){
	global $avalanche;
	
	$data = array("view" => "manage_teams",
			"subview" => "members",
			"team_id" => "1");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_manage_team_page_for_overview(){
	global $avalanche;
	
	$data = array("view" => "manage_teams",
			"subview" => "overview",
			"team_id" => "3");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageteams_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
};

?>