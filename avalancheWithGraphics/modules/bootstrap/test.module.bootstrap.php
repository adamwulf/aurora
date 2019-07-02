<?

Class TestBootstrap extends TestCase { 
   public function test_get_module() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert(is_object($bootstrap), "bootstrap is an object" );
   }

   public function test_name_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assertEquals($bootstrap->name(), "Bootstrap!", "bootstrap is named 'Bootstrap!'" );
   }

   public function test_version_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assertEquals($bootstrap->version(), "1.0.0", "bootstrap is version '1.0.0'" );
   }

   public function test_folder_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assertEquals($bootstrap->folder(), "bootstrap", "bootstrap is in folder 'bootstrap'" );
   }

   public function test_copyright_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assertEquals($bootstrap->copyright(), "Copyright 2002 Inversion Designs", "bootstrap is copyrighted by 'Copyright 2002 Inversion Designs'" );
   }

   public function test_author_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assertEquals($bootstrap->author(), "Adam Wulf", "bootstrap has author 'Adam Wulf'" );
   }

   public function test_user_logged_out_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->userLoggedOut("foo"), "user logs out notice" );
   }

   public function test_user_logged_in_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->userLoggedIn("foo"), "user logs in notice" );
   }

   public function test_add_usergroup_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->addUsergroup("foo"), "usergroup added notice" );
   }
   
   public function test_delete_usergroup_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->deleteUsergroup("foo"), "usergroup deleted notice" );
   }
   
   public function test_add_user_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->addUser("foo"), "user added notice" );
   }
   
   public function test_delete_user_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
	$this->assert($bootstrap->deleteUser("foo"), "user deleted notice" );
   }

};

?>