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
class OSLoginBootstrap extends module_bootstrap_module{

	private $avalanche;
	
	function __construct($avalanche){
		$this->setName("Login Bootstrap");
		$this->setInfo("responsible for making sure the user is logged in if needed. handles form input for login.");
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		$timer = new Timer();
		$strongcal = $this->avalanche->getModule("strongcal");
		if(is_array($data->data())){
			$data_list = $data->data();
			if (isset($data_list["logout"]) && $this->avalanche->loggedInHuh()) {
				$this->avalanche->logOut();
				if(isset($data_list["page"])){
					throw new RedirectException($data_list["page"]);
				}else{
					throw new RedirectException("index.php?view=login");
				}
			}
			
			if(isset($data_list["view"]) && $data_list["view"] == "login" && isset($data_list["email"]) && !isset($data_list["success"])){
				$success = $this->avalanche->resetPasswordFor($data_list["email"]);
				throw new RedirectException("index.php?view=login&email=" . $data_list["email"] . "&success=" . $success);
			}else if(isset($data_list["user"]) && isset($data_list["pass"]) && $this->avalanche->needLogIn()){
				$potential_user = $this->avalanche->findUser($data_list["user"]);
				if(is_int($potential_user)) $potential_user = $this->avalanche->getUser($potential_user);
				$first_time = false;
				if(is_object($potential_user) && $potential_user->lastLoggedIn() == "0000-00-00 00:00:00"){
					$first_time = true;
				}
				if($this->avalanche->logIn($data_list["user"], $data_list["pass"])){
					$this->avalanche->setCookie("login_error", "0");
				}else{
					$this->avalanche->setCookie("login_error", "1");
				};

				$start_page = $this->avalanche->getUserVar("start_page");
				if($first_time){
					throw new RedirectException("index.php?view=first_login");
				}else if(($start_page == "day" ||
				   $start_page == "week" ||
				   $start_page == "month") && $this->avalanche->loggedInHuh()){
					throw new RedirectException("index.php?view=" . $start_page . "&date=" . date("Y-m-d", $strongcal->localtimestamp()));
				}else if($start_page == "overview" && $this->avalanche->loggedInHuh()){
					throw new RedirectException("index.php?view=" . $start_page);
				}else if(isset($data_list["page"]) && $this->avalanche->loggedInHuh()){
					$page = urlencode($data_list["page"]);
					throw new RedirectException("index.php?page=" . $page);
				}else if(isset($data_list["view"])){
					$user = "";
					if(isset($data_list["user"])){
						$user = "&user=" . $data_list["user"];
					}
					throw new RedirectException("index.php?view=" . $data_list["view"] . $user);
				}else{
					throw new RedirectException("index.php?view=" . $start_page);
				}
			}
			 
			return false;
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be an array of form input.<br>");
		}
	}
}
?>