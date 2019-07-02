<?
class module_bootstrap_strongcal_hideshowcal_loader extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->setName("Aurora Hide/Show Calendar Loader");
		$this->setInfo("hides or shows a calendar, and forwards to main loader.");
	}
	
	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			$strongcal = $this->avalanche->getModule("strongcal");
			$bootstrap = $this->avalanche->getModule("bootstrap");
			
			if(isset($data_list["sh_cal_id"]) && 
			   (isset($data_list["hide"]) || isset($data_list["show"])) || isset($data_list["hide_all"])){
				$cal_id = (int) $data_list["sh_cal_id"];
				$cal = $strongcal->getCalendarFromDb($cal_id);
				if(isset($data_list["hide_all"])){
					$data = false;
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
					$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
					$data = $runner->run($data);
					$calendars = $data->data();
					foreach($calendars as $c){
						$strongcal->select($c->getId());
					}
					$strongcal->unselect($cal->getId());
				}else{
					$make_public = isset($data_list["show"]);
					if($make_public){
						if($strongcal->selected($cal)){
							$strongcal->unselect($cal_id);
						}
					}else/*make private*/{
						if(!$strongcal->selected($cal)){
							$strongcal->select($cal_id);
						}
					}
				}
			   }else if(isset($data_list["show_all"])){
					$data = false;
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
					$data = $runner->run($data);
					$calendars = $data->data();
					foreach($calendars as $c){
						$strongcal->unselect($c->getId());
					}
			   // }else if(isset($data_list["show_mine"])){
					// $data = false;
					// $runner = $bootstrap->newDefaultRunner();
					// $runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
					// $data = $runner->run($data);
					// $calendars = $data->data();
					// foreach($calendars as $c){
						// if($c->author() == $this->avalanche->getActiveUser()){
							// $strongcal->unselect($c->getId());
						// }else{
							// $strongcal->select($c->getId());
						// }
					// }
			   }else{
				   throw new IllegalArgumentException("sh_cal_id and either hide or show must be set in form input");
			   }

			// get the page header
			$module = new module_bootstrap_strongcal_main_loader($this->avalanche, $this->doc);
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$page = $runner->run($data);
			$page = $page->data();

			return new module_bootstrap_data($page, "the main aurora page");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}
	
	
	private function getLoader($data_list){
		if(!is_array($data_list)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array");
		}
		
		/** find the right loader for the data_listed view, if data_listed */
		
		// day
		if(isset($data_list["view"]) && $data_list["view"] == "day"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_dayview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// month
		if(isset($data_list["view"]) && $data_list["view"] == "month"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_monthview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// calendar management
		if(isset($data_list["view"]) && $data_list["view"] == "manage_cals"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_managecals_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// team management
		if(isset($data_list["view"]) && $data_list["view"] == "manage_teams"){
			$data_list["aurora_loader"] = "module_bootstrap_os_manageteams_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// user management
		if(isset($data_list["view"]) && $data_list["view"] == "manage_users"){
			$data_list["aurora_loader"] = "module_bootstrap_os_manageusers_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// event
		if(isset($data_list["view"]) && $data_list["view"] == "event"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_eventview_gui";
			$loader_name = $data_list["aurora_loader"];
			if(isset($data_list["cal_id"]) &&
			   isset($data_list["event_id"])){
				$cal_id = (int) $data_list["cal_id"];
				$event_id = (int) $data_list["event_id"];
				$module = new $loader_name($this->avalanche, $this->doc, $cal_id, $event_id);
			}else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"event_id\"");
			}
		}else
		// add_event_step_1
		if(isset($data_list["view"]) && $data_list["view"] == "add_event_step_1"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_selectcalendarview_gui";
			$postto = "index.php?aurora_loader=module_bootstrap_strongcal_addeventview_gui&view=add_event_step_2";
			$filter = "write_event";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc, $postto, $filter);
		}else
		// add event step 2
		if(isset($data_list["view"]) && $data_list["view"] == "add_event_step_2"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_addeventview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// delete_event_step_1
		if(isset($data_list["view"]) && $data_list["view"] == "edit_event"){
			if(isset($data_list["cal_id"]) &&
			   isset($data_list["event_id"])){
				$cal_id = (int) $data_list["cal_id"];
				$event_id = (int) $data_list["event_id"];
				$data_list["aurora_loader"] = "module_bootstrap_strongcal_editeventview_gui";
				$loader_name = $data_list["aurora_loader"];
				$module = new $loader_name($this->avalanche, $this->doc, $cal_id, $event_id);
			   }else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"event_id\"");
			   }
		}else
		// delete_event_step_1
		if(isset($data_list["view"]) && $data_list["view"] == "delete_event_step_1"){
			if(isset($data_list["cal_id"]) &&
			   isset($data_list["event_id"])){
				$cal_id = (int) $data_list["cal_id"];
				$event_id = (int) $data_list["event_id"];
				$data_list["aurora_loader"] = "module_bootstrap_strongcal_deleteeventview_gui";
				$loader_name = $data_list["aurora_loader"];
				$module = new $loader_name($this->avalanche, $this->doc, $cal_id, $event_id);
			   }else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"event_id\"");
			   }
		}else
		// delete_event_step_2
		if(isset($data_list["view"]) && $data_list["view"] == "delete_event_step_2"){
			if(isset($data_list["cal_id"]) &&
			   isset($data_list["event_id"]) && 
			   isset($data_list["commit"])){
				$cal_id = (int) $data_list["cal_id"];
				$event_id = (int) $data_list["event_id"];
				$data_list["aurora_loader"] = "module_bootstrap_strongcal_deleteeventview_gui";
				$loader_name = $data_list["aurora_loader"];
				$module = new $loader_name($this->avalanche, $this->doc, $cal_id, $event_id);
			   }else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"event_id\"");
			   }
		}else
		if(isset($data_list["aurora_loader"]) && $data_list["aurora_loader"] != "module_bootstrap_strongcal_main_loader"){
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		if(!isset($data_list["aurora_loader"]) || $data_list["aurora_loader"] == "module_bootstrap_strongcal_main_loader"){
			/**
			* if the loader name has not been resolved by now,
			* then load the default
			*/
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_default_view";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}
		
		if(!is_object($module)){
			throw new module_bootstrap_exception("Error loading Aurora Page Loader");
		}
		return $module;
	}

}
?>