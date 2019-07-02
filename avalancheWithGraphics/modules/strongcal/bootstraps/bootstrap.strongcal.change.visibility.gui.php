<?

class module_bootstrap_strongcal_change_visibility_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora visibility permission options to HTML");
		$this->setInfo("this module displays information for the visibility options of a clalendar.");
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

			if(!isset($data_list["cal_id"])){
				throw new IllegalArgumentException("cal_id must be passed in as form input to change permissions");
			}
			$cal_id = (int) $data_list["cal_id"];
			
			/**
			 * get the list of calendars
			 */
			$data = new module_bootstrap_data(array($cal_id), "the calendar we're change permissions for"); // send in false as the default value
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_obj_list = $data->data();
			if(count($calendar_obj_list) == 0){
				throw new Exception("cannot find calendar #$cal_id");
			}
			$main_cal_obj = $calendar_obj_list[0];
			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			
			if(isset($data_list["submit"])){
				if(!isset($data_list["team_ids"])){
					$data_list["team_ids"] = array();
				}
				$team_ids = $data_list["team_ids"];
				$groups = $this->avalanche->getAllUsergroups();
				foreach($groups as $group){
					if($group->type() == avalanche_usergroup::$PUBLIC ||
					   $group->type() == avalanche_usergroup::$PERSONAL){
							   if(!$main_cal_obj->isPublic()){
								   $main_cal_obj->isPublic(true);
							   }
						if(in_array($group->getId(), $team_ids) &&
						   !$main_cal_obj->canReadName(array($group))){
							$main_cal_obj->updatePermission("name", "r", $group->getId());
						}else if(!in_array($group->getId(), $team_ids) &&
							$main_cal_obj->canReadName(array($group))){
							$main_cal_obj->updatePermission("name", "", $group->getId());
						}
						
					}
				}
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
			$cal_info->setWidth("100%");
			$temp_panel = new QuotePanel(20);
			$temp_panel->setStyle(new Style("content_font"));
			$text = new Text("Select which teams below are allowed to view events as \"busy.\" Teams that are not checked will not see this calendar at all.");
			$text->setStyle(new Style("content_font"));
			$temp_panel->add($text);
			
			$group_panel = new GridPanel(1);
			$groups = $this->avalanche->getAllUsergroups();
			foreach($groups as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC ||
					   $group->type() == avalanche_usergroup::$PERSONAL && $group->author() == $this->avalanche->getActiveUser()){
					$check = new CheckInput($group->name());
					$check->getStyle()->setClassname("item_style");
					$check->setName("team_ids[]");
					$check->setValue((string)$group->getId());
					if($main_cal_obj->canReadName(array($group))){
						$check->setChecked(true);
					}
					$group_panel->add($check);
				}
			}
			$temp_panel->add($group_panel);

			$submit = new Text("<input type='submit' value='Update' name='submit' style='border: 1px solid black; margin-top: 4px;'>");
			
			$permissions = new Link("<u>Permissions</u>", "index.php?view=manage_cals&subview=share&subsubview=advanced&cal_id=" . $main_cal_obj->getId());
			$permissions->setStyle(new Style("content_title"));
			$permissions->getStyle()->setFontColor("black");
			
			$temp_title = new Text(" - Visibility");
			$temp_title->setStyle(new Style("content_title"));
			$title = new Panel();
			$title->add($permissions);
			$title->add($temp_title);
			$cal_info->add($title);
			$cal_info->add($temp_panel);
			$cal_info->add($submit);
			
			$form = new FormPanel("index.php");
			$form->setAsGet();
			$form->addHiddenField("view", "manage_cals");
			$form->addHiddenField("subview", "bootstrap");
			$form->addHiddenField("bootstrap", "module_bootstrap_strongcal_change_visibility_gui");
			$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
			
			$form->add($cal_info);
			
			return new module_bootstrap_data($form, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>