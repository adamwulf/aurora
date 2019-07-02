<?
Class TestMoveToAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new MoveToAction($t, 23, 35);
		$this->assertEquals($a->toJS(), "xMoveTo(\"$tid\", 23, 35);\n", "the javascript is correct");
	}	

	public function test_action_argument(){
		$t = new Text("temp");
		$tid = $t->getId();
		try{
			$a = new MoveToAction($t, "23", 35);
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
		try{
			$a = new MoveToAction($t, 23, "35");
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
	}	
};


?>