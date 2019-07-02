<?
class module_taskman_export_loader extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->setName("Aurora Main View Loader");
		$this->setInfo("loads the main page of aurora. this loader expects raw form input.
				the 'aurora_loader' variable in the input must be set to the classname of
				the view loader to load. the view loader is responsible for processing the
				rest of the form input and display its appropriate view.");
	}

	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$taskman = $this->avalanche->getModule("taskman");
			$os = $this->avalanche->getModule("os");
			$strongcal = $this->avalanche->getModule("strongcal");
			
			$data_list = $data->data();
			
			if(isset($data_list["date"])){
				$date = $data_list["date"];
			}else{
				$stamp = $strongcal->gmttimestamp();
				$date = date("Y-m-d", $stamp);
			}
			$date = new MMDateTime($date . " 00:00:00");
			$stamp = $date->getTimeStamp();
			
			if(isset($data_list["range"])){
				$range = $data_list["range"];
			}else{
				$range = "day";
			}
			
			$sdate = date("Y-m-d", $stamp);
			$edate = $sdate;
			$visitor = new ExportCalendarVisitor($this->avalanche, $sdate, $edate);
			
			if($range == "month"){
				$date->day(1);
				$sdate = date("Y-m-d", $date->getTimeStamp());
				$date->day((int)date("t", $date->getTimeStamp()));
				$edate = date("Y-m-d", $date->getTimeStamp());
				$visitor = new ExportCalendarVisitor($this->avalanche, $sdate, $edate);
			}else if($range == "week"){
				$date->day((int)(date("d", $date->getTimeStamp()) - date("w", $date->getTimeStamp())));
				$sdate = date("Y-m-d", $date->getTimeStamp());
				$date->day($date->day() + 6);
				$edate = date("Y-m-d", $date->getTimeStamp());
				$visitor = new ExportCalendarVisitor($this->avalanche, $sdate, $edate);
			}else if($range == "day"){
				$sdate = date("Y-m-d", $date->getTimeStamp());
				$edate = date("Y-m-d", $date->getTimeStamp());
				$visitor = new ExportCalendarVisitor($this->avalanche, $sdate, $edate);
			}else if($range == "future"){
				$date->day(1);
				$sdate = date("Y-m-d", $date->getTimeStamp());
				$date->year($date->year() + 1);
				$date->day((int)date("t", $date->getTimeStamp()));
				$edate = date("Y-m-d", $date->getTimeStamp());
				$visitor = new ExportCalendarVisitor($this->avalanche, $sdate, $edate);
			}else if($range == "event"){
				if(!isset($data_list["event_id"])){
					throw new IllegalArgumentException("event_id must be sent in form input");
				}
				if(!isset($data_list["cal_id"])){
					throw new IllegalArgumentException("cal_id must be sent in form input");
				}
				$event_id = (int)$data_list["event_id"];
				$cal_id = (int)$data_list["cal_id"];
				$visitor = new ExportEventVisitor($this->avalanche, $cal_id, $event_id);
			}else if($range == "task"){
				if(!isset($data_list["task_id"])){
					throw new IllegalArgumentException("cal_id must be sent in form input");
				}
				$task_id = (int)$data_list["task_id"];
				$visitor = new ExportTaskVisitor($this->avalanche, $task_id);
			}
			
			
			$body = $strongcal->execute($visitor);
			header("Content-Type: text/x-iCalendar");
			header("Content-Disposition: inline; filename=schedule.ics");
			echo $body;
			exit;
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}
	
	
	private function getLoader($data_list){
		if(!is_array($data_list)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array");
		}
		
		/** find the right loader for the data_listed view, if data_listed */
		
		// reset password
		// this is not from the 'view' variable. it needs to override this.
		if($this->avalanche->loggedInHuh() && $this->avalanche->getUser($this->avalanche->getActiveUser())->needToResetPassword()){
			$data_list["aurora_loader"] = "module_bootstrap_os_resetpassword_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// inviteusers 
		if(isset($data_list["view"]) && $data_list["view"] == "inviteusers"){
			$data_list["aurora_loader"] = "module_bootstrap_os_inviteusers_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// login 
		if(isset($data_list["view"]) && $data_list["view"] == "login"){
			$data_list["aurora_loader"] = "module_bootstrap_os_login_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// first_login 
		if(isset($data_list["view"]) && $data_list["view"] == "first_login" && $this->avalanche->loggedInHuh()){
			$data_list["aurora_loader"] = "module_bootstrap_os_firsttime_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// preferences
		if(isset($data_list["view"]) && $data_list["view"] == "preferences"){
			$data_list["aurora_loader"] = "module_bootstrap_os_preferences_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// faq
		if(isset($data_list["view"]) && $data_list["view"] == "faq"){
			$data_list["aurora_loader"] = "module_bootstrap_os_faq_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// about
		if(isset($data_list["view"]) && $data_list["view"] == "about"){
			$data_list["aurora_loader"] = "module_bootstrap_os_about_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// overview
		if(isset($data_list["view"]) && $data_list["view"] == "overview"){
			$data_list["aurora_loader"] = "module_bootstrap_os_overview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// day
		if(isset($data_list["view"]) && $data_list["view"] == "day"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_dayview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// week
		if(isset($data_list["view"]) && $data_list["view"] == "week"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_weekview_gui";
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
		// view user profile
		if(isset($data_list["view"]) && $data_list["view"] == "user_profile"){
			if(!isset($data_list["user_id"])){
				throw new IllegalArgumentException("user_id must be sent in as form input to view user profile");
			}
			$data_list["aurora_loader"] = "module_bootstrap_os_user_profile_gui";
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
		// task
		if(isset($data_list["view"]) && $data_list["view"] == "task"){
			$data_list["aurora_loader"] = "module_bootstrap_taskman_taskview_gui";
			$loader_name = $data_list["aurora_loader"];
			if(isset($data_list["task_id"])){
				$task_id = (int) $data_list["task_id"];
				$module = new $loader_name($this->avalanche, $this->doc, $task_id);
			}else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"event_id\"");
			}
		}else
		// edit_task
		if(isset($data_list["view"]) && $data_list["view"] == "edit_task"){
			if(isset($data_list["task_id"])){
				$task_id = (int) $data_list["task_id"];
				$data_list["aurora_loader"] = "module_bootstrap_taskman_edittaskview_gui";
				$loader_name = $data_list["aurora_loader"];
				$module = new $loader_name($this->avalanche, $this->doc, $task_id);
			   }else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"cal_id\" and \"task_id\"");
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
		// add_task_step_1
		if(isset($data_list["view"]) && $data_list["view"] == "add_task_step_1"){
			$data_list["aurora_loader"] = "module_bootstrap_strongcal_selectcalendarview_gui";
			$postto = "index.php?aurora_loader=module_bootstrap_taskman_addtaskview_gui&view=add_task_step_2";
			$filter = "write_event";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc, $postto, $filter);
		}else
		// add task step 2
		if(isset($data_list["view"]) && $data_list["view"] == "add_task_step_2"){
			$data_list["aurora_loader"] = "module_bootstrap_taskman_addtaskview_gui";
			$loader_name = $data_list["aurora_loader"];
			$module = new $loader_name($this->avalanche, $this->doc);
		}else
		// edit_event
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
		// search
		if(isset($data_list["view"]) && $data_list["view"] == "search"){
			if(isset($data_list["terms"])){
				$terms = $data_list["terms"];
				$this->avalanche->setCookie("search_terms", $terms);
				$data_list["aurora_loader"] = "module_bootstrap_os_search_gui";
				$loader_name = $data_list["aurora_loader"];
				$module = new $loader_name($this->avalanche, $this->doc, $terms);
			   }else{
				throw new IllegalArgumentException("paremeters to view=" . $data_list["view"] . " must include \"terms\"");
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