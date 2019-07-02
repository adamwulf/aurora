<?
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE |
//		E_CORE_ERROR | E_CORE_WARNING);
function error_handler($errno, $errstr, $errfile, $errline) {
	throw new Exception("$errno: \"$errstr\" in <b>$errfile</b> at <b>$errline</b>");
}
$old_handler = set_error_handler("error_handler", E_ALL);

try{
	include "../include.avalanche.fullApp.php";
		// we're dependant on these guys
		$bootstrap = $avalanche->getModule("bootstrap");
		$os = $avalanche->getModule("os");
		$strongcal = $avalanche->getModule("strongcal");
		// end dependencies
		
		$doc = new Document("Aurora - " . $avalanche->getVar("ORGANIZATION"));
		
		
		$data = new module_bootstrap_data(array_merge($_REQUEST, $_FILES), "post data and files");
		$module = new module_bootstrap_os_manageusers_gui2($avalanche, $doc);
		$bootstrap = $avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$output = $runner->run($data);
		$output = $output->data();

		$doc->add($output);
		
		$output = $doc->execute(new HtmlElementVisitor());
		header("Content-type: text/html");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$bufferSize = 8192;
		$splitString = str_split($output, $bufferSize);
		foreach($splitString as $chunk){
		echo $chunk;
		flush();
}
}catch(Exception $e){
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
		$text = new Text("Please hit the [Back] button on your browser and correct the problem.<br><br>");
		$text->getStyle()->setFontFamily("verdana, sans-serif");
		$text->getStyle()->setFontSize(9);
		$stuff->add($text);
		$link = new Link("Click for (alot) more info", "javascript:;");
		$link->getStyle()->setFontFamily("verdana, sans-serif");
		$link->getStyle()->setFontSize(9);
		$link->getStyle()->setFontColor("gray");
		$stuff->add($link);

		$trace  = "File: " . $e->getFile() . "<br>";
		$trace .= "Message: " . $e->getMessage() . "<br>";
		$trace .= "Trace:<br><ul>" . str_replace("\n", "<li>", $e->getTraceAsString()) . "</ul>";
		$trace = new Text($trace);
		$trace->getStyle()->setFontFamily("verdana, sans-serif");
		$trace->getStyle()->setFontSize(9);
		$trace->getStyle()->setDisplayNone();
		$stuff->add($trace);

		$link->addAction(new DisplayNoneAction($link));
		$link->addAction(new DisplayInlineAction($trace));

		$error = new ErrorPanel($stuff);
		$stuff->getStyle()->setBackground("white");
		$error->getStyle()->setBackground("silver");
		$doc->add($error);
		echo $doc->execute(new HtmlElementVisitor());
	}catch(Exception $e){
		$doc = new HtmlPage();
		$doc->header("Unexpected Exception");
		$doc->text("File: " . $e->getFile());
		$doc->text("Message: " . $e->getMessage());
		$doc->text("Trace:<br><ul>" . str_replace("\n", "<li>", $e->getTraceAsString()) . "</ul>");
		echo $doc;
	}
}
?>
