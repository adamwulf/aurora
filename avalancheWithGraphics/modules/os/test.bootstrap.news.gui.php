<?

class test_bootstrap_news_screen extends Abstract_Avalanche_TestCase { 

   public function test_news_dialog(){
	global $avalanche;
	
	$data = array("view" => "news");
	$data = new module_bootstrap_data($data, "fake form input that gives a bad module name");
	$runner = new module_bootstrap_runner();
	$runner->add(new OSNewsGui($avalanche, new Document()));
	$runner->run($data);
	$this->pass("everything is fine");		
    }
};


?>