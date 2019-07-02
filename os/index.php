<?
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE |
//		E_CORE_ERROR | E_CORE_WARNING);
function error_handler($errno, $errstr, $errfile, $errline) {
	throw new Exception("$errno: \"$errstr\" in <b>$errfile</b> at <b>$errline</b>");
}
$old_handler = set_error_handler("error_handler", E_ALL);

try{
	include "../include.avalanche.fullApp.php";
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
			$output = $data->data();
			$bufferSize = 8192;
			   $splitString = str_split($output, $bufferSize);
			   foreach($splitString as $chunk){
			       echo $chunk;
			       flush();
			   }
		}
		/**
		* end creating the content
		*/
		
	}catch(RedirectException $e){
		header("Location: " . $e->getMessage());
		exit;
	}catch(module_bootstrap_exception $e){
		include "../" . APPPATH . INCLUDEPATH . "include.avalanche.handle.error.php";
	}
}catch(Exception $e){
	include "../" . APPPATH . INCLUDEPATH . "include.avalanche.handle.error.php";
}

?>
