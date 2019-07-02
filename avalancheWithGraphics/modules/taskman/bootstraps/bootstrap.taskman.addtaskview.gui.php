<?

class module_bootstrap_taskman_addtaskview_gui extends module_bootstrap_module{

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

		$this->setName("Add task view for Aurora");
		$this->setInfo("adds an task. expects raw form input.");
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
			$os = $this->avalanche->getModule("os");

			if(!isset($data_list["cal_id"])){
				throw new IllegalArgumentException("form parameter \"cal_id\" must be sent into " . $this->name());
			}
			$cal_id = $data_list["cal_id"];
			if(!isset($data_list["date"])){
				$data_list["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $data_list["date"];
			if(!isset($data_list["time"])){
				$data_list["time"] = date("H:i", $strongcal->localtimestamp());
			}
			$time = $data_list["time"];

			$month = substr($date, 5, 2);
			$year  = substr($date, 0, 4);
			$day   = substr($date, 8, 2);
			$hour  = substr($time, 0, 2);
			$min   = substr($time, 3, 2);
			/** end initializing the input */

			/**
			 * let's make the panels !!!
			 */
			/************************************************************************
			get modules
			************************************************************************/
			$strongcal = $this->avalanche->getModule("strongcal");
			$taskman = $this->avalanche->getModule("taskman");
			$buffer = $this->avalanche->getSkin("buffer");
			$prefix = "taskman_";


			/**
			 * get the calendar
			 */
			$data = new module_bootstrap_data(array($cal_id), "send in a list of calendar ids to get");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_list = $data->data();
			$cal = $calendar_list[0];

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/add_task_style.css"));
			$this->doc->addStyleSheet($css);
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "taskman/gui/os/task_view.css"));
			$this->doc->addStyleSheet($css);


			/**
			 * the form has been submitted to here, so we need to actually add the task
			 */
			if(isset($data_list["add_task"])){
//				return new module_bootstrap_data(new Text(str_replace("\n", "<br>", print_r(array_merge($_REQUEST, $_FILES), true))), "data");
//				exit;
				$add_task_view = new Panel();
				$add_task_view->getStyle()->setWidth("100%");
				$add_task_view->getStyle()->setHeight("400");
				$add_task_view->setAlign("center");
				if($cal->canWriteEntries()){
					$date = new DateInput();
					$date->setName($prefix . "start_date");
					$date->loadFormValue($data_list);
					$time = new TimeInput();
					$time->setName($prefix . "start_time");
					$time->loadFormValue($data_list);
					$due_date = $date->getValue() . " " . $time->getValue();

					$new_task = $taskman->addTask($cal, array("summary" => $data_list[$prefix . "title"],
					      "due" => $due_date,
					      "priority" => (int)$data_list[$prefix . "priority"],
					      "description" => $data_list[$prefix . "description"]));


					if(is_object($new_task)){
						$comment = "";
						if(isset($data_list[$prefix . "with_comment"]) && $data_list[$prefix . "with_comment"]){
							$t = new SmallTextInput();
							$t->setName($prefix . "comment");
							$t->loadFormValue($data_list);
							$comment = $t->getValue();
						}
						if($data_list[$prefix . "status"] == module_taskman_task::$STATUS_DELEGATED){
							$new_task->status((int)$data_list[$prefix . "status"], (int)$data_list[$prefix . "user_id"], $comment);
						}else if($data_list[$prefix . "status"] != module_taskman_task::$STATUS_NEEDS_ACTION){
							$new_task->status((int)$data_list[$prefix . "status"], $comment);
						}else if(strlen($comment)){
							$new_task->status($new_task->status(), $comment);
						}
						header("Location: index.php?view=task&task_id=" . $new_task->getId());
						exit;
					}else{
						$done_text = new Text("there has been an error adding the task. please try again.");
						$done_text->setStyle(new Style("text"));
						$add_task_view->add($done_text);
						return new module_bootstrap_data($add_task_view, "error adding task");
					}
				}else{
					$done_text = new Text("you do not have permission to add tasks to this calendar.");
					$done_text->setStyle(new Style("text"));
					$add_task_view->add($done_text);
					return new module_bootstrap_data($add_task_view, "no permission to add task");
				}
			}


