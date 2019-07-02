<?

class test_bootstrap_manage_user_screen extends Abstract_Avalanche_TestCase { 

   public function test_manage_user_page_no_id(){
	global $avalanche;
	
	$data = array();
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageusers_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
   public function test_manage_user_page(){
	global $avalanche;
	
	$data = array("user_id" => 1);
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageusers_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
   public function test_manage_user_page_for_delete(){
	global $avalanche;
	
	try{
		$data = array("view" => "manage_users",
				"subview" => "delete_user",
				"user_id" => "17");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_manageusers_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("delete group is supposed to redirect");
	}catch(RedirectException $e){
		$this->pass("everything is fine");		
	}
    }
    
    
   public function test_manage_user_page_for_edit(){
	global $avalanche;
	
	$data = array("view" => "manage_users",
			"subview" => "edit_user",
			"user_id" => "1");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageusers_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_manage_user_page_for_add(){
	global $avalanche;
	
	$data = array("view" => "manage_users",
			"subview" => "add_user");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_manageusers_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }

    
};


?>