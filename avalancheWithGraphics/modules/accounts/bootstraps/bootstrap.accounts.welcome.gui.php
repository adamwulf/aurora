<?

class module_bootstrap_accounts_welcome_gui extends module_bootstrap_module{

	private $time_inc;
	private $column_width;
	private $avalanche;
	private $doc;
	private $account_name;

	function __construct($avalanche, Document $doc, $name){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		if(!is_string($name)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$this->setName("Account Welcome Screen");
		$this->setInfo("returns the day view of this calendar");
		$this->time_inc = 30;
		$this->column_width = "120px";

		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->account_name = $name;
	}

	function run($data = false){
		if($data !== false){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be false.<br>");
		}else{
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");

			$name_error = false;
			$title_error = false;
			$email_error = false;

			$accounts = $this->avalanche->getModule("accounts");
			$acct = $accounts->getAccount($this->account_name);

			$width = "320";
			// styles
			$panel_style = new Style("welcomeBanner");

			// panels

			$description = new SimplePanel();
			$description->setStyle($panel_style);
			$description->add(new Text("I have successfully created your free account. Remember, your password has been emailed to you!<br><br>"));
			$description->add(new Link("Continue", "http://" . $acct->domain() . "/" . $this->account_name . "/?view=login"));


			return new module_bootstrap_data($description, "a gui component for the day view");
		}
	}
}
?>