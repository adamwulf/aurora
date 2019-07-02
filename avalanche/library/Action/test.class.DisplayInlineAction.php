<?
Class TestDisplayInlineAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new DisplayInlineAction($t);
		$this->assertEquals($a->toJS(), "xDisplayInline(\"$tid\");\n", "the javascript is correct");
	}	
};

?>