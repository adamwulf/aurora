<?
Class TestFile extends TestCase{ 

   public function test_file() {
	$file = new File("/my/location/for/the/file.html");
	$this->assertEquals("/my/location/for/the/file.html", $file->getLocation(), "the default location is \"/my/location/for/the/file.html\"");
	$file->setLocation("/location/two.css");
	$this->assertEquals("/location/two.css", $file->getLocation(), "the new location is \"/location/two.css\"");
   }
};

?>