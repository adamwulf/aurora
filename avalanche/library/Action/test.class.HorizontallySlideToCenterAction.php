<?
Class TestHorizontallySlideCenterToAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new HorizontallySlideToCenterAction($t, 500);
		$this->assertEquals($a->toJS(), "xSlideTo(\"$tid\", xScrollLeft() + ((xClientWidth() - xWidth(\"$tid\")) / 2), xPageY(\"$tid\"), 500);\n", "the javascript is correct");
	}	

	public function test_action_argument(){
		$t = new Text("temp");
		try{
			$a = new HorizontallySlideToCenterAction($t, "23");
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
	}	
};


?>