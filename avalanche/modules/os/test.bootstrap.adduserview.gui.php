<?

class test_bootstrap_add_user_screen extends Abstract_Avalanche_TestCase { 

   public function test_add_user_page(){
	global $avalanche;
	
	$data = array();
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_addteamview_gui($avalanche, new Document()));
	$runner->run($data);
    }
    
    
   public function test_add_user_page_with_submit(){
	global $avalanche;
	
	$groups = $avalanche->getAllUsers();
	$count = count($groups);
	
	try{
		$data = array("submit" => "1",
				"title" => "Mr.",
				"first" => "Billy",
				"middle" => "J",
				"last" => "Idol",
				"username" => (string)rand(),
				"password" => "pw",
				"confirm" => "pw",
				"email" => "email@me.com");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_adduserview_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have been redirected after adding user");
	}catch(RedirectException $e){
		// they redirected us to the view page, that's good
	}
	
	$users = $avalanche->getAllUsers();
	$this->assertEquals($count + 1, count($users), "a user has been added");
	
	$count = 0;
	foreach($users as $u){
		if($u->first() == "Billy"){
			$count++;
			$this->assert($avalanche->deleteUser($u->getId()), "the user has been deleted");
		}
	}
	$this->assertEquals($count, 1, "1 user has been deleted");
    }
};

?>