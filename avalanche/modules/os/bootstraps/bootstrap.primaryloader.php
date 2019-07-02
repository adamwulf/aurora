<?
/**
 * This module is in charge of loading the entire page.
 *
 * it will end up running 2 bootstraps. the first will load the os header.
 * the second will load the content. the os header bootstrap will be passed in the URL,
 * as will the content loader.
 *
 * this loader will return an html page. it will be a table with two rows, one cell each.
 * the top cell will be the os header. the bottom cell will be the content.
 */
class PrimaryLoader extends module_bootstrap_module{

	private $allowed_loaders;

	// the main body content
	private $_content;
	// the header for the os
	private $_header;
	// the login dialog box
	private $_login;

	// the avalanche to use for this loader

	function __construct($avalanche){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Primary OS Loader");
		$this->setInfo("This module expects raw form input. the 'primary_loader' variable must be set
				to the name of the next loader to load. This loader contains a list of allowed
				module bootstrap loaders, and will throw a bootstrap exception if a loader that is
				not in the list is reuqested to be loaded.

				This loader basically delegates to a few more specific loaders.");

		$this->allowed_loaders = array("strongcal" => "module_bootstrap_strongcal_main_loader",
					       "strongcal_also" => "module_bootstrap_strongcal_hideshowcal_loader",
					       "export" => "module_taskman_export_loader");
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		$system_timer = new BenchmarkTimer();
		$system_timer->start();
		$user_timer = new BenchmarkTimer();
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			// we're active!
			$this->avalanche->setActive();
			// now on to loading the page...
			$buffer = $this->avalanche->getSkin("buffer");
			$user_timer->start();
			$doc = new Document("Aurora - " . $this->avalanche->getVar("ORGANIZATION"));

			$content_timer = new BenchmarkTimer();
			$header_timer = new BenchmarkTimer();
			$extra_timer = new BenchmarkTimer();
			$visitor_timer = new BenchmarkTimer();

			/**
			 * create the content for the page
			 */
			$content_timer->start();
			$data_list = $data->data();
			$module = false;

			$loader_name = $this->getLoaderName($data_list);

			if(!class_exists($loader_name)){
				throw new ClassDefNotFoundException($loader_name);
			}

			if(in_array($loader_name, $this->allowed_loaders)){
				$module = new $loader_name($this->avalanche, $doc);
			}else{
				throw new module_bootstrap_exception("$loader_name is not a valid Primary Aurora Page Loader");
			}

			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);

			$output = $runner->run($data);

			if(!($output instanceof module_bootstrap_data) || !($output->data() instanceof Component)){
				throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($output, true)));
			}else{
				$this->_content = $output->data();
			}
			$content_timer->stop();
			/**
			 * end creating the content
			 */

			/**
			 * reset login cookie
			 */
			$this->avalanche->setCookie("login_error", "0");
			/**
			 * end resetting login cookie
			 */

			/**
			 * create the header for the page
			 */
			$header_timer->start();
			$data = false;
			$loader = new OSHeaderGui($this->avalanche, $doc);
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($loader);
			$data = $runner->run($data);
			if(!($data instanceof module_bootstrap_data) || !($data->data() instanceof Component)){
				throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($data, true)));
			}else{
				$this->_header = $data->data();
			}
			$header_timer->stop();
			/**
			 * end creating the header
			*/

			$main_panel = new GridPanel(1);
			$main_style = new Style();
			$main_style->setWidth("100%");
			$main_panel->setStyle($main_style);

			$main_panel->add($this->_header);
			$main_panel->add($this->_content);

			$doc->addStyleSheet(new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/style.os.css")));
			$doc->addStyleSheet(new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/style.menu.css")));
			$doc->addStyleSheet(new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/login.css")));
			$doc->add($main_panel);

			$system_timer->stop();
			$user_timer->stop();

			$footer = new Text("<hr><center>" .
					   "<a href='index.php?view=about&subview=tos'>Terms of Service</a> - " .
					   "<a href='index.php?view=about&subview=privacy'>Privacy Policy</a> - " .
					   "<a href='index.php?view=about'>About Aurora Calendar</a> - " .
					   "<a href='http://inversiondesigns.com/helpdesk/faq.php' target='_new'>Frequently Asked Questions</a><br>" .
//					   "Created in: " . round($system_timer->read(), 4) . " sec - " .
//					   "Server time: " . round($user_timer->read(), 4) . " - " .
//					   $this->avalanche->getQueryCount() . " database queries - " .
					   "Powered by <a href='http://www.inversiondesigns.com/'>Inversion Designs</a><br>".
					   "Copyright (c) 2004 Inversion Designs all rights reserved" .
					   "</center>");
			$footer->getStyle()->setClassName("footer_text");
			$doc->add($footer);

			$visitor_timer->start();
			$output = new module_bootstrap_data($doc->execute(new HtmlElementVisitor()), $output->info());
			$visitor_timer->stop();
			$output = $output->data();
			//$output .= "<br>";
			//$output .= "content time: " . $content_timer->read() . "<br>";
			//$output .= "header time: " . $header_timer->read() . "<br>";
			//$output .= "extra time: " . $extra_timer->read() . "<br>";
			//$output .= "visitor time: " . $visitor_timer->read() . "<br>";

			return new module_bootstrap_data($output, "asdf");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}

	/**
	 * returns the name of the primary loader to launch
	 * first checks if the user is trying to load a module, we then pick out the
	 * default loader for that module. otherwise they can explicitly set a primary loader.
	 * if no loader or module is defined, we currenlty default to the calendar.
	 */
	private function getLoaderName($data_list){

		if(isset($data_list["module"]) && $data_list["module"] == "strongcal"){
			$data_list["primary_loader"] = "module_bootstrap_strongcal_main_loader";
		}else
		if(!isset($data_list["primary_loader"])){
			/**
			* base case, they don't have 'primary_loader' defined
			*/
			$data_list["primary_loader"] = "module_bootstrap_strongcal_main_loader";
		}

		return $data_list["primary_loader"];
	}

}
?>