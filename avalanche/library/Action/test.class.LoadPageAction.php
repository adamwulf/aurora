<?
Class TestLoadPageAction extends TestCase{ 

	public function test_action_javascript_no_frame(){
		$url = "mypage.php";
		$frame = "_self";
		$a = new LoadPageAction($url);
		$this->assertEquals($a->toJS(), "window.open(\"$url\",\"$frame\");\n", "the javascript is correct");
	}	

	public function test_action_javascript_with_frame(){
		$url = "mypage.php";
		$frame = "myframe";
		$a = new LoadPageAction($url, $frame);
		$this->assertEquals($a->toJS(), "window.open(\"$url\",\"$frame\");\n", "the javascript is correct");
	}	
};


?>