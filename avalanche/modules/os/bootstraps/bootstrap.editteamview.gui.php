<?

class module_bootstrap_os_editteamview_gui extends module_bootstrap_module{

	/** the avalanche object */
	private $avalanche;
	/** the document we're in */
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		
		$this->setName("Edit team view for Avalanche");
		$this->setInfo("edits an team to avalanche.");
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

			/************************************************************************
			get modules
			************************************************************************/
			$strongcal = $this->avalanche->getModule("strongcal");
			$buffer = $this->avalanche->getSkin("buffer");

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/add_cal_style.css"));
			$this->doc->addStyleSheet($css);

			if(!isset($data_list["team_id"])){
				throw new IllegalArgumentException("team_id is required form input for edit team");
			}
			$team_id = (int) $data_list["team_id"];
			$main_team_obj = $this->avalanche->getUsergroup($team_id);
			
			if(!is_object($main_team_obj)){
				throw new Exception("cannot find team $team_id");
			}
			
			
			$default_name = $main_team_obj->name();
			$default_desc = $main_team_obj->description();
			$default_key  = $main_team_obj->keywords();
			
			
			$error = false;
			try{
				// check form input to see if we need to edit a team....
				if(isset($data_list["submit"])){
					if(!isset($data_list["name"]) ||
					   !isset($data_list["description"]) ||
					   !isset($data_list["keywords"])){
						throw new IllegalArgumentException("arguments \$name and \$color must be sent in via GET or POST to add a calendar");
					}else{
						$text = new SmallTextInput();
						$text->setName("name");
						$text->loadFormValue($data_list);
						$name = $text->getValue();
							
						$text = new SmallTextInput();
						$text->setName("description");
						$text->loadFormValue($data_list);
						$desc = $text->getValue();
						
						$text = new SmallTextInput();
						$text->setName("keywords");
						$text->loadFormValue($data_list);
						$key = $text->getValue();
						
						$default_name = $name;
						$default_desc = $desc;
						$default_key  = $key;
						if(!strlen($name) > 0){
							throw new IllegalArgumentException("Team name must not be blank when trying to add team");
						}
						$team = $this->avalanche->getUsergroup($team_id);
						
						$team->name($name);
						$team->description($desc);
						$team->keywords($key);
						
						throw new RedirectException("index.php?view=manage_teams&team_id=" . $team->getId());
					}
				}
			}catch(IllegalArgumentException $e){
				$error = $e;
			}
			
			
			

			/************************************************************************
			    initialize panels
			************************************************************************/
			$cal_info_panel = new BorderPanel();
			$cal_info_panel->getStyle()->setWidth("450px");
			$cal_info_panel->getStyle()->setHeight("450px");
			$cal_info_panel->getStyle()->setBorderWidth(1);
			$cal_info_panel->getStyle()->setBorderStyle("solid");
			$cal_info_panel->getStyle()->setBorderColor("black");

			$my_form = new FormPanel("index.php");
			$my_form->addHiddenField("view", "manage_teams");
			$my_form->addHiddenField("subview", "edit_team");
			$my_form->addHiddenField("team_id", (string)$team_id);
			$my_form->addHiddenField("submit", "1");
			$my_form->setAsGet();
			$my_container = new GridPanel(1);
			
			$title_choose = new GridPanel(2);
			$error_msg = new GridPanel(1);
			$description_main = new GridPanel(1);
			$button_panel = new GridPanel(2);
			
			$button_row = new Panel();
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			$my_container->setValign("top");
			
			$description_main->getStyle()->setClassname("edit");
			$description_main->getCellStyle()->setPadding(4);
			$description_main->getStyle()->setWidth("450px");
			
			$title_choose->getStyle()->setClassname("edit");
			$title_choose->getStyle()->setHeight("50px");
			$title_choose->getStyle()->setWidth("450px");
			$title_choose->getCellStyle()->setPadding(4);
			$title_choose->setValign("top");
			
			$error_msg->getStyle()->setClassname("edit");
			$error_msg->getStyle()->setHeight("50px");
			$error_msg->getStyle()->setWidth("450px");
			$error_msg->getStyle()->setFontColor("#CC0000");
			$error_msg->getCellStyle()->setPadding(4);
			$error_msg->setValign("top");
			$error_msg->getStyle()->setDisplayNone();
			
