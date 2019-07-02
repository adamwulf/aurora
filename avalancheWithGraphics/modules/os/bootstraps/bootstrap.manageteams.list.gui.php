<?

class module_bootstrap_manageteams_list_gui extends module_bootstrap_module{

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
			$os = $this->avalanche->getModule("os");
			$strongcal = $this->avalanche->getModule("strongcal");

			/** end initializing the input */			

			/**
			 * get the list of calendars
			 */
			$group_obj_list = $this->avalanche->getAllUsergroups($this->avalanche->loggedInHuh());
			$filtered_list = array();
			foreach($group_obj_list as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC ||
				   $group->type() == avalanche_usergroup::$PERSONAL &&
				   $group->author() == $this->avalanche->getActiveUser()){
					$filtered_list[] = $group;
				}
			}
			$group_obj_list = $filtered_list;
			
			$sorter = new MDASorter();
			$group_obj_list = $sorter->sortASC($group_obj_list, new OSUsergroupComparator());
						/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/manage_cals.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			
			$nobg = new Style("nobg");
			$info_style = new Style("info");
			
			$title_style = new Style();
			$title_style->setFontFamily("verdana, sans-serif");
			$title_style->setFontSize(10);
			$title_style->setFontWeight("bold");

			/************************************************************************
			    initialize panels
			************************************************************************/
			
			$cal_list_inner_panel = new GridPanel(1);
			$cal_list_panel = new GridPanel(1);
			$scroll_panel = new ScrollPanel();
			$cal_info_panel = new BorderPanel();
			
			$left_panel = new Panel();
			
			$left_button_panel = new BorderPanel();
			
			$width = "450px";
			
			/************************************************************************
			************************************************************************/
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			$cal_list_inner_panel->setWidth("100%");
			
			$cal_list_panel->getStyle()->setWidth($width);
			$cal_list_panel->getStyle()->setHeight("250px");
			$cal_list_panel->getStyle()->setBorderWidth(1);
			$cal_list_panel->getStyle()->setBorderStyle("solid");
			$cal_list_panel->getStyle()->setBorderColor("black");
			
			$scroll_panel->getStyle()->setWidth($width);
			$scroll_panel->getStyle()->setHeight("250px");
			$scroll_panel->add($cal_list_inner_panel);
			
			$left_button_panel->getStyle()->setWidth($width);
			$left_panel->getStyle()->setWidth($width);



			$current_row = new ErrorPanel(new Text("Public Groups"));
			$current_row->getStyle()->setPadding(4);
			$current_row->getStyle()->setFontFamily("verdana, sans-serif");
			$current_row->getStyle()->setFontWeight("bold");
			$current_row->getStyle()->setFontSize(10);
			$cal_list_inner_panel->add($current_row);

			// add headers
			$current_row = new BorderPanel();
			$name = new Text("Name");
			$name->getStyle()->setClassname("aurora_sidebar_text");
			$name = new ErrorPanel($name);
			$name->getStyle()->setPadding(4);
			$name->getStyle()->setWidth("120px");
			$current_row->setWest($name);
			$desc = new Text("Description");
			$desc->getStyle()->setClassname("aurora_sidebar_text");
			$desc->getStyle()->setPaddingLeft(20);
			$current_row->setCenter($desc);
			
			$cal_list_inner_panel->add($current_row);

			
			$count_shown = 0;
			foreach($group_obj_list as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC){
					$count_shown++;
					$current_row = new BorderPanel();
					$cal_name = new Link($group->name(), "index.php?view=manage_teams&team_id=" . $group->getId());
					$cal_name->getStyle()->setClassname("aurora_sidebar_text");
					$cal_name->getStyle()->setFontColor("black");
					
					$name = new Panel();
					$name->getStyle()->setWidth("120px");
					$name->getStyle()->setPaddingLeft(10);
					$name->setAlign("left");
					$name->add($cal_name);
					
					$desc = strlen($group->description()) == 0 ? "<i>no description</i>" : $group->description();
					
					$group_desc = strip_tags($desc, "<b><i><u>");
					$group_desc = (strlen($group_desc) > 50) ? (substr($group_desc, 0, 47) . "...") : $group_desc;
					$group_desc = new Link($group_desc, "index.php?view=manage_teams&team_id=" . $group->getId());
					$group_desc->getStyle()->setClassname("aurora_sidebar_text");
					
					$current_row->setWest($name);
					$current_row->setCenter($group_desc);
					$current_row->setWidth("100%");
					
					$current_row->setStyle(new Style("manage_cal_row"));
					$current_row->getStyle()->setHandCursor();
					$current_row->addAction(new LoadPageAction("index.php?view=manage_teams&team_id=" . $group->getId()));
					
					$cal_list_inner_panel->add($current_row);
				}
			}

			if($count_shown == 0){
				$current_row = new Panel();
				$cal_name = new Text("<i>none</i>");
				$cal_name->getStyle()->setClassname("aurora_sidebar_text");
				$current_row->add($cal_name);
				
				$current_row->setWidth("100%");
				$current_row->getStyle()->setPadding(3);
				$current_row->setAlign("center");
				$cal_list_inner_panel->add($current_row);
			}
			
			$current_row = new ErrorPanel(new Text("Private Groups"));
			$current_row->getStyle()->setPadding(4);
			$current_row->getStyle()->setFontFamily("verdana, sans-serif");
			$current_row->getStyle()->setFontWeight("bold");
			$current_row->getStyle()->setFontSize(10);
			$cal_list_inner_panel->add($current_row);

			$count_shown = 0;
			foreach($group_obj_list as $group){
				if($group->type() == avalanche_usergroup::$PERSONAL){
					$count_shown++;
					$current_row = new BorderPanel();
					$cal_name = new Link($group->name(), "index.php?view=manage_teams&team_id=" . $group->getId());
					$cal_name->getStyle()->setClassname("aurora_sidebar_text");
					$cal_name->getStyle()->setFontColor("black");
					
					$name = new Panel();
					$name->getStyle()->setWidth("120px");
					$name->getStyle()->setPaddingLeft(10);
					$name->setAlign("left");
					$name->add($cal_name);
					
					$desc = strlen($group->description()) == 0 ? "<i>no description</i>" : $group->description();

					$group_desc = strip_tags($desc, "<b><i><u>");
					$group_desc = (strlen($group_desc) > 50) ? (substr($group_desc, 0, 47) . "...") : $group_desc;
					$group_desc = new Link($group_desc, "index.php?view=manage_teams&team_id=" . $group->getId());
					$group_desc->getStyle()->setClassname("aurora_sidebar_text");
					
					$current_row->setWest($name);
					$current_row->setCenter($group_desc);
					$current_row->setWidth("100%");
					
					$current_row->setStyle(new Style("manage_cal_row"));
					$current_row->getStyle()->setHandCursor();
					$current_row->addAction(new LoadPageAction("index.php?view=manage_teams&team_id=" . $group->getId()));
					
					$cal_list_inner_panel->add($current_row);
				}
			}

			if($count_shown == 0){
				$current_row = new Panel();
				$cal_name = new Text("<i>none</i>");
				$cal_name->getStyle()->setClassname("aurora_sidebar_text");
				$current_row->add($cal_name);
				
				$current_row->setWidth("100%");
				$current_row->getStyle()->setPadding(3);
				$current_row->setAlign("center");
				$cal_list_inner_panel->add($current_row);
			}


			$add_button = new Button("add new");
			$add_button->getStyle()->setClassname("manage_cals_button");
			$add_button->addAction(new LoadPageAction("index.php?view=manage_teams&subview=add_team"));			
			$left_button_panel->setCenter(new Text("&nbsp;"));
			if($strongcal->canAddCalendar()){
				$left_button_panel->setEast($add_button);
			}

			/** $left_panel is the calendar list **/
			/** done making calendar list for the left side **/
			
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$title = "Group Management";
			$header->add(new Text($title));
			
			$text = new Text("Please select a group by clicking on its name");
			$text->getStyle()->setPaddingLeft(10);
			$text->getStyle()->setFontFamily("verdanan, sans-serif");
			$text->getStyle()->setFontSize(8);
			$text->getStyle()->setFontColor("black");
			
			$instructions = new Panel();
			$instructions->setWidth("100%");
			$instructions->setStyle(new Style("preferences_buttons_panel"));
			$instructions->add($text);
			
			
			$cal_list_panel->add($header);
			$cal_list_panel->add($instructions);
			$cal_list_panel->add($scroll_panel);
			
			$left_panel->add($cal_list_panel);
			$left_panel->add($left_button_panel);
			
			$final_panel = new Panel();
			$final_panel->add($left_panel);
			$final_panel->setWidth("100%");
			$final_panel->setAlign("center");
			$final_panel->getStyle()->setPaddingTop(30);
			$final_panel->getStyle()->setPaddingBottom(30);
			return new module_bootstrap_data($final_panel, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>