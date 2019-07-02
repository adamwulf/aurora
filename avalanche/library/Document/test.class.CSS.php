<?
Class TestCSS extends TestCase{ 

   public function test_css() {
	$file1 = new File("/my/location/for/the/file.html");
	$css = new CSS($file1);
	$this->assertEquals($file1, $css->getLocation(), "the default location is \"File: /my/location/for/the/file.html\"");
	$file2 = new File("/location/two.css");
	$css->setLocation($file2);
	$this->assertEquals($file2, $css->getLocation(), "the new location is \"File: /location/two.css\"");
   }
};


?>