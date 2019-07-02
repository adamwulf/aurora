<?
Class TestWidthAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new WidthAction($t, 23);
		$this->assertEquals($a->toJS(), "xWidth(\"$tid\", 23);\n", "the javascript is correct");
	}	

	public function test_action_argument(){
		$t = new Text("temp");
		$tid = $t->getId();
		try{
			$a = new WidthAction($t, "23");
			$this->fail("should have thrown an IllegalArgumentException");
		}catch(IllegalArgumentException $e){
		}
	}	
};


?>