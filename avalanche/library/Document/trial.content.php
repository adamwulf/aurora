<?
	try{
		// we're dependant on these guys
		$bootstrap = $avalanche->getModule("bootstrap");
		$os = $avalanche->getModule("os");
		$accounts = $avalanche->getModule("accounts");
		// end dependencies


		/**
		* create the content for the page
		*/

		$data = new module_bootstrap_data(array_merge($_REQUEST, $_FILES), "post data and files");


		$loader = new module_bootstrap_accounts_create_gui($avalanche, new Document("temp"));
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($loader);
		$data = $runner->run($data);

		if(!($data instanceof module_bootstrap_data)){
			throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($data, true)));
		}

		$data = $data->data();
		if($data instanceof FormPanel){
			$data->setLocation("trial.php");
			$data->addHiddenField("page", "trial");
			$data->addHiddenField("testserver", "0");
		}
		if($data instanceof ErrorPanel){
			$comps = $data->getComponents();
			if(count($comps) >= 1){
				$data = $comps[0];
			}
		}

		$data = $data->execute(new HtmlElementVisitor());

		if(!is_string($data)){
			throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($data, true)));
		}else{
			echo $data;
		}
		/**
		* end creating the content
		*/

	}catch(Exception $e){
		echo "Trial signup is currently disabled and undergoing routine maintenance.";
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
