<?

Class TestBootstrap_Module extends TestCase { 
   public function test_info_ok() {
	global $avalanche;

	$bootstrap = $avalanche->getModule("bootstrap");
   	$mod = new module_bootstrap_module_testcase();
	$this->assertEquals($mod->info(), "This is the abstract case for the Bootstrap! module.", "the Bootstrap! module defaults to correct information" );
   }

   public function test_name_ok() {
	global $avalanche;
	$bootstrap = $avalanche->getModule("bootstrap");
   	$mod = new module_bootstrap_module_testcase();
	$this->assertEquals($mod->name(), "Default Bootstrap! Module", "the Bootstrap! module defaults to correct name" );
   }
};

?>