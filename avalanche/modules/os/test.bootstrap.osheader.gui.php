<?

class test_bootstrap_osheader_screen extends Abstract_Avalanche_TestCase { 

   public function test_osheader_dialog(){
	global $avalanche;
	
	$data = false;
	$runner = new module_bootstrap_runner();
	$runner->add(new OSHeaderGui($avalanche, new Document(), new Text("")));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
};


?>