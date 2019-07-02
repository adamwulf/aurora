<?

class module_bootstrap_strongcal_sidebar extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
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
			$open_width = 152;
			$open_height = 18;
			$button_height = 18;
			$button_width = 152;
			$request = $data->data();
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");

			if(!isset($request["date"])){
				$request["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $request["date"];
			if(!isset($request["view"])){
				$request["view"] = $strongcal->getUserVar("highlight");
			}
			$view = $request["view"];
			if(!($view == "day" || $view == "week" || $view == "month")){
				$view = $strongcal->getUserVar("highlight");
			}
			if(!($view == "day" || $view == "week" || $view == "month")){
				$view = "month";
			}

			/**
			* end initialization and checking
			*/


			/**
			* add the style sheet to the document for this page
			*/
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/sidebar_style.css"));
			$this->doc->addStyleSheet($css);

			$container = new SidebarPanel();
			$open_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/opensidebar.gif");
			$close_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/sidebarcontrol.gif");

			$tip = OsGuiHelper::createToolTip(new Text("Collapse the sidebar"));
			$menu_action = new ToolTipAction($close_icon, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$tip = OsGuiHelper::createToolTip(new Text("Expand the sidebar"));
			$menu_action = new ToolTipAction($open_icon, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);


			$container->setOpenIcon($open_icon);
			$container->setCloseIcon($close_icon);
			$container->setOpenWidth(40);
			$container->setOpenHeight(15);
			$container->setCloseWidth(186);
			$container->setCloseHeight(15);
			$container->setClosedBackgroundImage($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/closedsidebarbg.gif");
			$container->getStyle()->setClassname("sidebar");
			$container->getStyle()->setBackgroundImage($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/sidebarbg.gif");
			$container->getStyle()->repeatBackgroundVertically();
			//$container->getStyle()->setWidth($open_width . "px");
			$container->setHeight("100%");
			//$container->getStyle()->setPaddingTop(2);
			//$container->getStyle()->setBorderWidth(1);
			$container->getStyle()->setBorderStyle("solid");
			$container->getStyle()->setBorderColor("black");

			$main_panel = new GridPanel(1);
			$main_panel->getStyle()->setWidth("100%");
			$main_panel->setValign("top");

			$cal_list_panel = new GridPanel(1);
			$cal_list_panel->getStyle()->setWidth("100%");
			$cal_list_panel->getCellStyle()->setPaddingTop(4);

			$task_list_panel = new GridPanel(1);
			$task_list_panel->getStyle()->setWidth("100%");
			$task_list_panel->getCellStyle()->setPaddingTop(4);

			$button_panel = new GridPanel(1);
			$button_panel->getStyle()->setWidth("100%");

			// make the navigation
			$nav_panel = new GridPanel(1);
			$nav_panel->getStyle()->setWidth("152px");
			//$nav_panel->setAlign("center");
			$nav_panel->getStyle()->setMarginTop(4);
			$nav_panel->getStyle()->setMarginLeft(4);
			$nav_text = new Text("Navigation");
			$nav_text->getStyle()->setFontSize(8);
			$nav_text->getStyle()->setPaddingLeft(4);
			$nav_text->getStyle()->setPaddingTop(4);

			$y = (int)substr($date, 0, 4);
			$m = (int)substr($date, 5, 2);
			$month_panel = new MonthPanel($y, $m, $view, "index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=day&date=%y-%m-%d","index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=week&date=%y-%m-%d","index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=month&date=%y-%m-%d");
			$month_panel->getStyle()->setWidth(152);

			$list_panel = new ScrollPanel(1);
			$list_panel->getStyle()->setWidth("152px");
			$list_panel->getStyle()->setClassname("border_top");
			//$list_panel->getStyle()->setMarginLeft(4);
			//$list_panel->getStyle()->setMarginRight(4);
			$list_panel->getStyle()->setMarginBottom(4);
			$list_panel->getStyle()->setBackground("#FFFFFF");
			$list_panel->setAlign("left");
			$list_panel->add($month_panel);

			$manage = new Panel();
			$hide_cals_button = new Button();
			$show_cals_button = new Button();
			$icon = new IconWithText($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/calendarlist.gif", $nav_text);
			$icon->getStyle()->setWidth("152px");
			$icon->getStyle()->setHeight("18px");
			$icon->setAlign("left");
			$hide_cals_button->setIcon($icon);
			$hide_cals_button->setStyle(new Style("sidebar_button"));
			$hide_cals_button->getStyle()->setWidth(152);
			$hide_cals_button->getStyle()->setHeight($button_height);
			$hide_cals_button->getStyle()->setDisplayBlock();
			$hide_cals_button->addAction(new DisplayNoneAction($list_panel));
			$hide_cals_button->addAction(new DisplayNoneAction($hide_cals_button));
			$hide_cals_button->addAction(new DisplayBlockAction($show_cals_button));
			$hide_cals_button->addAction(new SetCookieAction("cook_sidebar_nav", "close"));


			$show_cals_button->setIcon($icon);
			$show_cals_button->setStyle(new Style("sidebar_button"));
			$show_cals_button->getStyle()->setWidth(152);
			$show_cals_button->getStyle()->setHeight($button_height);
			$show_cals_button->getStyle()->setDisplayNone();
			$show_cals_button->addAction(new DisplayNoneAction($show_cals_button));
			$show_cals_button->addAction(new DisplayBlockAction($hide_cals_button));
			$show_cals_button->addAction(new DisplayBlockAction($list_panel));
			$show_cals_button->addAction(new SetCookieAction("cook_sidebar_nav", "open"));

			if(isset($data_list["cook_sidebar_nav"]) && $data_list["cook_sidebar_nav"] == "close"){
				$list_panel->getStyle()->setDisplayNone();
				$hide_cals_button->getStyle()->setDisplayNone();
				$show_cals_button->getStyle()->setDisplayBlock();
			}

			$buttons = new Panel();
			$buttons->add($hide_cals_button);
			$buttons->add($show_cals_button);

			$tip = OsGuiHelper::createToolTip(new Text("Collapse or expand the navigation menu"));
			$menu_action = new ToolTipAction($buttons, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$nav_panel->add($buttons);
			$nav_panel->add($list_panel);
			$nav_panel->getStyle()->setMarginBottom(5);

			// make the calendar list
			$module = new module_bootstrap_strongcal_sidebar_calendarlist($this->avalanche, $this->doc);
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$calendar_list = $runner->run($data);
			$calendar_list = $calendar_list->data();
			$cal_list_panel->add($calendar_list);
			$cal_list_panel->getStyle()->setMarginBottom(5);

			// make the task list
			$module = new module_bootstrap_taskman_sidebar_tasklist($this->avalanche, $this->doc);
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$task_list = $runner->run($data);
			$task_list = $task_list->data();
			$task_list_panel->add($task_list);
			$task_list_panel->getStyle()->setMarginBottom(5);

			$main_panel->add($nav_panel);
			$main_panel->add($cal_list_panel);
			$main_panel->add($task_list_panel);
			$main_panel->add($button_panel);
			$main_panel->setAlign("left");
			$container->add($main_panel);
			$container->setAlign("left");

			$this->doc->addAction($month_panel->getDisplayAction());
			$this->doc->addJS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/date.js"));
			$this->doc->addStyleSheet(new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/date_style.css")));
			return new module_bootstrap_data($container, "a gui component for the event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>