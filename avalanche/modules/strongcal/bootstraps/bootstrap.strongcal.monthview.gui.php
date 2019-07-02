<?

class module_bootstrap_strongcal_monthview_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List to HTML");
		$this->setInfo("this module takes as input an array of calendar objects. the output is a very basic
				html list of the calendars.");
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
			$strongcal->setUserVar("highlight", "month");

			if(!isset($data_list["date"])){
				$data_list["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $data_list["date"];
			$this->avalanche->setCookie("date", $date);

			$month = substr($date, 5, 2);
			$year  = substr($date, 0, 4);
			$start_date = $year . "-" . $month . "-" . "01";
			$end_date = $year . "-" . $month . "-" . date("t", mktime(0,0,0,$month,1, $year));
			/** end initializing the input */

			/**
			 * get the list of calendars
			 * we'll send in an array of calendar id's if we have one
			 */
			$data = false;
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
			$data = $runner->run($data);
			$calendar_list = $data->data();

			$writeable_calendars = array();
			foreach($calendar_list as $cal){
				if($cal->canWriteEntries()){
					$writeable_calendars[] = $cal;
				}
			}


			// filter out selected calendars

			// end filter
			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/month_style.css"));
			$this->doc->addStyleSheet($css);

			$container_style = new Style("container");
			$container_style->setWidth("100%");
			$container_style->setHeight("100%");

			$cell_style = new Style("container");
			$cell_style->setVerticalAlign("top");
			$cell_style->setTextAlign("left");
			$cell_style->setWidth("14%");

			$past_cell_style = clone $cell_style;
			$past_cell_style->setBackground("#EEEEEE");

			$today_cell_style = clone $cell_style;
			$today_cell_style->setBackground("#F9F9F9");

			$my_container = new GridPanel(8);
			$my_container->setStyle($container_style);
			$my_container->setCellStyle($cell_style);
			$my_row = new Panel();


			$corner = new Panel();
			$my_container->add($corner, new Style("top_corner"));
			$dow_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
			for($i=0;$i<7;$i++){
				$dow_text = new Text($dow_names[$i]);
				$dow_text->getStyle()->setFontSize(10);
				$dow_text->getStyle()->setFontFamily("verdana, sans-serif");
				$my_container->add($dow_text, new Style("top_row"));
			}


			$a_stamp = mktime(0,0,0, $month, 1, $year);
			$offset = date("w", $a_stamp);
			$day = 1 - $offset;
			$full_date = mktime(0,0,0,$month,$day,$year);
			$load_start_date = date("Y-m-d", $full_date);


			$offset = date("w", mktime(0,0,0, $month, date("t", $a_stamp), $year));
			$day = date("t", $a_stamp) + (6 - $offset);
			$full_date = mktime(0,0,0,$month,$day,$year);
			$load_end_date = date("Y-m-d", $full_date);

			$data = new module_bootstrap_data($calendar_list, "send in all calendars to get events from");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_eventlist($load_start_date, $load_end_date));
			$data = $runner->run($data);
			$events = $data->data();


			$offset = date("w", mktime(0,0,0, $month, 1, $year));
			for ($i=1; $i<43; $i++) {
				$day = $i - $offset;
				$full_date = mktime(0,0,0,$month,$day,$year);
				$last_month = (date("m", $full_date) != $month);
				$now_date_month = date("m", $full_date);
				$day = date("d", $full_date);
				$now_date = $year . "-" . $now_date_month . "-" . $day;

				if($i<=(date("t", mktime(0,0,0,$month,1, $year)) + $offset) ||
				   $last_month && ($day - date("w", $full_date) - 1 < 0)){
					// show week button
					if($i % 7 == 1){
						$week_button = new Button();
						$week_button->setIcon(new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/month_view_week.png"));
						$week_button->setStyle(new Style());
						$week_button->getStyle()->setHandCursor();
						$week_button->setHeight("100%");
						$week_button->setWidth("100%");
						$week_button->addAction(new LoadPageAction("index.php?view=week&date=" . date("Y-m-d", $full_date)));
						$my_container->add($week_button, new Style("month_view_week_button"));

					}
					// create cell
					$temp_panel = new Panel();
					$temp_panel->setWidth("100%");
					$temp_panel->setHeight("100%");
					$temp_panel->setValign("top");


					$temp_panel->setStyle(new Style("month_cell_text"));
					$month_cell_style = new Style("month_cell_title");
					$cell_day = new Button($day);
					$cell_day->addAction(new LoadPageAction("index.php?view=day&date=" . date("Y-m-d", $full_date)));
					$cell_day->setAlign("left");
					$cell_day->setWidth("100%");
					$cell_day->setStyle($month_cell_style);
					$temp_panel->add($cell_day);

					$events = array();
					foreach($calendar_list as $cal){
						$events = array_merge($events, $cal->getEventsOn(date("Y-m-d", $full_date)));
					}
					$data = new module_bootstrap_data($events, "send in all events to sort");
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_eventsorter());
					$data = $runner->run($data);
					$events = $data->data();

					$event_count = count($events);

					$count_for_day = 0;
					while(count($events) &&
					$events[0]->getDisplayValue("start_date") <= $now_date &&
					$events[0]->getDisplayValue("end_date")   >= $now_date){
						$count_for_day++;

						// make the calendar color cell
						$icon = new Panel();
						//$icon->getStyle()->setBackground($events[0]->calendar()->color());
						$icon->getStyle()->setClassname("aurora_view_icon");
						$color_box = new Panel();
						$color_box->getStyle()->setBorderWidth(4);
						$color_box->getStyle()->setBorderStyle("solid");
						$color_box->getStyle()->setBorderColor($events[0]->calendar()->color());
						$icon->add($color_box);

						$event_title = StrongcalGuiHelper::eventTitleString($this->avalanche, $events[0], $now_date);

						$item_panel = new BorderPanel();
						$item_panel->setValign("top");
						$item_panel->setStyle($temp_panel->getStyle());
						$item_panel->setCenter($event_title);
						$item_panel->setWest($icon);

						if($events[0]->getDisplayValue("description") || $events[0]->hasComments() || $events[0]->isAllDay()){
							$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $events[0]);
							$menu_action = new ToolTipAction($event_title, $tip);
							$this->doc->addAction($menu_action);
							$this->doc->addHidden($tip);
						}

						$temp_panel->add($item_panel);
						//$temp_panel->add(new Text("<br>"));
						array_splice($events, 0, 1);
					}
					while($count_for_day++<6){
						$temp_panel->add(new Text("<br>"));
					}
					if(count($writeable_calendars) > 0){
						$temp_panel->addDblClickAction(new LoadPageAction("index.php?view=add_event_step_1&date=" . $now_date));
					}
					if(date("Y-m-d", $full_date) < date("Y-m-d", $strongcal->localtimestamp())){
						$my_container->add($temp_panel, $past_cell_style);
					}else if(date("Y-m-d", $full_date) == date("Y-m-d", $strongcal->localtimestamp())){
						$my_container->add($temp_panel, $today_cell_style);
					}else{
						$my_container->add($temp_panel);
					}
				   }
			}


			$date_formatted = "&nbsp;&nbsp;" . date("F, Y", $a_stamp) . "&nbsp;&nbsp;";
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$last = strtotime("-1 month", $a_stamp);
			$next = strtotime("+1 month", $a_stamp);
			$last = "index.php?view=month&date=" . date("Y-m-d",$last);
			$next = "index.php?view=month&date=" . date("Y-m-d",$next);
			$last = new Link("&lt; prev", $last);
			$last->getStyle()->setFontSize(8);
			$next = new Link("next &gt;", $next);
			$next->getStyle()->setFontSize(8);
			
			$header->add($last);
			$header->add(new Text($date_formatted));
			$header->add($next);

			$rbracket = new Text(" ] ");
			$lbracket = new Text("&nbsp;&nbsp; [ ");
			$export = new Link("export", "?view=export&date=$date&range=month");
			$rbracket->getStyle()->setFontSize(8);
			$lbracket->getStyle()->setFontSize(8);
			$export->getStyle()->setFontSize(8);

			$header->add($lbracket);
			$header->add($export);
			$header->add($rbracket);

			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			$grid->add($my_container);

			$month_view = $grid;
			return new module_bootstrap_data($month_view, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	// creates a menu for an event
	private function createEventMenu($trigger, $cal_id, $event_id){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if(!is_int($event_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = false;
		$module = new StrongcalEventMenu($this->avalanche, $this->doc, $trigger, $cal_id, $event_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}
}
?>