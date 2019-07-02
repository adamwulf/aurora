<?

class module_bootstrap_os_overview_tasks_this_week_gui extends module_bootstrap_module{

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
			$date_time_style->setWidth("95px");

			$this_week_content_titles = new GridPanel(3);
			$this_week_content_titles->getStyle()->setBackground("#ACCEA5");
			$this_week_content_titles->setWidth("100%");
			$this_week_content_titles->add(new Text("Due"), $date_time_style);
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

			$this_week->add(new Text("<b>Tasks This Week</b>"), $section_title_style);


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

			// events this week

			$content_right_style = new Style("overview_cell_style");
			$content_right_style->setWidth("80px");
			$content_right_style->setBackground("#FFFFFF");
			$content_right_style->setPaddingLeft(14);
			$title_style = new Style("overview_cell_style");
			$title_style->setBackground("#FFFFFF");
			$date_style = new Style("overview_cell_style");
			$date_style->setBackground("#CED6EB");

			$stamp = $strongcal->localtimestamp();
			$today = date("Y-m-d", $strongcal->localtimestamp());

			$tasks = $this->getTasksDueThisWeek($this->avalanche->getActiveUser());
			$total_height = 0;
			if(count($tasks)){
				// header for day
				$overdue_set = false;
				$today_set = false;
				foreach($tasks as $task){
					$total_height += 16;
					$due = new DateTime($task->due());
					$stamp = $due->getTimeStamp();
					$due = $due->toString();
					//echo $overdue_set . " vs " . !$today_set . "<br>";
					if($today != substr($due, 0, 10) || $today == substr($due, 0, 10) &&  !$today_set){
						$total_height += 20;
						if($today < substr($due, 0, 10) || $today == substr($due, 0, 10) && $overdue_set){
							$this_week_content_content = new GridPanel(2);
							$this_week_content_content->setWidth("100%");
							$this_week_content_content->getStyle()->setClassname("overview_cell_style");

							$title = new Panel();
							$title->setWidth("100%");
							$title->setStyle($date_header_style);
							$link = new Link(date("l, M jS", $stamp), "index.php?view=day&date=" . date("Y-m-d", $stamp));
							$link->setStyle(new Style("black_link"));
							$title->add($link);
							$today = date("Y-m-d", $stamp);
							$scroll_content->add($title, $date_style);
							$scroll_content->add($this_week_content_content);
							$today_set = true;
						}else{
							if(!$overdue_set){
								$this_week_content_content = new GridPanel(2);
								$this_week_content_content->setWidth("100%");
								$this_week_content_content->getStyle()->setClassname("overview_cell_style");

								$title = new Panel();
								$title->setWidth("100%");
								$title->setStyle($date_header_style);
								$title->add(new Text("Overdue"));
								$overdue_set = true;
								$scroll_content->add($title, $date_style);
								$scroll_content->add($this_week_content_content);
							}
						}
					}
					// find start time text
					if($today == substr($due, 0, 10)){
						$start_time = $due;
						$start_hour = (int)substr($start_time, 11, 2);
						$start_min  = (int)substr($start_time, 14, 2);
						$am_pm = "a";
						if($start_hour >= 12){
							$am_pm = "p";
						}
						if($start_hour == 0){
							$start_hour = 12;
						}
						if($start_hour > 12){
							$start_hour -= 12;
						}
						if($start_min < 10){
							$start_min = "0" . $start_min;
						}
						$start_time = $start_hour . ":" . $start_min . $am_pm;
					}else{
						$start_time = (int)substr($due, 5, 2) . "/" . (int)substr($due, 8, 2);
					}
					// find title
					$title = $task->title();
					if(strlen($title) == 0){
						$title = "<i>no title</i>";
					}
					if($task->priority() == module_taskman_task::$PRIORITY_HIGH){
						$title = "<b>" . $title . "</b>";
					}else if($task->priority() == module_taskman_task::$PRIORITY_LOW){
						$title = "<i>" . $title . "</i>";
					}
					$color = new Panel();
					$color->setStyle(clone $color_style);
					$color->getStyle()->setBackground($strongcal->getCalendarFromDb($task->calId())->color());

					$start_time = new Text($start_time . " ");
					$start_time->getStyle()->setFontSize(8);

					$title_panel = new GridPanel(3);
					$title_panel->getStyle()->setClassname("overview_cell_style");
					$title_panel->getCellStyle()->setPaddingLeft(3);
					$title_panel->add($color);


					$title_name = TaskmanGuiHelper::taskTitleString($this->avalanche, $task);
					$title_name->getStyle()->setClassname("overview_cell_style");

					$tip = TaskmanGuiHelper::createTaskTip($this->avalanche, $task);
					$menu_action = new ToolTipAction($title_name, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);

					$title_panel->add($title_name);

					$this_week_content_content->add($start_time, $content_right_style);
					$this_week_content_content->add($title_panel, $title_style);
				}
			}
			if(!count($tasks)){
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


	private function getTasksDueThisWeek($author){
		if(!is_int($author)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$strongcal = $this->avalanche->getModule("strongcal");
		$next_week = date("Y-m-d", $strongcal->gmttimestamp() + 60 * 60 * 24 * 7);

		$taskman = $this->avalanche->getModule("taskman");
		$tasks = $taskman->getTasks();
		$due_tasks = array();
		foreach($tasks as $task){
			if(!$strongcal->selected($task->calId())){
				if(($task->assignedTo() == $author && $task->status() != module_taskman_task::$STATUS_DELEGATED ||
				    $task->delegatedTo() == $author && $task->status() != module_taskman_task::$STATUS_DECLINED) &&
				    $task->due() < $next_week &&
				    $task->status() != module_taskman_task::$STATUS_COMPLETED &&
				    $task->status() != module_taskman_task::$STATUS_CANCELLED){
					$due_tasks[] = $task;
				}
			}
		}
		return $due_tasks;
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