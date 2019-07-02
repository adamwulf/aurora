<?

class module_bootstrap_os_export_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	
	private $data_list;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List to HTML");
		$this->setInfo("this module takes as input an array of calendar objects. the output is a very basic
				html list of the calendars.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->data_list = array();
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			$this->data_list = $data_list;
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");
			$taskman = $this->avalanche->getModule("taskman");
			$os = $this->avalanche->getModule("os");
			
			$style = new Style();
			$style->setPadding(8);
			$style->setBorderColor("black");
			$style->setBorderWidth(1);
			$style->setBorderStyle("solid");
			$style->setWidth("400px");
			$style->setFontFamily("verdana, sans-serif");
			$style->setFontSize(10);
			
			$title_cell_style = clone $style;
			$title_cell_style->setBackground("#EEEEEE");
			
			$blue_bar_style = clone $style;
			$blue_bar_style->setPadding(0);
			$blue_bar_style->setPaddingLeft(8);
			$blue_bar_style->setPaddingBottom(4);
			$blue_bar_style->setPaddingTop(4);
			$blue_bar_style->setClassname("preferences_buttons_panel");
			
			$green_style = clone $style;
			$green_style->setPadding(8);
			$green_style->setHeight("105px");
			$green_style->setBackground("#C7D7C4");
			$green_style->setBorderWidth(1);
			$green_style->setBorderColor("#7DAD73");
			$green_style->setBorderStyle("solid");
			
			$title_style = new Style();
			$title_style->setFontSize(10);
			$title_style->setFontWeight("bold");
			
			$export_button = new Button("Download");
			
			
			try{
				if(isset($data_list["range"])){
					$range = $data_list["range"];
				}else{
					$range = "day";
				}
				
				if($range == "month" || $range == "day" || $range == "week"){
					if(isset($data_list["date"])){
						$date = $data_list["date"];
					}else{
						$stamp = $strongcal->gmttimestamp();
						$date = date("Y-m-d", $stamp);
					}
					
					
					
					// make the time midnight...
					$date .= " 00:00:00";
					$date = new DateTime($date);
					$title = "Export " . ucwords($range);
					if($range == "month"){
						$description = date("F, Y", $date->getTimeStamp());
					}else if($range == "week"){
						$date->day($date->day() - date("w", $date->getTimeStamp()));
						$last_of_week = clone $date;
						$last_of_week->day($last_of_week->day() + 6);
						$date_formatted  = date("l, F jS", $date->getTimeStamp());
						$date_formatted .= " through ";
						$date_formatted .= date("l, F jS", $last_of_week->getTimeStamp());
						$description = $date_formatted;
					}else{
						$description = date("F jS, Y", $date->getTimeStamp());
					}
					$export_button->addAction(new LoadPageAction("?primary_loader=module_taskman_export_loader&date=" . date("Y-m-d", $date->getTimeStamp()) . "&range=$range"));
					//$export = new Link("click here to download", "?primary_loader=module_taskman_export_loader&date=" . date("Y-m-d", $date->getTimeStamp()) . "&range=$range");
				}else if($range == "event"){
					if(!isset($data_list["event_id"])){
						throw new IllegalArgumentException("event_id must be sent in form input");
					}
					if(!isset($data_list["cal_id"])){
						throw new IllegalArgumentException("cal_id must be sent in form input");
					}
					$event_id = (int)$data_list["event_id"];
					$cal_id = (int)$data_list["cal_id"];
					
					$cal = $strongcal->getCalendarFromDb($cal_id);
					$event = $cal->getEvent($event_id);
					
					if(!is_object($event)){
						throw new IllegalArgumentException("Event cannot be found");
					}
					$title = "Export Event";
					$description = $event->getDisplayValue("title") . " in the " . $cal->name() . " calendar.";
					
					$export_button->addAction(new LoadPageAction("?primary_loader=module_taskman_export_loader&cal_id=$cal_id&event_id=$event_id&range=$range"));
					//$export = new Link("click here to download", "?primary_loader=module_taskman_export_loader&cal_id=$cal_id&event_id=$event_id&range=$range");
				}else if($range == "task"){
					if(!isset($data_list["task_id"])){
						throw new IllegalArgumentException("event_id must be sent in form input");
					}
					$task_id = (int)$data_list["task_id"];
					
					$task = $taskman->getTask($task_id);
					$cal = $strongcal->getCalendarFromDb($task->calId());
					
					
					$title = "Export Task";
					$description = $task->title() . " in the " . $cal->name() . " calendar.";
					$export_button->addAction(new LoadPageAction("?primary_loader=module_taskman_export_loader&task_id=$task_id&range=$range"));

					//$export = new Link("click here to download", "?primary_loader=module_taskman_export_loader&task_id=$task_id&range=$range");
				}else{
					throw new IllegalArgumentException("Range for export is missing.");
				}
				/**
				 * end initialization and checking
				 */
				$content = new GridPanel(1);
				$content->getCellStyle()->setPadding(8);
				$content->setStyle($style);
				$content->setValign("top");
				$title = new Text($title);
				$title->setStyle($title_style);
				$description = new Text($description);
				
				$content->add($title, $title_cell_style);
				$content->add($description, $blue_bar_style);

				$export_button->getStyle()->setBorderColor("#000000");
				$export_button->getStyle()->setBackground("#EEEEEE");
				$export_button->getStyle()->setFontSize(9);
				
				$green_box = new Panel();
				$green_box->setValign("top");
				$green_box->setWidth("100%");
				$green_box->setStyle($green_style);
				$text = new Text("To import to Microsoft Outlook, save this file to disk and then choose 'Import and Export...' from Outlook's 'File' menu and follow on screen instructions.");
				$green_box->add($text);
				$content->add($green_box);
				$content->add(new Text("<font color='#990000'><i>Outlook cannot import tasks.</i></font>"));
				$content->add($export_button);
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$event_view = $error;
			}catch(IllegalArgumentException $e){
				$content = new Panel();
				$content->setStyle($title_cell_style);
				$content->setAlign("center");
				$content->add(new Text("Export Error:<br>" . $e->getMessage()));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$event_view = $error;
			}
			return new module_bootstrap_data($event_view, "a gui component for the event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
	
	
	
	private function createUserMenu($trigger, $user_id){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = false;
		$module = new OSUserMenu($this->avalanche, $this->doc, $trigger, $user_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}	
}
?>