<?

Class TestBootstrap_Data extends TestCase { 
   public function test_info_ok() {
	global $avalanche;

	$bootstrap = $avalanche->getModule("bootstrap");
   	$data = new module_bootstrap_data(array(1,2,3), "a small array");
	$this->assertEquals($data->info(), "a small array", "the data object returns the correct information about the data" );
   }

   public function test_data_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
   	$data = new module_bootstrap_data(array(1,2,3), "a small array");
	$this->assertEquals($data->data(), array(1,2,3), "the data object returns the correct data" );
   }
};

?>