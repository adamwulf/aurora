<?
Class TestShowAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new ShowAction($t);
		$this->assertEquals($a->toJS(), "xShow(\"$tid\");\n", "the javascript is correct");
	}	
};


?>