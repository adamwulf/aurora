<?

class module_bootstrap_strongcal_weekview_gui extends module_bootstrap_module{

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
			$strongcal->setUserVar("highlight", "week");

			if(!isset($data_list["date"])){
				$data_list["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $data_list["date"];
			$this->avalanche->setCookie("date", $date);

			$day = substr($date, 8, 2);
			$month = substr($date, 5, 2);
			$year  = substr($date, 0, 4);
			$stamp = mktime(0,0,0,$month, $day, $year);
			// find start of week
			while((int)date("w", $stamp) != 0){
				// go backwards a day
				$stamp = strtotime("-1 day", $stamp);
			}
			$start_date = date("Y-m-d", $stamp);
			$end_date = date("Y-m-d", $stamp);
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
			// filter out selected calendars

			$writeable_calendars = array();
			foreach($calendar_list as $cal){
				if($cal->canWriteEntries()){
					$writeable_calendars[] = $cal;
				}
			}

			// end filter
			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/month_style.css"));
			$this->doc->addStyleSheet($css);

			$container_style = new Style("container");

			$cell_style = new Style("container");
			$cell_style->setVerticalAlign("top");
			$cell_style->setTextAlign("left");
			$cell_style->setWidth("14%");

			$my_container = new GridPanel(7);
			$my_container->setStyle($container_style);
			$my_container->setHeight("500");
			$my_container->setWidth("100%");
			$my_container->setCellStyle($cell_style);
			$my_row = new Panel();



			// add headers to top row
			$dow_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
			for($i=0;$i<count($dow_names);$i++){
				$dow_text = new Text($dow_names[$i]);
				$dow_text->getStyle()->setFontSize(10);
				$dow_text->getStyle()->setFontFamily("verdana, sans-serif");
				$my_container->add($dow_text, new Style("top_row"));
			}

			// add first row of day cells
			for ($i=0; $i<7; $i++) {
				$full_date = $stamp + $i * 60 * 60 * 25;
				// create cell
				$temp_panel = $this->createDayCell($full_date, $calendar_list, $writeable_calendars);
				$my_container->add($temp_panel);
			}

			$first_of_week = mktime(0,0,0,date("m", $stamp), date("d", $stamp) - date("w", $stamp), date("Y", $stamp));
			$last_of_week = mktime(0,0,0,date("m", $first_of_week), date("d", $first_of_week) + 6, date("Y", $first_of_week));

			$date_formatted  = date("l, F jS", $first_of_week);
			$date_formatted .= " through ";
			$date_formatted .= date("l, F jS", $last_of_week);
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add(new Text($date_formatted));

			$rbracket = new Text(" ] ");
			$lbracket = new Text(" [ ");
			$export = new Link("export", "?view=export&date=$date&range=week");
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

			$week_view = $grid;
			return new module_bootstrap_data($week_view, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}


	// creates a day cell for the given time stamp
	function createDayCell($full_date, $calendar_list, $writeable_calendars){
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$strongcal = $this->avalanche->getModule("strongcal");
		$reminders = $this->avalanche->getModule("reminder");

		$now_date = date("Y-m-d", $full_date);
		$day = date("d", $full_date);

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

		$data = new module_bootstrap_data($calendar_list, "send in all calendars to get events from");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add(new module_bootstrap_strongcal_eventlist($now_date));
		$runner->add(new module_bootstrap_strongcal_eventsorter());
		$data = $runner->run($data);
		$events = $data->data();
		$event_count = count($events);

		$count_for_day = 0;
		while(count($events) &&
		$events[0]->getDisplayValue("start_date") <= $now_date &&
		$events[0]->getDisplayValue("end_date")   >= $now_date){
			$count_for_day++;

			$icon = new Panel();
			$icon->getStyle()->setBackground($events[0]->calendar()->color());
			$icon->getStyle()->setClassname("aurora_view_icon");

			$event_title = StrongcalGuiHelper::eventTitleString($this->avalanche, $events[0], $now_date);

			$item_panel = new BorderPanel();
			$item_panel->setValign("top");
			$item_panel->setStyle(clone $temp_panel->getStyle());
			$item_panel->getStyle()->setPaddingBottom(4);
			$item_panel->setCenter($event_title);
			$item_panel->setWest($icon);

			if($events[0]->getDisplayValue("description") || $events[0]->hasComments() || $events[0]->isAllDay()){
				$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $events[0]);
				$menu_action = new ToolTipAction($event_title, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);
			}


			$temp_panel->add($item_panel);
			array_splice($events, 0, 1);
		}

		while($count_for_day++<10){
			$temp_panel->add(new Text("<br>"));
		}
		if(count($writeable_calendars) > 0){
			$temp_panel->addDblClickAction(new LoadPageAction("index.php?view=add_event_step_1&date=" . $now_date));
		}
		return $temp_panel;
	}
}
?>