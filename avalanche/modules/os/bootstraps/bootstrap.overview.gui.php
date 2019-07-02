<?

class module_bootstrap_os_overview_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche Overview to HTML");
		$this->setInfo("outputs an overview of the system.");
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
			$reminders = $this->avalanche->getModule("reminder");
			$os = $this->avalanche->getModule("os");

			$strongcal->setUserVar("highlight", "overview");

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/overview_style.css"));
			$this->doc->addStyleSheet($css);


			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_user";
			}
			/** end initializing the input */
			$content = new GridPanel(1);
			$content->setValign("top");
			$content->setWidth("95%");
			$content->getCellStyle()->setPaddingBottom(20);
			$content->getCellStyle()->setPaddingRight(50);

			$overview_title = new Text("<b>Overview</b>");
			$overview_title->getStyle()->setFontFamily("verdana, sans-serif");
			$overview_title->getStyle()->setFontSize(12);

			// events this week
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_events_this_week_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$this_week = $data->data();
				$content->add($this_week);
			}

			// tasks that are due this week
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_tasks_this_week_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$due_tasks = $data->data();
				$content->add($due_tasks);
			}

			// tasks that have been delegated to me
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_tasks_delegated_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$just_delegated = $data->data();
				$content->add($just_delegated);
			}

			// tasks i'm tracking
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_tasks_im_tracking_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$tracking_tasks = $data->data();
				$content->add($tracking_tasks);
			}

			// new events
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_new_events_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$new_events = $data->data();
				$content->add($new_events);
			}

			// new tasks
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_new_tasks_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$new_tasks = $data->data();
				$content->add($new_tasks);
			}

			// new comments
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_new_comments_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$new_comments = $data->data();
				$content->add($new_comments);
			}

			// new calendars
			$data = new module_bootstrap_data($data_list, "form data");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_os_overview_new_calendars_gui($this->avalanche, $this->doc));
			$data = $runner->run($data);
			if(is_object($data)){
				$new_calendars = $data->data();
				$content->add($new_calendars);
			}

			if(!count($content->getComponents())){
				$simple = new SimplePanel();
				$simple->setStyle(new Style("emptyOverview"));
				$simple->add(new Text("No events or tasks this week! Add some events or tasks to fill up your overview."));
				$content->add($simple);
			}

			$main_content = new GridPanel(1);
			$main_content->setWidth("100%");
			$main_content->add($overview_title);
			$main_content->add($content);

			$main_content->getStyle()->setMarginLeft(20);
			$main_content->getCellStyle()->setPaddingBottom(10);
			/************************************************************************
			put it all together
			************************************************************************/

			$title = "Hello, " . $os->getUsername($this->avalanche->getActiveUser()) . "!";
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add(new Text($title));
			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			$grid->add($main_content);

			$overview = $grid;

			return new module_bootstrap_data($overview, "a gui component for the overview page");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}

	private function getNewStuff(){
		$date = $this->avalanche->lastLoggedOut();
		if(!$this->avalanche->loggedInHuh()){
			// if not logged in, get new events since yesterday...
			$date = date("Y-m-d H:i:s", $this->avalanche->getModule("strongcal")->gmttimestamp() - 60 * 60 * 24);
		}
		$visitor = new visitor_search_new($this->avalanche, $date);
		$results = $this->avalanche->execute($visitor);
		return $results;
	}

	private function timeDifference($datetime1, $datetime2){
		if($datetime2 > $datetime1){
			$temp = $datetime1;
			$datetime1 = $datetime2;
			$datetime2 = $temp;
		}
		$datetime1 = mktime(substr($datetime1, 11, 2), substr($datetime1, 14, 2), substr($datetime1, 17, 2), substr($datetime1, 5, 2), substr($datetime1, 8, 2), substr($datetime1, 0, 4));
		$datetime2 = mktime(substr($datetime2, 11, 2), substr($datetime2, 14, 2), substr($datetime2, 17, 2), substr($datetime2, 5, 2), substr($datetime2, 8, 2), substr($datetime2, 0, 4));
		$time_spent = $datetime1 - $datetime2;

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
		return $time_old;
	}
}
?>