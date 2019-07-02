<?
Class TestDisplayBlockAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new DisplayBlockAction($t);
		$this->assertEquals($a->toJS(), "xDisplayBlock(\"$tid\");\n", "the javascript is correct");
	}	
};


?>