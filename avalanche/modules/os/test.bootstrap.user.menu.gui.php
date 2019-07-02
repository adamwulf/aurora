<?

class test_bootstrap_user_menu_screen extends Abstract_Avalanche_TestCase { 

   public function test_preferences_dialog(){
	global $avalanche;
	
	$data = false;
	$runner = new module_bootstrap_runner();
	$runner->add(new OSUserMenu($avalanche, new Document(), new Text("asdf"), 1));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
};

?>