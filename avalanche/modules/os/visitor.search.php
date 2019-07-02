<?

class visitor_search extends visitor_basic_search{

	function __construct($avalanche, $terms){
		parent::__construct($avalanche, $terms);

		$cook = $this->avalanche->getCookie("visitor_search");
		if(is_string($cook)){
			$cook = explode(",", $cook);
			for($i=0;$i<count($cook);$i++){
				$cook[$i] = (int) $cook[$i];
			}
			$this->search_for = $cook;
		}else{
			$this->search_for = array(visitor_search::$USERS,
					    visitor_search::$TEAMS,
					    visitor_search::$CALENDARS,
					    visitor_search::$EVENTS,
					    visitor_search::$TASKS,
					    visitor_search::$COMMENTS);
		}
	}

	function searchFor($type){
		parent::searchFor($type);
		$this->setCookie();
	}

	function doNotSearchFor($type){
		parent::doNotSearchFor($type);
		$this->setCookie();
	}

	private function setCookie(){
		$str = implode(",", $this->search_for);
		$this->avalanche->setCookie("visitor_search", $str);
	}

	function moduleCase($module){
		if($module instanceof module_strongcal){
			// search for calendars and events
			$results = array();
			if(in_array(visitor_search::$CALENDARS, $this->search_for)){
				$calendar_results = $module->getAllCalendarsMatching($this->search_terms);
				$results[] = $calendar_results;
			}
			if(in_array(visitor_search::$EVENTS, $this->search_for)){
				$bootstrap = $this->avalanche->getModule("bootstrap");
				// get the calendar list
					$data = false;
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
					$data = $runner->run($data);
					$calendars = $data->data();
				// end getting the calendar list
				$event_results = array();
				foreach($calendars as $cal){
					if(!$module->selected($cal)){
						$event_results = array_merge($event_results, $cal->getAllEventsMatching($this->search_terms, 30));
					}
				}

				$data = new module_bootstrap_data($event_results, "send in list of events to sort");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_strongcal_eventsorter());
				$data = $runner->run($data);
				$event_results = array_reverse($data->data());
				$results[] = $event_results;
			}
			if(in_array(visitor_search::$COMMENTS, $this->search_for)){
				$bootstrap = $this->avalanche->getModule("bootstrap");
				// get the calendar list
					$data = false;
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
					$data = $runner->run($data);
					$calendars = $data->data();
				// end getting the calendar list
				$comment_results = array();
				foreach($calendars as $cal){
					if(!$module->selected($cal)){
						$comment_results = array_merge($comment_results, $cal->getAllCommentsMatching($this->search_terms));
					}
				}

				$data = new module_bootstrap_data($comment_results, "send in list of comments to sort");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_strongcal_commentsorter());
				$data = $runner->run($data);
				$comment_results = array_reverse($data->data());
				$results[] = $comment_results;
			}
			return $results;
		}else if($module instanceof module_taskman){
			$bootstrap = $this->avalanche->getModule("bootstrap");
			// search for tasks
			$results = array();
			// get the calendar list
				$data = false;
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
				$data = $runner->run($data);
				$calendars = $data->data();
			// end getting the calendar list
			$cal_ids = "";
			foreach($calendars as $cal){
				if($cal->canReadEntries()){
					$cal_ids .= " OR cal_id='" . $cal->getId() . "'";
				}
			}
			if(in_array(visitor_search::$TASKS, $this->search_for)){
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$task_results = $module->getAllTasksMatching($this->search_terms);
				// sort the tasks
				$data = new module_bootstrap_data($task_results, "send in list of tasks to sort");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_taskman_tasksorter());
				$data = $runner->run($data);
				$task_results = array_reverse($data->data());
				$results[] = $task_results;
			}
			return $results;
		}else if($module instanceof module_os){
			// search for users and groups
			$users = array();
			if(in_array(visitor_search::$USERS, $this->search_for)){
				$users = $module->getAllUsersMatching($this->search_terms);
			}
			$groups = array();
			if(in_array(visitor_search::$TEAMS, $this->search_for)){
				$groups = $module->getAllTeamsMatching($this->search_terms);
			}
			return array($users, $groups);
		}else{
			return array();
		}
	}

	function skinCase($skin){
		return array();
	}

	function usergroupCase($usergroup){
		return array();
	}




}

?>