<?

class module_bootstrap_taskman_taskview_gui extends module_bootstrap_module{

	private $avalanche;
	private $task_id;
	private $doc;
	private $data_list;

	function __construct($avalanche, Document $doc, $task_id){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_integer($task_id)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an integer");
		}
		$this->setName("Aurora Task view in HTML");
		$this->setInfo("this module takes in a task id and displays the task");
		$this->task_id = $task_id;
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$prefix = "taskman_";
			$data_list = $data->data();
			$this->data_list = $data->data();
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");
			$taskman = $this->avalanche->getModule("taskman");
			$os = $this->avalanche->getModule("os");
			$subview = "task";
			if(isset($data_list["subview"])){
				$subview = $data_list["subview"];
			}

			/**
			 * end initialization and checking
			 */
			 try{
				$task = $taskman->getTask($this->task_id);
				if(!is_object($task)){
					$task_view = new Text("task "  . $this->task_id . " could not be found");
					$task_view->getStyle()->setFontFamily("verdana, sans-serif");
					$task_view->getStyle()->setFontSize(9);
					return new module_bootstrap_data(new ErrorPanel($task_view), "error: couldn't find task");
				}
				$cal = $strongcal->getCalendarFromDb($task->calId());
				if(!is_object($cal)){
					$task_view = new Text("calendar " . $task->calId() . " could not be found");
					$task_view->getStyle()->setFontFamily("verdana, sans-serif");
					$task_view->getStyle()->setFontSize(9);
					return new module_bootstrap_data(new ErrorPanel($task_view), "error: couldn't find calendar");
				}


				$cal = $strongcal->getCalendarFromDb($task->calId());
				if($task->canRead()){
					/**
					 * i now have an $task object for my current task to display
					 */

					 $fifth_row = new GridPanel(1);

					/**
					 * add the style sheet to the document for this page
					 */
					$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/task_view.css"));
					$this->doc->addStyleSheet($css);

					$label_style = new Style("add_event_label");
					$label_style->setWidth("80px");

					if(isset($data_list["commit"]) && $data_list["commit"] == "1" &&
					   isset($data_list["subview"]) && $data_list["subview"] == "delete"){
						$deltask_view = new Panel();
						$deltask_view->getStyle()->setClassname("big_container");
						$deltask_view->getStyle()->setWidth("100%");
						$deltask_view->getStyle()->setHeight("400");
						$deltask_view->setAlign("center");
						$deltask_view->setValign("middle");

						$notice = new Panel();
						$notice->setAlign("center");
						$notice->setStyle(new Style("calendar_form"));

						if($taskman->deleteTask($this->task_id)){
							$view = $strongcal->getUserVar("highlight");
							throw new RedirectException("index.php?view=$view");
						}else{
							$text = new Text("Task " . $this->task_id . " has NOT been deleted.");
						}

						$text->setStyle(new Style("form_header"));
						$notice->add($text);
						$deltask_view->add($notice);
						return new module_bootstrap_data($deltask_view, "the delete task view");
					}

					$label_style = new Style();
					$label_style->setFontSize(8);
					$label_style->setFontFamily("verdana, sans-serif");

					$container_style = new Style("container");
					$container_style->setWidth("450px");
					$container_style->setHeight("325px");

					$left_cell_style = new Style("panel");
					$left_cell_style->setWidth("50px");
					$left_cell_style->setHeight("50px");

					$right_cell_style = new Style("title");
					$right_cell_style->setWidth("370px");
					$right_cell_style->setHeight("50px");

					$date_style = new Style("date_style");
					$date_style->setWidth("420px");

					$notice_style = new Style("notice_panel");
					$notice_style->setWidth("420px");

					$content_style = new Style("content_style");
					$content_style->setWidth("420px");
					$content_style->setHeight("285px");

					$image_style = new Style();
					$image_style->setWidth("420px");

					$history_style = new Style("historys");
					$history_style->setWidth("420px");
					$history_style->setHeight("200px");
					$history_style->setBackground("#D5DAD0");

					$panel_style = new Style();
					$panel_style->setHeight("325px");

					$reminder_content_style = new Style("content_style");
					$reminder_content_style->setWidth("420px");
					$reminder_content_style->setHeight("315px");

					$bottom_style = new Style("bottom");
					$bottom_style->setWidth("450px");
					$bottom_style->setHeight("220px");

					$color_style = new Style("container");
					$color_style->setWidth("35px");
					$color_style->setHeight("35px");
					$color_style->setBackground($cal->color());

					$border_bottom_style = new Style("border_bottom");
					/**
					 * end defining styles
					 */


					/**
					 * begin constructing container
					 */
					 // this conains the form input for changing the status
					$status_panel = new FormPanel("index.php");
					$status_panel->setStyle(clone $notice_style);
					$status_panel->getStyle()->setDisplayNone();

					$to_label = new Text("To");
					$to_label->setStyle($label_style);

					$my_container = new GridPanel(1);
					$my_container->setValign("top");
					$my_container->setStyle($container_style);

					$title = new Text("<b>Delete Task?</b><br>");
					$text = new Text("Delete the task <i>" . $task->title() . "</i>?<br>");
					$warning = new Text("(All related information will be lost. This cannot be reversed.)");
					$warning->getStyle()->setFontSize(8);
					$delete_confirm_window = new SimpleWindow($title);
					$delete_confirm_window->add($text);
					$delete_confirm_window->add($warning);
					$yes_action = new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=task&subview=delete&commit=1&task_id=" . $this->task_id);
					$no_action = new MoveToAction($delete_confirm_window, -1000, -1000);

					$buttons = new SimplePanel();
					$buttons->setStyle(new Style("button_group_style"));

					$no_button = new Button("Never Mind");
					$no_button->setStyle(new Style("confirm_window_no"));
					$no_button->addAction($no_action);
					$buttons->add($no_button);

					$yes_button = new Button("Delete");
					$yes_button->setStyle(new Style("confirm_window_yes"));
					$yes_button->addAction($yes_action);
					$yes_button->addAction($no_action);
					$buttons->add($yes_button);
					$delete_confirm_window->add($buttons);

					$delete_button = new Button("delete");
					$delete_button->getStyle()->setPaddingTop(4);
					$delete_button->getStyle()->setPaddingBottom(4);
					$delete_button->getStyle()->setClassname("event_button");
					$delete_button->addAction(new MoveToCenterAction($delete_confirm_window));

					$edit_button = new Button("edit");
					$edit_button->getStyle()->setPaddingTop(4);
					$edit_button->getStyle()->setPaddingBottom(4);
					$edit_button->getStyle()->setMarginLeft(10);
					$edit_button->getStyle()->setClassname("event_button");
					$edit_button->addAction(new LoadPageAction("index.php?view=edit_task&task_id=" . $this->task_id));


					$show_history_button = new Button("view history");
					$hide_history_button = new Button("hide history");

					$show_history_button->getStyle()->setPaddingTop(4);
					$show_history_button->getStyle()->setPaddingBottom(4);
					$show_history_button->getStyle()->setMarginRight(10);
					$show_history_button->getStyle()->setClassname("event_button");
					$show_history_button->addAction(new DisplayNoneAction($show_history_button));
					$show_history_button->addAction(new DisplayBlockAction($hide_history_button));
					$show_history_button->addAction(new DisplayBlockAction($fifth_row));

					$hide_history_button->getStyle()->setPaddingTop(4);
					$hide_history_button->getStyle()->setPaddingBottom(4);
					$hide_history_button->getStyle()->setMarginRight(10);
					$hide_history_button->getStyle()->setClassname("event_button");
					$hide_history_button->getStyle()->setDisplayNone();
					$hide_history_button->addAction(new DisplayNoneAction($hide_history_button));
					$hide_history_button->addAction(new DisplayBlockAction($show_history_button));
					$hide_history_button->addAction(new DisplayNoneAction($fifth_row));

					$history_panel = new Panel();
					$history_panel->add($show_history_button);
					$history_panel->add($hide_history_button);

					$export_button = new Button("");
					$export_button->setStyle(new Style("export_button"));
					$export_button->addAction(new LoadPageAction("?view=export&range=task&task_id=" . $task->getId()));

					$history_export_panel = new GridPanel(2);
					$history_export_panel->add($export_button);
					$history_export_panel->add($history_panel);


					$button_panel = new RowPanel();
					if($task->canWrite() || $task->delegatedTo() == $this->avalanche->loggedInHuh()){
						$button_panel->add($edit_button);
					}
					if($task->canWrite()){
						$button_panel->add($delete_button);
						$this->doc->addHidden($delete_confirm_window);
					}

					$top_row = new BorderPanel();
					$color_panel = new Panel();
					$color_panel->setStyle($left_cell_style);
					$color_panel2 = new Panel();
					$color_panel2->setStyle($color_style);
					$real_title_panel = new GridPanel(1);
					$real_title_panel->setStyle($right_cell_style);
					$color_panel->add($color_panel2);
					$color_panel->setAlign("center");
					$color_panel->setValign("middle");
					$title = $task->title();
					if(strlen($title) == 0){
						$title = "<i>no title</i>";
					}
					$real_title_panel->add(new Text($title));
					$calendar_text = new Text("in the " . $cal->name() . " calendar");
					$calendar_text->setStyle(new Style("cal_title"));
					$real_title_panel->add($calendar_text);
					$top_row->setWest($color_panel);
					$top_row->setCenter($real_title_panel);
					// $top_row->setEast($button_panel);
					$top_row->setEastWidth("200");
					$top_row->setAlign("center");

					// task panel will hold all basic event info
					$task_panel = new GridPanel(1);
					$task_panel->setStyle(clone $panel_style);
					$task_panel->setAlign("center");
					$task_panel->setWidth("100%");

					// reminder panel
					$reminder_panel = new GridPanel(1);
					$reminder_panel->setStyle(clone $panel_style);
					$reminder_panel->setAlign("center");
					$reminder_panel->setWidth("100%");
					$content_panel = new ScrollPanel();
					$content_panel->setStyle($reminder_content_style);
					$panel = $this->getReminderPanel($task);
					$content_panel->add($panel);
					$reminder_panel->add($content_panel);

					// create tabs for top view
					$tab_panel = new GridPanel(1);
					$tab_panel->setWidth("100%");
					$tab_panel->getStyle()->setWidth("450px");
					$tab_panel->add($this->getTabs($subview, $task_panel, $reminder_panel, $task));


					// format the start/end times/dates
					$start_time = substr($task->createdOn(), 11);
					$start_date = substr($task->createdOn(), 0, 10);
					$start_hour = substr($start_time, 0, 2);
					$start_min  = substr($start_time, 3, 2);
					$start_year  = substr($start_date, 0, 4);
					$start_month = substr($start_date, 5, 2);
					$start_day   = substr($start_date, 8, 2);

					$end_time = substr($task->due(), 11);
					$end_date = substr($task->due(), 0, 10);
					$end_hour = substr($end_time, 0, 2);
					$end_min  = substr($end_time, 3, 2);
					$end_year  = substr($end_date, 0, 4);
					$end_month = substr($end_date, 5, 2);
					$end_day   = substr($end_date, 8, 2);

					$start_stamp = mktime($start_hour, $start_min, 0, $start_month, $start_day, $start_year);
					$end_stamp   = mktime($end_hour, $end_min, 0, $end_month, $end_day, $end_year);

					$created_on_date = "on " .
					"<a href='index.php?view=day&date=" . date("Y-m-d", $start_stamp) . "' style='color:black;'>" .
					date("l, F jS, Y", $start_stamp) . "</a>" .
					" at " .
					date("g:i A ", $start_stamp);

					$created_by = $os->getUsername($task->author());
					$created_by = new Text($created_by);
					$this->createUserMenu($created_by, $task->assignedTo());
					$created_by->getStyle()->setFontColor("black");

					$due_date =   "Due: " .
					"<a href='index.php?view=day&date=" . date("Y-m-d", $end_stamp) . "' style='color:black;'>" .
					date("l, F jS, Y", $end_stamp) . "</a>" .
					" at " .
					date("g:i A ", $end_stamp);


					$second_row = new GridPanel(1);

					$assigned_to_panel = new Panel();
					$assigned_to_panel->setStyle(new Style("created_on_date_style"));
					$assigned_to_panel->add(new Text("Assigned to "));
					$assigned_to = $os->getUsername($task->assignedTo());
					$assigned_to = new Text($assigned_to);
					$this->createUserMenu($assigned_to, $task->assignedTo());
					$assigned_to->getStyle()->setFontColor("black");
					$assigned_to_panel->add($assigned_to);
					$assigned_to_panel->getStyle()->setMarginLeft(3);
					$second_row->add($assigned_to_panel);

					$date_panel = new Panel();
					$date_panel->setStyle($date_style);
					$date_panel->add(new Text($due_date));
					$date_panel->setValign("top");

					// priority
					$field_txt = $task->priority();
					if($field_txt == module_taskman_task::$PRIORITY_LOW){
						$field_txt = "Low";
					}else if($field_txt == module_taskman_task::$PRIORITY_NORMAL){
						$field_txt = "Normal";
					}else if($field_txt == module_taskman_task::$PRIORITY_HIGH){
						$field_txt = "High";
						if($task->status() != module_taskman_task::$STATUS_COMPLETED){
							$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $strongcal->folder() . "/gui/os/exclamation.gif");
							$icon->setWidth(35);
							$icon->setHeight(35);
							$color_panel2->add($icon);
						}
					}else{
						$field_txt = "Unknown";
					}
					$field_panel = new Panel();
					$field_panel->setStyle(new Style("created_on_date_style"));
					$field_panel->add(new Text("Priority: " . $field_txt));
					$date_panel->add($field_panel);


					// status
					$field_val = $task->status();
					$field_panel = new Panel();
					$field_panel->setStyle(new Style("created_on_date_style"));
					if($field_val == module_taskman_task::$STATUS_ACCEPTED){
						$field_panel->add(new Text("Status: Accepted by "));
						$field_user = $os->getUsername($task->assignedTo());
						$user = new Link($field_user, "javascript:;");
						$this->createUserMenu($user, $task->assignedTo());
						$user->getStyle()->setFontColor("black");
						$field_panel->add($user);
					}else if($field_val == module_taskman_task::$STATUS_NEEDS_ACTION){
						$field_panel->add(new Text("Status: Needs Action"));
					}else if($field_val == module_taskman_task::$STATUS_DECLINED){
						$field_panel->add(new Text("Status: Declined by "));
						$field_user = $os->getUsername($task->delegatedTo());
						$user = new Link($field_user, "javascript:;");
						$this->createUserMenu($user, $task->delegatedTo());
						$user->getStyle()->setFontColor("black");
						$field_panel->add($user);
					}else if($field_val == module_taskman_task::$STATUS_COMPLETED){
						$field_panel->add(new Text("Status: Completed"));
						$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $taskman->folder() . "/gui/os/completedlarge.gif");
						$icon->setWidth(35);
						$icon->setHeight(35);
						$color_panel2->add($icon);
					}else if($field_val == module_taskman_task::$STATUS_DELEGATED){
						$field_panel->add(new Text("Status: Delegated to "));
						$field_user = $os->getUsername($task->delegatedTo());
						$user = new Link($field_user, "javascript:;");
						$this->createUserMenu($user, $task->delegatedTo());
						$user->getStyle()->setFontColor("black");
						$field_panel->add($user);
					}else if($field_val == module_taskman_task::$STATUS_CANCELLED){
						$field_panel->add(new Text("Status: Cancelled"));
						$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $taskman->folder() . "/gui/os/cancelledlarge.gif");
						$icon->setWidth(35);
						$icon->setHeight(35);
						$color_panel2->add($icon);
					}else{
						$field_txt = "Status: Unknown";
						$field_panel->add(new Text($field_txt));
					}
					if($task->canWrite()){
						$field_panel->add(new Text(" ("));
						$change = new Link("change", "javascript:;");
						$dont_change = new Link(htmlspecialchars("don't change"), "javascript:;");
						$change->getStyle()->setFontColor("#660000");
						$change->addAction(new DisplayNoneAction($change));
						$change->addAction(new DisplayInlineAction($dont_change));
						$change->addAction(new DisplayBlockAction($status_panel));
						$dont_change->getStyle()->setFontColor("#660000");
						$dont_change->getStyle()->setDisplayNone();
						$dont_change->addAction(new DisplayNoneAction($dont_change));
						$dont_change->addAction(new DisplayInlineAction($change));
						$dont_change->addAction(new DisplayNoneAction($status_panel));
						$field_panel->add($change);
						$field_panel->add($dont_change);
						$field_panel->add(new Text(")"));
						$date_panel->add($field_panel);
					}

					if($task->status() == module_taskman_task::$STATUS_COMPLETED && $task->completed() != "0000-00-00 00:00:00"){
						$history = $task->history();
						$completer = false;
						foreach($history as $item){
							if($item["status"] == module_taskman_task::$STATUS_COMPLETED &&
							   $completer === false){
								$completer = $item["modified_by"];
							}
						}
						// completed on
						$field_panel = new Panel();
						$field_panel->setStyle(new Style("content_font"));
						$text = new Text("Completed on " . $task->completed() . " by ");
						$text->setStyle(new Style("created_on_date_style"));
						$field_panel->add($text);
						$field_user = $os->getUsername($completer);
						$link = new Link($field_user, "javascript:;");
						$this->createUserMenu($link, $completer);
						$link->getStyle()->setFontColor("black");
						$link->getStyle()->setFontFamily("verdana, sans-serif");
						$link->getStyle()->setFontSize(8);
						$field_panel->add($link);
						$date_panel->add($field_panel);
					}

					if($task->status() == module_taskman_task::$STATUS_CANCELLED && $task->cancelled() != "0000-00-00 00:00:00"){
						$history = $task->history();
						$completer = $task->modifiedBy();
						foreach($history as $item){
							if($item["status"] == module_taskman_task::$STATUS_CANCELLED &&
							   $completer === false){
								$completer = $item["modified_by"];
							}
						}
						// completed on
						$field_panel = new Panel();
						$field_panel->setStyle(new Style("content_font"));
						$text = new Text("Cancelled on " . $task->completed() . " by ");
						$text->setStyle(new Style("created_on_date_style"));
						$field_panel->add($text);
						$field_user = $os->getUsername($completer);
						$link = new Link($field_user, "javascript:;");
						$this->createUserMenu($link, $completer);
						$link->getStyle()->setFontColor("black");
						$link->getStyle()->setFontFamily("verdana, sans-serif");
						$link->getStyle()->setFontSize(8);
						$field_panel->add($link);
						$date_panel->add($field_panel);
					}

					$second_row->add($date_panel);

					// most recent comment in status history, if any
					$comments = $task->history();
					if(count($comments) && strlen($comments[0]["comment"])){
						$comment_panel = new BorderPanel(1);
						$comment_panel->setStyle(clone $notice_style);
						$comment_panel->getStyle()->setPadding(6);
						$comment_panel->setCenter(new Text($comments[0]["comment"] . "<br><div width='100%' align='right' style='padding-right: 10px;'> - " . $os->getUsername($comments[0]["modified_by"]) . "</div>"));
						$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/bubble.gif");
						$icon->getStyle()->setPadding(3);
						$icon->getStyle()->setMarginRight(8);
						$icon->getStyle()->setBackground("white");
						$icon->getStyle()->setBorderColor("black");
						$icon->getStyle()->setBorderWidth(1);
						$icon->getStyle()->setBorderStyle("solid");
						$comment_panel->setWest($icon);
						$second_row->add($comment_panel);
					}


					$inner_status_panel = new GridPanel(1);

					$status_panel->addHiddenField("view", "edit_task");
					$status_panel->addHiddenField("edit_task", "1");
					$status_panel->addHiddenField("task_id", (string) $task->getId());
					if($task->status() == module_taskman_task::$STATUS_DELEGATED && $this->avalanche->loggedInHuh() == $task->delegatedTo()){
						$inner_panel = new GridPanel(1);
						// if it's delegated to me, then i can accept or decline it.
						$status_field = new GridPanel(2);
						$status_field->getCellStyle()->setPaddingRight(2);

						$accept_button = new RadioInput();
						$accept_button->setChecked(true);
						$accept_button->setName($prefix . "status");
						$accept_button->setValue((string) module_taskman_task::$STATUS_ACCEPTED);
						$accept_panel = new GridPanel(2);
						$accept_panel->setStyle(new Style("radio_button_style"));
						$accept_panel->getCellStyle()->setPaddingTop(2);
						$accept_panel->getCellStyle()->setPaddingBottom(2);
						$accept_panel->getCellStyle()->setPaddingRight(3);
						$accept_panel->getStyle()->setBackground("#FFFFFF");
						//$accept_panel->setValign("middle");
						$accept_panel->add($accept_button);
						$accept_text = new Text("Accept");
						$accept_panel->add($accept_text);
						$accept_panel->addAction(new CheckAction($accept_button));

						$decline_button = new RadioInput();
						$decline_button->setName($prefix . "status");
						$decline_button->setValue((string) module_taskman_task::$STATUS_DECLINED);
						$decline_panel = new GridPanel(2);
						$decline_panel->setStyle(new Style("radio_button_style"));
						$decline_panel->getCellStyle()->setPaddingTop(2);
						$decline_panel->getCellStyle()->setPaddingBottom(2);
						$decline_panel->getCellStyle()->setPaddingRight(3);
						$decline_panel->getStyle()->setBackground("#FFFFFF");
						//$decline_panel->setValign("middle");
						$decline_text = new Text("Decline");
						$decline_panel->add($decline_button);
						$decline_panel->add($decline_text);
						$decline_panel->addAction(new CheckAction($decline_button));

						$status_field->add($accept_panel);
						$status_field->add($decline_panel);

						$inner_panel->getStyle()->setFontSize(10);
						$inner_panel->add(new Text("This task has been delegated to you!"));
						$inner_panel->add($status_field);

						$status_panel->getStyle()->setDisplayBlock();
						if($task->canWrite()){
							$change->getStyle()->setDisplayNone();
							$dont_change->getStyle()->setDisplayInline();
						}
						$inner_status_panel->add($inner_panel);
					}else if($task->canWrite() || $task->delegatedTo() == $this->avalanche->loggedInHuh()){
						$inner_panel = new GridPanel(3);

						// if it's not delegated to me, then i can either delegate it, complete it, or set it as needing action.
						$status_field = new DropDownInput();
						$status_field->setName($prefix . "status");
						$status_field->addOption(new DropDownOption("Needs Action", (string) module_taskman_task::$STATUS_NEEDS_ACTION));
						$status_field->addOption(new DropDownOption("Completed", (string) module_taskman_task::$STATUS_COMPLETED));
						$status_field->addOption(new DropDownOption("Delegated", (string) module_taskman_task::$STATUS_DELEGATED));
						$status_field->addOption(new DropDownOption("Cancelled", (string) module_taskman_task::$STATUS_CANCELLED));
						$status_field->setValue((string) $task->status());

						// make user drop down
						$users = $this->avalanche->getAllUsers();
						$users_dropdown = new DropDownInput();
						$users_dropdown->setName($prefix . "user_id");
						foreach($users as $user){
							$users_dropdown->addOption(new DropDownOption($os->getUsername($user->getId()), (string) $user->getId()));
						}
						$users_dropdown->setValue((string)$task->delegatedTo());
						$to_label->getStyle()->setPaddingRight(3);
						$users_dropdown->getStyle()->setMarginBottom(5);

						if($task->status() != module_taskman_task::$STATUS_DELEGATED){
							// if it's not currently delegated, then hide the delegation options.
							$to_label->getStyle()->setDisplayNone();
							$users_dropdown->getStyle()->setDisplayNone();
						}

						$ddaction = new DropDownAction($status_field);
						$ddaction->addAction((string)module_taskman_task::$STATUS_DELEGATED, new DisplayBlockAction($to_label));
						$ddaction->addAction((string)module_taskman_task::$STATUS_DELEGATED, new DisplayBlockAction($users_dropdown));
						$ddaction->addAction((string)module_taskman_task::$STATUS_COMPLETED, new DisplayNoneAction($to_label));
						$ddaction->addAction((string)module_taskman_task::$STATUS_COMPLETED, new DisplayNoneAction($users_dropdown));
						$ddaction->addAction((string)module_taskman_task::$STATUS_CANCELLED, new DisplayNoneAction($to_label));
						$ddaction->addAction((string)module_taskman_task::$STATUS_CANCELLED, new DisplayNoneAction($users_dropdown));
						$ddaction->addAction((string)module_taskman_task::$STATUS_NEEDS_ACTION, new DisplayNoneAction($to_label));
						$ddaction->addAction((string)module_taskman_task::$STATUS_NEEDS_ACTION, new DisplayNoneAction($users_dropdown));
						$status_field->addChangeAction($ddaction);
						$inner_panel->add($status_field);
						$inner_panel->add($to_label);
						$inner_panel->add($users_dropdown);
						$inner_status_panel->add($inner_panel);
					}
					// the comment checkbox
					$comment_input = new TextAreaInput();
					$comment_input->setName($prefix . "comment");
					$comment_input->setCols(35);
					$comment_input->setRows(3);
					$comment_input->setStyle(new Style("input"));
					$comment_input->getStyle()->setDisplayNone();
					$comments_check = new CheckInput("[with comment]");
					$comments_check->setName($prefix . "with_comment");
					$comments_check->setValue("1");
					$comments_check->addClickAction(new IfCheckedThenAction($comments_check, new DisplayBlockAction($comment_input)));
					$comments_check->addClickAction(new IfNotCheckedThenAction($comments_check, new DisplayNoneAction($comment_input)));
					$comments_check->setStyle(new Style("task_label"));

					$button = new Text("<input type='submit' value='Update' style='border:1px solid black;'>");

					$inner_status_panel->add($comments_check);
					$inner_status_panel->add($comment_input);
					$format_status_panel = new GridPanel(1);
					$format_status_panel->add($inner_status_panel);
					$format_status_panel->add($button);
					$status_panel->add($format_status_panel);
					$second_row->add($status_panel);

					$third_row = new GridPanel(1);
					$content_panel = new ScrollPanel();
					$content_panel->setStyle($content_style);

					// description
					$field_txt = $task->description();
					if(strlen($field_txt) == 0){
						$field_txt = "<i>No Data</i>";
					}
					$field_name = "Description";
					$field_txt = str_replace("\n", "<br>", $field_txt);
					$field_panel = new QuotePanel(20);
					$field_panel->setStyle(new Style("content_font"));
					$field_panel->add(new Text($field_txt));
					$field_title = new Text($field_name);
					$field_title->setStyle(new Style("content_title"));
					$content_panel->add($field_title);
					$content_panel->add($field_panel);


					$content_panel->setAlign("left");
					$content_panel->setValign("top");
					$third_row->add($content_panel);

					$created_on = new Text($created_on_date);
					$created_on->setStyle(new Style("created_on_date_style"));

					$created_panel = new GridPanel(2);
					$created_panel->setCellStyle(new Style("created_on_date_style"));
					$created_panel->getCellStyle()->setPaddingLeft(3);
					$created_panel->add(new Text("Created"));
					$created_panel->add(new Text($created_on_date));
					$created_panel->add(new Text(""));
					$by = new Panel();
					$by->setStyle(new Style("created_on_date_style"));
					$by->getStyle()->setPaddingRight(3);
					$by->add(new Text("by "));
					$by->add($created_by);
					$created_panel->add($by);

					$third_row->add($created_panel);


					$fourth_row = new BorderPanel();
					$fourth_row->setWidth("100%");
					$fourth_row->getStyle()->setMarginLeft(0);
					$fourth_row->setWest($button_panel);
					$fourth_row->setCenter(new Panel());
					$fourth_row->setEast($history_export_panel);

					$history_panel = new ScrollPanel();
					$history_panel->setStyle($history_style);
					$history_panel->getStyle()->setWidth("420px");
					$history_panel->getStyle()->setHeight("110px");
					$history_panel->setValign("top");

					$history_panel_holder = new Panel();
					$history_panel_holder->setValign("middle");
					$history_panel_holder->setAlign("center");
					$history_panel_holder_style = new Style("historys_holder");
					$history_panel_holder_style->setWidth("420px");
					$history_panel_holder_style->setHeight("110px");
					$history_panel_holder->setStyle($history_panel_holder_style);
					$history_panel_holder->add($history_panel);

					$fifth_row->setValign("middle");
					$fifth_row->setAlign("center");
					$fifth_row_style = new Style("history_row");
					$fifth_row_style->setPadding(10);
					$fifth_row_style->setWidth("100%");
					$fifth_row_style->setHeight("150px");
					$fifth_row_style->setDisplayNone();
					$fifth_row->setStyle($fifth_row_style);
					$fifth_row->add($history_panel_holder);

					$author_style = new Style("history_author");
					$title_style = new Style("history_title");
					$date_style = new Style("history_date");
					$light = true;
					$status_history = $task->history();
					foreach($status_history as $item){
						$title_panel = new Panel();
						$title_panel->setWidth("100%");
						if($light){
							$temp_style = new Style("historys_light");
							$button_style = new Style("buttons_dark");
						}else{
							$temp_style = new Style("historys_dark");
							$button_style = new Style("buttons_light");
						}
						$title_panel->getStyle()->setFontFamily("arial, sans-serif");
						$title_panel->getStyle()->setFontSize(9);

						$author = new Link($os->getUsername($item["modified_by"]), "javascript:;");
						$this->createUserMenu($author, (int)$item["modified_by"]);
						$author->setStyle($author_style);

						$title = new Text($this->getStatusName((int)$item["status"]));
						$title->setStyle($title_style);

						$date = $item["stamp"];
						$date = explode(" ", $date);
						$time = $date[1];
						$date = $date[0];
						$d = $strongcal->adjust($date, $time);
						$date = $d["date"];
						$time = $d["time"];
						$date = explode("-", $date);
						$time = explode(":", $time);
						$date = mktime($time[0],$time[1],$time[2], $date[1], $date[2], $date[0]);
						$date = date("M jS g:ia", $date);

						$date = new Text($date);
						$date->setStyle($date_style);

						$title_panel->add($title);
						if(((int)$item["status"]) == module_taskman_task::$STATUS_DELEGATED){
							$assignee = new Link($os->getUsername($item["assigned_to"]), "javascript:;");
							$this->createUserMenu($assignee, (int)$item["assigned_to"]);
							$assignee->setStyle($author_style);
							$title_panel->add(new Text(" to "));
							$title_panel->add($assignee);
						}
						if(strlen($item["comment"])){
							$title_panel->add(new Text(": " . $item["comment"]));
						}

						$author_panel = new Panel();
						$author_panel->getStyle()->setFontFamily("arial, sans-serif");
						$author_panel->getStyle()->setFontSize(9);
						$author_panel->setWidth("100%");
						$author_panel->setAlign("right");
						$author_panel->add(new Text("by "));
						$author_panel->add($author);


						$north_panel = new GridPanel(2);
						$north_panel->setValign("bottom");
						$north_panel->getCellStyle()->setPadding(2);
						$north_panel->setWidth("100%");
						$north_panel->add($date);
						$north_panel->add($author_panel);
						$temp_panel = new BorderPanel();
						$temp_panel->setStyle($temp_style);
						$temp_panel->setWidth("100%");
						$temp_panel->setNorth($north_panel);
						$temp_panel->setCenter($title_panel);

						$history_panel->add($temp_panel);
						$light = !$light;
					}

					$task_panel->add($third_row);

					$content = new Panel();
					$content->add($task_panel);
					$content->add($reminder_panel);

					$my_container->add($top_row);
					$my_container->add($second_row);
					$my_container->add($tab_panel);
					$my_container->add($content);
					$my_container->add($fourth_row);
					$my_container->add($fifth_row);
					$my_container->setAlign("center");

					/**
					 * end constructing the container
					 */

					$task_view = new Panel();

					$task_style = new Style("big_task_container");
					$task_style->setPaddingTop(70);
					$task_style->setPaddingLeft(110);
					$task_style->setWidth("100%");
					$task_view->setStyle($task_style);
					$task_view->setAlign("left");
					$task_view->add($my_container);
				}else{
					$content = new Panel();
					$content->getStyle()->setClassname("error_panel");
					$content->add(new Text("You are not allowed to view this task."));
					$error = new ErrorPanel($content);
					$error->getStyle()->setHeight("400px");
					$task_view = $error;
				}
			}catch(TaskNotFoundException $e){
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("Task #" . $this->task_id . " does not exist."));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$task_view = $error;
			}
			return new module_bootstrap_data($task_view, "a gui component for the task view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}



	private function getTabs($subview, $task_panel, $reminder_panel, $task){
		$reminder = $this->avalanche->getModule("reminder");
		$view = "task";
		$subview = "task";
		if(isset($this->data_list["subview"])){
			$subview = $this->data_list["subview"];
		}


		$buttons = new RowPanel();
		$buttons->setRowHeight("26");
		$buttons->setStyle(new Style("preferences_buttons_panel"));
		$buttons->setAlign("left");
		//$buttons->getStyle()->setWidth("150px");

		$open_general_button = new Button();
		$open_general_button->setStyle(new Style("preferences_tab_open"));
		$open_general_button->setAlign("left");
		$open_general_button->add(new Text("Task"));

		$closed_general_button = new Button();
		$closed_general_button->setStyle(new Style("preferences_tab_closed"));
		$closed_general_button->setAlign("left");
		$closed_general_button->add(new Text("Task"));

		$open_calendar_button = new Button();
		$open_calendar_button->setStyle(new Style("preferences_tab_open"));
		$open_calendar_button->setAlign("center");
		$open_calendar_button->add(new Text("Attendees"));

		$closed_calendar_button = new Button();
		$closed_calendar_button->setStyle(new Style("preferences_tab_closed"));
		$closed_calendar_button->setAlign("center");
		$closed_calendar_button->add(new Text("Attendees"));

		$open_notifier_button = new Button();
		$open_notifier_button->setStyle(new Style("preferences_tab_open"));
		$open_notifier_button->setAlign("center");
		$open_notifier_button->add(new Text("Reminders"));

		$closed_notifier_button = new Button();
		$closed_notifier_button->setStyle(new Style("preferences_tab_closed"));
		$closed_notifier_button->setAlign("center");
		$closed_notifier_button->add(new Text("Reminders"));

		$open_general_button->getStyle()->setDisplayNone();
		$closed_general_button->getStyle()->setDisplayBlock();
		$open_calendar_button->getStyle()->setDisplayNone();
		$closed_calendar_button->getStyle()->setDisplayBlock();
		$open_notifier_button->getStyle()->setDisplayNone();
		$closed_notifier_button->getStyle()->setDisplayBlock();

		// set button visibility
		$task_panel->getStyle()->setDisplayNone();
		$reminder_panel->getStyle()->setDisplayNone();
		if($subview == "task"){
			$open_general_button->getStyle()->setDisplayBlock();
			$closed_general_button->getStyle()->setDisplayNone();
			$task_panel->getStyle()->setDisplayBlock();
		}else if($subview == "reminders"){
			if(isset($this->data_list["add_reminder"]) && $this->data_list["add_reminder"]){
				if(!isset($this->data_list["body"])){
					throw new IllegalArgumentException("body must be sent as form input to add an reminder");
				}
				if(!isset($this->data_list["subject"])){
					throw new IllegalArgumentException("subject must be sent as form input to add an reminder");
				}
				if(!isset($this->data_list["hours_before"])){
					throw new IllegalArgumentException("hours_before must be sent as form input to add an reminder");
				}
				if(!isset($this->data_list["minutes_before"])){
					throw new IllegalArgumentException("minutes_before must be sent as form input to add an reminder");
				}
				if(!isset($this->data_list["days_before"])){
					throw new IllegalArgumentException("days_before must be sent as form input to add an reminder");
				}

				$reminder = $reminder->addReminder();
				$reminder->type(module_reminder_reminder::$TYPE_TASK, $task);

				$reminder->day((int)$this->data_list["days_before"]);
				$reminder->hour((int)$this->data_list["hours_before"]);
				$reminder->minute((int)$this->data_list["minutes_before"]);

				$subject = new SmallTextInput();
				$subject->setName("subject");
				$subject->loadFormValue($this->data_list);
				$reminder->subject($subject->getValue());

				$body = new SmallTextInput();
				$body->setName("body");
				$body->loadFormValue($this->data_list);
				$reminder->body($body->getValue());

				header("Location: index.php?view=$view&subview=$subview&task_id=" . $task->getId() . "&reminder_added=" . $reminder->getId());
				exit;
			}
			if(isset($this->data_list["remove_reminder"]) && $this->data_list["remove_reminder"]){
				if(!isset($this->data_list["reminder_id"])){
					throw new IllegalArgumentException("reminder_id must be sent as form input to delete a reminder");
				}
				$reminder->deleteReminder((int)$this->data_list["reminder_id"]);
				header("Location: index.php?view=$view&subview=$subview&task_id=" . $task->getId());
				exit;
			}
			$open_notifier_button->getStyle()->setDisplayBlock();
			$closed_notifier_button->getStyle()->setDisplayNone();
			$reminder_panel->getStyle()->setDisplayBlock();
		}

		// add buttons
		$general = new Panel();
		$general->add($open_general_button);
		$general->add($closed_general_button);
		$notifier = new Panel();
		$notifier->add($open_notifier_button);
		$notifier->add($closed_notifier_button);
		$buttons->add($general);
		$buttons->add($notifier);

		// create visibility functions and set up actions
		$closefunction = new NewFunctionAction("close_general_tabs");
		$closefunction->addAction(new DisplayNoneAction($open_general_button));
		$closefunction->addAction(new DisplayNoneAction($task_panel));
		$closefunction->addAction(new DisplayBlockAction($closed_general_button));
		$closefunction->addAction(new DisplayNoneAction($open_notifier_button));
		$closefunction->addAction(new DisplayNoneAction($reminder_panel));
		$closefunction->addAction(new DisplayBlockAction($closed_notifier_button));
		$this->doc->addFunction($closefunction);

		$closed_general_button->addAction(new CallFunctionAction("close_general_tabs"));
		$closed_general_button->addAction(new DisplayNoneAction($closed_general_button));
		$closed_general_button->addAction(new DisplayBlockAction($open_general_button));
		$closed_general_button->addAction(new DisplayBlockAction($task_panel));

		$closed_notifier_button->addAction(new CallFunctionAction("close_general_tabs"));
		$closed_notifier_button->addAction(new DisplayNoneAction($closed_notifier_button));
		$closed_notifier_button->addAction(new DisplayBlockAction($open_notifier_button));
		$closed_notifier_button->addAction(new DisplayBlockAction($reminder_panel));

		return $buttons;
	}

	private function getReminderPanel($task){
		$os = $this->avalanche->getModule("os");
		$reminder = $this->avalanche->getModule("reminder");
		$strongcal = $this->avalanche->getModule("strongcal");
		$tab_panel = new TabbedPanel();
		$tab_panel->setWidth("100%");

		$title_style = new Style();
		$title_style->setFontFamily("verdana, sans-serif");
		$title_style->setFontSize(10);
		$title_style->setFontWeight("bold");

		$content_style = new Style();
		$content_style->setFontFamily("verdana, sans-serif");
		$content_style->setFontSize(10);

		$input_style = new Style();
		$input_style->setBorderWidth(1);
		$input_style->setBorderStyle("solid");
		$input_style->setBorderColor("black");
		$input_style->setFontSize(10);

		// list reminders tab
		$rs = $reminder->getRemindersFor($task);
		$reminder_text = new GridPanel(3);
		$reminder_text->setCellStyle(new Style("content_font"));
		$reminder_text->getCellStyle()->setPaddingRight(20);

		$error_style = new Style("content_font");
		$error_style->setFontColor("#CC0000");

		$rs_me = array();
		foreach($rs as $r){
			if(in_array((int)$this->avalanche->getActiveUser(), $r->getUsers())){
				$rs_me[] = $r;
			}
		}
		$rs = $rs_me;

		if(count($rs) == 0){
			$reminder_text = new Panel();
			$reminder_text->setStyle(new Style("content_font"));
			$reminder_text->setWidth("100%");
			$reminder_text->add(new Text("<br><i>There are no reminders registered for this event. Click the [Create New] tab to create a reminder for yourself.</i>"));
		}else{
			$reminder_text->add(new Text("<b>Reminders</b>"));
			$reminder_text->add(new Text(""));
			$reminder_text->add(new Text(""));
		}
		foreach($rs as $r){
			$added = "";
			$day = ($r->day() ? ($r->day() . " day" . ($r->day() > 1 ? "s":"")): "");
			$hour = ($r->day() && $r->hour() ? " and " : "") . ($r->hour() ? ($r->hour() . " hour" . ($r->hour() > 1 ? "s":"")): "");
			$minute = ((($r->day() || $r->hour()) && $r->minute()) ? " and " : "") . ($r->minute() ? ($r->minute() . " minute" . ($r->minute() > 1 ? "s":"")): "");
			if(strlen($day) || strlen($hour) || strlen($minute)){
				$text = $day . $hour . $minute . " before task is due";
			}else{
				$text = "When the task is due";
			}
			if(isset($this->data_list["reminder_added"]) && $r->getId() == $this->data_list["reminder_added"]){
				$text = "<b>$text</b>";
				$added = "<b>(just added)</b>";
			}
			$reminder_text->add(new Text((string)$text));
			if($r->sentOn() == "0000-00-00 00:00:00"){
				if($this->avalanche->loggedInHuh() == $task->author() ||
				   $strongcal->getCalendarFromDb($task->calId())->canWriteEntries() ||
				   $r->author() == $this->avalanche->getActiveUser()){
					$reminder_text->add(new Link("remove", "index.php?view=task&subview=reminders&reminder_id=" . $r->getId() . "&remove_reminder=1&task_id=" . $task->getId()));
				}else{
					$reminder_text->add(new Text("&nbsp;"));
				}
			}else{
				$datetime = $r->sentOn();
				$timestamp = mktime(substr($datetime, 11, 2) + $strongcal->timezone(),substr($datetime, 14, 2),substr($datetime, 17, 2),substr($datetime, 5, 2),substr($datetime, 8, 2),substr($datetime, 0, 4));
				$date = date("D, M jS g:ia", $timestamp);
				$date = substr($date, 0, strlen($date) - 1);
				$reminder_text->add(new Text("sent on " . $date));
			}
			$reminder_text->add(new Text($added));
		}
		if(count($rs)){
			$panel = new GridPanel(1);
			$panel->setWidth("100%");
			$panel->setCellStyle(new Style("content_font"));
			$panel->add(new Text("This is the list of reminders for this task."));
			$panel->add($reminder_text);
			$reminder_text = $panel;
		}

		$content = new GridPanel(1);
		$content->setWidth("100%");
		$content->setStyle($content_style);

		$s = clone $title_style;
		$s->setPaddingBottom(6);
		$content->add(new Text("List of Reminders"), $s);
		$content->add($reminder_text);

		$open_button = new Button("Reminders");
		$open_button->setStyle(new Style("task_tab_light"));
		$close_button = new Button("Reminders");
		$close_button->setStyle(new Style("task_tab_dark"));
		$tab_panel->add($content, $open_button, $close_button);


		// remind me tab
		if($strongcal->getCalendarFromDb($task->calId())->canReadEntries() ||
		   $this->avalanche->getActiveUser() == $task->assignedTo() ||
		   $this->avalanche->getActiveUser() == $task->delegatedTo() ||
		   $this->avalanche->loggedInHuh() == $task->author()){
			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(clone $content_style);

			$form = new FormPanel("index.php");
			$form->setAsGet();
			$form->addHiddenField("view", "task");
			$form->addHiddenField("task_id", (string)$task->getId());
			$form->addHiddenField("subview", "reminders");
			$form->addHiddenField("add_reminder", "1");

			$day_input = new SmallTextInput();
			$day_input->setSize(2);
			$day_input->setStyle($input_style);
			$day_input->setName("days_before");
			$day_input->setValue("0");
			$day_input->addKeyPressAction(new NumberOnlyAction());

			$hour_input = new SmallTextInput();
			$hour_input->setSize(2);
			$hour_input->setStyle($input_style);
			$hour_input->setName("hours_before");
			$hour_input->setValue("0");
			$hour_input->addKeyPressAction(new NumberOnlyAction());

			$minute_input = new SmallTextInput();
			$minute_input->setSize(2);
			$minute_input->setStyle($input_style);
			$minute_input->setName("minutes_before");
			$minute_input->setValue("15");
			$minute_input->addKeyPressAction(new NumberOnlyAction());

			$inputs = new Panel();
			$inputs->setWidth("100%");
			$inputs->setStyle($content_style);
			$inputs->add(new Text("Please contact me "));
			$inputs->add($day_input);
			$inputs->add(new Text(" days, "));
			$inputs->add($hour_input);
			$inputs->add(new Text(" hours and "));
			$inputs->add($minute_input);
			$inputs->add(new Text(" minutes before this task is due."));

			$subject = new SmallTextInput();
			$subject->setName("subject");
			$subject->setValue($task->title());
			$subject->setStyle($input_style);
			$subject->setSize(30);

			$body = new TextAreaInput();
			$body->setName("body");

			$date = substr($task->due(), 0, 10);
			$time = substr($task->due(),11);
			$time = mktime(substr($time, 0, 2), substr($time, 3, 2), substr($time, 6, 2), substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

			$text = "This is a reminder for the \"" . $strongcal->getCalendarFromDb($task->calId())->name() . "\" task \"" . $task->title() . "\"";
			$body->setValue($text);
			$body->setStyle($input_style);
			$body->setRows(4);
			$body->setCols(30);

			$form->add($inputs);
			$form_body = new GridPanel(2);
			$form_body->setStyle($content_style);
			$form_body->getCellStyle()->setPaddingTop(4);
			$form_body->getCellStyle()->setPaddingRight(4);
			$form_body->add(new Text("subject: "));
			$form_body->add($subject);
			$form_body->add(new Text("body: "));
			$form_body->add($body);
			$form_body->add(new Text(""));

			$form->add($form_body);

			$content->add(new Text("New Reminder for Me"), $title_style);
			$content->add(new Text("Never miss an important deadline again. Just set up a reminder to automatically contact you before the task is due.<br><br>"));
			if(strlen($this->avalanche->getUser($this->avalanche->getActiveUser())->email()) == 0){
				$content->add(new Text("You cannot set up this reminder because you do not have an email address set up in your profile. To set up your email, click the sprocket in the top right of the page and click \"My Profile.\"<br><br>"), $error_style);
				$disabled = " DISABLED";
			}else{
				$disabled = "";
			}
			$form_body->add(new Text("<input type='submit' value='Add Reminder' style='border:1px solid black' $disabled>"));
			$content->add($form);

			$open_button = new Button("Create New");
			$open_button->setStyle(new Style("task_tab_light"));
			$close_button = new Button("Create New");
			$close_button->setStyle(new Style("task_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}

		$tab_panel->setHolderStyle(new Style("task_holder"));
		$tab_panel->setContentStyle(new Style("reminder_content"));
		$this->doc->addFunction($tab_panel->getCloseFunction());

		$content = new GridPanel(1);
		$content->setWidth("100%");
		$s = clone($title_style);
		$s->setFontSize(12);
		$s->setPaddingBottom(3);
		$content->add(new Text("Reminders"), $s);
		$content->add($tab_panel);

		return $content;
	}


	private function formatName($user_id){
		$username = $this->avalanche->getUsername($user_id);
		$name = $this->avalanche->getName($user_id);
		$first = $name["first"];
		$last = $name["last"];
		if(strlen($first) > 0 && strlen($last) > 0){
			$name = $first . " " . $last;
		}else if(strlen($first) > 0){
			$name = $first;
		}else{
			$name = $last;
		}

		if(strlen($name) > 0){
			return $name;
		}else{
			return $username;
		}
	}

	private function getStatusName($status){
		if(!is_int($status)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		if($status == module_taskman_task::$STATUS_ACCEPTED){
			return "Accepted";
		}else if($status == module_taskman_task::$STATUS_NEEDS_ACTION){
			return "Needs Action";
		}else if($status == module_taskman_task::$STATUS_DECLINED){
			return "Declined";
		}else if($status == module_taskman_task::$STATUS_COMPLETED){
			return "Completed";
		}else if($status == module_taskman_task::$STATUS_CANCELLED){
			return "Cancelled";
		}else if($status == module_taskman_task::$STATUS_DELEGATED){
			return "Delegated";
		}
		return "Unknown";
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
}
?>