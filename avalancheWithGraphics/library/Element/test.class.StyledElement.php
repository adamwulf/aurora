<?
Class TestStyledElement extends TestCase{ 

   public function test_add_style(){
	   $document = new Document();
	   $css1 = new Style("classname");
	   $css2 = new Style();
	   
	   $this->assertEquals(false, $document->getStyle(), "the document has no style");

	   $document->setStyle($css1);
	   $this->assertEquals($css1, $document->getStyle(), "the document has the correct style");

	   $document->setStyle($css2);
	   $this->assertEquals($css2, $document->getStyle(), "the document has the correct style");
   }
   
   public function test_remove_stylesheets(){
	   $document = new Document();
	   $css1 = new Style("classname");
	   $css2 = new Style();
	   
	   $document->setStyle($css1);
	   $document->removeStyle();
	   $this->assertEquals(false, $document->getStyle(), "the document has the correct style");
   }
};

?>