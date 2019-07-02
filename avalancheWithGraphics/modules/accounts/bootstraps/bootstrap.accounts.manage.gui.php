<?

class module_bootstrap_accounts_manage_gui extends module_bootstrap_module{

	private $time_inc;
	private $column_width;
	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("Aurora Day View");
		$this->setInfo("returns the day view of this calendar");
		$this->time_inc = 30;
		$this->column_width = "120px";

		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");

			if($this->avalanche->loggedInHuh() || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_cp")){
				$width = "700";

				// styles
				$title_bar_style = new Style();
				$title_bar_style->setWidth($width . "px");
				$title_bar_style->setPadding(5);
				$title_bar_style->setFontFamily("verdana, sans-serif");
				$title_bar_style->setFontSize(10);
				$title_bar_style->setFontColor("black");
				$title_bar_style->setBackground("#ADB8D0");

				$panel_style = new Style();
				$panel_style->setPadding(5);
				$panel_style->setFontFamily("verdana, sans-serif");
				$panel_style->setFontSize(10);
				$panel_style->setFontColor("black");
				$panel_style->setBackground("#CDD8F0");

				$description_style = new Style();
				$description_style->setWidth($width . "px");
				$description_style->setPaddingLeft(3);
				$description_style->setPaddingRight(3);
				$description_style->setPaddingBottom(5);
				$description_style->setPaddingTop(2);
				$description_style->setFontFamily("verdana, sans-serif");
				$description_style->setFontSize(8);
				$description_style->setFontColor("black");
				$description_style->setBackground("#CDD8F0");

				$error_style = new Style();
				$error_style->setWidth($width . "px");
				$error_style->setPaddingLeft(3);
				$error_style->setPaddingRight(3);
				$error_style->setPaddingBottom(5);
				$error_style->setPaddingTop(2);
				$error_style->setFontFamily("verdana, sans-serif");
				$error_style->setFontSize(8);
				$error_style->setFontColor("maroon");
				$error_style->setBackground("#CDD8F0");

				$input_style = new Style();
				$input_style->setBorderWidth(1);
				$input_style->setBorderStyle("solid");
				$input_style->setBorderColor("black");



				if(!isset($data_list["delete"]) && !isset($data_list["add"]) && !isset($data_list["disable"])){
					// just show all domains
					$main_panel = new GridPanel(1);
					$main_panel->getStyle()->setBorderStyle("solid");
					$main_panel->getStyle()->setBorderWidth(1);
					$main_panel->getStyle()->setBorderColor("black");

					$title_panel = new Panel();
					$title_panel->setStyle($title_bar_style);
					$title_panel->add(new Text("Manage Accounts - "));
					$title_panel->add(new Link("Add", "index.php?add=1"));

					$account_panel = new GridPanel(7);
					$account_panel->setWidth($width);
					$account_panel->setCellStyle($panel_style);

					$accounts = $this->avalanche->getModule("accounts");
					$list = $accounts->getAccounts();
					$sorter = new MDASorter();
					$comp   = new AccountsAccountComparator();
					$list = $sorter->sort($list, $comp);

					foreach($list as $account){
						if($account->disabled()){
							$account_panel->add(new Text("D"));
						}else{
							$account_panel->add(new Text(""));
						}
						$url = $account->name() . "." . $account->domain();
						$account_panel->add(new Link($url, "http://" . $url));
						$dt_added = $account->addedOn();
						$stamp_added = mktime(substr($dt_added, 11, 2), substr($dt_added, 14, 2), substr($dt_added, 17, 2), substr($dt_added, 5, 2), substr($dt_added, 8, 2), substr($dt_added, 0, 4));
						$stamp_now = $strongcal->gmttimestamp();
						$time_spent = $stamp_now - $stamp_added;
						$days_old = (int) ($time_spent / (24 * 60 * 60));
						if($days_old < 1){
							$hours_old = (int) ($time_spent / (60 * 60));
							if($hours_old < 1){
								$min_old = (int) ($time_spent / 60);
								$time_old = $min_old . " min";
							}else{
								$time_old = $hours_old . " hours";
							}
						}else{
							$time_old = $days_old . " days";
						}
						$account_panel->add(new Text($time_old));
						$expire = new MMDateTime($account->expiresOn());
						$expire = date("M jS, `y H:i", $expire->getTimestamp());
						$account_panel->add(new Text($expire));
						$account_panel->add(new Link($account->email(), "mailto:" . $account->email()));
						$account_name = $account->name();
						$link = "index.php?view=account&account_name=" . $account_name;
						$account_panel->add(new Link("more info", $link));
						$button = new Button("delete");
						$button->addAction(new LoadPageAction("index.php?delete=1&name=" . $account->name()));
						$button->getStyle()->setFontSize(8);
						$account_panel->add($button);
					}

					$main_panel->add($title_panel);
					$main_panel->add($account_panel);

					$panel = new ErrorPanel($main_panel);
					$panel->getStyle()->setHeight("600");
				}else if(isset($data_list["add"])){
					$bootstrap = $this->avalanche->getModule("bootstrap");
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_accounts_create_gui($this->avalanche, $this->doc));
					$output = $runner->run($data);
					$panel = $output->data();
				}else if(isset($data_list["delete"]) && !isset($data_list["confirm"])){
					if(!isset($data_list["name"])){
						throw new IllegalArgumentException("form argument \$name must be sent in to delete account page");
					}
					// ask to make sure
					$main_panel = new GridPanel(1);
					$main_panel->getStyle()->setBorderStyle("solid");
					$main_panel->getStyle()->setBorderWidth(1);
					$main_panel->getStyle()->setBorderColor("black");

					$title_panel = new Panel();
					$title_panel->setStyle($title_bar_style);
					$title_panel->add(new Text("Delete Account \"" . $data_list["name"] . "\"?"));

					$account_panel = new GridPanel(2);
					$account_panel->setWidth($width);
					$account_panel->setAlign("center");
					$account_panel->setCellStyle($panel_style);
					$back = new Button("back");
					$back->getStyle()->setFontSize(8);
					$back->addAction(new LoadPageAction("index.php"));
					$delete = new Button("delete");
					$delete->getStyle()->setFontSize(8);
					$delete->addAction(new LoadPageAction("index.php?delete=1&confirm=1&name=" . $data_list["name"]));
					$account_panel->add($back);
					$account_panel->add($delete);


					$main_panel->add($title_panel);
					$main_panel->add($account_panel);

					$panel = new ErrorPanel($main_panel);
					$panel->getStyle()->setHeight("600");
				}else if(isset($data_list["delete"]) && isset($data_list["confirm"])){
					if(!isset($data_list["name"])){
						throw new IllegalArgumentException("form argument \$name must be sent in to delete account page");
					}
					$accounts = $this->avalanche->getModule("accounts");
					$accounts->deleteAccount($data_list["name"]);

					header("Location: " . $this->avalanche->HOSTURL() . "accounts/index.php");
					exit;
					// we're sure now, so delete
				}else if(isset($data_list["disable"])){
					$accounts = $this->avalanche->getModule("accounts");
					$account = $accounts->getAccount($data_list["name"]);
					$account->disable((bool)$data_list["disable"]);


					header("Location: " . $this->avalanche->HOSTURL() . "accounts/index.php?view=account&account_name=" . $data_list["name"]);
					exit;
				}

				return new module_bootstrap_data($panel, "a gui component for the day view");
			}else{
			}
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>