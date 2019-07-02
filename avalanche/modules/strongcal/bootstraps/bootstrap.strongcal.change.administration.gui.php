<?

class module_bootstrap_strongcal_change_administration_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $cal;
	
	function __construct($avalanche, Document $doc, module_strongcal_calendar $calendar){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora visibility permission options to HTML");
		$this->setInfo("this module displays information for the visibility options of a clalendar.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->cal = $calendar;
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

			$main_cal_obj = $this->cal;
			
			try{
				// update the permission
				if(isset($data_list["new_val"]) && isset($data_list["subsubview"]) && "name" == $data_list["subsubview"]){
					$new_val = $data_list["new_val"];
					if(!isset($data_list["team_id"]) || !is_array($data_list["team_id"])){
						throw new IllegalArgumentException("team_id must be an array sent in to change permission");
					}
					$team_ids = $data_list["team_id"];
					foreach($team_ids as $team_id){
						$group = $this->avalanche->getUsergroup($team_id);
						if($group->type() == avalanche_usergroup::$PUBLIC || $group->type() == avalanche_usergroup::$USER ||
							   $group->type() == avalanche_usergroup::$PERSONAL && $group->author() == $this->avalanche->getActiveUser()){
							   if(!$main_cal_obj->isPublic()){
								   $main_cal_obj->isPublic(true);
							   }
							if($new_val == "rw" || $new_val == "r"){
								$main_cal_obj->updatePermission("name", $new_val, $group->getId());
							}else{
								throw new IllegalArgumentException("new_val must be either r, rw, or the empty string");
							}
						}
					}
					throw new RedirectException("index.php?view=manage_cals&subview=share&subsubview=name&cal_id=" . $main_cal_obj->getId());
				}
			}catch(IllegalArgumentException $e){
			}
			
			$nobg = new Style("nobg");
			$info_style = new Style("info");
			
			/************************************************************************
			    initialize panels
			************************************************************************/
			/**
			cal info should have whatever you want it to have. at the very least the owner (maybe his avatar, too)
			the calendar color, the calendar name, and those three buttons i've got included.
			**/
			
			// get the header
			$module = new module_bootstrap_strongcal_calendar_header_gui($this->avalanche, $this->doc, $main_cal_obj);
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$data = $runner->run(false);
			$cal_info_header = $data->data();
			
			$cal_info = new GridPanel(1);
			$cal_info->getStyle()->setWidth("100%");
			$temp_panel = new GridPanel(2);
			$temp_panel->setAlign("center");
			$temp_panel->setValign("top");
			$temp_panel->setStyle(new Style("content_font"));
			$normal_cell_style = new Style();
			$normal_cell_style->setPaddingLeft(4);
			$normal_cell_style->setPaddingRight(4);
			$border_cell_style = clone $normal_cell_style;
			$border_cell_style->setClassname("list_border");
			
			$list_style = new Style();
			$list_style->setWidth("276px");
			$item_style = new Style("item_style");
			$hide_panel = new GridPanel(1);
			$hide_panel->setStyle(clone $list_style);
			$hide_panel->setCellStyle($item_style);
			$busy_panel = new GridPanel(1);
			$busy_panel->setStyle(clone $list_style);
			$busy_panel->setCellStyle($item_style);
			$groups = $this->avalanche->getAllUsergroups();


			$hide_b_panel = new Panel();
			$hide_b_panel->setWidth("100%");
			$hide_b_panel->setAlign("center");
			$busy_b_panel = new GridPanel(2);
			$busy_b_panel->setWidth("100%");
			$busy_b_panel->setAlign("center");
			
			$new_val_1 = new HiddenInput();
			$new_val_1->setName("new_val");
			
			$new_val_2 = new HiddenInput();
			$new_val_2->setName("new_val");
			
			$new_val_3 = new HiddenInput();
			$new_val_3->setName("new_val");
			
			$new_val_4 = new HiddenInput();
			$new_val_4->setName("new_val");
			
			$hide_form = new FormPanel("index.php");
			$hide_form->setName("name_hide");
			$busy_form = new FormPanel("index.php");
			$busy_form->setName("name_manage");
			
			$button = new Button("<");
			$button->setStyle(new Style("sharing_button"));
			$b = clone $button;
			$b->addAction(new SetValueAction($new_val_2, "r"));
			$b->addAction(new ManualAction("xGetElementById(\"" . $busy_form->getName() . "\").submit();"));
			$busy_b_panel->add($b);
			
			$button = new Button(">");
			$button->setStyle(new Style("sharing_button"));
			$b = clone $button;
			$b->addAction(new SetValueAction($new_val_1, "rw"));
			$b->addAction(new ManualAction("xGetElementById(\"" . $hide_form->getName() . "\").submit();"));
			$hide_b_panel->add($b);
			
			$hide_panel->add($hide_b_panel);
			$busy_panel->add($busy_b_panel);
			
			
			foreach($groups as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC || $group->type() == avalanche_usergroup::$USER && $group->getId() != -$main_cal_obj->author() ||
					   $group->type() == avalanche_usergroup::$PERSONAL && $group->author() == $this->avalanche->getActiveUser()){
					if($main_cal_obj->canWriteFields(array($group))){
						$entry = new BorderPanel();
						$entry->setStyle(new Style("sharing_item_style"));
						$entry->getStyle()->setHandCursor();
						$name = new Panel();
						$name->setStyle(new Style("sharing_item_text"));
						$entry->setCenter($name);
						if($group->type() == avalanche_usergroup::$USER){
							$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/users.png");
							$groups_icon->getStyle()->setWidth(14);
							$groups_icon->getStyle()->setHeight(22);
						}else{
							$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/groups.png");
							$groups_icon->getStyle()->setWidth(17);
							$groups_icon->getStyle()->setHeight(22);
						}
						$name->add($groups_icon);
						$t = new Text($group->name());
						$t->getStyle()->setPaddingLeft(3);
						$name->add($t);
						$check = new CheckInput();
						$check->setName("team_id[]");
						$check->setValue((string)$group->getId());
						$entry->addAction(new ToggleCheckedAction($check));
						$check->addClickAction(new ToggleCheckedAction($check));
						$entry->setWest($check);
						$busy_panel->add($entry);
					}else if($main_cal_obj->canReadEntries(array($group))){
						$entry = new BorderPanel();
						$entry->setStyle(new Style("sharing_item_style"));
						$entry->getStyle()->setHandCursor();
						$name = new Panel();
						$name->setStyle(new Style("sharing_item_text"));
						$entry->setCenter($name);
						if($group->type() == avalanche_usergroup::$USER){
							$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/users.png");
							$groups_icon->getStyle()->setWidth(14);
							$groups_icon->getStyle()->setHeight(22);
						}else{
							$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/groups.png");
							$groups_icon->getStyle()->setWidth(17);
							$groups_icon->getStyle()->setHeight(22);
						}
						$name->add($groups_icon);
						$t = new Text($group->name());
						$t->getStyle()->setPaddingLeft(3);
						$name->add($t);
						$check = new CheckInput();
						$check->setName("team_id[]");
						$check->setValue((string)$group->getId());
						$entry->addAction(new ToggleCheckedAction($check));
						$check->addClickAction(new ToggleCheckedAction($check));
						$entry->setWest($check);
						$hide_panel->add($entry);
					}
				}
			}
			
			$hide_panel = $this->setUpForm($hide_form, $hide_panel, $main_cal_obj, $new_val_1);
			$busy_panel = $this->setUpForm($busy_form, $busy_panel, $main_cal_obj, $new_val_2);
			
			$temp_panel->add(new Text("<b>Normal</b>"), $border_cell_style);
			$temp_panel->add(new Text("<b>Manage Calendar</b>"), $normal_cell_style);
			$temp_panel->add($hide_panel, $border_cell_style);
			$temp_panel->add($busy_panel, $normal_cell_style);
			
			$title = new Text("Calendar Administration");
			$title->setStyle(new Style("content_title"));
			$cal_info->add($title);
			$text = new Text("Here you can decide which users or groups are allowed to manage your calendar and decide sharing permissions of other users and groups. For a user or group to be eligable for this permission, the Event Transparency for that user or group must be set to at least \"Show All.\"");
			$t = new Panel();
			$t->setValign("top");
			$t->add($text);
			$t->setWidth("100%");
			$green_style = new Style("content_font");
			$green_style->setPadding(3);
			$green_style->setMarginBottom(8);
			$green_style->setHeight("60px");
			$green_style->setBackground("#C7D7C4");
			$green_style->setBorderWidth(1);
			$green_style->setBorderColor("#7DAD73");
			$green_style->setBorderStyle("solid");
			
			$t->setStyle($green_style);
			$cal_info->add($t);
			$cal_info->add($temp_panel);
			
			return new module_bootstrap_data($cal_info, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
	
	private function setUpForm(FormPanel $form, $panel, $main_cal_obj, $new_val){
		$form->add($new_val);
		$form->setAsGet();
		$form->addHiddenField("view", "manage_cals");
		$form->addHiddenField("subview", "share");
		$form->addHiddenField("subsubview", "name");
		$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
		$form->add($panel);
		return $form;
	}
}
?>