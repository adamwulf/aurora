<?

class module_bootstrap_strongcal_editeventview_gui extends module_bootstrap_module{

	private $avalanche;
	private $cal_id;
	private $event_id;
	private $doc;

	function __construct($avalanche, Document $doc, $cal_id, $event_id){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_integer($cal_id)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an integer");
		}
		if(!is_integer($event_id)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be an integer");
		}
		$this->setName("Edit Event");
		$this->setInfo("this module returns a component representing the edit event panel. it takes in a calendar id and an event id.");
		$this->cal_id = $cal_id;
		$this->event_id = $event_id;
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


			$cal = $strongcal->getCalendarFromDb($this->cal_id);
			$event = $cal->getEvent($this->event_id);
			/** end initializing the input */
			if(is_object($event)){
				$recur = $event->stealRecurrance();
			}else{
				$recur = false;
			}
			/**
			 * let's make the panel's !!!
			 */
			/************************************************************************
			get modules
			************************************************************************/
			$strongcal = $this->avalanche->getModule("strongcal");
			$buffer = $this->avalanche->getSkin("buffer");
			$prefix = "strongcal_";


			/**
			 * get the calendar
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/add_event_style.css"));
			$this->doc->addStyleSheet($css);


			if(!($cal->canWriteEntries() && $event->author() == $this->avalanche->getActiveUser() || $cal->canWriteName())){
				if($cal->canWriteEntries()){
					$done_text = new Text("you do not have permission to edit this event.");
				}else{
					$done_text = new Text("you do not have permission to edit events to this calendar.");
				}
				$done_text->setStyle(new Style("text"));
				$edit_event_view = new Panel();
				$edit_event_view->getStyle()->setWidth("100%");
				$edit_event_view->getStyle()->setHeight("400");
				$edit_event_view->setAlign("center");
				$edit_event_view->add($done_text);
				return new module_bootstrap_data($edit_event_view, "no permission to edit event");
			}
			/**
			 * the form has been submitted to here, so we need to actually add the event
			 */
			if(isset($data_list["edit_event"]) && $data_list["edit_event"]){
				if(isset($data_list["all_day"])){
					$event->setAllDay(true);
				}else{
					$event->setAllDay(false);
				}

				$edit_event_view = new Panel();
				$edit_event_view->getStyle()->setWidth("100%");
				$edit_event_view->getStyle()->setHeight("400");
				$edit_event_view->setAlign("center");
					$fields = $event->fields();
					$list = array();
					$list[] = array("field" => "all_day", "value" => isset($data_list["all_day"]));
					for($i=0;$i<count($fields);$i++){
						$load_ok = $fields[$i]->load_form_value($prefix, $data_list);
						if(get_magic_quotes_gpc()){
							$val = $fields[$i]->to_add();
							$val = stripslashes($val["value"]);
						}else{
							$val = $fields[$i]->to_add();
							$val = $val["value"];
						}
						if($load_ok){
							$list[] = array("field" => $fields[$i], "value" => $val);
						}
					}


				if(!$event->isAllDay()){
					$event->setTimeZone($strongcal->timezone());
				}

					if(is_object($recur) && isset($data_list["all_events_submit"])){
						$cal->editSeries($list, $recur);
					}

					// send the user back to main view
					throw new RedirectException("index.php");
					exit;

					// $module = new module_bootstrap_strongcal_eventview_gui($this->avalanche, $this->doc, $this->cal_id, $this->event_id);
					// $bootstrap = $this->avalanche->getModule("bootstrap");
					// $runner = $bootstrap->newDefaultRunner();
					// $runner->add($module);
					// $edit_event_view = $runner->run($data);
					// $edit_event_view = $edit_event_view->data();
					// return new module_bootstrap_data($edit_event_view, "event edited");
			}


