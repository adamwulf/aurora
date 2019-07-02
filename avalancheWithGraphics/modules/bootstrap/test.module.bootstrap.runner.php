<?

Class TestBootstrap_Runner extends TestCase { 
   public function test_add1_module() {
	global $avalanche;

	$bootstrap = $avalanche->getModule("bootstrap");
   	$runner = new module_bootstrap_runner();
	$data   = new module_bootstrap_data(4, "an int");
	$mod    = new module_bootstrap_module_add1();

	$runner->add($mod);
	$data = $runner->run($data);

	$this->assertEquals($data->info(), "an int", "the info is correct after processing" );
	$this->assertEquals($data->data(), 5, "the module is returning the correct answer");
   }

   public function test_series_of_add1_modules() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
   	$runner = new module_bootstrap_runner();
	$data   = new module_bootstrap_data(4, "an int");
	$mod    = new module_bootstrap_module_add1();

	$runner->add($mod);
	$runner->add($mod);
	$runner->add($mod);
	$data = $runner->run($data);

	$this->assertEquals($data->info(), "an int", "the info is correct after processing" );
	$this->assertEquals($data->data(), 7, "the module is returning the correct answer");
   }


   public function test_bad_input() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
   	$runner = new module_bootstrap_runner();
	$data   = new module_bootstrap_data(4, "an int");
	$mod    = new module_bootstrap_module_add1();

	$runner->add($mod);


	try{
		$data = $runner->run("schwaat?");
		throw new UnitException("bootstrap runner did not catch bad input to run()");
	}catch(module_bootstrap_exception $e){

	}
   }


   public function test_base_case() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
   	$runner = new module_bootstrap_runner();
	$mod    = new module_bootstrap_module_add1();

	$runner->add($mod);


	$data = $runner->run();
	$this->assertEquals($data->data(), 1, "the module is returning the correct answer");
   }
};


?>