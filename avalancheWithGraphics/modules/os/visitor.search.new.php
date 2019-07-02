<?

// searches for calendars/events/tasks added since the input date
class visitor_search_new extends visitor_basic_search{

	function __construct($avalanche, $datetime){
		parent::__construct($avalanche, $datetime);
	}

	function moduleCase($module){
		if($this->search_terms != "0000-00-00 00:00:00"){
			if($module instanceof module_strongcal){
				// search for calendars and events
				$results = array();
				if(in_array(visitor_search::$CALENDARS, $this->search_for)){
					$cal_array = array();
					$sql = "SELECT id FROM " . $this->avalanche->PREFIX() . "strongcal_calendars WHERE added_on > '" . $this->search_terms . "'";
					$result = $this->avalanche->mysql_query($sql);
					while($myrow = mysql_fetch_array($result)){
						$cal = $module->getCalendarFromDb($myrow["id"]);
						if($cal->author() != $this->avalanche->getActiveUser()){
							if(is_object($cal) && $cal->canReadName()){
								$cal_array[] = $cal;
							}
						}
					}

					$results[] = $cal_array;
				}
				if(in_array(visitor_search::$EVENTS, $this->search_for)){
					$bootstrap = $this->avalanche->getModule("bootstrap");
					// get the calendar list
						$data = false;
						$runner = $bootstrap->newDefaultRunner();
						$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
						$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
						$data = $runner->run($data);
						$calendars = $data->data();
					// end getting the calendar list
					$event_results = array();
					foreach($calendars as $cal){
						$events_from_cal = $cal->getEventsAfter($this->search_terms, 30);
						$event_results = array_merge($event_results, $events_from_cal);
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
						$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
						$data = $runner->run($data);
						$calendars = $data->data();
					// end getting the calendar list
					$comment_results = array();
					foreach($calendars as $cal){
						if($cal->canReadComments()){
							$comments_from_cal = array();
							$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_comments WHERE post_date > '" . $this->search_terms . "'";
							$result = $this->avalanche->mysql_query($sql);
							while($myrow = mysql_fetch_array($result)){
								if($myrow["author"] != $this->avalanche->getActiveUser()){
									$myrow["id"] = (int)$myrow["id"];
									$myrow["event_id"] = (int)$myrow["event_id"];
									$myrow["author"] = (int)$myrow["author"];
									$myrow["cal_id"] = $cal->getId();
									$myrow["date"] = $myrow["post_date"];
									$comments_from_cal[] = $myrow;
								}
							}
							$comment_results = array_merge($comment_results, $comments_from_cal);
						}
					}

					$data = new module_bootstrap_data($comment_results, "send in list of events to sort");
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
				if(in_array(visitor_search::$TASKS, $this->search_for)){
					// get the calendar list
						$data = false;
						$runner = $bootstrap->newDefaultRunner();
						$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
						$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
						$data = $runner->run($data);
						$calendars = $data->data();
					// end getting the calendar list
					$cal_ids = "";
					foreach($calendars as $cal){
						if($cal->canReadEntries()){
							$cal_ids .= " OR cal_id='" . $cal->getId() . "'";
						}
					}
					$task_results = array();
					$sql = "SELECT id FROM " . $this->avalanche->PREFIX() . "taskman_tasks WHERE created_on > '" . $this->search_terms . "' AND ( 0 $cal_ids)";
					$result = $this->avalanche->mysql_query($sql);
					while($myrow = mysql_fetch_array($result)){
						$task = $module->getTask((int)$myrow["id"]);
						if($task->author() != $this->avalanche->getActiveUser()){
							$task_results[] = $task;
						}
					}
					$results[] = $task_results;
				}
				return $results;
			}else{
				return array();
			}
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