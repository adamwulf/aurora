<?
Class TestHideAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new HideAction($t);
		$this->assertEquals($a->toJS(), "xHide(\"$tid\");\n", "the javascript is correct");
	}	
};


?>