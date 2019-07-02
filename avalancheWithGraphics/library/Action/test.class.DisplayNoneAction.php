<?
Class TestDisplayNoneAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new DisplayNoneAction($t);
		$this->assertEquals($a->toJS(), "xDisplayNone(\"$tid\");\n", "the javascript is correct");
	}	
};


?>