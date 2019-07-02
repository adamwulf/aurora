<?
Class TestStyle extends TestCase{ 

   public function test_new_style() {
	$style = new Style();
	$this->assertEquals(false, $style->getClassname(), "the classname is \"\"");
	$style->setClassname("classname");
	$this->assertEquals("classname", $style->getClassname(), "the classname is \"classname\"");
   }
   
   public function test_set_illegal_classname(){
	   try{
		   $style = new Style(2);
		   $this->fail("did not throw IllegalArugmentException");
	   }catch(IllegalArgumentException $e){
		   // good, we wanted to catch this
	   }
	   try{
		   $style = new Style();
		   $style->setClassname(new Paragraph("asfd"));
		   $this->fail("did not throw IllegalArugmentException");
	   }catch(IllegalArgumentException $e){
		   // good, we wanted to catch this
	   }
   }
   
   public function test_set_attributes(){
	   $style = new Style();
	   
	   $style->setWidth("100%");
	   $style->setHeight(25);
	   $style->setTextAlign("left");
	   $style->setVerticalAlign("top");
	   
	   $this->assertEquals("100%", $style->getWidth(), "the width is 100%");
	   $this->assertEquals(25, $style->getHeight(), "the height is 25");
	   $this->assertEquals("left", $style->getTextAlign(), "the text align is left");
	   $this->assertEquals("top", $style->getVerticalAlign(), "the vertical alignment is top");
   }
};


?>