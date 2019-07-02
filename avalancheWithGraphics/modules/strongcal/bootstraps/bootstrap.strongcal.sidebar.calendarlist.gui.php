<?

class module_bootstrap_strongcal_sidebar_calendarlist extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List to Gui");
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
			$strongcal = $this->avalanche->getModule("strongcal");

			$data = false;
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendars = $data->data();
			/**
			* end initialization and checking
			*/

			/**
			* add the style sheet to the document for this page
			*/

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
			$calendar_panel = new GridPanel(1);
			$calendar_panel->getStyle()->setWidth((130) . "px");
			$list_panel->add($calendar_panel);

			$height = 0;
			$key_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/key.gif");
			$lock_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/lock.gif");
			$blank_icon = new Panel();
			$blank_icon->getStyle()->setWidth("10px");
			$blank_icon->getStyle()->setHeight("10px");

			// $instructions = new Text("click calendar to hide");
			// $instructions->getStyle()->setFontFamily("verdana, sans-serif");
			// $instructions->getStyle()->setFontSize(7);
			// $calendar_panel->add($instructions);

			$hidden_cals = array();
			// first add all non selected(hidden) calendars
			foreach($calendars as $cal){
				if(!$strongcal->selected($cal)){
					$icons = new GridPanel(3);
					if($cal->canWriteName()){
						$icons->add($key_icon);
					}else{
						$icons->add($blank_icon);
					}
					if(!$cal->isPublic()){
						$icons->add($lock_icon);
					}else{
						$icons->add($blank_icon);
					}

					$cal_panel = new BorderPanel();
					$icon = new Panel();
					$icon->setStyle(new Style("aurora_view_icon"));
					$icon->getStyle()->setBackground($cal->color());
					$icons->add($icon);
					$cal_panel->setWest($icons);
					// $cal_name = new Link($cal->name(), "index.php?primary_loader=module_bootstrap_strongcal_hideshowcal_loader&hide=1&cal_id=" . $cal->getId() . $view_text . $subview_text);
					$cal_name = new Link($cal->name(), "javascript:;");
					$this->createCalendarMenu($cal_name, $cal->getId(), $data_list);
					$cal_name->getStyle()->setClassname("aurora_sidebar_text");
					$cal_panel->setCenter($cal_name);
					$calendar_panel->add($cal_panel);
					$height += (int)(15 * ceil(strlen($cal->name()) / 13));
				}else{
					$hidden_cals[] = $cal;
				}
			}
			if($strongcal->canAddCalendar()){
				$link = new Link("[add new]", "index.php?view=manage_cals&subview=add_cal");
				$link->getStyle()->setFontFamily("verdana, sans-serif");
				$link->getStyle()->setFontSize(8);
				$link->getStyle()->setPaddingLeft(33);
				$calendar_panel->add($link);
			}
			// figure out height of non hidden calendar list
			if($height == 0){
				$none_panel = new Panel();
				$none_panel->setWidth("100%");
				$none_panel->setAlign("center");
				$cal_name = new Text("<i>none</i>");
				$cal_name->getStyle()->setClassname("aurora_sidebar_text");
				$none_panel->add($cal_name);
				$height += 15;
				$calendar_panel->add($none_panel);
				// $instructions->getStyle()->setDisplayNone();
			}
			// add in height for instructions or the none
			// add in the height of padding in $list_panel
			$height += 18;

			// if any calendars are hidden...
			if(count($hidden_cals) > 0){
				$height += 15; // for the more link
				$unhide_function = new NewFunctionAction("unhide_cal_list");
				$hide_function = new NewFunctionAction("hide_cal_list");
				$this->doc->addFunction($unhide_function);
				$this->doc->addFunction($hide_function);
				$hidden_height = $height; // add 15 for the more link

				$less_link = new Button();
				$more_link = new Button();
				$more_link->setStyle(new Style());
				$more_link->setIcon(new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $strongcal->folder() . "/gui/os/expandcals.gif"));
				$more_link->getStyle()->setPaddingLeft(16);
				$unhide_function->addAction(new DisplayNoneAction($more_link));
				$unhide_function->addAction(new DisplayBlockAction($less_link));
				$calendar_panel->add($more_link);
				$tip = OsGuiHelper::createToolTip(new Text("Reveal hidden calendars"));
				$menu_action = new ToolTipAction($more_link, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);

				// right now, just add a text as a seperator
				$less_link->setStyle(new Style());
				$less_link->getStyle()->setDisplayNone();
				$less_link->setIcon(new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $strongcal->folder() . "/gui/os/collapsecals.gif"));
				$less_link->getStyle()->setPaddingLeft(16);
				$hide_function->addAction(new DisplayNoneAction($less_link));
				$hide_function->addAction(new DisplayBlockAction($more_link));
				$tip = OsGuiHelper::createToolTip(new Text("Conceal hidden calendars"));
				$menu_action = new ToolTipAction($less_link, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);

				$calendar_panel->add($less_link);
				// now add all the other calendars
				foreach($hidden_cals as $cal){
					$icons = new GridPanel(3);
					if($cal->canWriteName()){
						$icons->add($key_icon);
					}else{
						$icons->add($blank_icon);
					}
					if(!$cal->isPublic()){
						$icons->add($lock_icon);
					}else{
						$icons->add($blank_icon);
					}

					$cal_panel = new BorderPanel();
					$icon = new Panel();
					$icon->setStyle(new Style("aurora_view_icon"));
					$icon->getStyle()->setBackground($cal->color());
					$icons->add($icon);
					$cal_panel->setWest($icons);
					if(isset($view)){
						$view_text = "&view=$view";
					}else{
						$view_text = "";
					}
					if(isset($subview)){
						$subview_text = "&subview=$subview";
					}else{
						$subview_text = "";
					}
					if(isset($data_list["event_id"])){
						$view_text .= "&event_id=" . $data_list["event_id"];
					}
					if(isset($data_list["task_id"])){
						$view_text .= "&task_id=" . $data_list["task_id"];
					}
					if(isset($data_list["terms"])){
						$terms = $data_list["terms"];
						if(get_magic_quotes_gpc()){
							$terms = stripslashes($data_list["terms"]);
						}
						$view_text .= "&terms=" . urlencode($terms);
					}
					//$cal_name = new Link($cal->name(), "index.php?primary_loader=module_bootstrap_strongcal_hideshowcal_loader&show=1&cal_id=" . $cal->getId() . $view_text . $subview_text);
					$cal_name = new Link($cal->name(), "javascript:;");
					$this->createCalendarMenu($cal_name, $cal->getId(), $data_list);
					$cal_name->getStyle()->setClassname("aurora_sidebar_text");
					$cal_panel->setCenter($cal_name);
					$calendar_panel->add($cal_panel);
					// make the panel hidden
					$cal_panel->getStyle()->setDisplayNone();
					$unhide_function->addAction(new DisplayBlockAction($cal_panel));
					$hide_function->addAction(new DisplayNoneAction($cal_panel));
					$hidden_height += 15;
				}
				// now add the actions to unhide them
				$more_link->addAction(new CallFunctionAction("unhide_cal_list"));
				$less_link->addAction(new CallFunctionAction("hide_cal_list"));
				if($hidden_height > $cal_list_height) $hidden_height = $cal_list_height;
				$more_link->addAction(new HeightAction($list_panel, $hidden_height));
				if($height > $cal_list_height) $height = $cal_list_height;
				$less_link->addAction(new HeightAction($list_panel, $height));
			}

			if($height > $cal_list_height) $height = $cal_list_height;
			$list_panel->getStyle()->setHeight($height);
			$list_panel->getStyle()->setDisplayBlock();


			$calendar_text = new Text("Calendars");
			$calendar_text->getStyle()->setFontSize(8);
			$calendar_text->getStyle()->setPaddingLeft(4);
			$calendar_text->getStyle()->setPaddingTop(4);

			$manage = new Panel();
			$hide_cals_button = new Button();
			$show_cals_button = new Button();
			$icon = new IconWithText($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/calendarlist.gif", $calendar_text);
			$icon->getStyle()->setWidth($open_width . "px");
			$icon->getStyle()->setHeight($open_height . "px");
			$icon->setAlign("left");
			$hide_cals_button->setIcon($icon);
			$hide_cals_button->setStyle(new Style("sidebar_button"));
			$hide_cals_button->getStyle()->setWidth(152);
			$hide_cals_button->getStyle()->setHeight($button_height);
			$hide_cals_button->getStyle()->setDisplayBlock();
			$hide_cals_button->addAction(new DisplayNoneAction($list_panel));
			$hide_cals_button->addAction(new DisplayNoneAction($hide_cals_button));
			$hide_cals_button->addAction(new DisplayNoneAction($manage));
			$hide_cals_button->addAction(new DisplayBlockAction($show_cals_button));
			$hide_cals_button->addAction(new SetCookieAction("cook_sidebar_cals", "close"));


			$show_cals_button->setIcon($icon);
			$show_cals_button->setStyle(new Style("sidebar_button"));
			$show_cals_button->getStyle()->setWidth(152);
			$show_cals_button->getStyle()->setHeight($button_height);
			$show_cals_button->getStyle()->setDisplayNone();
			$show_cals_button->addAction(new DisplayNoneAction($show_cals_button));
			$show_cals_button->addAction(new DisplayBlockAction($hide_cals_button));
			$show_cals_button->addAction(new DisplayInlineAction($manage));
			$show_cals_button->addAction(new DisplayBlockAction($list_panel));
			$show_cals_button->addAction(new SetCookieAction("cook_sidebar_cals", "open"));

			if(isset($data_list["cook_sidebar_cals"]) && $data_list["cook_sidebar_cals"] == "close"){
				$list_panel->getStyle()->setDisplayNone();
				$hide_cals_button->getStyle()->setDisplayNone();
				$manage->getStyle()->setDisplayNone();
				$show_cals_button->getStyle()->setDisplayBlock();
			}

			$buttons = new Panel();
			$buttons->add($hide_cals_button);
			$buttons->add($show_cals_button);

			$tip = OsGuiHelper::createToolTip(new Text("Collapse or expand the calendars menu"));
			$menu_action = new ToolTipAction($buttons, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);


			$manage->getStyle()->setClassname("text_align_center");
			$manage->setAlign("center");
			$manage_link = new Link("[ manage calendars ]", "index.php?view=manage_cals");
			$manage_link->setStyle(new Style("aurora_sidebar_link"));
			$manage->add($manage_link);



			$main_panel->add($buttons);
			$main_panel->add($list_panel);
			$main_panel->add($manage);
			$main_panel->setAlign("center");

			return new module_bootstrap_data($main_panel, "a gui component for the event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	// creates the popup menu for a calendar
	private function createCalendarMenu($trigger, $cal_id, $data_list){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = new module_bootstrap_data($data_list, "form input");;
		$module = new StrongcalCalendarMenu($this->avalanche, $this->doc, $trigger, $cal_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}
}
?>