<?
Class AvalancheTestComparator implements Comparator{
  public function compare($left, $right){
    if($left == $right){
      return 0;
    }else if($left < $right){
      return -1;
    }elseif($left > $right){
      return 1;
    };
  }
}


Class TestAvalanche extends Abstract_Avalanche_TestCase { 

   public function test_logout() {
	global $avalanche;
	$avalanche->logOut();
	$this->assert(!$avalanche->loggedInHuh(), "php unit has logged out" );
   }

   public function test_bad_login() {
	global $avalanche;
	$avalanche->logOut();
	$avalanche->logIn(PHP_UNIT_USER, "not the right password");
	$this->assert(!$avalanche->loggedInHuh(), "php unit has not logged in" );
   }

   public function test_find_all_usergroups(){
	global $avalanche;

	$groups = $avalanche->getAllUsergroups();
	
	$this->assertEquals(count($groups), 6, "there are 6 usergroups");

	foreach($groups as $group){
		$users = $avalanche->getAllUsers();
		$user_name_ok = false;
		foreach($users as $u){
			$user_name_ok = $user_name_ok || $group->name() == $avalanche->getModule("os")->getUsername($u->getId());
		}
		$this->assert($group->name() == "Administration" ||
		              $group->name() == "Guests" ||
			      $group->name() == "All Users" ||
			      $group->name() == "Guests" ||
			      $user_name_ok, "the group name is right");
	}
   }
   
   public function test_find_all_users(){
	global $avalanche;

	$users = $avalanche->getAllUsers();
	
	$this->assertEquals(count($users), 3, "there are 3 users");

	$this->assertEquals($users[0]->username(), "awulf", "the first user is the admin");
	$this->assertEquals($users[1]->username(), "guest", "the second user is the guest");
	$this->assertEquals($users[2]->username(), "phpunit", "the third user is the phpunit");

   }
   
   public function test_add_usergroup(){
	global $avalanche;
	// add a group
	$group_id = $avalanche->addUsergroup("SYSTEM", "my own group", "no description", "no keywords");
	   
	   
	$this->assert(is_integer($group_id), "the group id is an integer");
	$this->assert($group_id > 4, "the group id is greater than 4");
	   
	$group = $avalanche->getUsergroup($group_id);
	$this->assertEquals($group->name(), "my own group", "the name of the group is correct");
	$this->assertEquals($group->type(), avalanche_usergroup::$SYSTEM, "the group is a system group");
	   
   }

   public function test_delete_usergroup(){
	global $avalanche;
	// add a group
	$group_id = $avalanche->addUsergroup("SYSTEM", "my own group", "no description", "no keywords");
	$this->assert($avalanche->deleteUsergroup($group_id), "the usergroup has been deleted");
	   
	$group = $avalanche->getUsergroup($group_id);
	$this->assert(is_bool($group), "the deleted group has not been returned");
	$this->assert(!$group, "the group is false");
   }

   public function test_add_user(){
	global $avalanche;
	$def_group_id = $avalanche->getVar("USERGROUP");
	$all_group_id = $avalanche->getVar("ALLUSERS");
	   
	$user_id = $avalanche->addUser("billy", "pass", "myemail@dot.com");
	   
	$this->assert(is_integer($user_id), "the user was added correctly");
	$this->assert($avalanche->getUsername($user_id), "billy", "the username is correct");
	$this->assert($avalanche->getPassword($user_id), "pass", "the password is correct");
	$this->assertEquals($avalanche->getAvatar($user_id), $avalanche->defaultAvatar(), "the avatar is the default");
	   
	$groups = $avalanche->getAllUsergroupsFor($user_id);
	$this->assertEquals($groups[0]->name(), "Guests", "the first group is the admin group");
	$this->assertEquals($groups[1]->name(), "All Users", "the first group is the admin group");
   }
   
   public function test_delete_user(){
	global $avalanche;
	$user_id = $avalanche->addUser("billy", "pass", "myemail@dot.com");
	$result = $avalanche->deleteUser($user_id);
	$this->assert($result, "the user has been deleted"); 
   }
   
   public function test_rename_user(){
	global $avalanche;
	$user_id = $avalanche->addUser("billy", "pass", "myemail@dot.com");
	$avalanche->renameUser($user_id, "sam");
	$username = $avalanche->getUsername($user_id);
	$this->assertEquals($username, "sam", "the username has been changed to sam");
   }

   public function test_rename_usergroup(){
	global $avalanche;
	$group_id = $avalanche->addUsergroup("SYSTEM", "my own group", "no description", "no keywords");
	   
	$group = $avalanche->getUsergroup($group_id);
	$this->assertEquals($group->name(), "my own group", "the name of the group is correct");
	$this->assert($group->name("billy's group"), "the group was renamed successfully");
	$this->assertEquals($group->name(), "billy's group", "the name of the group is correct");
   }

   public function test_set_permissions(){
	global $avalanche;
	$group_id = $avalanche->addUsergroup("SYSTEM", "my own group", "no description", "no keywords");
	$group = $avalanche->getUsergroup($group_id);
	$this->assertEquals($group->hasPermissionHuh("link_user"), "0", "the group now has permission to link users");
	$result = $group->updatePermissions(array("link_user" => "1"));
	$this->assert($result, "the update went smoothly");
	$this->assertEquals($group->hasPermissionHuh("link_user"), "1", "the group now has permission to link users");
   }

   public function test_defined_classes(){
	$class_array =    array("avalanche_class",
				"Abstract_Avalanche_TestCase",
				"avalanche_usergroup",
				"ColorConversion",
				"CookieJar",
				"HashTable",
				"MDASorter",
				"ClassDefNotFoundException",
				"DatabaseException",
				"IllegalArgumentException",
				"Comparator",
				"avalanche_interface_cookieTray",
				"avalanche_interface_sprocket",
				"visitor_getAllUsergroups",
				"visitor_getAllUsergroupsFor");
	
	for($i=0;$i<count($class_array);$i++){
		$class = $class_array[$i];
		$this->assert(class_exists($class) || interface_exists($class), "the class $class exists");
	}
   }

   public function test_MDASorter(){
	$sorter = new MDASorter();
	$comp = new AvalancheTestComparator();
	$data = array (1, 12,5,23,789,11);

	$sorted_data = $sorter->sort($data, $comp);
	$answer_key = array(1, 5, 11, 12, 23, 789);

	$this->assertEquals(count($data), count($sorted_data), "the data is the correct length");
	for($i=0;$i<count($data);$i++){
		$this->assertEquals($answer_key[$i], $sorted_data[$i], "the array is sorted");
	}
   }

   public function test_MDASorter_ASC(){
	$sorter = new MDASorter();
	$comp = new AvalancheTestComparator();
	$data = array (1, 12,5,23,789,11);

	$sorted_data = $sorter->sortASC($data, $comp);
	$answer_key = array(1, 5, 11, 12, 23, 789);

	$this->assertEquals(count($data), count($sorted_data), "the data is the correct length");
	for($i=0;$i<count($data);$i++){
		$this->assertEquals($answer_key[$i], $sorted_data[$i], "the array is sorted");
	}
   }

   public function test_MDASorter_DESC(){
	$sorter = new MDASorter();
	$comp = new AvalancheTestComparator();
	$data = array (1, 12,5,23,789,11);

	$sorted_data = $sorter->sortDESC($data, $comp);
	$answer_key = array_reverse(array(1, 5, 11, 12, 23, 789));

	$this->assertEquals(count($data), count($sorted_data), "the data is the correct length");
	for($i=0;$i<count($data);$i++){
		$this->assertEquals($answer_key[$i], $sorted_data[$i], "the array is sorted");
	}
   }

   public function test_bad_module(){
	global $avalanche;
	try{
		$avalanche->getModule("asdf");
	}catch(ModuleNotInstalledException $e){
		// good, that's the exception we want
		$this->pass("correct exception thrown");
		return;
	}catch(Exception $e){
		$this->fail(get_class($e) . " was thrown instead of a ModuleNotInstalledException.");
	}
	$this->fail("ModuleNotInstalledException was not thrown");
   }

   public function test_bad_skin(){
	global $avalanche;
	try{
		$avalanche->getSkin("asdf");
	}catch(SkinNotFoundException $e){
		// good, that's the exception we want
		$this->pass("correct exception thrown");
		return;
	}catch(Exception $e){
		$this->fail(get_class($e) . " was thrown instead of a SkinNotFoundException.");
	}
	$this->fail("SkinNotFoundException was not thrown");
   }
   
   public function test_system_user(){
	global $avalanche;
	$user = $avalanche->getUser(-1);
	$this->assertEquals($user->name(), "SYSTEM", "the user's name is SYSTEM");
	$this->assertEquals($user->email(), "noreply@" . $avalanche->DOMAIN(), "the user's email is correct");
	try{
		$user->disable();
		$this->fail("Exception not thrown when disabling system user");
	}catch(Exception $e){
		$this->pass("correct exception thrown");
		// noop
	}
   }

};

?>