<?

class module_bootstrap_taskman_sidebar_tasklist extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Taskman Task List to Gui");
		$this->setInfo("this module takes as input an array of form input. the output is a very basic
				html list of the tasks.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			if(isset($data_list["view"])){
				$view = $data_list["view"];
			}
			if(isset($data_list["subview"])){
				$subview = $data_list["subview"];
			}

			$open_width = 152;
			$open_height = 18;
			$button_height = 18;
			$button_width = 152;
			$cal_list_height = 140;
			$request = $data->data();
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$taskman = $this->avalanche->getModule("taskman");
			$strongcal = $this->avalanche->getModule("strongcal");

			$tasks = $taskman->getTasks($this->avalanche->getActiveUser());

			/**
			* add the style sheet to the document for this page
			*/

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/sidebar_style.css"));
			$this->doc->addStyleSheet($css);
			$main_panel = new GridPanel(1);
			$main_panel->getStyle()->setWidth($open_width . "px");
			//$main_panel->setAlign("center");
			$main_panel->getStyle()->setMarginLeft(4);

			$list_panel = new ScrollPanel(1);
			$list_panel->getStyle()->setWidth((152) . "px");
			$list_panel->getStyle()->setClassname("border_top");
			$list_panel->getStyle()->setPaddingTop(4);
			$list_panel->getStyle()->setPaddingBottom(2);
			//$list_panel->getStyle()->setMarginLeft(4);
			//$list_panel->getStyle()->setMarginRight(4);
			$list_panel->getStyle()->setMarginBottom(4);
			$list_panel->getStyle()->setBackground("#FFFFFF");
			$list_panel->setAlign("left");

			$height = 0;
			$blank_icon = new Panel();
			$blank_icon->getStyle()->setWidth("10px");
			$blank_icon->getStyle()->setHeight("10px");

			// first add all non selected(hidden) tasks
			$completed_tasks = array();
			$cancelled_tasks = array();
			$today_tasks = array();
			$tomorrow_tasks = array();
			$upcoming_tasks = array();
			$overdue_tasks = array();
			$localstamp = $strongcal->localtimestamp();
			$now_datetime = date("Y-m-d H:i:s", $localstamp);
			$today_datetime = date("Y-m-d 24:00:00", $localstamp);
			$tomorrow_datetime = date("Y-m-d 24:00:00", $localstamp + 60 * 60 * 24);
			$last_week_datetime = date("Y-m-d 24:00:00", $localstamp - 60 * 60 * 24 * 3);
			$next_week_datetime = date("Y-m-d 24:00:00", $localstamp + 60 * 60 * 24 * 7);
			foreach($tasks as $task){
				if($strongcal->selected($task->calId())){
					// noop
				}else if($task->status() == module_taskman_task::$STATUS_COMPLETED){
					// if it's less than a 3 days old
					if($task->completed() > $last_week_datetime){
						$completed_tasks[] = $task;
					}
				}else if($task->status() == module_taskman_task::$STATUS_CANCELLED){
					// if it's less than a 3 days old
					if($task->cancelled() > $last_week_datetime){
						$cancelled_tasks[] = $task;
					}
				}else if($task->due() < $now_datetime){
					$overdue_tasks[] = $task;
				}else if($task->due() < $today_datetime){
					$today_tasks[] = $task;
				}else if($task->due() < $tomorrow_datetime){
					$tomorrow_tasks[] = $task;
				}else{
					// if it is within the next week
					if($task->due() < $next_week_datetime){
						$upcoming_tasks[] = $task;
					}
				}
			}
			if(count($overdue_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($overdue_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$task_panel->getStyle()->setMarginBottom(3);
				$label = new Text("Overdue");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(8);
				$label->getStyle()->setFontWeight("bold");
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			if(count($today_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($today_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$task_panel->getStyle()->setMarginBottom(3);
				$label = new Text("Due Today");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(8);
				$label->getStyle()->setFontWeight("bold");
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			if(count($tomorrow_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($tomorrow_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$task_panel->getStyle()->setMarginBottom(3);
				$label = new Text("Due Tomorrow");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(8);
				$label->getStyle()->setFontWeight("bold");
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			if(count($upcoming_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($upcoming_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$task_panel->getStyle()->setMarginBottom(3);
				$label = new Text("Upcoming");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(8);
				$label->getStyle()->setFontWeight("bold");
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			if(count($completed_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($completed_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$label = new Text("Completed");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(9);
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			if(count($cancelled_tasks)){
				// add 15 to height for label
				$task_panel = $this->makeTaskList($cancelled_tasks);
				$height += $task_panel->getHeight() + 15;
				$task_panel->setHeight(0);
				$label = new Text("Cancelled");
				$label->getStyle()->setFontFamily("verdana");
				$label->getStyle()->setFontSize(9);
				$list_panel->add($label);
				$list_panel->add($task_panel);
			}
			// figure out height of non hidden task list
			if($height == 0){
				$none_panel = new Panel();
				$none_panel->setWidth("100%");
				$none_panel->setAlign("center");
				$cal_name = new Text("<i>none</i>");
				$cal_name->getStyle()->setClassname("aurora_sidebar_text");
				$none_panel->add($cal_name);
				$list_panel->add($none_panel);
				$height += 15;
			}

			// add in the height of padding in $list_panel
			$height += 6;


			if($height > $cal_list_height){
				$height = $cal_list_height;
			}
			$list_panel->getStyle()->setHeight($height);
			$list_panel->getStyle()->setDisplayBlock();

			$hide_cals_button = new Button();
			$show_cals_button = new Button();
			$task_text = new Text("Tasks");
			$task_text->getStyle()->setFontSize(8);
			$task_text->getStyle()->setPaddingLeft(4);
			$task_text->getStyle()->setPaddingTop(4);

			$icon = new IconWithText($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/calendarlist.gif", $task_text);
			$icon->setAlign("left");
			$icon->getStyle()->setWidth($open_width);
			$icon->getStyle()->setHeight($open_height);
			$hide_cals_button->setIcon($icon);
			$hide_cals_button->setStyle(new Style("sidebar_button"));
			$hide_cals_button->getStyle()->setWidth(152);
			$hide_cals_button->getStyle()->setHeight($button_height);
			$hide_cals_button->getStyle()->setDisplayBlock();
			$hide_cals_button->addAction(new DisplayNoneAction($list_panel));
			$hide_cals_button->addAction(new DisplayNoneAction($hide_cals_button));
			$hide_cals_button->addAction(new DisplayBlockAction($show_cals_button));
			$hide_cals_button->addAction(new SetCookieAction("cook_sidebar_tasks", "close"));

			$show_cals_button->setIcon($icon);
			$show_cals_button->setStyle(new Style("sidebar_button"));
			$show_cals_button->getStyle()->setWidth(152);
			$show_cals_button->getStyle()->setHeight($button_height);
			$show_cals_button->getStyle()->setDisplayNone();
			$show_cals_button->addAction(new DisplayNoneAction($show_cals_button));
			$show_cals_button->addAction(new DisplayBlockAction($hide_cals_button));
			$show_cals_button->addAction(new DisplayBlockAction($list_panel));
			$show_cals_button->addAction(new SetCookieAction("cook_sidebar_tasks", "open"));

			if(isset($data_list["cook_sidebar_tasks"]) && $data_list["cook_sidebar_tasks"] == "close"){
				$list_panel->getStyle()->setDisplayNone();
				$hide_cals_button->getStyle()->setDisplayNone();
				$show_cals_button->getStyle()->setDisplayBlock();
			}


			$buttons = new Panel();
			$buttons->add($hide_cals_button);
			$buttons->add($show_cals_button);

			$tip = OsGuiHelper::createToolTip(new Text("Collapse or expand the upcoming tasks menu"));
			$menu_action = new ToolTipAction($buttons, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$main_panel->add($buttons);
			$main_panel->add($list_panel);
			$main_panel->setAlign("center");

			return new module_bootstrap_data($main_panel, "a gui component for the event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of tasks.<br>");
		}
	}

	private function makeTaskList($tasks){
		$height = 0;
		$taskman = $this->avalanche->getModule("taskman");
		$strongcal = $this->avalanche->getModule("strongcal");
		$task_panel = new GridPanel(1);
		$task_panel->getStyle()->setWidth("130px");
		foreach($tasks as $task){
			// save the icons panel for now, in case we want to add an ! or something to overdue events
			$icons = new GridPanel(3);
			$cal_id = $task->calId();
			$cal = $strongcal->getCalendarFromDb($cal_id);
			$panel = new BorderPanel();
			$panel->setValign("top");
			$icon = new Panel();
			$icon->setStyle(new Style("aurora_view_icon"));
			$icon->getStyle()->setBackground($cal->color());
			if($task->status() == module_taskman_task::$STATUS_COMPLETED){
				$c = new Color($cal->color());
				if($c->isDark()){
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedwhite.gif");
				}else{
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedblack.gif");
				}
				$img->setWidth(8);
				$img->setHeight(8);
				$icon->add($img);
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
				$icon->add($img);
			}
			$icons->add($icon);
			$panel->setWest($icons);
			$title = $task->title();
			$title_panel = new Panel();
			$title_panel->setWidth("100%");

			$task_name = TaskmanGuiHelper::taskTitleString($this->avalanche, $task);

			$tip = TaskmanGuiHelper::createTaskTip($this->avalanche, $task);
			$menu_action = new ToolTipAction($task_name, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$task_name->getStyle()->setClassname("aurora_sidebar_text");

			$title_panel->add($task_name);
			$panel->setCenter($title_panel);
			$task_panel->add($panel);
			$height += 15 * ceil(strlen($title) / 13);
		}
		$task_panel->setHeight($height);
		return $task_panel;
	}
}
?>