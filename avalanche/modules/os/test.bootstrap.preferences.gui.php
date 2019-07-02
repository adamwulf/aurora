<?

class test_bootstrap_preferences_screen extends Abstract_Avalanche_TestCase { 

   public function test_preferences_dialog(){
	global $avalanche;
	
	$data = array("view" => "preferences");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_preferences_gui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
};


?>