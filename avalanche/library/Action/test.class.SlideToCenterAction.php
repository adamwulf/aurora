<?
Class TestSlideToCenterAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new SlideToCenterAction($t, 500);
		$this->assertEquals($a->toJS(), "xSlideTo(\"$tid\", xScrollLeft() + (xClientWidth() / 2), xScrollTop() + (xClientHeight() / 2), 500);\n", "the javascript is correct");
	}	
};


?>