			if($cal->canWriteEntries()){
				/************************************************************************
				create style objects to apply to the panels
				************************************************************************/

				$container_style = new Style("edit_event_container");
				$container_style->setWidth("480px");

				$color_style = new Style("color_container");
				$color_style->setWidth("35px");
				$color_style->setHeight("35px");
				$color_style->setBackground($cal->color());

				$left_cell_style = new Style("add_event_panel");
				$left_cell_style->setWidth("50px");
				$left_cell_style->setHeight("50px");

				$label_style = new Style("add_event_label");
				$label_style->setWidth("80px");

				$lower_label_style = new Style("add_event_label");
				$lower_label_style->setWidth("80px");

				$input_style = new Style("add_event_input");

				$buffer_style = new Style();
				$buffer_style->setHeight("50px");

				$mini_container_style = new Style("add_event_mini_container");
				$mini_container_style->setWidth("450px");

				$lower_container_style = new Style("add_event_mini_container2");
				$lower_container_style->setWidth("480px");
				$lower_container_style_cell = new Style("add_event_mini_container2_cell");

				$inner_lower_style = new Style("add_event_inner_mini");
				$inner_lower_style->setWidth("450px");

				/************************************************************************
				initialize panels
				************************************************************************/

				// containers
				$my_container = 		new FormPanel("index.php");
				$my_container->addHiddenField("view", "edit_event");
				$my_container->addHiddenField("edit_event", "1");
				$my_container->addHiddenField("cal_id", (string) $this->cal_id);
				$my_container->addHiddenField("event_id", (string) $this->event_id);
				$mini_container1 =		new Panel();
				$mini_container2 = 		new Panel();
				$lower_container = 		new GridPanel(1);
				$inner_lower =			new GridPanel(1);

				// inputs and input labels
				$title_label = 			new Panel();
				$title_input = 			new Panel();
				$start_date_label = 	new Panel();
				$start_date_input = 	new Panel();
				$start_time_input = 	new Panel();
				$end_time_input = 		new Panel();
				$end_date_label = 		new Panel();
				$end_date_input = 		new Panel();
				$description_label = 	new Panel();
				$description_input = 	new Panel();

				// specific cells in the table
				$color_panel = 			new Panel();
				$color_panel2 = 		new Panel();
				$top_right = 			new Panel();

				// table rows
				$top_row = 			new BorderPanel();
				$title_row =		new Panel();
				$all_day_row = 		new SimplePanel();
				$second_row = 			new GridPanel(3);
				$third_row = 			new GridPanel(3);
				$fourth_row = 			new Panel();
				$fifth_row = 			new Panel();
				$buffer_row = 			new Panel();
				$header_label = 		new Panel();
				/************************************************************************
				************************************************************************/

				/************************************************************************
				apply styles to created panels
				************************************************************************/

				// inputs and input labels
				$title_label->setStyle($label_style);
				$title_input->setStyle($input_style);
				$start_date_label->setStyle($label_style);
				$start_date_input->setStyle($input_style);
				$start_time_input->setStyle(clone $input_style);
				$end_date_label->setStyle($label_style);
				$end_time_input->setStyle(clone $input_style);
				$end_date_input->setStyle($input_style);
				$description_label->setStyle($label_style);
				$description_input->setStyle($input_style);
				$header_label->setStyle(new Style("cal_label"));


				// containers
				$my_container->setStyle($container_style);
				$mini_container1->setStyle($mini_container_style);
				$mini_container2->setStyle($mini_container_style);
				$lower_container->setStyle($lower_container_style);
				$lower_container->setCellStyle($lower_container_style_cell);
				$inner_lower->setStyle($inner_lower_style);

				// specific cells in the table
				$color_panel->setStyle($left_cell_style);
				$color_panel2->setStyle($color_style);
				$buffer_row->setStyle($buffer_style);

				// tweaking panel attributes
				$my_container->setValign("top");
				$my_container->setAlign("center");
				$color_panel->setAlign("left");
				$color_panel->setValign("middle");
				$lower_container->setAlign("center");

				/************************************************************************
				************************************************************************/

				/************************************************************************
				add necessary text and html
				************************************************************************/

				$title_label->add(new Text("Event Title:"));
				$start_date_label->add(new Text("Start:"));
				$end_date_label->add(new Text("End:"));
				$description_label->add(new Text("Event Description:"));

				// create form manager objects
				$title_field = $event->getField("title");
				$title_field->setLength(37);
				$s_time_field = $event->getField("start_time");
				$e_time_field = $event->getField("end_time");
				$s_date_field = $event->getField("start_date");
				$e_date_field = $event->getField("end_date");
				$desc_field = $event->getField("description");
				$desc_field->setCols(42);
				$desc_field->setRows(5);

				// output form manager object html to specific panels
				$title_temp = $title_field->toGUI($prefix);
				$title_temp->setStyle(new Style("input"));
				$title_input->add($title_temp);
				$sdate = $s_date_field->toGUI($prefix);
				$edate = $e_date_field->toGUI($prefix);
				$stime = $s_time_field->toGUI($prefix);
				$etime = $e_time_field->toGUI($prefix);
				// add actions
				$sdate->addChangeAction(new MinDateTimeAction($sdate, $stime, $edate, $etime));
				$sdate->addChangeAction($edate->getChangeAction());
				$stime->addChangeAction(new MinDateTimeAction($sdate, $stime, $edate, $etime));
				$edate->addChangeAction(new MaxDateTimeAction($sdate, $stime, $edate, $etime));
				$edate->addChangeAction($sdate->getChangeAction());
				$etime->addChangeAction(new MaxDateTimeAction($sdate, $stime, $edate, $etime));
				// add to panels
				$start_date_input->add($sdate);
				$end_date_input->add($edate);
				$start_time_input->add($stime);
				$end_time_input->add($etime);
				$desc_temp = $desc_field->toGUI($prefix);
				$desc_temp->setStyle(new Style("input"));
				$description_input->add($desc_temp);


				$header_label->add(new Text($cal->name()));

				/************************************************************************
				************************************************************************/

				$all_day_input = new CheckInput("All day event?");
				$all_day_input->setName("all_day");
				$all_day_input->setChecked($event->isAllDay());
				$all_day_input->setValue("1");
				$all_day_input->setStyle(new Style("add_event_label"));
				$all_day_input->addClickAction(new IfCheckedThenAction($all_day_input, new DisplayNoneAction($start_time_input)));
				$all_day_input->addClickAction(new IfCheckedThenAction($all_day_input, new DisplayNoneAction($end_time_input)));
				$all_day_input->addClickAction(new IfNotCheckedThenAction($all_day_input, new DisplayBlockAction($start_time_input)));
				$all_day_input->addClickAction(new IfNotCheckedThenAction($all_day_input, new DisplayBlockAction($end_time_input)));
				if($event->isAllDay()){
					$start_time_input->getStyle()->setDisplayNone();
					$end_time_input->getStyle()->setDisplayNone();
				}
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
				//$header_label->setWidth("400px");
				$top_row->setEast($header_label);



				$title_row->add($title_label);
				$title_row->add($title_input);

				/************************************************************************
				remember:
				all_day_row is a simplepanel
				************************************************************************/
				$all_day_row->add($all_day_input);
				/************************************************************************
				remember:
				second_row is gridpanel(1)
				************************************************************************/
				$second_row->add($start_date_label);
				$second_row->add($start_date_input);
				$second_row->add($start_time_input);

				/************************************************************************
				remember:
				third_row is gridpanel(1)
				************************************************************************/
				$third_row->add($end_date_label);
				$third_row->add($end_date_input);
				$third_row->add($end_time_input);

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
				$inner_lower->getStyle()->setPaddingTop(4);
				$inner_lower->getStyle()->setPaddingBottom(4);
				$inner_lower->getCellStyle()->setPaddingLeft(8);
				$inner_lower->getCellStyle()->setPaddingRight(8);
				$fields = $event->fields();
				$should_add_lower = false;
				foreach($fields as $f){
					if($f->field() == "start_date" ||
					   $f->field() == "start_time" ||
					   $f->field() == "end_date" ||
					   $f->field() == "end_time" ||
					   $f->field() == "title" ||
					   $f->field() == "description"){
					}else{
						$should_add_lower = true;
						$label_panel = new Panel();
						$label_panel->setStyle($lower_label_style);
						$label_panel->add(new Text($f->prompt()));
						$inner_lower->add($label_panel);

						$input_panel = new Panel();
						$input_panel->setStyle($input_style);
						$temp = $f->toGUI($prefix);
						//$temp->setStyle(new Style("input"));
						$temp->getStyle()->setFontFamily("verdana, sans-serif");
						$temp->getStyle()->setFontSize(9);
						$input_panel->add($temp);
						$inner_lower->add($input_panel);
					}
				}

				$lower_container->add($inner_lower);

				$mini_container1->add($top_row);
				$mini_container1->add($title_row);
				$mini_container1->add($all_day_row);
				$mini_container1->add($second_row);
				$mini_container1->add($third_row);
				$mini_container2->add($fourth_row);
				$mini_container2->add($fifth_row);

				$my_container->add($mini_container1);
				$my_container->add($buffer_row);
				$my_container->add($mini_container2);

				$button_panel = new GridPanel(3);
				$button_panel->getCellStyle()->setPadding(7);
				$button_panel->setAlign("center");
				$button_panel->setWidth("100%");
				$lower_container->add($button_panel);
				$my_container->add($lower_container);

				$back = new ButtonInput("Back");
				$back->getStyle()->setBorderWidth(1);
				$back->getStyle()->setBorderColor("black");
				$back->getStyle()->setBorderStyle("solid");
				$back->addClickAction(new LoadPageAction("index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId()));
				$button_panel->add($back);
				if($should_add_lower){
					$button_panel->add(new Text("<input style='border: 1px solid black;margin:4px;' type='submit' value='Edit Event' name='single_event_submit'>"));
					if(is_object($recur)){
						$button_panel->add(new Text("<input style='border: 1px solid black;margin:4px;' type='submit' value='Edit Series' name='all_events_submit'>"));
					}
				}else{
					$button_panel->add(new Text("<input style='border: 1px solid black' type='submit' value='Edit Event'>"));
				}

				$edit_event_view = new Panel();
				$edit_event_view->setStyle(new Style("big_event_container"));
				$edit_event_view->getStyle()->setPaddingTop(70);
				$edit_event_view->getStyle()->setPaddingLeft(110);
				$edit_event_view->getStyle()->setWidth("100%");
				$edit_event_view->setAlign("left");
				$edit_event_view->add($my_container);
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to edit events for the " . $cal->name() . " calendar."));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$edit_event_view = $error;
			}


			return new module_bootstrap_data($edit_event_view, "a gui component for the edit_event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>