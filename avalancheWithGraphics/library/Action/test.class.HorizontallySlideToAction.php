<?
Class TestHorizontallySlideToAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new HorizontallySlideToAction($t, 23, 500);
		$this->assertEquals($a->toJS(), "xSlideTo(\"$tid\", 23, xPageY(\"$tid\"), 500);\n", "the javascript is correct");
	}	

	public function test_action_argument(){
		$t = new Text("temp");
		try{
			$a = new HorizontallySlidetoAction($t, "23", 35);
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
		try{
			$a = new HorizontallySlidetoAction($t, 23, "35");
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
	}	
};


?>