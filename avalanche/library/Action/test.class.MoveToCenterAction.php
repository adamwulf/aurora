<?
Class TestMoveToCenterAction extends TestCase{

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new MoveToCenterAction($t);
		$this->assertEquals($a->toJS(), "xMoveTo(\"$tid\", xScrollLeft() + (xClientWidth() / 2) - xWidth(\"$tid\") / 2, xScrollTop() + 200);\n", "the javascript is correct");
	}
};


?>