<?

class module_bootstrap_os_search_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $terms;
	private $max_result;

	function __construct($avalanche, Document $doc, $terms){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_string($terms)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string");
		}
		$this->setName("OS Search Results page");
		$this->setInfo("displays the results of a search with the given terms");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->terms = $terms;
		$this->max_result = 30;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$visitor = new visitor_search($this->avalanche, $this->terms);
			$data_list = $data->data();
			$search_for = $visitor->getSearchTypes();
			// search for only specific types
			if(isset($data_list["search_calendars"]) && $data_list["search_calendars"]){
				$visitor->searchFor(visitor_search::$CALENDARS);
			}else if(isset($data_list["search_calendars"])){
				$visitor->doNotSearchFor(visitor_search::$CALENDARS);
			}
			if(isset($data_list["search_events"]) && $data_list["search_events"]){
				$visitor->searchFor(visitor_search::$EVENTS);
			}else if(isset($data_list["search_events"])){
				$visitor->doNotSearchFor(visitor_search::$EVENTS);
			}
			if(isset($data_list["search_tasks"]) && $data_list["search_tasks"]){
				$visitor->searchFor(visitor_search::$TASKS);
			}else if(isset($data_list["search_tasks"])){
				$visitor->doNotSearchFor(visitor_search::$TASKS);
			}
			if(isset($data_list["search_users"]) && $data_list["search_users"]){
				$visitor->searchFor(visitor_search::$USERS);
			}else if(isset($data_list["search_users"])){
				$visitor->doNotSearchFor(visitor_search::$USERS);
			}
			if(isset($data_list["search_teams"]) && $data_list["search_teams"]){
				$visitor->searchFor(visitor_search::$TEAMS);
			}else if(isset($data_list["search_teams"])){
				$visitor->doNotSearchFor(visitor_search::$TEAMS);
			}
			if(isset($data_list["search_comments"]) && $data_list["search_comments"]){
				$visitor->searchFor(visitor_search::$COMMENTS);
			}else if(isset($data_list["search_comments"])){
				$visitor->doNotSearchFor(visitor_search::$COMMENTS);
			}


			$results = $this->avalanche->execute($visitor);
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/search_style.css"));
			$this->doc->addStyleSheet($css);


			$results_view = new GridPanel(1);
			$results_view->setWidth("100%");

			$result_style = new Style("results_table");

			$results_view->setStyle($result_style);
			$results_view->setCellStyle($result_style);
			$section_header_style = new Style("section_header");

			$count_results = 0;
			foreach($results as $result){
				$count = min(count($result), $this->max_result);
				if(count($result) > $this->max_result){
					$countText = $count . "+";
				}else{
					$countText = $count;
				}
				$count_results += $count;
				if(!count($result)){
					// noop
				}else if($result[0] instanceof avalanche_user){
					$div = new SimplePanel();
					$div->add(new Text("$countText User" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatUserResults($result));
				}else if($result[0] instanceof avalanche_usergroup){
					$div = new SimplePanel();
					$div->add(new Text("$countText Groups" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatUsergroupResults($result));
				}else if($result[0] instanceof module_strongcal_calendar){
					$div = new SimplePanel();
					$div->add(new Text("$countText Calendar" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatCalendarResults($result));
				}else if($result[0] instanceof module_taskman_task){
					$div = new SimplePanel();
					$div->add(new Text("$countText Task" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatTaskResults($result));
				}else if($result[0] instanceof module_strongcal_event){
					$div = new SimplePanel();
					$div->add(new Text("$countText Event" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatEventResults($result));
				}else if(is_array($result[0])){
					$div = new SimplePanel();
					$div->add(new Text("$countText Comment" . ($count == 1 ? "" : "s")));
					if(count($result) > $this->max_result){
						$t = new Text(" (please refine your search)");
						$t->getStyle()->setFontSize(10);
						$div->add($t);
					}
					$results_view->add($div, $section_header_style);
					$results_view->add($this->formatCommentResults($result));
				}
			}
			if($count_results == 0){
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("No results matching:<br>\"" . $this->terms . "\""));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$results_view = $error;
			}else{
			}

			$title = "Search Results";
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add(new Text($title));
			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			$grid->add($results_view);

			return new module_bootstrap_data($grid, "a gui component for the manage users view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of form input.<br>");
		}
	}

	// formats the results of users
	private function formatUserResults($result){
		$os = $this->avalanche->getModule("os");
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of users");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$user_result = new GridPanel(6);
		$user_result->setWidth("100%");
		$user_result->setCellStyle($result_style);

		$time_style = new Style();
		$time_style->setFontSize(7);

		$user_result->add(new Text("Username"), $title_style);
		$user_result->add(new Text("Full Name"), $title_style);
		$user_result->add(new Text("Email"), $title_style);
		$user_result->add(new Text("Last Login"), $title_style);
		$user_result->add(new Text("Last Active"), $title_style);
		$user_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$user = $result[$i];
			if(!($user instanceof avalanche_user)){
				throw new IllegalArgumentException("argument to " . __METHOD . " must be an array of users; found " . is_object($user) ? get_class($user) : gettype($user));
			}
			$link = new Link($user->username(),"javascript:;");
			$this->createUserMenu($link, $user->getId());
			$user_result->add($link);
			$name = $os->getUsername($user->getId());
			if(!strlen($name)){
				$name = "<i>not specified</i>";
			}
			$link = new Link($name,"javascript:;");
			$user_result->add($link);
			$this->createUserMenu($link, $user->getId());

			$email = $user->email();
			if(strlen($email)){
				$email = new Link($email, "mailto:$email");
			}else{
				$email = new Text("<i>unavailable</i>");
			}
			$user_result->add($email);

			$datetime = $user->lastLoggedIn();
			if($datetime == "0000-00-00 00:00:00"){
				$date = new Text("Never");
			}else{
				$date = substr($datetime, 0, 10);
				$time = substr($datetime, 11);
				$datetime = $this->adjust($date, $time, $strongcal->timezone());
				$datetime = $datetime["date"] . " " . $datetime["time"];
				$stamp = mktime(substr($datetime, 11, 2), substr($datetime, 14, 2), 0, substr($datetime, 5, 2), substr($datetime, 8, 2), substr($datetime, 0, 4));
				$time = date("g:ia", $stamp);
				$time = substr($time, 0, strlen($time)-1);
				$date = $time . " " . date("D, m/d/y", $stamp);

				$date = new Text($date);
			}
			$date->setStyle($time_style);
			$user_result->add($date);

			$datetime = $user->lastActive();
			if($datetime == "0000-00-00 00:00:00"){
				$date = new Text("Never");
			}else{
				$date = substr($datetime, 0, 10);
				$time = substr($datetime, 11);
				$datetime = $this->adjust($date, $time, $strongcal->timezone());
				$datetime = $datetime["date"] . " " . $datetime["time"];
				$stamp = mktime(substr($datetime, 11, 2), substr($datetime, 14, 2), 0, substr($datetime, 5, 2), substr($datetime, 8, 2), substr($datetime, 0, 4));
				$time = date("g:ia", $stamp);
				$time = substr($time, 0, strlen($time)-1);
				$date = $time . " " . date("D, m/d/y", $stamp);

				$date = new Text($date);
			}
			$date->setStyle($time_style);
			$user_result->add($date);

			$link = new Link("more info", "javascript:;");
			$user_result->add($link, $info_style);
			$this->createUserMenu($link, $user->getId());
		}
		return $user_result;
	}


	// formats the results of usergroups
	private function formatUsergroupResults($result){
		$os = $this->avalanche->getModule("os");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of usergroups");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$team_result = new GridPanel(3);
		$team_result->setWidth("100%");
		$team_result->setCellStyle($result_style);


		$team_result->add(new Text("Name"), $title_style);
		$team_result->add(new Text("Description"), $title_style);
		$team_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$team = $result[$i];
			if(!($team instanceof avalanche_usergroup)){
				throw new IllegalArgumentException("argument to " . __METHOD . " must be an array of usergroups; found " . is_object($team) ? get_class($team) : gettype($team));
			}
			$team_result->add(new Link($team->name(),"index.php?view=manage_teams&team_id=" . $team->getId()));

			$description = $team->description();
			if(!strlen($description)){
				$description = new Text("<i>no description</i>");
			}else{
				$description = new Text($this->cropString($description));
			}
			$team_result->add($description);

			$team_result->add(new Link("more info", "index.php?view=manage_teams&team_id=" . $team->getId()), $info_style);
		}
		return $team_result;
	}


	// formats the results of calendars
	private function formatCalendarResults($result){
		$os = $this->avalanche->getModule("os");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of calendars");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$cal_result = new GridPanel(4);
		$cal_result->setWidth("100%");
		$cal_result->setCellStyle($result_style);


		$cal_result->add(new Text("&nbsp;"), $title_style);
		$cal_result->add(new Text("Name"), $title_style);
		$cal_result->add(new Text("Author"), $title_style);
		$cal_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$cal = $result[$i];
			if(!($cal instanceof module_strongcal_calendar)){
				throw new IllegalArgumentException("argument to " . __METHOD . " must be an array of calendarss; found " . is_object($cal) ? get_class($cal) : gettype($cal));
			}
			$color_block = new Panel();
			$color_block->getStyle()->setClassname("color_block");
			$color_block->getStyle()->setBackground($cal->color());
			$cal_result->add($color_block, new Style("color_cell_style"));

			$link = new Link($cal->name(), "index.php?view=manage_cals&cal_id=" . $cal->getId());

			$tip = OsGuiHelper::createToolTip(new Text("Click to manage this calendar"));
			$menu_action = new ToolTipAction($link, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$cal_result->add($link);

			$author = $cal->author();
			$link = new Link($os->getUsername($author), "javascript:;");
			$this->createUserMenu($link, $author);
			$cal_result->add($link);

			$cal_result->add(new Link("more info", "index.php?view=manage_cals&cal_id=" . $cal->getId()), $info_style);
		}
		return $cal_result;
	}

	// formats the results of tasks
	private function formatTaskResults($result){
		$os = $this->avalanche->getModule("os");
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of tasks");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$task_result = new GridPanel(6);
		$task_result->setWidth("100%");
		$task_result->setCellStyle($result_style);

		$date_style = new Style("title_row_style");
		$date_style->setWidth("100px");
		$time_style = new Style();
		$time_style->setFontSize(7);

		$task_result->add(new Text("&nbsp;"), $title_style);
		$task_result->add(new Text("Author"), $title_style);
		$task_result->add(new Text("Due"), $date_style);
		$task_result->add(new Text("Title"), $title_style);
		$task_result->add(new Text("Description"), $title_style);
		$task_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$task = $result[$i];
			if(!($task instanceof module_taskman_task)){
				throw new IllegalArgumentException("argument to " . __METHOD . " must be an array of tasks; found " . is_object($task) ? get_class($task) : gettype($task));
			}
			$due = $task->due();
			$stamp = mktime(substr($due, 11, 2), substr($due, 14, 2), 0, substr($due, 5, 2), substr($due, 8, 2), substr($due, 0, 4));
			$time = date("g:ia", $stamp);
			$time = substr($time, 0, strlen($time)-1);
			$date = $time . " " . date("D, m/d/y", $stamp);


			$color_block = new Panel();
			$color_block->getStyle()->setClassname("color_block");
			$cal = $strongcal->getCalendarFromDb($task->calId());
			$color_block->getStyle()->setBackground($cal->color());
			if($task->status() == module_taskman_task::$STATUS_COMPLETED){
				$c = new Color($cal->color());
				if($c->isDark()){
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedwhite.gif");
				}else{
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycompletedblack.gif");
				}
				$img->setWidth(8);
				$img->setHeight(8);
				$color_block->add($img);
			}
			if($task->status() == module_taskman_task::$STATUS_CANCELLED){
				$c = new Color($cal->color());
				if($c->isDark()){
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycancelledwhite.gif");
				}else{
					$img = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/tinycancelledblack.gif");
				}
				$img->setWidth(8);
				$img->setHeight(8);
				$color_block->add($img);
			}
			$task_result->add($color_block, new Style("color_cell_style"));

			$author = $task->author();
			$link = new Link($os->getUsername($author), "javascript:;");
			$this->createUserMenu($link, $author);
			$task_result->add($link);

			$due = new Text($date);
			$due->setStyle($time_style);
			$task_result->add($due);

			$task_title = new Link($this->cropString($task->title()), "index.php?view=task&task_id=" . $task->getId());

			$tip = TaskmanGuiHelper::createTaskTip($this->avalanche, $task);
			$menu_action = new ToolTipAction($task_title, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			$task_result->add($task_title);

			$task_result->add(new Text($this->cropString($task->description())));
			$task_result->add(new Link("more info", "index.php?view=task&task_id=" . $task->getId()), $info_style);
		}
		return $task_result;
	}

	// formats the results of events
	private function formatEventResults($result){
		$os = $this->avalanche->getModule("os");
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of events");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$event_result = new GridPanel(6);
		$event_result->setWidth("100%");
		$event_result->setCellStyle($result_style);

		$date_style = new Style("title_row_style");
		$date_style->setWidth("130px");
		$author_style = new Style("title_row_style");
		$author_style->setWidth("80px");

		$time_style = new Style();
		$time_style->setFontSize(7);

		$event_result->add(new Text("&nbsp;"), $title_style);
		$event_result->add(new Text("Author"), $author_style);
		$event_result->add(new Text("Date"), $date_style);
		$event_result->add(new Text("Title"), $title_style);
		$event_result->add(new Text("Description"), $title_style);
		$event_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$event = $result[$i];
			if(!($event instanceof module_strongcal_event)){
				throw new IllegalArgumentException("argument to " . __METHOD . " must be an array of events; found " . is_object($event) ? get_class($event) : gettype($event));
			}
			$sd = $event->getDisplayValue("start_date");
			$st = $event->getDisplayValue("start_time");
			$stamp = mktime(substr($st, 0, 2), substr($st, 3, 2), 0, substr($sd, 5, 2), substr($sd, 8, 2), substr($sd, 0, 4));
			$time = date("g:ia", $stamp);
			$time = substr($time, 0, strlen($time)-1);
			$date = $time . " " . date("D, m/d/y", $stamp);

			$color_block = new Panel();
			$color_block->getStyle()->setClassname("color_block");
			$cal = $event->calendar();
			$color_block->getStyle()->setBackground($cal->color());
			$event_result->add($color_block, new Style("color_cell_style"));

			$author = $event->author();
			$link = new Link($os->getUsername($author), "javascript:;");
			$this->createUserMenu($link, $author);
			$event_result->add($link);

			$date = new Text($date);
			$date->setStyle($time_style);
			$event_result->add($date);

			$link = new Link($this->cropString($event->getDisplayValue("title")), "index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId());

			$event_result->add($link);

			$event_result->add(new Text($this->cropString($event->getDisplayValue("description"))));
			$event_result->add(new Link("more info", "index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId()), $info_style);

			if($event->getDisplayValue("description") || $event->hasComments() || $event->isAllDay()){
				$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $event);
				$menu_action = new ToolTipAction($link, $tip);
				$this->doc->addAction($menu_action);
				$this->doc->addHidden($tip);
			}
		}
		return $event_result;
	}

	// formats the results of comments
	private function formatCommentResults($result){
		$os = $this->avalanche->getModule("os");
		$strongcal = $this->avalanche->getModule("strongcal");
		if(!is_array($result)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of comments");
		}

		$title_style = new Style("title_row_style");
		$result_style = new Style("result_row_style");
		$info_style = new Style("result_row_style");
		$info_style->setWidth("80px");
		$info_style->setTextAlign("right");

		$comment_result = new GridPanel(6);
		$comment_result->setWidth("100%");
		$comment_result->setCellStyle($result_style);

		$date_style = new Style("title_row_style");
		$date_style->setWidth("100px");
		$time_style = new Style();
		$time_style->setFontSize(7);

		$comment_result->add(new Text("&nbsp;"), $title_style);
		$comment_result->add(new Text("Author"), $title_style);
		$comment_result->add(new Text("Date"), $date_style);
		$comment_result->add(new Text("Event"), $date_style);
		$comment_result->add(new Text("Comment"), $title_style);
		$comment_result->add(new Text("&nbsp;"), $title_style);

		for ($i=0;$i<min(count($result),$this->max_result); $i++){
			$comment = $result[$i];
			if(!is_array($comment)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an array of comments; found " . is_object($comment) ? get_class($comment) : gettype($comment));
			}
			$cal_id = $comment["cal_id"];
			$cal = $strongcal->getCalendarFromDb($cal_id);
			$post_date = $comment["date"];
			$stamp = mktime(substr($post_date, 11, 2), substr($post_date, 14, 2), 0, substr($post_date, 5, 2), substr($post_date, 8, 2), substr($post_date, 0, 4));
			$time = date("g:ia", $stamp);
			$time = substr($time, 0, strlen($time)-1);
			$date = $time . " " . date("D, m/d/y", $stamp);


			$color_block = new Panel();
			$color_block->getStyle()->setClassname("color_block");
			$color_block->getStyle()->setBackground($cal->color());
			$comment_result->add($color_block, new Style("color_cell_style"));

			$event = $cal->getEvent($comment["event_id"]);
			if(is_object($event)){
				$author = (int)$comment["author"];
				$link = new Link($os->getUsername($author), "javascript:;");
				$this->createUserMenu($link, $author);
				$comment_result->add($link);

				$post_date = new Text($date);
				$post_date->setStyle($time_style);
				$comment_result->add($post_date);

				$title = $event->getDisplayValue("title");
				if(strlen(trim($title))){
					$title = "<i>no title</i>";
				}
				$link = new Link($this->cropString($title), "index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId());
				if($event->getDisplayValue("description") || $event->hasComments() || $event->isAllDay()){
					$tip = StrongcalGuiHelper::createEventTip($this->avalanche, $event);
					$menu_action = new ToolTipAction($link, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				}
				$comment_result->add($link);

				$comment_result->add(new Text($this->cropString($comment["title"])));
				$comment_result->add(new Link("more info", "index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId()), $info_style);
			}
		}
		return $comment_result;
	}


	private function cropString($str){
		$len = 50;
		if(strlen($str) > $len){
			$str = substr($str, 0, $len-3) . "...";
		}
		return $str;
	}

	private function createUserMenu($trigger, $user_id){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = false;
		$module = new OSUserMenu($this->avalanche, $this->doc, $trigger, $user_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}

	// puts timezone into effect
	private function adjust($gmtdate, $gmttime, $timezone = false){
		if($timezone === false){
			$timezone = $this->timezone();
		}
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$year  = substr($gmtdate, 0, 4);
		$month = substr($gmtdate, 5, 2);
		$day   = substr($gmtdate, 8, 2);
		$hour  = substr($gmttime, 0, 2);
		$min   = substr($gmttime, 3, 2);
		$sec   = substr($gmttime, 6, 2);
		$stamp = mktime($hour + $hour_offset, $min + $min_offset, $sec, $month, $day, $year);
		$localdate = @date("Y-m-d", $stamp);
		$localtime = @date("H:i:s", $stamp);
		return array("date" => $localdate, "time" => $localtime);
	}



}
?>