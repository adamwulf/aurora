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
class AccountsPrimaryLoader extends module_bootstrap_module{

	private $allowed_loaders;

	// the main body content
	private $_content;
	// the header for the os
	private $_header;
	// the login dialog box
	private $_login;
	// the help dialog box
	private $_help;
	// the news panel
	private $_news;
	
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
				
		$this->allowed_loaders = array("create account" => "module_bootstrap_accounts_create_gui",
					       "manage account" => "module_bootstrap_accounts_manage_gui",
					       "account overview" => "module_bootstrap_accounts_overview_gui");
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		$timer = new Timer();
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			// we're active!
			$this->avalanche->setActive();
			// now on to loading the page...
			$buffer = $this->avalanche->getSkin("buffer");
			$timer->start();
			$doc = new Document("Inversion Designs OS");
			
			/**
			 * create the content for the page
			 */
			$data_list = $data->data();
			$module = false;

			$loader_name = $this->getLoaderName($data_list);
			if(!class_exists($loader_name)){
				throw new ClassDefNotFoundException($loader_name);
			}
			if(in_array($loader_name, $this->allowed_loaders)){
				$module = new $loader_name($this->avalanche, $doc);
			}else{
				throw new module_bootstrap_exception("$loader_name is not a valid Primary Accounts Page Loader");
			}
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$output = $runner->run($data);
			

			if(!($output instanceof module_bootstrap_data) || !($output->data() instanceof Component)){
				throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($output, true)));
			}else{
				$content = $output->data();
			}
			$panel = new ErrorPanel($content);
			$panel->getStyle()->setHeight("500");

			/**
			 * end creating the content
			 */
			 
			// get login screen
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_accounts_login_gui($this->avalanche, $doc));
			$output = $runner->run($data);
			$login = $output->data();
			// end getting login screen
			
			 
			$this->_content = new GridPanel(1);
			$this->_content->setWidth("100%");
			$this->_content->add($login);
			$this->_content->add($content);
			
			
			$doc->add($this->_content);
			$timer->stop();

			$output = new module_bootstrap_data($doc->execute(new HtmlElementVisitor()), $output->info());
			$output = $output->data();
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
		if(!$this->avalanche->loggedInHuh()){
			/**
			* base case, they don't have 'primary_loader' defined
			*/
			$data_list["primary_loader"] = "module_bootstrap_accounts_create_gui"; 
		}else if($this->avalanche->loggedInHuh() || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_cp")){
			if(isset($data_list["view"]) && $data_list["view"] == "account"){
				$data_list["primary_loader"] = "module_bootstrap_accounts_overview_gui"; 
			}else{
				$data_list["primary_loader"] = "module_bootstrap_accounts_manage_gui"; 
			}
		}
		
		return $data_list["primary_loader"];
	}

}
?>