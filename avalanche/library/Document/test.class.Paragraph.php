<?
Class TestParagraph extends TestCase{ 

   public function test_paragraph() {
	$p = new Paragraph();
	$this->assertEquals("", $p->getText(), "the default text is \"\"");
	$p->setText("my new text");
	$this->assertEquals("my new text", $p->getText(), "the new text is \"my new text\"");
   }

   public function test_initialize_paragraph() {
	$p = new Paragraph("initial paragraph");
	$this->assertEquals("initial paragraph", $p->getText(), "the initial text is \"initial paragraph\"");
   }
};


?>