<?

class module_bootstrap_os_overview_new_tasks_gui extends module_bootstrap_module{

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

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_user";
			}
			/** end initializing the input */
			$section_title_style = new Style("overview_title_style");

			$date_header_style = new Style("date_header_style");

			$section_content_style = new Style("overview_cell_style");
			$section_content_style->setFontFamily("verdana, sans-serif");
			$section_content_style->setFontSize(8);

			$this_week = new GridPanel(1);
			$this_week->setWidth("100%");

			$date_time_style = new Style("overview_title_style");
			$date_time_style->setWidth("75px");

			$author_style = new Style("overview_title_style");
			$author_style->setWidth("125px");

			$this_week_content_titles = new GridPanel(4);
			$this_week_content_titles->getStyle()->setBackground("#ACCEA5");
			$this_week_content_titles->setWidth("100%");
			$this_week_content_titles->add(new Text("Added"), $date_time_style);
			$this_week_content_titles->add(new Text("Due"), $date_time_style);
			$this_week_content_titles->add(new Text("Author"), $author_style);
			$this_week_content_titles->add(new Text("Title"), new Style("overview_title_style"));

			$scroll_content = new GridPanel(1);
			$scroll_content->setWidth("100%");

			$scrollable_holder = new ScrollPanel();
			$scrollable_holder->setWidth("100%");
			$scrollable_holder->add($scroll_content);

			$this_week_content = new GridPanel(1);
			$this_week_content->setStyle(new Style("overview_section_style"));
			$this_week_content->setWidth("100%");

			$this_week_content->add($this_week_content_titles);
			$this_week_content->add($scrollable_holder);

			$this_week->add(new Text("<b>New Tasks</b>"), $section_title_style);


			// icons
			$share_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/share.jpg");
			$gift_icon  = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/gift.jpg");

			$color_style = new Style();
			$color_style->setWidth("10px");
			$color_style->setHeight("10px");
			$color_style->setBorderWidth(1);
			$color_style->setBorderStyle("solid");
			$color_style->setBorderColor("black");
			$color_style->setMarginRight(3);
			$color_style->setMarginLeft(7);
			$color_style->setMarginTop(2);
			$color_style->setMarginBottom(2);

			// tasks this week

			$content_right_style = new Style("overview_cell_style");
			$content_right_style->setWidth("60px");
			$content_right_style->setBackground("#FFFFFF");
			$content_right_style->setPaddingLeft(14);
			$content_author_style = clone $content_right_style;
			$content_author_style->setWidth("110px");
			$title_style = new Style("overview_cell_style");
			$title_style->setBackground("#FFFFFF");
			$date_style = new Style("overview_cell_style");
			$date_style->setBackground("#CED6EB");

			$stamp = $strongcal->localtimestamp();
			$today = "0000-00-00";

			$total_height = 0;
			$task_list = $this->getNewStuff();
			$has_tasks = count($task_list);

			foreach($task_list as $task){
				$cal = $strongcal->getCalendarFromDb($task->calId());
				$added_on_time = new MMDateTime($task->createdOn());
				$added_on_time = $added_on_time->toString();
				if($today != substr($added_on_time,0,10)){
					$today = substr($added_on_time,0,10);
					$has_tasks = true;
					$total_height += 20;
					// header for day
					$this_week_content_content = new GridPanel(4);
					$this_week_content_content->setWidth("100%");
					$this_week_content_content->getStyle()->setClassname("overview_cell_style");

					$stamp = mktime(0, 0, 0, substr($today, 5, 2), substr($today, 8, 2), substr($today, 0, 4));
					$today = date("Y-m-d", $stamp);
					$title = new Panel();
					$title->setWidth("100%");
					$title->setStyle($date_header_style);
					$link = new Link(date("l, M jS", $stamp), "index.php?view=day&date=" . date("Y-m-d", $stamp));
					$link->setStyle(new Style("black_link"));
					$title->add($link);

					$scroll_content->add($title, $date_style);
					$scroll_content->add($this_week_content_content);
				}
				$total_height += 16;
				// find due time text
				$due = new MMDateTime($task->due());
				$due->hour($due->hour() + (int)floor($strongcal->timezone()));
				$due->minute($due->minute() + (int)(60 * ($strongcal->timezone() - floor($strongcal->timezone()))));
				$due = $due->toString();
				if($today == $due){
					$due_time = substr($due,11);
					$due_hour = (int)substr($due_time, 0, 2);
					$due_min  = (int)substr($due_time, 3, 2);
					$am_pm = "a";
					if($due_hour >= 12){
						$am_pm = "p";
					}
					if($due_hour == 0){
						$start_hour = 12;
					}
					if($due_hour > 12){
						$due_hour -= 12;
					}
					if($due_min < 10){
						$due_min = "0" . $due_min;
					}
					$due_time = $due_hour . ":" . $due_min . $am_pm;
				}else{
					$due_time = (int)substr($due, 5, 2) . "/" . (int)substr($due, 8, 2);
				}
				// find added on time
				$added_on_hour = (int)substr($added_on_time, 11, 2);
				$added_on_min  = (int)substr($added_on_time, 14, 2);
				$am_pm = "a";
				if($added_on_hour >= 12){
					$am_pm = "p";
				}
				if($added_on_hour == 0){
					$added_on_hour = 12;
				}
				if($added_on_hour > 12){
					$added_on_hour -= 12;
				}
				if($added_on_min < 10){
					$added_on_min = "0" . $added_on_min;
				}
				$added_on_time = $added_on_hour . ":" . $added_on_min . $am_pm;
				// find the author
				$user_id = $task->author();
				$username = $os->getUsername($user_id);
				$username = new Link($username, "javascript:;");
				$this->createUserMenu($username, $user_id);

				$color = new Panel();
				$color->setStyle(clone $color_style);
				$color->getStyle()->setBackground($cal->color());
				$cal_id = $task->calId();
				$cal = $strongcal->getCalendarFromDb($cal_id);
				if($task->status() == module_taskman_task::$STATUS_COMPLETED){
					$c = new Color($cal->color());
					if($c->isDark()){
						$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedwhite.gif");
					}else{
						$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedblack.gif");
					}
					$img->setWidth(8);
					$img->setHeight(8);
					$color->add($img);
				}
				if($task->status() == module_taskman_task::$STATUS_CANCELLED){
					$c = new Color($cal->color());
					if($c->isDark()){
						$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycancelledwhite.gif");
					}else{
						$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycancelledblack.gif");
					}
					$img->setWidth(8);
					$img->setHeight(8);
					$color->add($img);
				}

				$due_time = new Text($due_time . " ");
				$due_time->getStyle()->setFontSize(8);

				$added_on_time = new Text($added_on_time . " ");
				$added_on_time->getStyle()->setFontSize(8);

				$title_panel = new GridPanel(3);
				$title_panel->getStyle()->setClassname("overview_cell_style");
				$title_panel->getCellStyle()->setPaddingLeft(3);
				$title_panel->add($color);

				$title_name = TaskmanGuiHelper::taskTitleString($this->avalanche, $task);
				$title_name->getStyle()->setClassname("overview_cell_style");
				$title_panel->add($title_name);

				$tip = TaskmanGuiHelper::createTaskTip($this->avalanche, $task);
				$menu_action = new ToolTipAction($title_panel, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);


				$this_week_content_content->add($added_on_time, $content_right_style);
				$this_week_content_content->add($due_time, $content_right_style);
				$this_week_content_content->add($username, $content_author_style);
				$this_week_content_content->add($title_panel, $title_style);
			}
			if(!$has_tasks){
				return false;
			}

			if($total_height < 50){
				$total_height = 50;
			}
			if($total_height > 200){
				$total_height = 200;
			}

			$scrollable_holder->getStyle()->setHeight($total_height . "px");
			$this_week->add($this_week_content);

			return new module_bootstrap_data($this_week, "a gui component for the overview page");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}


	private function getNewStuff(){
		$date = $this->avalanche->lastLoggedOut();
		if(!$this->avalanche->loggedInHuh()){
			// if not logged in, get new tasks since yesterday...
			$date = date("Y-m-d H:i:s", $this->avalanche->getModule("strongcal")->gmttimestamp() - 60 * 60 * 24);
		}
		$visitor = new visitor_search_new($this->avalanche, $date);
		$visitor->searchFor(visitor_search::$TASKS);
		$visitor->doNotSearchFor(visitor_search::$EVENTS);
		$visitor->doNotSearchFor(visitor_search::$USERS);
		$visitor->doNotSearchFor(visitor_search::$TEAMS);
		$visitor->doNotSearchFor(visitor_search::$CALENDARS);
		$visitor->doNotSearchFor(visitor_search::$COMMENTS);

		$results = $this->avalanche->execute($visitor);
		if(count($results)){
			foreach($results as $key => $value){
				$sorter = new MDASorter();
				$comp   = new TaskmanTaskAddedComparator();
				$sorted_list = $sorter->sortDESC($value, $comp);
				return $sorted_list;
			}
		}else{
			return array();
		}
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