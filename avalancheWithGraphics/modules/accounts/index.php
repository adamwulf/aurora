<?
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE |
//		E_CORE_ERROR | E_CORE_WARNING);
function error_handler($errno, $errstr, $errfile, $errline) {
	throw new Exception("$errno: \"$errstr\" in <b>$errfile</b> at <b>$errline</b>");
}


$old_handler = set_error_handler("error_handler");

try{
	include "include.avalanche.fullApp.php";
	
	try{
		// we're dependant on these guys
		$bootstrap = $avalanche->getModule("bootstrap");
		$os = $avalanche->getModule("os");
		$strongcal = $avalanche->getModule("strongcal");
		// end dependencies
		
		/**
		 * run the login bootstrap to make sure they're ready for the rest of the page...
		 */
		$data = new module_bootstrap_data(array_merge($_REQUEST, $_FILES), "post data and files");
		$loader = new OSLoginBootstrap($avalanche);
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($loader);
		$data = $runner->run($data);
	
		
		/**
		* create the content for the page
		*/
		
		$data = new module_bootstrap_data(array_merge($_REQUEST, $_FILES), "post data and files");
		
		$loader = new PrimaryLoader($avalanche);
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($loader);
		$data = $runner->run($data);
		
		if(!($data instanceof module_bootstrap_data) || !is_string($data->data())){
			throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($data, true)));
		}else{
			header("Content-type: text/html");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			echo $data->data();
		}
		/**
		* end creating the content
		*/
		
	}catch(RedirectException $e){
		header("Location: " . $e->getMessage());
		exit;
	}catch(module_bootstrap_exception $e){
		$doc = new HtmlPage();
		$doc->header("A Bootstrap Exception had occurred");
		$doc->text("File: " . $e->getFile());
		$doc->text("Message: " . $e->getMessage());
		$doc->text("Trace:<br>" . str_replace("\n", "<br>", $e->getTraceAsString()));
		send_error_mail($e);
		echo $doc;
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
		send_error_mail($e);
		echo $doc->execute(new HtmlElementVisitor());
	}catch(Exception $e){
		$doc = new HtmlPage();
		$doc->header("Unexpected Exception");
		$doc->text("File: " . $e->getFile());
		$doc->text("Message: " . $e->getMessage());
		$doc->text("Trace:<br><ul>" . str_replace("\n", "<li>", $e->getTraceAsString()) . "</ul>");
		send_error_mail($e);
		echo $doc;
	}
}catch(Exception $e){
	echo "sorry. there was a really bad error. the Support team has been emailed.";
	send_error_mail($e);
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
