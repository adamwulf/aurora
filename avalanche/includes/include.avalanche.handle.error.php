<?

send_error_mail($e);
try{
	$doc = new Document();
	$stuff = new GridPanel(1);
	$stuff->getCellStyle()->setPadding(3);
	$stuff->getStyle()->setBorderWidth(1);
	$stuff->getStyle()->setBorderColor("black");
	$stuff->getStyle()->setBorderStyle("solid");
	$stuff->setWidth(300);
	$header = new Text("Whoops! There was an error!");
	$header->getStyle()->setFontFamily("verdana, sans-serif");
	$header->getStyle()->setFontSize(11);
	$stuff->add($header);
	$text = new Text("You tried to do something on the webpage, but the <tt>" . get_class($e) . "</tt> said, \"" . $e->getMessage() . ".\"<br><br>");
	$text->getStyle()->setFontFamily("verdana, sans-serif");
	$text->getStyle()->setFontSize(9);
	$stuff->add($text);
	$text = new Text("The Support team has been notified, and the problem should be fixed within the day.<br><br>");
	$text->getStyle()->setFontFamily("verdana, sans-serif");
	$text->getStyle()->setFontSize(9);
	$stuff->add($text);
	$text = new Text("Please hit the [Back] button on your browser and continue working.<br><br>");
	$text->getStyle()->setFontFamily("verdana, sans-serif");
	$text->getStyle()->setFontSize(9);
	$stuff->add($text);
	
	$error = new ErrorPanel($stuff);
	$stuff->getStyle()->setBackground("white");
	$error->getStyle()->setBackground("silver");
	$doc->add($error);
	echo $doc->execute(new HtmlElementVisitor());
}catch(Exception $e){
	$doc = new HtmlPage();
	$doc->header("Unexpected Exception");
	$doc->text("You tried to do something on the webpage, but the <tt>" . get_class($e) . "</tt> said, \"" . $e->getMessage() . ".\"<br><br>");
	$doc->text("The Support team has been notified, and the problem should be fixed within the day.<br><br>");
	echo $doc;
}
	

function send_error_mail($e){
	global $avalanche;
	if(is_object($avalanche)){
		$body = "Avalanche Account: " . $avalanche->ACCOUNT() . "\n<br>";
	}else{
		$body = "Could not load Avalanche!";
	}
	$body .= $e->getFile() . "<br>\n<br>\n";
	$body .= $e->getMessage() . "<br>\n<br>\n";
	$body .= str_replace("\n", "<br>\n", $e->getTraceAsString());
	mail("awulf@inversiondesigns.com", "Bootstrap Exception", $body); 
}

?>
