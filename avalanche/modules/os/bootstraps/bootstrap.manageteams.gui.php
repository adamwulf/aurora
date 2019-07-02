<?

class module_bootstrap_os_manageteams_gui extends module_bootstrap_module{

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

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "overview";
			}
			
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
			$group_obj_list = $sorter->sortDESC($group_obj_list, new OSUsergroupComparator());
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
			
			$cal_info_panel = new BorderPanel();
			
			/************************************************************************
			************************************************************************/
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			/** done making calendar list for the left side **/
			
			$cal_info_panel->getStyle()->setWidth("450px");
			$cal_info_panel->getStyle()->setHeight("450px");
			$cal_info_panel->getStyle()->setBorderWidth(1);
			$cal_info_panel->getStyle()->setBorderStyle("solid");
			$cal_info_panel->getStyle()->setBorderColor("black");
			
			if($subview == "delete_team"){
				if(isset($data_list["team_id"])){
					// casting is ok, since we're coming from form input
					$this->avalanche->deleteUsergroup((int)$data_list["team_id"]);
					throw new RedirectException("index.php?view=manage_teams");
				}else{
					throw new IllegalArgumentException("team_id must be sent as form input to delete a team");
				}
			}else if($subview == "edit_team"){
				$module = new module_bootstrap_os_editteamview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$add_cal_form = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				$add_cal_form = $add_cal_form->data();
				$content_panel = $add_cal_form;
			}else if($subview == "add_team"){
				$module = new module_bootstrap_os_addteamview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$add_cal_form = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				$add_cal_form = $add_cal_form->data();
				$content_panel = $add_cal_form;
			}else if(count($group_obj_list) > 0 && isset($data_list["team_id"])){
					/**
					 * manage this calendar
					 */
					$data = new module_bootstrap_data($data_list);
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_manageteam_gui($this->avalanche, $this->doc));
					$data = $runner->run($data);
					$content_panel = $data->data();
			}else{
				if(count($group_obj_list) > 0){
					/**
					 * get the list of calendars
					 */
					$data = new module_bootstrap_data($data_list);
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_manageteams_list_gui($this->avalanche, $this->doc));
					$data = $runner->run($data);
					$content_panel = $data->data();
				}else{

					// there are no calendars in the list
					$content = new Panel();
					$content->getStyle()->setClassname("error_panel");
					$content->add(new Text("There are no groups<br> to manage yet."));
					$content_panel = new ErrorPanel($content);
					$content_panel->getStyle()->setHeight("300px");
				}
			}
			/************************************************************************
			put it all together
			************************************************************************/
			
			
			$manage_view = $content_panel;
			return new module_bootstrap_data($manage_view, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	private function getCalendar($calendar_obj_list, $cal_id){
		/**
		 * get the main calendar
		 */
		$main_cal_obj = false;
		foreach($calendar_obj_list as $cal){
			if($cal->getId() == $cal_id){
				$main_cal_obj = $cal;
				break;
			}
		}
		if(!is_object($main_cal_obj) && is_object($calendar_obj_list[0])){
			// they specified an incorrect calendar id
			// just use the first calendar
			$main_cal_obj = $calendar_obj_list[0];
		}
		return $main_cal_obj;
	}
}
?>