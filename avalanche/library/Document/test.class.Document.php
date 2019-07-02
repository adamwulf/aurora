<?
Class TestDocument extends TestCase{ 

   public function test_title() {
	$document = new Document();
	$this->assertEquals("No Title", $document->getTitle(), "the default title is \"No Title\"");

	$document->setTitle("ASDF");

	$this->assertEquals("ASDF", $document->getTitle(), "the new title is \"ASDF\"");
   }
   
   public function test_add_element(){
	   $document = new Document();
	   $p1 = new Paragraph("asdf");
	   $p2 = new Paragraph("asdf2");
	   
	   $this->assertEquals(0, count($document->getElements()), "the document has no elements");
	   $document->add($p1);
	   $this->assertEquals(1, count($document->getElements()), "the document has one element");
	   $document->add($p2);
	   $this->assertEquals(2, count($document->getElements()), "the document has two elements");
   }

   public function test_remove_elements(){
	   $document = new Document();
	   $p1 = new Paragraph("asdf");
	   $p2 = new Paragraph("asdf2");
	   
	   $document->add($p1);
	   $document->add($p2);
	   $this->assertEquals(2, count($document->getElements()), "the document has two elements");

	   $this->assert($document->remove($p1), "the document has removed the paragraph");
	   $this->assertEquals(1, count($document->getElements()), "the document has one element");

	   $es = $document->getElements();
	   $this->assertEquals($p2, $es[0], "paragraph 2 is left in the document");
   }
   
   public function test_add_stylesheet(){
	   $document = new Document();
	   $css1 = new CSS(new File("temp/temp.css"));
	   $css2 = new CSS(new File("other/file.css"));
	   
	   $document->addStyleSheet($css1);
	   $this->assertEquals(1, count($document->getStyleSheets()), "the document has one style sheet");

	   $document->addStyleSheet($css2);
	   $this->assertEquals(2, count($document->getStyleSheets()), "the document has two style sheets");
   }
   
   public function test_remove_stylesheets(){
	   $document = new Document();
	   $css1 = new CSS(new File("temp/temp.css"));
	   $css2 = new CSS(new File("other/file.css"));
	   
	   $document->addStyleSheet($css1);
	   $document->addStyleSheet($css2);
	   $this->assertEquals(2, count($document->getStyleSheets()), "the document has two style sheets");

	   $this->assert($document->removeStyleSheet($css1), "the style sheet has been removed");
	   $this->assertEquals(1, count($document->getStyleSheets()), "the document has one style sheet");
	   
	   $lc = $document->getStyleSheets();
	   $this->assertEquals($css2, $lc[0], "the second style sheet is still in the document");
   }

   public function test_add_action(){
	   $document = new Document();
	   $action1 = new MoveToAction(new Text("temp"), 0, 0);
	   $action2 = new MoveToAction(new Text("temp"), 0, 0);
	   
	   $document->addAction($action1);
	   $this->assertEquals(1, count($document->getActions()), "the document has one action");

	   $document->addAction($action2);
	   $this->assertEquals(2, count($document->getActions()), "the document has two actions");
   }
   
   public function test_remove_action(){
	   $document = new Document();
	   $action1 = new MoveToAction(new Text("temp"), 0, 0);
	   $action2 = new MoveToAction(new Text("temp"), 0, 0);
	   
	   $document->addAction($action1);
	   $document->addAction($action2);
	   $this->assertEquals(2, count($document->getActions()), "the document has two actions");

	   $this->assert($document->removeAction($action1), "the action has been removed");
	   $this->assertEquals(1, count($document->getActions()), "the document has one action");
	   
	   $lc = $document->getActions();
	   $this->assertEquals($action2, $lc[0], "the second action is still in the document");
   }
};


?>