			if($cal->canWriteEntries()){
				/************************************************************************
				create style objects to apply to the panels
				************************************************************************/

				$container_style = new Style("add_task_container");
				$container_style->setWidth("470px");

				$color_style = new Style("color_container");
				$color_style->setWidth("35px");
				$color_style->setHeight("35px");
				$color_style->setBackground($cal->color());

				$left_cell_style = new Style("add_task_panel");
				$left_cell_style->setWidth("50px");
				$left_cell_style->setHeight("50px");

				$label_style = new Style("add_task_label");
				$label_style->setWidth("80px");

				$lower_label_style = new Style("add_task_label");

				$input_style = new Style("add_task_input");

				$buffer_style = new Style();
				$buffer_style->setHeight("50px");

				$mini_container_style = new Style("add_task_mini_container");
				$mini_container_style->setWidth("440px");

				$lower_container_style = new Style("add_task_mini_container2");
				$lower_container_style->setWidth("470px");
				$lower_container_style_cell = new Style("add_task_mini_container2_cell");

				$inner_lower_style = new Style("add_task_inner_mini");
				$inner_lower_style->setWidth("440px");



				/************************************************************************
				initialize panels
				************************************************************************/

				// containers
				$big_container =	new Panel();
				$my_container = 	new FormPanel("index.php");
				$my_container->addHiddenField("aurora_loader", "module_bootstrap_taskman_addtaskview_gui");
				$my_container->addHiddenField("add_task", "1");
				$my_container->addHiddenField("view", "add_task_step_3");
				$my_container->addHiddenField("cal_id", (string) $cal_id);
				$mini_container1 =	new Panel();
				$mini_container2 = 	new Panel();
				$header_label = 	new Panel();

				// inputs and input labels
				$title_label = 		new Panel();
				$title_input = 		new Panel();
				$due_date_label = 	new Panel();
				$due_date_input = 	new Panel();
				$due_time_label = 	new Panel();
				$due_time_input = 	new Panel();
				$status_label = 	new Panel();
				$to_label =		new Text("To");
				$status_input = 	new Panel();
				$priority_label = 	new Panel();
				$priority_input = 	new Panel();
				$description_label = 	new Panel();
				$description_input = 	new Panel();

				// specific cells in the table
				$color_panel = 		new Panel();
				$color_panel2 = 	new Panel();
				$top_right = 		new Panel();

				// table rows
				$top_row = 		new BorderPanel();
				$title_row =		new Panel();
				$second_row = 		new GridPanel(3);
				$third_row = 		new GridPanel(2);
				$fourth_row = 		new Panel();
				$fifth_row = 		new Panel();
				$buffer_row = 		new Panel();

				/************************************************************************
				************************************************************************/

				/************************************************************************

				apply styles to created panels
				************************************************************************/

				// inputs and input labels
				$title_label->setStyle($label_style);
				$title_input->setStyle($input_style);
				$due_date_label->setStyle($label_style);
				$due_date_input->setStyle($input_style);
				$due_time_label->setStyle($label_style);
				$due_time_input->setStyle($input_style);
				$status_label->setStyle($label_style);
				$status_input->setStyle($input_style);
				$to_label->setStyle($label_style);
				$priority_label->setStyle($label_style);
				$priority_input->setStyle($input_style);
				$description_label->setStyle($label_style);
				$description_input->setStyle($input_style);
				$header_label->setStyle(new Style("cal_label"));

				// containers
				$big_container->getStyle()->setClassname("big_container");
				$big_container->getStyle()->setWidth("100%");
				$my_container->setStyle($container_style);
				$mini_container1->setStyle($mini_container_style);
				$mini_container2->setStyle($mini_container_style);

				// specific cells in the table
				$color_panel->setStyle($left_cell_style);
				$color_panel2->setStyle($color_style);
				$buffer_row->setStyle($buffer_style);

				// tweaking panel attributes
				$my_container->setValign("top");
				$my_container->setAlign("center");
				$color_panel->setAlign("left");
				$color_panel->setValign("middle");

				/************************************************************************
				************************************************************************/

				/************************************************************************
				add necessary text and html
				************************************************************************/

				$title_label->add(new Text("Task Title:"));
				$due_date_label->add(new Text("Due:"));
				$description_label->add(new Text("Task Description:"));
				$status_label->add(new Text("Status:"));
				$priority_label->add(new Text("Priority:"));

				// create form manager objects
				$title_field = $cal->getField("title");
				$title_field->setLength(37);
				$s_time_field = $cal->getField("start_time");
				$s_date_field = $cal->getField("start_date");
				$desc_field = $cal->getField("description");
				$desc_field->setCols(42);
				$desc_field->setRows(5);

				// output form manager object html to specific panels
				$title_temp = $title_field->toGUI($prefix);
				$title_temp->setStyle(new Style("input"));
				$title_input->add($title_temp);
				$due_date_input->add($s_date_field->toGUI($prefix, $date));
				$due_time_input->add($s_time_field->toGUI($prefix, $time));
				$desc_temp = $desc_field->toGUI($prefix);
				$desc_temp->setCols(42);
				$desc_temp->setRows(5);
				$desc_temp->setStyle(new Style("input"));
				$description_input->add($desc_temp);

				$header_label->add(new Text($cal->name()));

				$status_field = new DropDownInput();
				$status_field->setName($prefix . "status");
				$status_field->addOption(new DropDownOption("Needs Action", (string) module_taskman_task::$STATUS_NEEDS_ACTION));
				$status_field->addOption(new DropDownOption("Completed", (string) module_taskman_task::$STATUS_COMPLETED));
				$status_field->addOption(new DropDownOption("Delegated", (string) module_taskman_task::$STATUS_DELEGATED));
				$status_field->addOption(new DropDownOption("Cancelled", (string) module_taskman_task::$STATUS_CANCELLED));

				// make user drop down
				$users = $this->avalanche->getAllUsers();
				$users_dropdown = new DropDownInput();
				$users_dropdown->setName($prefix . "user_id");
				foreach($users as $user){
					$users_dropdown->addOption(new DropDownOption($os->getUsername($user->getId()), (string) $user->getId()));
				}

				$to_label->setStyle(new Style($label_style->getClassname()));
				$to_label->getStyle()->setDisplayNone();
				$to_label->getStyle()->setPaddingRight(3);
				$users_dropdown->getStyle()->setDisplayNone();
				$users_dropdown->getStyle()->setMarginBottom(5);

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

				$priority_field = new DropDownInput();
				$priority_field->setName($prefix . "priority");
				$priority_field->addOption(new DropDownOption("High", (string) module_taskman_task::$PRIORITY_HIGH));
				$priority_field->addOption(new DropDownOption("Normal", (string) module_taskman_task::$PRIORITY_NORMAL));
				$priority_field->addOption(new DropDownOption("Low", (string) module_taskman_task::$PRIORITY_LOW));
				$priority_field->setValue((string) module_taskman_task::$PRIORITY_NORMAL);
				/************************************************************************
				************************************************************************/

				/************************************************************************
				put it all together
				************************************************************************/

				/************************************************************************
				remember:
				top_row is gridpanel(2)
				************************************************************************/
				$color_panel->add($color_panel2);

				$top_row->setWest($color_panel);
				$top_row->setEastWidth(400);
				$top_row->setEast($header_label);

				$title_row->add($title_label);
				$title_row->add($title_input);
				/************************************************************************
				remember:
				second_row is gridpanel(1)
				************************************************************************/
				$second_row->add($due_date_label);
				$second_row->add($due_date_input);
				$second_row->add($due_time_input);

				// status
				$comment_input = new TextAreaInput();
				$comment_input->setName($prefix . "comment");
				$comment_input->setCols(35);
				$comment_input->setRows(2);
				$comment_input->setStyle(new Style("input"));
				$comment_input->getStyle()->setDisplayNone();
				$comments_check = new CheckInput("[with comment]");
				$comments_check->setName($prefix . "with_comment");
				$comments_check->setValue("1");
				$comments_check->addClickAction(new IfCheckedThenAction($comments_check, new DisplayBlockAction($comment_input)));
				$comments_check->addClickAction(new IfNotCheckedThenAction($comments_check, new DisplayNoneAction($comment_input)));
				$comments_check->setStyle(new Style("add_task_label"));
				$status_input_panel = new GridPanel(3);
				$status_input->add($status_field);
				$third_row->add($status_label);
				$third_row->add($status_input_panel);
				$status_input_panel->add($status_input);
				$status_input_panel->add($to_label);
				$status_input_panel->add($users_dropdown);
				$third_row->add(new Text(""));
				$third_row->add($comments_check);
				$third_row->add(new Text(""));
				$third_row->add($comment_input);


				// priority
				$priority_input->add($priority_field);
				$third_row->add($priority_label);
				$third_row->add($priority_input);

				/************************************************************************
				remember:
				fourth_row is gridpanel(1)
				************************************************************************/
				$fourth_row->add($description_label);

				/************************************************************************
				remember:
				fifth_row is gridpanel(1)
				************************************************************************/
				$fifth_row->add($description_input);

				/************************************************************************
				remember:
				buffer_row is height 50px
				************************************************************************/
				$buffer_row->add(new Text("&nbsp;"));

				/************************************************************************
				this is the lower box for custom fields
				************************************************************************/
				//$fields = $cal->fields();
				// add more task fields
					// $should_add_lower = true;
					// $label_panel = new Panel();
					// $label_panel->setStyle($lower_label_style);
					// $label_panel->add(new Text($f->prompt()));
					// $inner_lower->add($label_panel);
					//
					// $input_panel = new Panel();
					// $input_panel->setStyle($input_style);
					// $temp = $f->toGUI($prefix);
					// $temp->setStyle(new Style("input"));
					// $input_panel->add($temp);
					// $inner_lower->add($input_panel);
				// end adding more task fields


				/*************************************************************************
				stuff
				**************************************************************************/
				$mini_container1->add($top_row);
				$mini_container1->add($title_row);
				$mini_container1->add($second_row);
				$mini_container1->add($third_row);
				$mini_container2->add($fourth_row);
				$mini_container2->add($fifth_row);

				$my_container->add($mini_container1);
				$my_container->add($buffer_row);
				$my_container->add($mini_container2);

				$button_panel = new Panel();
				$button_panel->getStyle()->setPadding(7);
				$button_panel->setAlign("center");
				$button_panel->setWidth("100%");
				$button_panel->add(new Text("<input style='border:1px solid black;' type='submit' value='Add Task'>"));

				$fifth_row->add($button_panel);

				$big_container->add($my_container);

				$add_task_view = new Panel();
				$add_task_view->getStyle()->setWidth("100%");
				$add_task_view->setAlign("left");
				$add_task_view->add($big_container);
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to add tasks for the " . $cal->name() . " calendar."));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$add_task_view = $error;
			}



			return new module_bootstrap_data($add_task_view, "a gui component for the add_task view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>