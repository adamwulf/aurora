<?
class module_bootstrap_strongcal_header extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Header");
		$this->setInfo("returns the Gui Component for the Aurora Header.
				(include links to views etc)");

		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			$module = false;
			$strongcal = $this->avalanche->getModule("strongcal");
			$bootstrap = $this->avalanche->getModule("bootstrap");

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/header_style.css"));
			$this->doc->addStyleSheet($css);

			// init search options
			$visitor = new visitor_search($this->avalanche, "");
			$types = $visitor->getSearchTypes();
			if(in_array(visitor_search::$CALENDARS,$types)){
				$search_calendars = "1";
			}else{
				$search_calendars = "0";
			}
			if(in_array(visitor_search::$EVENTS,$types)){
				$search_events = "1";
			}else{
				$search_events = "0";
			}
			if(in_array(visitor_search::$TASKS,$types)){
				$search_tasks = "1";
			}else{
				$search_tasks = "0";
			}
			if(in_array(visitor_search::$USERS,$types)){
				$search_users = "1";
			}else{
				$search_users = "0";
			}
			if(in_array(visitor_search::$TEAMS,$types)){
				$search_teams = "1";
			}else{
				$search_teams = "0";
			}
			if(in_array(visitor_search::$COMMENTS,$types)){
				$search_comments = "1";
			}else{
				$search_comments = "0";
			}
			// end init search options
			/************************************************************************
			    initialize panels
			************************************************************************/

			$my_container = new BorderPanel();

			$program_nav = new RowPanel();

			$filler = new GridPanel(7);

			$shortcuts = new GridPanel(4);


			/************************************************************************
			    apply styles to created panels
			************************************************************************/

			$my_container->getStyle()->setWidth("100%");
			$my_container->getStyle()->setHeight("25px");
			$my_container->getStyle()->setClassname("calendarheader");

			$program_nav->getStyle()->setClassname("header_left_content");
			$program_nav->getStyle()->setBackground("#91A1C6");
			$program_nav->getStyle()->setHeight("25px");
			$program_nav->getStyle()->setWidth("300px");
			$program_nav->setRowHeight("25px");

			$shortcuts->getStyle()->setClassname("shortcuts");
			$shortcuts->getStyle()->setBackground("#91A1C6");
			$shortcuts->getStyle()->setHeight("25px");
			$shortcuts->getCellStyle()->setPaddingLeft(4);

			$shortcuts->setAlign("right");

			$filler->getStyle()->setBackground("#91A1C6");
			$filler->getStyle()->setHeight("25px");
			$filler->setWidth("100%");

			/************************************************************************
			    add necessary text and html
			************************************************************************/

			$overview = new Button("Overview");
			$month = new Button("Month");
			$week = new Button("Week");
			$day = new Button("Day");

			/** add tooltips for buttons **/
			$tip = OsGuiHelper::createToolTip(new Text("Go to Overview Page"));
			$menu_action = new ToolTipAction($overview, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$tip = OsGuiHelper::createToolTip(new Text("Go to Month Page"));
			$menu_action = new ToolTipAction($month, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$tip = OsGuiHelper::createToolTip(new Text("Go to Week Page"));
			$menu_action = new ToolTipAction($week, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$tip = OsGuiHelper::createToolTip(new Text("Go to Day Page"));
			$menu_action = new ToolTipAction($day, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			/** set styles for buttons **/
			$overview->setStyle(new Style("aurora_header_button"));
			$month->setStyle(new Style("aurora_header_button"));
			$week->setStyle(new Style("aurora_header_button"));
			$day->setStyle(new Style("aurora_header_button"));

			$overview_action = new LoadPageAction("index.php?view=overview");
			$month_action = new LoadPageAction("index.php?view=month");
			$week_action = new LoadPageAction("index.php?view=week");
			$day_action = new LoadPageAction("index.php?view=day");

			$overview->addAction($overview_action);
			$month->addAction($month_action);
			$week->addAction($week_action);
			$day->addAction($day_action);


			$program_nav->add($overview);
			$program_nav->add(new Text("|"));
			$program_nav->add($month);
			$program_nav->add($week);
			$program_nav->add($day);

			$add_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/addtaskicon.gif");
			$add_task_button = new Button();
			$add_task_button->setStyle(new Style("xTrigger"));
			$add_task_button->setIcon($add_icon);

			$tip = OsGuiHelper::createToolTip(new Text("Click to Add a Task"));
			$menu_action = new ToolTipAction($add_task_button, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$add_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/addeventicon.gif");
			$add_event_button = new Button();
			$add_event_button->setStyle(new Style("xTrigger"));
			$add_event_button->setIcon($add_icon);

			$tip = OsGuiHelper::createToolTip(new Text("Click to Add an Event"));
			$menu_action = new ToolTipAction($add_event_button, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			// create add event drop down menu //
			$event_text = new Panel();
			$event_text->getStyle()->setWidth("120px");
			$event_text->getStyle()->setFontFamily("verdana, sans-serif");
			$event_text->getStyle()->setFontSize(8);
			$event_text->setAlign("left");
			$event_text->add(new Text("Add Event to:"));

			// create add event drop down menu //
			$task_text = new Panel();
			$task_text->getStyle()->setWidth("120px");
			$task_text->getStyle()->setFontFamily("verdana, sans-serif");
			$task_text->getStyle()->setFontSize(8);
			$task_text->setAlign("left");
			$task_text->add(new Text("Add Task to:"));

			$event_menu_panel = new GridPanel(1);
			$event_menu_panel->setStyle(new Style("xMenu"));
			$event_menu_panel->getStyle()->setWidth("120px");
			$event_menu_panel->getStyle()->setBackground("#EEEEEE");
			$event_menu_panel->add($event_text);

			$task_menu_panel = new GridPanel(1);
			$task_menu_panel->setStyle($event_menu_panel->getStyle());
			$task_menu_panel->add($task_text);

			/**
			 * get the list of calendars
			 */
			$data = false; // send in false as the default value
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_obj_list = $data->data();

			$now_date = date("Y-m-d", $strongcal->localtimestamp());
			$now_time = date("H:i:s", $strongcal->localtimestamp());

			$count = 0;
			foreach($calendar_obj_list as $cal){
				if(!$strongcal->selected($cal)){
					if($cal->canWriteEntries()){
						$count++;
						$color_box = new Panel();
						$color_box->setStyle(new Style("aurora_view_icon"));
						$color_box->getStyle()->setBackground($cal->color());
						$color_box = new ErrorPanel($color_box);
						$color_box->setStyle(new Style("menu_icon"));
						$color_box->getStyle()->setHeight("16px");
						$color_box->getStyle()->setWidth("16px");
						// make menu item for event menu
						$add_event_action = new LoadPageAction("index.php?view=add_event_step_2&cal_id=" . $cal->getId() . "&date=" . $now_date . "&time=" . $now_time);
						$add_event_link = new Button($cal->name());
						$add_event_link->setAlign("left");
						$add_event_link->setStyle(new Style("menu_button"));
						$add_event_link->getStyle()->setWidth("120px");
						$add_event_link->getStyle()->setHeight("16px");
						$add_event_link->addAction($add_event_action);

						$item = new BorderPanel();
						$item->setWest($color_box);
						$item->setCenter($add_event_link);
						$event_menu_panel->add($item);

						// make menu item for task menu
						$add_task_action = new LoadPageAction("index.php?view=add_task_step_2&cal_id=" . $cal->getId() . "&date=" . $now_date . "&time=" . $now_time);
						$add_task_link = new Button($cal->name());
						$add_task_link->setAlign("left");
						$add_task_link->setStyle(new Style("menu_button"));
						$add_task_link->getStyle()->setWidth("120px");
						$add_task_link->getStyle()->setHeight("16px");
						$add_task_link->addAction($add_task_action);

						$item = new BorderPanel();
						$item->setWest($color_box);
						$item->setCenter($add_task_link);
						$task_menu_panel->add($item);
					}
				}
			}
			if($count == 0){
				// reset events menu
				$event_text = new Panel();
				$event_text->getStyle()->setWidth("120px");
				$event_text->getStyle()->setFontFamily("verdana, sans-serif");
				$event_text->getStyle()->setFontSize(8);
				$event_text->setAlign("left");
				$event_text->add(new Text("No calendars<br>to add event to."));
				// remove the "Add Event to:" text...
				$comps = $event_menu_panel->getComponents();
				$event_menu_panel->remove($comps[0]);
				// add our new text
				$event_menu_panel->add($event_text);

				// reset tasks menu
				$task_text = new Panel();
				$task_text->getStyle()->setWidth("120px");
				$task_text->getStyle()->setFontFamily("verdana, sans-serif");
				$task_text->getStyle()->setFontSize(8);
				$task_text->setAlign("left");
				$task_text->add(new Text("No calendars<br>to add task to."));
				// remove the "Add Event to:" text...
				$comps = $task_menu_panel->getComponents();
				$task_menu_panel->remove($comps[0]);
				// add our new text
				$task_menu_panel->add($task_text);
			}

			$event_menu = new MenuInitAction($add_event_button, $event_menu_panel);
			$this->doc->addAction($event_menu);

			$task_menu = new MenuInitAction($add_task_button, $task_menu_panel);
			$this->doc->addAction($task_menu);



			//////////////////////////
			// search field		//
			//////////////////////////
			// create add event drop down menu //
			$search_text = new Panel();
			$search_text->getStyle()->setWidth("120px");
			$search_text->getStyle()->setFontFamily("verdana, sans-serif");
			$search_text->getStyle()->setFontSize(8);
			$search_text->setAlign("left");
			$search_text->add(new Text("Search For:"));
			$search_menu_panel = new GridPanel(1);
			$search_menu_panel->setStyle($event_menu_panel->getStyle());
			$search_menu_panel->add($search_text);

			$search_menu_panel->add($this->makeItem("Calendars", "search_calendars", $search_calendars));
			$search_menu_panel->add($this->makeItem("Events", "search_events", $search_events));
			$search_menu_panel->add($this->makeItem("Tasks", "search_tasks", $search_tasks));
			$search_menu_panel->add($this->makeItem("Users", "search_users", $search_users));
			$search_menu_panel->add($this->makeItem("Groups", "search_teams", $search_teams));
			$search_menu_panel->add($this->makeItem("Comments", "search_comments", $search_comments));

			$search_form = new FormPanel("index.php");
			$search_form->addHiddenField("search_calendars", $search_calendars);
			$search_form->addHiddenField("search_events", $search_events);
			$search_form->addHiddenField("search_tasks", $search_tasks);
			$search_form->addHiddenField("search_users", $search_users);
			$search_form->addHiddenField("search_teams", $search_teams);
			$search_form->addHiddenField("search_comments", $search_comments);
			$search_form->setAsGet();
			$search_form->setAlign("right");
			$search_form->addHiddenField("view", "search");
			$text_box = new SmallTextInput();
			$text_box->setName("terms");
			if(isset($data_list["terms"])){
				$terms = $data_list["terms"];
				if(get_magic_quotes_gpc()){
					$terms = stripslashes($terms);
				}
				$text_box->setValue($terms);
			}else if($this->avalanche->getCookie("search_terms")){
				$text_box->setValue($this->avalanche->getCookie("search_terms"));
			}
			$text_box->setStyle(new Style("aurora_search_no_border"));
			$text_box->setSize(14);
			$icon = new Button();
			$icon->setIcon(new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/magnify.gif"));
			$icon->setStyle(new Style("aurora_search_no_border"));
			$text_field = new GridPanel(2);
			$text_field->setStyle(new Style("aurora_search_field"));
			$text_field->add($text_box);
			$text_field->add($icon);
			$search_form->add($text_field);

			$search_menu = new MenuInitAction($icon, $search_menu_panel);
			$this->doc->addAction($search_menu);

			$tip = OsGuiHelper::createToolTip(new Text("Click to filter search results"));
			$menu_action = new ToolTipAction($icon, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			// done creating search field


			$shortcuts->add($add_task_button);
			$shortcuts->add($add_event_button);
			$shortcuts->add($search_form);


			$filler->add($event_menu_panel);
			$filler->add($task_menu_panel);
			$filler->add($search_menu_panel);

			/************************************************************************
			    put it all together
			************************************************************************/


			$my_container->setWest($program_nav);
			$my_container->setCenter($filler);
			$my_container->setEast($shortcuts);

			return new module_bootstrap_data($my_container, "the gui component for the aurora header");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}

	private function makeItem($name, $field_name, $visible){
		// make menu items for search menu
		$check = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/check.gif");
		$check->getStyle()->setWidth("13px");
		$check->getStyle()->setHeight("13px");
		$check->getStyle()->setPaddingRight(2);
		$check->getStyle()->setPaddingLeft(1);
		$check->getStyle()->setPaddingTop(2);
		$check->getStyle()->setPaddingBottom(1);
		$spacer = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/spacer.gif");
		$spacer->setWidth("16");
		$spacer->setHeight("16");
		$search_calendars_link = new Button($name);
		$search_calendars_link->setAlign("left");
		$search_calendars_link->setStyle(new Style("menu_button"));
		$search_calendars_link->getStyle()->setWidth("120px");
		$search_calendars_link->getStyle()->setHeight("16px");
		$check_id = $check->getId();
		$spacer_id = $spacer->getId();
		$search_calendars_link->addAction(new ManualAction("if(xGetDisplay(\"$check_id\")==\"none\"){xDisplayBlock(\"$check_id\");xDisplayNone(\"$spacer_id\");xGetElementById(\"$field_name\").value=\"1\";}else{xDisplayNone(\"$check_id\");xDisplayBlock(\"$spacer_id\");xGetElementById(\"$field_name\").value=\"0\";}"));
		if(!$visible){
			$check->getStyle()->setDisplayNone();
			$spacer->getStyle()->setDisplayBlock();
		}else{
			$check->getStyle()->setDisplayBlock();
			$spacer->getStyle()->setDisplayNone();
		}
		$item = new BorderPanel();
		$icons = new Panel();
		$icons->add($check);
		$icons->add($spacer);
		$icons->getStyle()->setClassname("menu_icon");
		$item->setWest($icons);
		$item->setCenter($search_calendars_link);
		return $item;
	}
}
?>