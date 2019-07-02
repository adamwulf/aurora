<?

class test_bootstrap_search_screen extends Abstract_Avalanche_TestCase { 

   public function test_search_dialog(){
	global $avalanche;
	
	$data = array();
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new module_bootstrap_os_search_gui($avalanche, new Document(), "terms"));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
};


?>