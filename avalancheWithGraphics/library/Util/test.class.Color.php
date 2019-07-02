<?
Class TestColor extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new BackgroundAction($t, "blue");
		$this->assertEquals($a->toJS(), "xBackground(\"$tid\", \"blue\");\n", "the javascript is correct");
	}	
};


?>