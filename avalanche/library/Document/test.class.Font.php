<?
Class TestFont extends TestCase{ 

   public function test_new_font() {
	$font = new Font("verdana", 12);
	$this->assertEquals("verdana", $font->getFace(), "the face of the font is verdana");
	$this->assertEquals(12, $font->getSize(), "the size of the font is 12");
	$this->assert(!$font->isBold(), "the font is not bold");
	$this->assert(!$font->isItalic(), "the font is not italic");
	$this->assert(!$font->isUnderline(), "the font is not underline");
   }
   
   public function test_bold_font(){
	$font = new Font("verdana", 12, array(Font::$BOLD));
	$this->assertEquals("verdana", $font->getFace(), "the face of the font is verdana");
	$this->assertEquals(12, $font->getSize(), "the size of the font is 12");
	$this->assert($font->isBold(), "the font is bold");
	$this->assert(!$font->isItalic(), "the font is not italic");
	$this->assert(!$font->isUnderline(), "the font is not underline");
   }

   public function test_bold_italic_font(){
	$font = new Font("verdana", 12, array(Font::$BOLD, Font::$ITALIC));
	$this->assertEquals("verdana", $font->getFace(), "the face of the font is verdana");
	$this->assertEquals(12, $font->getSize(), "the size of the font is 12");
	$this->assert($font->isBold(), "the font is bold");
	$this->assert($font->isItalic(), "the font is italic");
	$this->assert(!$font->isUnderline(), "the font is not underline");
   }
   
   public function test_underline_font(){
	$font = new Font("verdana", 12, array(FONT::$UNDERLINE));
	$this->assertEquals("verdana", $font->getFace(), "the face of the font is verdana");
	$this->assertEquals(12, $font->getSize(), "the size of the font is 12");
	$this->assert(!$font->isBold(), "the font is not bold");
	$this->assert(!$font->isItalic(), "the font is not italic");
	$this->assert($font->isUnderline(), "the font is underline");
   }
};


?>