<?

class TestOS_bootstrap_primaryloader extends Abstract_Avalanche_TestCase { 

   public function test_bad_loader(){
	global $avalanche;
	
	try{
		$data = array("aurora_loader" => "asdfaf");
		new module_bootstrap_data($data, "fake form input that gives a bad module name");

		$runner = new module_bootstrap_runner();
		$runner->add(new PrimaryLoader($avalanche));
		$runner->run($data);


		$this->fail("loader should have thrown bootstrap exception");
	}catch(module_bootstrap_exception $e){
		$this->pass("everything is fine");		
	}
    }


   public function test_good_loader(){
	global $avalanche;
					  
	$data = array("primary_loader" => "module_bootstrap_strongcal_main_loader",
		      "aurora_loader" => "module_bootstrap_strongcal_default_view");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");

	$runner = new module_bootstrap_runner();
	$runner->add(new PrimaryLoader($avalanche));
	$output = $runner->run($data);

	$this->assert($output instanceof module_bootstrap_data, "the output a bootstrap data object");
	$this->assert(is_string($output->data()), "the data is a string (hopefully html)");
	$this->assert(is_numeric(strpos($output->data(), "id='GridPanel_")), "the data has \"id='GridPanel_\" in it");
    }
};


?>