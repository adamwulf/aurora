<?

class module_bootstrap_strongcal_dayview_gui extends module_bootstrap_module{

	private $time_inc;
	private $column_width;
	private $avalanche;
	private $doc;

	// the hour of the day to start on
	private $beginning_of_day;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("Aurora Day View");
		$this->setInfo("returns the day view of this calendar");
		$this->time_inc = 30;
		$this->column_width = "220px";

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
			$reminders = $this->avalanche->getModule("reminder");
			$strongcal = $this->avalanche->getModule("strongcal");
			$strongcal->setUserVar("highlight", "day");


			$this->beginning_of_day = substr($strongcal->getUserVar("day_start"), 0, 2);
			$this->end_of_day = substr($strongcal->getUserVar("day_end"), 0, 2) + substr($strongcal->getUserVar("day_end"), 3, 2)/60 + .5;


			if(!isset($data_list["date"])){
				$data_list["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $data_list["date"];
			$this->avalanche->setCookie("date", $date);

			$month = substr($date, 8, 2);
			$month = substr($date, 5, 2);
			$year  = substr($date, 0, 4);
			/** end initializing the input */

			/**
			 * get the list of calendars
			 * we'll send in an array of calendar id's if we have one
			 */
			if(isset($data_list["cal_ids"]) && is_array($data_list["cal_ids"])){
				 $data = new module_bootstrap_data($cal_ids, "send in a list of calendar ids to get");
			}else{
				 $data = false; // send in false as the default value
			}
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
			$data = $runner->run($data);
			$calendar_list = $data->data();


			$data = new module_bootstrap_data($calendar_list, "send in calendars");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_eventlist($date));
			$runner->add(new module_bootstrap_strongcal_eventsorter());
			$data = $runner->run($data);
			$event_list = $data->data();


			$writeable_calendars = array();
			foreach($calendar_list as $cal){
				if($cal->canWriteEntries()){
					$writeable_calendars[] = $cal;
				}
			}
			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/day_style.css"));
			$this->doc->addStyleSheet($css);

			$day_events = array();
			$all_day_events = array();
			$all_day_panel = new SimplePanel();
			$all_day_panel->getStyle()->setPaddingLeft(6);
			$all_day_panel->getStyle()->setPaddingTop(6);

			foreach($event_list as $e){
				if($e->isAllDay()){
					$all_day_events[] = $e;
				}else{
					$day_events[] = $e;
				}
			}
			$event_list = $day_events;


			/***************************
			 create all day panel
			***************************/
			if(count($all_day_events)){
				$shadow = new SimplePanel();
				$shadow->setStyle(new Style("dropshadow"));

				$title = new Text("All Day Events<br>");
				$title->setStyle(new Style("all_day_title"));
				$p = new SimplePanel();
				$p->setWidth("100%");
				$p->add($title);
				$p->setStyle(new Style("all_day_frame"));
				$p->getStyle()->setPosition("relative");
				$p->getStyle()->setRight(6);
				$p->getStyle()->setBottom(6);
				foreach($all_day_events as $e){
					$t = new GridPanel(2);
					$t->getCellStyle()->setPadding(3);
					$link = StrongcalGuiHelper::eventTitleString($this->avalanche, $e, $date);
					$color = new Panel();
					$color->setStyle(new Style("aurora_view_icon"));
					$color->getStyle()->setBackground($e->calendar()->color());
					$t->add($color);
					$t->add($link);
					$p->add($t);
				}
				$shadow->add($p);
				$all_day_panel->add($shadow);

				$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $e);
				$menu_action = new ToolTipAction($link, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);
			}


			/***************************
			 create main day view panel
			***************************/
			$panel = new RowPanel();
			$panel->setWidth("100%");
			$panel->setAlign("left");
			$panel->setValign("top");
			$panel->getStyle()->setClassname("container");
			$panel->getCellStyle()->setClassname("event_cell");
			$panel->setRowHeight("30");
			if(count($event_list)){
				$time = $event_list[0]->getDisplayValue("start_time");
				$start_hour = (int) substr($time, 0, 2);
				if($start_hour <= $this->beginning_of_day){
					$this->beginning_of_day = $start_hour;
				}
				if(is_int($this->beginning_of_day) && $this->beginning_of_day < 10){
					$this->beginning_of_day = "0" . $this->beginning_of_day;
				}else if(is_int($this->beginning_of_day)){
					$this->beginning_of_day = (string) $this->beginning_of_day;
				}
			}
			if(count($event_list)){
				foreach($event_list as $event){
					$start_date = $event->getDisplayValue("start_date");
					$start_time = $event->getDisplayValue("start_time");
					$end_date = $event->getDisplayValue("end_date");
					$end_time = $event->getDisplayValue("end_time");
					$start_hour = (int) substr($start_time, 0, 2);
					$end_hour = (int) substr($end_time, 0, 2);
					if($end_date == $date && $end_hour >= $this->end_of_day){
						$this->end_of_day = $end_hour + 1;
					}else if($end_date != $date && $start_date == $date && $start_hour >= $this->end_of_day && $start_hour){
						$this->end_of_day = $start_hour + 1;
					}
					if(is_int($this->end_of_day) && $this->end_of_day < 10){
						$this->end_of_day = "0" . $this->end_of_day;
					}else if(is_int($this->end_of_day)){
						$this->end_of_day = (string) $this->end_of_day;
					}
				}
			}


