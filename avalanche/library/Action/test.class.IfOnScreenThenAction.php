<?
Class TestIfOnScreenThenAction extends TestCase{ 

	public function test_action_javascript(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new IfOnScreenThenAction($t, new HideAction($t));
		$this->assertEquals($a->toJS(), "if(xPageX(\"$tid\")>0 && xPageX(\"$tid\")<xClientWidth() && xPageY(\"$tid\")>0 && xPageY(\"$tid\")<xClientHeight()) { xHide(\"$tid\");\n }", "the javascript is correct");
	}	

	public function test_add_action(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new IfOnScreenThenAction($t, new HideAction($t));
		$a->addAction(new ShowAction($t));
		$this->assertEquals($a->toJS(), "if(xPageX(\"$tid\")>0 && xPageX(\"$tid\")<xClientWidth() && xPageY(\"$tid\")>0 && xPageY(\"$tid\")<xClientHeight()) { xHide(\"$tid\");\nxShow(\"$tid\");\n }", "the javascript is correct");
	}

	public function test_remove_action(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new IfOnScreenThenAction($t, new HideAction($t));
		$act = new ShowAction($t);
		$a->addAction($act);
		$a->removeAction($act);
		
		$this->assertEquals($a->toJS(), "if(xPageX(\"$tid\")>0 && xPageX(\"$tid\")<xClientWidth() && xPageY(\"$tid\")>0 && xPageY(\"$tid\")<xClientHeight()) { xHide(\"$tid\");\n }", "the javascript is correct");
	}

	public function test_get_actions(){
		$t = new Text("temp");
		$tid = $t->getId();
		$a = new IfOnScreenThenAction($t, new HideAction($t));
		$a->addAction(new ShowAction($t));
		$this->assertEquals(count($a->getActions()), 2, "the correct number of actions are in the IfOnScreenThenAction");
	}
};


?>