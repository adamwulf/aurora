<?

class test_bootstrap_edit_user_screen extends Abstract_Avalanche_TestCase { 

   public function test_edit_user_page_no_id(){
	global $avalanche;
	
	try{
		$data = array();
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_edituserview_gui($avalanche, new Document()));
		$runner->run($data);
		 fail("should have thrown IllegalArgumentException");
	}catch(IllegalArgumentException $e){
		$this->pass("everything is fine");		
	}
   }
    
   public function test_edit_user_page(){
	global $avalanche;
	
	$data = array("user_id" => 1);
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_edituserview_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
   }
    
   public function test_edit_user_page_with_id(){
	global $avalanche;
	
	$data = array("view" => "manage_users",
			"subview" => "edit_user",
			"user_id" => $avalanche->getActiveUser());
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_edituserview_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
    
    
   public function test_edit_user_page_with_submit(){
	global $avalanche;
	
	$user = $avalanche->addUser("username", "pw", "email");
	$user = $avalanche->getUser($user);
	try{
		$data = array("submit" => "1",
				"user_id" => $user->getId(),
				"title" => "Mr",
				"first" => "First",
				"middle" => "Middle",
				"last" => "Last",
				"email" => "mail@mail.com",
				"sms" => "my phone",
				"bio" => "my bio!",
				"username" => "new name",
				"change_pass" => "1",
				"password" => "new pass",
				"confirm" => "new pass");
		$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_os_edituserview_gui($avalanche, new Document()));
		$runner->run($data);
		$this->fail("should have been redirected after editing user");
	}catch(RedirectException $e){
		$this->pass("everything is fine");		
	}
	
	$user = $avalanche->getUser($user->getId());
	$this->assertEquals($user->title(), "Mr", "title is right");
	$this->assertEquals($user->first(), "First", "first is right");
	$this->assertEquals($user->middle(), "Middle", "middle is right");
	$this->assertEquals($user->last(), "Last", "last is right");
	$this->assertEquals($user->email(), "mail@mail.com", "email is right");
	$this->assertEquals($user->sms(), "my phone", "sms is right");
	$this->assertEquals($user->bio(), "my bio!", "bio is right");
	$this->assertEquals($user->username(), "new name", "username is right");
	$this->assertEquals($user->password(), "new pass", "password is right");
    }
};


?>