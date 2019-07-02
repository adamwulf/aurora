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
class GetAccount extends module_bootstrap_module{

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
			$account = false;
			$data_list = $data->data();
			if(isset($data_list["account"])){
				$accountmod = $this->avalanche->getModule("accounts");
				try{
					$account = $accountmod->getAccount($data_list["account"]);
					$avalanche = $account->getAvalanche();
					if(isset($data_list["submit"]) && isset($data_list["logout"])){
						$avalanche->logOut();
					}else if(isset($data_list["submit"]) && isset($data_list["login"])){
						$avalanche->logIn($data_list["username"], $data_list["password"]);
					}
				}catch(AccountNotFoundException $e){
					
				}
			}
		}
		return new module_bootstrap_data($account, "asdf");
	}
}
?>