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
class MembersAreaPrimaryLoader extends module_bootstrap_module{

	function __construct($avalanche){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Primary Customer Account Management Loader");
		$this->setInfo("");
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			// now on to loading the page...
			$account = false;
			$error = false;
			try{
				$module = new GetAccount($this->avalanche);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$output = $runner->run($data);
				$account = $output->data();
			}catch(IllegalArgumentException $e){
				$error = $e;
			}
			
			if(!is_object($account) || is_object($account) && (!$account->getAvalanche()->loggedInHuh() || !$account->getAvalanche()->hasPermissionHuh($account->getAvalanche()->getActiveUser(), "view_cp"))){
				$module = new LoginScreen($this->avalanche, $account, $error);
				$data = new module_bootstrap_data($data_list);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$output = $runner->run($data);
				$output = $output->data();
			}else{
				$module = new AccountMain($this->avalanche, $account);
				$data = new module_bootstrap_data($data_list);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$output = $runner->run($data);
				$output = $output->data();
			}
	
			return new module_bootstrap_data($output, "asdf");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}
}

?>