			$button_panel->getStyle()->setClassname("edit");
			$button_panel->getStyle()->setHeight("50px");
			$button_panel->getStyle()->setWidth("450px");
			$button_panel->getCellStyle()->setPadding(4);
			$button_panel->setValign("middle");
			$button_panel->setAlign("center");
			
			/************************************************************************
			    add necessary text and html
			************************************************************************/
			
			$cal_name_input = new SmallTextInput();
			$cal_name_input->setName("name");
			$cal_name_input->setSize(30);
			$cal_name_input->getStyle()->setClassname("calendar_input");
			$cal_name_input->setValue($default_name);
			
			$the_type = new Text($main_team_obj->display_type());
			
			$menu = new Panel();
			$menu->getStyle()->setWidth("160px");
			$menu->getStyle()->setPadding(4);
			$menu->getStyle()->setBorderColor("black");
			$menu->getStyle()->setBorderWidth(1);
			$menu->getStyle()->setBorderStyle("solid");
			$menu->getStyle()->setBackground("#EEEEEE");
			$menu->getStyle()->setFontFamily("verdana, sans-serif");
			$menu->getStyle()->setFontSize(10);
			$menu->add(new Text("<b>Public</b> groups are visible to all users on the system.<br><br><b>Personal</b> groups are visible only to you."));
			
			$type = new Link("Type (?):", "javascript:;");
			$type->getStyle()->setFontColor("#000000");
			
			$this->createMenu($type, $menu);
			
			if(is_object($error)){
				$error_msg->getStyle()->setDisplayBlock();
				$error_msg->add(new Text($error->getMessage()));
			}
			
			$title_choose->add(new Text("Group Name:"));
			$title_choose->add($type);
			$title_choose->add($cal_name_input);
			$title_choose->add($the_type);
			
			$description_input = new TextAreaInput();
			$description_input->setName("description");
			$description_input->setCols(40);
			$description_input->setRows(5);
			$description_input->getStyle()->setClassname("calendar_input");
			$description_input->setValue($default_desc);
			$description_main->add(new Text("Group Description: "));
			$description_main->add($description_input);
			
			$keyword_input = new TextAreaInput();
			$keyword_input->setName("keywords");
			$keyword_input->setCols(40);
			$keyword_input->setRows(3);
			$keyword_input->getStyle()->setClassname("calendar_input");
			$keyword_input->setValue($default_key);
			$description_main->add(new Text("Keywords: "));
			$description_main->add($keyword_input);
			
			$back = new ButtonInput("Cancel");
			$back->getStyle()->setBorderWidth(1);
			$back->getStyle()->setBorderColor("black");
			$back->getStyle()->setBorderStyle("solid");
			$back->addClickAction(new LoadPageAction("index.php?view=manage_teams&team_id=$team_id"));

			$button_panel->add(new Text("<input type='submit' value='Save' class='go_button'>"));
			$button_panel->add($back);
			/************************************************************************
			put it all together
			************************************************************************/
			
			$my_container->add($error_msg);
			$my_container->add($title_choose);
			$my_container->add($description_main);
			$my_container->add($button_panel);
			
			$my_form->add($my_container);
			
			$header_text = new Text("Edit Group");
			$header_text->getStyle()->setClassname("page_header");
			$header = new ErrorPanel($header_text);
			$header->getStyle()->setHeight("30px");
			
			$cal_info_panel->setValign("top");
			$cal_info_panel->setNorth($header);
			$cal_info_panel->setCenter($my_form);
			$content_panel = new ErrorPanel($cal_info_panel);
			$content_panel->getStyle()->setPaddingTop(30);
			$content_panel->getStyle()->setPaddingBottom(30);

			return new module_bootstrap_data($content_panel, "a gui component for the add calendar view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
	
	private function createMenu($trigger, $menu){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!$menu instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		
		$menu_panel = new Panel();
		$menu_panel->add($menu);
		$menu_panel->setStyle(new Style("xMenu"));
		$this->doc->addHidden($menu_panel);
		$trigger->getStyle()->setClassname("xTrigger");
		$menu_action = new MenuInitAction($trigger, $menu_panel);
		$this->doc->addAction($menu_action);
	}
}
?>