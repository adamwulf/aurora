<?
Class TestHorizontallyMoveToCenterAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new HorizontallyMoveToCenterAction($t);
		$this->assertEquals($a->toJS(), "xMoveTo(\"$tid\", xScrollLeft() + ((xClientWidth() - xWidth(\"$tid\")) / 2), xPageY(\"$tid\"));\n", "the javascript is correct");
	}	
};


?>