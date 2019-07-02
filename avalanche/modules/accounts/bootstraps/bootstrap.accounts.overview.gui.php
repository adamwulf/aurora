<?

class module_bootstrap_accounts_overview_gui extends module_bootstrap_module{

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
			$accounts = $this->avalanche->getModule("accounts");
			
			if(!isset($data_list["account_name"])){
				throw new IllegalArgumentException("parameter \"account_name\" must be sent in via form input to view account overview");
			}
			$account_name = $data_list["account_name"];
			$account = $accounts->getAccount($account_name);
			
			if($this->avalanche->loggedInHuh() || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_cp")){
				$width = "500";
				
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
				
				$main_panel = new GridPanel(1);
				$main_panel->getStyle()->setBorderStyle("solid");
				$main_panel->getStyle()->setBorderWidth(1);
				$main_panel->getStyle()->setBorderColor("black");
				
				$title_panel = new Panel();
				$title_panel->setStyle($title_bar_style);
				$disabled = $account->disabled() ? "(D) " : "";
				$title_panel->add(new Text($disabled . "<b>" . $account->name() . "</b> Account Overview - "));
				$title_panel->add(new Link("Back", "index.php"));
				
				$account_panel = new GridPanel(2);
				$account_panel->setWidth($width);
				$account_panel->setCellStyle($panel_style);
				
				$account_panel->add(new Text("URL:"));
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
				$account_panel->add(new Text("Age:"));
				$account_panel->add(new Text($time_old));
				
				$foreign_avalanche = $account->getAvalanche();
				
				$users = $foreign_avalanche->getAllUsers();
				$account_panel->add(new Text("Users:"));
				$account_panel->add(new Text((string)count($users)));
				
				$name = $foreign_avalanche->getUsername($foreign_avalanche->loggedInHuh());
				$account_panel->add(new Text("Logged in as:"));
				$account_panel->add(new Text($name));
				
				$button = new Button("delete");
				$button->addAction(new LoadPageAction("index.php?delete=1&name=" . $account->name()));
				$button->getStyle()->setFontSize(8);
				$account_panel->add($button);
				
				if($account->disabled()){
					$button = new Button("enable");
					$button->addAction(new LoadPageAction("index.php?disable=0&name=" . $account->name()));
				}else{
					$button = new Button("disable");
					$button->addAction(new LoadPageAction("index.php?disable=1&name=" . $account->name()));
				}
				$button->getStyle()->setFontSize(8);
				$account_panel->add($button);
				
				$main_panel->add($title_panel);
				$main_panel->add($account_panel);
				
				$panel = new ErrorPanel($main_panel);
				$panel->getStyle()->setHeight("600");
	
				return new module_bootstrap_data($panel, "a gui component for the day view");
			}else{
			}
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>