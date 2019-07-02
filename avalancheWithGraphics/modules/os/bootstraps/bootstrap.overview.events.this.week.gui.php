<?

class module_bootstrap_os_overview_events_this_week_gui extends module_bootstrap_module{

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
			$this_week_content_titles->add(new Text("Start"), $date_time_style);
			$this_week_content_titles->add(new Text("End"), $date_time_style);
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

			$this_week->add(new Text("<b>Events This Week</b>"), $section_title_style);


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
			$today = "0000-00-00";

			$total_height = 0;
			$has_events = false;
			for($i=0;$i<7;$i++){
				$event_list = $this->getEventsForDay($i);
				$today = $this->getDay($i);
				if(count($event_list)){
					$has_events = true;
					$total_height += 20;
					// header for day
					$this_week_content_content = new GridPanel(3);
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
					foreach($event_list as $event){
						$total_height += 16;
						// find start time text
						if($today == $event->getDisplayValue("start_date")){
							$start_time = $event->getDisplayValue("start_time");
							$start_hour = (int)substr($start_time, 0, 2);
							$start_min  = (int)substr($start_time, 3, 2);
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
							$start_time = (int)substr($event->getDisplayValue("start_date"), 5, 2) . "/" . (int)substr($event->getDisplayValue("start_date"), 8, 2);
						}
						// find end time text
						if($today == $event->getDisplayValue("end_date")){
							$end_time = $event->getDisplayValue("end_time");
							$end_hour = (int)substr($end_time, 0, 2);
							$end_min  = (int)substr($end_time, 3, 2);
							$am_pm = "a";
							if($end_hour >= 12){
								$am_pm = "p";
							}
							if($end_hour == 0){
								$end_hour = 12;
							}
							if($end_hour > 12){
								$end_hour -= 12;
							}
							if($end_min < 10){
								$end_min = "0" . $end_min;
							}
							$end_time = $end_hour . ":" . $end_min . $am_pm;
						}else{
							$end_time = (int)substr($event->getDisplayValue("end_date"), 5, 2) . "/" . (int)substr($event->getDisplayValue("end_date"), 8, 2);
						}
						// find title
						$title = $event->getDisplayValue("title");
						if(strlen($title) == 0){
							$title = "<i>no title</i>";
						}
						if(strcasecmp($event->getDisplayValue("priority"), "high") == 0){
							$title = "<b>" . $title . "</b>";
						}else if(strcasecmp($event->getDisplayValue("priority"), "low") == 0){
							$title = "<i>" . $title . "</i>";
						}
						if($event->isAllDay()){
							$title = " [" . $title . "]";
							$end_time = "---";
							$start_time = "---";
						}
						$color = new Panel();
						$color->setStyle(clone $color_style);
						$color->getStyle()->setBackground($event->calendar()->color());

						$start_time = new Text($start_time . " ");
						$start_time->getStyle()->setFontSize(8);

						$end_time = new Text($end_time . " ");
						$end_time->getStyle()->setFontSize(8);

						// comments icon
						if($event->hasComments()){
							$title = "<img src='" . $this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/small-bubble.png' width='10' height='10' border='0'>&nbsp;" . $title;
						}

						$title_panel = new GridPanel(3);
						$title_panel->getStyle()->setClassname("overview_cell_style");
						$title_panel->getCellStyle()->setPaddingLeft(3);
						$title_panel->add($color);
						if(count($reminders->getMyRemindersFor($event)) > 0){
							$reminder = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/alarm.gif");
							$title_panel->add($reminder);
						}
						$link = new Link($title, "index.php?view=event&cal_id=" . $event->calendar()->getId() . "&event_id=" . $event->getId());
						if($event->getDisplayValue("description") || $event->hasComments() || $event->isAllDay()){
							$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $event);
							$menu_action = new ToolTipAction($link, $tip);
							$this->doc->addAction($menu_action);
							$this->doc->addHidden($tip);
						}
						$title_panel->add($link);

						$this_week_content_content->add($start_time, $content_right_style);
						$this_week_content_content->add($end_time, $content_right_style);
						$this_week_content_content->add($title_panel, $title_style);
					}
				}
			}
			if(!$has_events){
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


	private $_all_events = false;

	// 0 is first day (today)
	// 1 is tomorrow
	// etc
	private function getEventsForDay($i){
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_array($this->_all_events)){

			$begin_date = date("Y-m-d", $strongcal->localtimestamp() + 60 * 60 * 24 * $i);
			$end_date = date("Y-m-d", $strongcal->localtimestamp() + 60 * 60 * 24 * 6);
			$data = false;

			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
			$runner->add(new module_bootstrap_strongcal_eventlist($begin_date, $end_date));
			$runner->add(new module_bootstrap_strongcal_eventsorter());
			$data = $runner->run($data);
			$this->_all_events = $data->data();
		}

		$now_date = date("Y-m-d", $strongcal->localtimestamp() + 60 * 60 * 24 * $i);
		$ret = array();
		foreach($this->_all_events as $e){
			if($e->getDisplayValue("start_date") <= $now_date &&
			   $e->getDisplayValue("end_date") >= $now_date){
			   	$ret[] = $e;
			}
		}
		return $ret;
	}

	private function getDay($i){
		$strongcal = $this->avalanche->getModule("strongcal");
		$now_date = date("Y-m-d", $strongcal->localtimestamp() + 60 * 60 * 24 * $i);
		return $now_date;
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