			foreach($event_list as $event){
				$end_time = $event->getDisplayValue("end_time");
				$end_date = $event->getDisplayValue("end_date");
				$hour = (int)substr($end_time, 0, 2);
				if($hour < 10){
					$hour = "0" . $hour;
				}
				if($end_date == $date && $hour < $this->beginning_of_day){
					$this->beginning_of_day = (string)$hour;
				}
			}
			for($i=$this->beginning_of_day*60;$i<$this->end_of_day*60;$i+=$this->time_inc){
				$true_hour = ($i - ($i % 60)) / 60;
				$hour = $true_hour;
				$min = $i % 60;

				if($hour < 12){
					$ampm = "am";
				}else{
					$ampm = "pm";
				}
				if($hour > 12){
					$hour -= 12;
				}
				if($hour == 0){
					$hour = 12;
				}


				if($min < 10) $min = "0" . $min;
				$time_format = $hour . ":" . $min . $ampm;
				if($i == 0){
					$time_format = "midnight";
				}else if($i == 12*60){
					$time_format = "noon";
				}
				if($true_hour < 10) $true_hour = "0" . $true_hour;
				$txt = new Link("$time_format", "index.php?view=add_event_step_1&date=$date&time=$true_hour:$min");
				$txt->setStyle(new Style("time"));
				$time_panel = new Panel();
				$time_panel->add($txt);
				$time_panel->getStyle()->setPadding(4);
				$panel->add($time_panel);
				while(count($event_list) && $this->checkEvent($event_list[0], $date, $i)){
					$start_date = $event_list[0]->getDisplayValue("start_date");
					$end_date   = $event_list[0]->getDisplayValue("end_date");
					$start_time = $event_list[0]->getDisplayValue("start_time");
					$start_time_disp = $this->formatTime($start_time);
					if($start_date < $date){
						$start_time = $this->beginning_of_day . ":00";
						$start_time_disp = (int)substr($start_date, 5, 2) . "/" . (int)substr($start_date, 8, 2);
					}
					$start_hour = substr($start_time, 0, 2);
					$start_min  = substr($start_time, 3, 2);
					$start_i = $start_hour * 60 + $start_min;
					$end_time = $event_list[0]->getDisplayValue("end_time");
					$end_time_disp = $this->formatTime($end_time);
					if($end_date > $date){
						$end_time = $this->end_of_day . ":00";
						$end_time_disp = (int)substr($end_date, 5, 2) . "/" . (int)substr($end_date, 8, 2);
					}
					$end_hour = substr($end_time, 0, 2);
					$end_min  = substr($end_time, 3, 2);

					$duration = ($end_hour - $start_hour)*60 + $end_min - $start_min;
					$title = trim($event_list[0]->getDisplayValue("title"));
					if(strlen($title) == 0){
						$title = "<i>no title</i>";
					}
					/* find number of rows to span */
					$row_span = (int)floor($duration / $this->time_inc);
					if($duration % $this->time_inc){
						$row_span++;
					}
					/* ensure at least one row */
					if($row_span == 0) $row_span = 1;

					$cal_id = $event_list[0]->calendar()->getId();
					$event_id = $event_list[0]->getId();

					$icon = new Panel();
					$icon->setStyle(new Style("aurora_view_icon"));
					//$icon->getStyle()->setBackground($event_list[0]->calendar()->color());
					$color_box = new Panel();
					$color_box->getStyle()->setBorderWidth(4);
					$color_box->getStyle()->setBorderStyle("solid");
					$color_box->getStyle()->setBorderColor($event_list[0]->calendar()->color());
					$icon->add($color_box);


					$event_panel = new Button();
					$event_panel->addAction(new LoadPageAction("index.php?view=event&cal_id=$cal_id&event_id=$event_id"));
					$event_panel->setStyle(new Style("event_panel"));
					$event_panel->getStyle()->setWidth($this->column_width);
					$event_panel->getStyle()->setHeight($panel->getRowHeight() * $row_span + $row_span - 0);
					$button_content = new BorderPanel();
					$button_content->getStyle()->setClassname("event_panel_content");
					$button_content->setWidth("100%");
					$button_content->setHeight("100%");
					$button_content->setAlign("left");
					if(!$event_list[0]->isAllDay()){
						$title = $start_time_disp . " - " . $end_time_disp . "<br>" . $title;
					}else{
						$title = " [" . $title . "]";
					}

					// comments icon
					if($event_list[0]->hasComments()){
						$title = "<img src='" . $this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/small-bubble.png' width='10' height='10' border='0'>&nbsp;" . $title;
					}
					if(strcasecmp($event_list[0]->getDisplayValue("priority"), "high") == 0){
						$title = "<b>" . $title . "</b>";
					}else if(strcasecmp($event_list[0]->getDisplayValue("priority"), "low") == 0){
						$title = "<i>" . $title . "</i>";
					}
					$center = new Panel();
					$center->setStyle($button_content->getStyle());
					if(count($reminders->getMyRemindersFor($event_list[0])) > 0){
						$reminder = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/alarm.gif");
						$center->add($reminder);
					}
					$center->add(new Text($title));
					$button_content->setCenter($center);
					$button_content->setWest($icon);
					$button_content->setValign("top");
					$event_panel->add($button_content);

					if($event_list[0]->getDisplayValue("description") || $event_list[0]->hasComments() || $event_list[0]->isAllDay()){
						$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $event_list[0]);
						$menu_action = new ToolTipAction($event_panel, $tip);
						$this->doc->addAction($menu_action);
						$this->doc->addHidden($tip);
					}

					$panel->add($event_panel, $row_span);
					array_splice($event_list, 0, 1);
				}
				if(count($writeable_calendars) > 0){
					$panel->addRowDblClickAction(new LoadPageAction("index.php?view=add_event_step_1&date=$date&time=$true_hour:$min"));
				}
				$panel->nextRow();
			}


			$date_stamp = mktime(0,0,0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
			$date_formatted = "&nbsp;&nbsp;" . date("F jS, Y", $date_stamp) . "&nbsp;&nbsp;";

			$last = strtotime("-1 day", $date_stamp);
			$next = strtotime("+1 day", $date_stamp);
			$last = "index.php?view=day&date=" . date("Y-m-d",$last);
			$next = "index.php?view=day&date=" . date("Y-m-d",$next);
			$last = new Link("&lt; prev", $last);
			$last->getStyle()->setFontSize(8);
			$next = new Link("next &gt;", $next);
			$next->getStyle()->setFontSize(8);


			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add($last);
			$header->add(new Text($date_formatted));
			$header->add($next);

			$rbracket = new Text(" ] ");
			$lbracket = new Text("&nbsp;&nbsp; [ ");
			$export = new Link("export", "?view=export&date=$date&range=day");
			$rbracket->getStyle()->setFontSize(8);
			$lbracket->getStyle()->setFontSize(8);
			$export->getStyle()->setFontSize(8);

			$header->add($lbracket);
			$header->add($export);
			$header->add($rbracket);

			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			if(count($all_day_events)){
				$grid->add($all_day_panel);
			}
			$grid->add($panel);

			return new module_bootstrap_data($grid, "a gui component for the day view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}


	private function checkEvent(module_strongcal_event $e, $date, $i){
		$start_time = $e->getDisplayValue("start_time");
		$start_hour = substr($start_time, 0, 2);
		$start_min  = substr($start_time, 3, 2);
		$start_i = $start_hour * 60 + $start_min;
		if($e->getDisplayValue("start_date") < $date){
			$start_i = $this->beginning_of_day * 60;
		}
		if($start_i >= $i && $start_i < ($i+$this->time_inc)){
			return true;
		}else{
			return false;
		}
	}

	private function formatTime($time){
		$hour = (int) substr($time, 0, 2);
		$min  = (int) substr($time, 3, 2);
		$am_pm = "a";
		if($hour >= 12){
			$am_pm = "p";
		}
		if($hour == 0){
			$hour = 12;
		}
		if($hour > 12){
			$hour -= 12;
		}
		if($min < 10){
			$min = "0" . $min;
		}
		return $hour . ":" . $min . $am_pm;
	}
}
?>