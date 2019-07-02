<?

class module_bootstrap_strongcal_addeventview_gui extends module_bootstrap_module{

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

		$this->setName("Add event view for Aurora");
		$this->setInfo("adds an event to a calendar. expecs \$cal_id to come as an integer input.");
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
			$buffer = $this->avalanche->getSkin("buffer");
			$prefix = "strongcal_";


			/**
			 * get the calendar
			 */
			$data = new module_bootstrap_data(array($cal_id), "send in a list of calendar ids to get");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_list = $data->data();
			$cal = $calendar_list[0];

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/add_event_style.css"));
			$this->doc->addStyleSheet($css);

			$error = false;
			try{
				/**
				 * the form has been submitted to here, so we need to actually add the event
				 */
				if(isset($data_list["add_event"])){
	//				return new module_bootstrap_data(new Text(str_replace("\n", "<br>", print_r(array_merge($_REQUEST, $_FILES), true))), "data");
	//				exit;
					$add_event_view = new Panel();
					$add_event_view->getStyle()->setWidth("100%");
					$add_event_view->getStyle()->setHeight("400");
					$add_event_view->setAlign("center");
					if($cal->canWriteEntries()){
						// add the event
						// but i can't add drop down fields here, b/c they require crazy stuff
						// this means that notifications can't use the value of any drop downs in their body,
						// b/c the text will be the default value, not the actual value for the event
						$fields = $cal->fields();
						$event_array = array();
						for($i=0;$i<count($fields);$i++){
							$field = $fields[$i]->toGUI($prefix);
							if(!$field instanceof DropDownInput){
								$field->loadFormValue($data_list);
								$event_array[] = array("field" => $fields[$i], "value" => $field->getValue());
							}else{
								$event_array[] = array("field" => $fields[$i], "value" => $fields[$i]->value());
							}
						}
						$new_event = $cal->addEvent($event_array);
						if(is_object($new_event)){
							$fields = $new_event->fields();
							// now get dropdown
							for($i=0;$i<count($fields);$i++){
								$field = $fields[$i]->toGUI($prefix);
								if($field instanceof DropDownInput){
									$fields[$i]->load_form_value($prefix, $data_list);
								}
							}
							$new_event->reload();
							// $new_event->setTimeZone($strongcal->timezone());
							// add recurrance, if any

							if(isset($data_list["all_day"]) && $data_list["all_day"]){
								$new_event->setAllDay(true);
							}
							if($data_list["recur_type"]){
								$new_recur = $cal->getNewRecurrancePattern();
								// let's start the recurring on the date of the event
								$new_recur->setStartDate($new_event->getDisplayValue("start_date"));

								// now let's figure out the end type
								if($data_list["recur_end_type"] == RECUR_NO_END_DATE){
									$new_recur->setEndType(RECUR_NO_END_DATE);
								}else
								if($data_list["recur_end_type"] == RECUR_END_BY){
									$end_date = new DateInput();
									$end_date->setName("recur_end_date");
									$end_date->loadFormValue($data_list);
									$new_recur->setEndType(RECUR_END_BY, $end_date->getValue());
									//check against start date
									$field = $cal->getField("start_date");
									$field = $field->toGui($prefix);
									$field->loadFormValue($data_list);
									if($end_date->getValue() < $field->getValue()){
										throw new IllegalArgumentException("Recur end date must be set to after the event's start date");
									}
								}else
								if($data_list["recur_end_type"] == RECUR_END_AFTER){
									$blank = new SmallTextInput();
									$blank->setName("recur_end_num");
									$blank->loadFormValue($data_list);
									$new_recur->setEndType(RECUR_END_AFTER, $blank->getValue());
								}


								// now that we've set up the start and end dates for the recurrance,
								// we can figure out the pattern.
								if($data_list["recur_type"] == RECUR_DAILY){
									$day_radio = new SmallTextInput();
									$day_radio->setName("recur_d_type");
									$day_radio->loadFormValue($data_list);
									if($day_radio->getValue() == "day"){
										$blank = new SmallTextInput();
										$blank->setName("recur_d_days");
										$blank->loadFormValue($data_list);
										$new_recur->setToDaily($blank->getValue());
									}else if($day_radio->getValue() == "weekday"){
										$new_recur->setToWeekly("1", "12345");
									}else{
										throw new IllegalArgumentException("trying to create daily recurrance, but subtype is not defined from form input.");
									}
								}else
								if($data_list["recur_type"] == RECUR_WEEKLY){
									// we have to implode $week_days from an array of days to a string.
									$blank = new SmallTextInput();
									$blank->setName("recur_w_weeks");
									$blank->loadFormValue($data_list);
									$week_days = "";
									if(isset($data_list["recur_w_sun"])){
										$week_days .= "0";
									}
									if(isset($data_list["recur_w_mon"])){
										$week_days .= "1";
									}
									if(isset($data_list["recur_w_tue"])){
										$week_days .= "2";
									}
									if(isset($data_list["recur_w_wed"])){
										$week_days .= "3";
									}
									if(isset($data_list["recur_w_thu"])){
										$week_days .= "4";
									}
									if(isset($data_list["recur_w_fri"])){
										$week_days .= "5";
									}
									if(isset($data_list["recur_w_sat"])){
										$week_days .= "6";
									}
									$new_recur->setToWeekly($blank->getValue(), $week_days);
								}else
								if($data_list["recur_type"] == RECUR_MONTHLY){
									$blank = new SmallTextInput();
									$blank->setName("recur_m_type");
									$blank->loadFormValue($data_list);
									if($blank->getValue() == RECUR_MONTHLY_DOM){
										$month = new SmallTextInput();
										$month->setName("recur_m_dom_num_month");
										$month->loadFormValue($data_list);
										$day = new SmallTextInput();
										$day->setName("recur_m_dom_day");
										$day->loadFormValue($data_list);
										$new_recur->setToMonthly(RECUR_MONTHLY_DOM, $month->getValue(), $day->getValue());
									}else
									if($blank->getValue() == RECUR_MONTHLY_DOW){
										$week = new SmallTextInput();
										$week->setName("recur_m_dowom_inc");
										$week->loadFormValue($data_list);
										$day = new SmallTextInput();
										$day->setName("recur_m_dowom_dow");
										$day->loadFormValue($data_list);
										$month = new SmallTextInput();
										$month->setName("recur_m_dowom_num_months");
										$month->loadFormValue($data_list);
										$new_recur->setToMonthly(RECUR_MONTHLY_DOW, $month->getValue(), $week->getValue(), $day->getValue());
									}else{
										throw new IllegalArgumentException("trying to create monthly recurrance, but subtype is not defined from form input.");
									}
								}else
								if($data_list["recur_type"] == RECUR_YEARLY){
									$blank = new SmallTextInput();
									$blank->setName("recur_y_type");
									$blank->loadFormValue($data_list);
									if($blank->getValue() == RECUR_YEARLY_DOM){
										$month = new SmallTextInput();
										$month->setName("recur_y_domoy_month");
										$month->loadFormValue($data_list);
										$day = new SmallTextInput();
										$day->setName("recur_y_domoy_day");
										$day->loadFormValue($data_list);
										$new_recur->setToYearly(RECUR_YEARLY_DOM, $day->getValue(), $month->getValue());
									}else
									if($blank->getValue() == RECUR_YEARLY_DOW){
										$week = new SmallTextInput();
										$week->setName("recur_y_dowomoy_week");
										$week->loadFormValue($data_list);
										$day = new SmallTextInput();
										$day->setName("recur_y_dowomoy_dow");
										$day->loadFormValue($data_list);
										$month = new SmallTextInput();
										$month->setName("recur_y_dowomoy_month");
										$month->loadFormValue($data_list);
										$new_recur->setToYearly(RECUR_YEARLY_DOW, $week->getValue(), $month->getValue(), $day->getValue());
									}
								}else{
									$new_recur = false;
								}
							}else{
								$new_recur = false;
							}

							// now we're done setting up the recurrance, so let's tell the event what it's recurrance pattern is.
							$new_event->returnRecurrance($new_recur);

							header("Location: index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $new_event->getId());
							exit;
						}else{
							$done_text = new Text("there has been an error adding the event. please try again.");
							$done_text->setStyle(new Style("text"));
							$add_event_view->add($done_text);
							return new module_bootstrap_data($add_event_view, "error adding event");
						}
					}else{
						$done_text = new Text("you do not have permission to add events to this calendar.");
						$done_text->setStyle(new Style("text"));
						$add_event_view->add($done_text);
						return new module_bootstrap_data($add_event_view, "no permission to add event");
					}
				}
			}catch(IllegalArgumentException $e){
				$error = $e;
			}


			if($cal->canWriteEntries()){
				/************************************************************************
				create style objects to apply to the panels
				************************************************************************/

				$container_style = new Style("add_event_container");
				$container_style->setWidth("470px");

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

				$input_style = new Style("add_event_input");

				$buffer_style = new Style();
				$buffer_style->setHeight("50px");

				$mini_container_style = new Style("add_event_mini_container");
				$mini_container_style->setWidth("440px");

				$lower_container_style = new Style("add_event_mini_container2");
				$lower_container_style->setWidth("470px");
				$lower_container_style_cell = new Style("add_event_mini_container2_cell");

				$inner_lower_style = new Style("add_event_inner_mini");
				$inner_lower_style->setWidth("440px");



				/************************************************************************
				initialize panels
				************************************************************************/

				// containers
				$big_container =	new Panel();
				$my_container = 	new FormPanel("index.php");
				$my_container->addHiddenField("aurora_loader", "module_bootstrap_strongcal_addeventview_gui");
				$my_container->addHiddenField("add_event", "1");
				$my_container->addHiddenField("view", "add_event_step_3");
				$my_container->addHiddenField("cal_id", (string) $cal_id);
				$mini_container1 =	new Panel();
				$mini_container2 = 	new Panel();
				$lower_container = 	new GridPanel(1);
				$inner_lower =		new GridPanel(1);
				$recur_lower = 		new GridPanel(1);
				$header_label = 	new Panel();

				// inputs and input labels
				$title_label = 		new Panel();
				$title_input = 		new Panel();
				$start_date_label = 	new Panel();
				$start_date_input = 	new Panel();
				$start_time_label = 	new Panel();
				$start_time_input = 	new Panel();
				$end_time_label = 	new Panel();
				$end_time_input = 	new Panel();
				$end_date_label = 	new Panel();
				$end_date_input = 	new Panel();
				$description_label = 	new Panel();
				$description_input = 	new Panel();

				// specific cells in the table
				$color_panel = 		new Panel();
				$color_panel2 = 	new Panel();
				$top_right = 		new Panel();

				// table rows
				$top_row = 			new BorderPanel();
				$title_row =		new Panel();
				$second_row = 		new GridPanel(3);
				$all_day_row = 		new SimplePanel();
				$third_row = 		new GridPanel(3);
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
				$start_date_label->setStyle($label_style);
				$start_date_input->setStyle($input_style);
				$start_time_label->setStyle($label_style);
				$start_time_input->setStyle($input_style);
				$end_time_label->setStyle($label_style);
				$end_time_input->setStyle($input_style);
				$end_date_label->setStyle($label_style);
				$end_date_input->setStyle($input_style);
				$description_label->setStyle($label_style);
				$description_input->setStyle($input_style);
				$header_label->setStyle(new Style("cal_label"));


				// containers
				$big_container->getStyle()->setClassname("big_container");
				$big_container->getStyle()->setWidth("100%");
				$my_container->setStyle($container_style);
				$mini_container1->setStyle($mini_container_style);
				$mini_container2->setStyle($mini_container_style);
				$lower_container->setStyle($lower_container_style);
				$lower_container->setCellStyle($lower_container_style_cell);
				$inner_lower->setStyle($inner_lower_style);
				$recur_lower->setSTyle($inner_lower_style);


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
				$title_field = $cal->getField("title");
				$title_field->setLength(37);
				$s_time_field = $cal->getField("start_time");
				$e_time_field = $cal->getField("end_time");
				$s_date_field = $cal->getField("start_date");
				$e_date_field = $cal->getField("end_date");
				$desc_field = $cal->getField("description");
				$desc_field->setCols(42);
				$desc_field->setRows(5);

				// output form manager object html to specific panels
				$title_temp = $title_field->toGUI($prefix);
				$title_temp->setStyle(new Style("input"));
				$title_input->add($title_temp);
				$sdate = $s_date_field->toGUI($prefix, $date);
				$edate = $e_date_field->toGUI($prefix, $date);
				$stime = $s_time_field->toGUI($prefix, $time);
				$etime = $e_time_field->toGUI($prefix, $time);
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
				$desc_temp->setCols(42);
				$desc_temp->setRows(5);
				$desc_temp->setStyle(new Style("input"));
				$description_input->add($desc_temp);

				$header_label->add(new Text($cal->name()));


				$all_day_input = new CheckInput("All day event?");
				$all_day_input->setName("all_day");
				//$all_day_label = new Text("All day event?");
				$all_day_input->setStyle(new Style("add_event_label"));
				$all_day_input->addClickAction(new IfCheckedThenAction($all_day_input, new DisplayNoneAction($start_time_input)));
				$all_day_input->addClickAction(new IfCheckedThenAction($all_day_input, new DisplayNoneAction($end_time_input)));
				$all_day_input->addClickAction(new IfNotCheckedThenAction($all_day_input, new DisplayBlockAction($start_time_input)));
				$all_day_input->addClickAction(new IfNotCheckedThenAction($all_day_input, new DisplayBlockAction($end_time_input)));

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
				all_day_row is a simplepanel
				************************************************************************/
				$all_day_row->add($all_day_input);
				//$all_day_row->add($all_day_label);

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
				$fields = $cal->fields();
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
						//$temp->getStyle()->setClassname("input");
						$temp->getStyle()->setFontFamily("verdana, sans-serif");
						$temp->getStyle()->setFontSize(9);
						$input_panel->add($temp);
						$inner_lower->add($input_panel);
					}
				}

				$lower_container->add($inner_lower);

				/**************************************************************************
				this is the lower box for recurring options
				***************************************************************************/

				// the day panel
				$day_recur_panel = new GridPanel(2);
				$day_recur_panel->getCellStyle()->setPaddingTop(6);
				$day_recur_panel->getStyle()->setDisplayNone();
				$day_recur_panel->setWidth("100%");

				$day_radio = new RadioInput();
				$day_radio->setName("recur_d_type");
				$day_radio->setChecked(true);
				$day_radio->setValue("day");

				$weekday_radio = new RadioInput();
				$weekday_radio->setName("recur_d_type");
				$weekday_radio->setValue("weekday");

				$day_recur_panel->add($day_radio);
					$every___days = new Panel();
					$every = new Text("every ");
					$every->setStyle($lower_label_style);
					$every___days->add($every);
					$blank = new SmallTextInput();
					$blank->addFocusGainedAction(new CheckAction($day_radio));
					$blank->setName("recur_d_days");
					$blank->setStyle(new Style("input"));
					$blank->setValue("1");
					$blank->setSize(2);
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$every___days->add($blank);
					$days = new Text(" days");
					$days->setStyle($lower_label_style);
					$every___days->add($days);
				$day_recur_panel->add($every___days);
				$day_recur_panel->add($weekday_radio);
				$every_weekday = new Text("every weekday");
				$every_weekday->setStyle($lower_label_style);
				$day_recur_panel->add($every_weekday);

				// the week panel
				$week_recur_panel = new Panel();
				$week_recur_panel->getStyle()->setDisplayNone();
				$week_recur_panel->setWidth("100%");
					$every___days = new Panel();
					$every = new Text("every ");
					$every->setStyle($lower_label_style);
					$every___days->add($every);
					$blank = new SmallTextInput();
					$blank->setStyle(new Style("input"));
					$blank->setValue("1");
					$blank->setSize(2);
					$blank->setName("recur_w_weeks");
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$every___days->add($blank);
					$days = new Text(" weeks on:<br>");
					$days->setStyle($lower_label_style);
					$every___days->add($days);
					$dow = new GridPanel(4);
					$dow->getCellStyle()->setPadding(2);
					$dow->setWidth("100%");
					$sunday_check = new CheckInput("Sunday");
					$sunday_check->setStyle($lower_label_style);
					$sunday_check->setName("recur_w_sun");
					$sunday_check->setValue("1");
					$monday_check = new CheckInput("Monday");
					$monday_check->setStyle($lower_label_style);
					$monday_check->setName("recur_w_mon");
					$monday_check->setValue("1");
					$tuesday_check = new CheckInput("Tuesday");
					$tuesday_check->setStyle($lower_label_style);
					$tuesday_check->setName("recur_w_tue");
					$tuesday_check->setValue("1");
					$wednesday_check = new CheckInput("Wednesday");
					$wednesday_check->setStyle($lower_label_style);
					$wednesday_check->setName("recur_w_wed");
					$wednesday_check->setValue("1");
					$thursday_check = new CheckInput("Thursday");
					$thursday_check->setStyle($lower_label_style);
					$thursday_check->setName("recur_w_thu");
					$thursday_check->setValue("1");
					$friday_check = new CheckInput("Friday");
					$friday_check->setStyle($lower_label_style);
					$friday_check->setName("recur_w_fri");
					$friday_check->setValue("1");
					$saturday_check = new CheckInput("Saturday");
					$saturday_check->setStyle($lower_label_style);
					$saturday_check->setName("recur_w_sat");
					$saturday_check->setValue("1");
					$dow->add($sunday_check);
					$dow->add($monday_check);
					$dow->add($tuesday_check);
					$dow->add($wednesday_check);
					$dow->add($thursday_check);
					$dow->add($friday_check);
					$dow->add($saturday_check);
					//$dow->add(new Text("&nbsp;"));
				$week_recur_panel->add($every);
				$week_recur_panel->add($blank);
				$week_recur_panel->add($days);
				$week_recur_panel->add($dow);

				// the month panel
				$month_recur_panel = new GridPanel(2);
				$month_recur_panel->getStyle()->setDisplayNone();
				$month_recur_panel->getCellStyle()->setPaddingTop(6);
				$month_recur_panel->setWidth("100%");

				$dom_radio = new RadioInput();
				$dom_radio->setName("recur_m_type");
				$dom_radio->setChecked(true);
				$dom_radio->setValue(RECUR_MONTHLY_DOM);

				$dowom_radio = new RadioInput();
				$dowom_radio->setName("recur_m_type");
				$dowom_radio->setValue(RECUR_MONTHLY_DOW);

				$month_recur_panel->add($dom_radio);
					$day_of_month = new Panel();
					$every = new Text("Day ");
					$every->setStyle($lower_label_style);
					$day_of_month->add($every);
					$blank = new SmallTextInput();
					$blank->addFocusGainedAction(new CheckAction($dom_radio));
					$blank->setStyle(new Style("input"));
					$blank->setValue($day);
					$blank->setSize(2);
					$blank->setName("recur_m_dom_day");
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$day_of_month->add($blank);
					$days = new Text(" of every ");
					$days->setStyle($lower_label_style);
					$day_of_month->add($days);
					$blank = new SmallTextInput();
					$blank->addFocusGainedAction(new CheckAction($dom_radio));
					$blank->setStyle(new Style("input"));
					$blank->setValue("1");
					$blank->setSize(2);
					$blank->setName("recur_m_dom_num_month");
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$day_of_month->add($blank);
					$months = new Text(" month(s)");
					$months->setStyle($lower_label_style);
					$day_of_month->add($months);
					$month_recur_panel->add($day_of_month);
				$month_recur_panel->add($dowom_radio);
					$day_of_week_of_month = new Panel();
					$the = new Text("The ");
					$the->setStyle($lower_label_style);
					$day_of_week_of_month->add($the);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($dowom_radio));
					$inc->setName("recur_m_dowom_inc");
					$inc->addOption(new DropDownOption("first",  "1"));
					$inc->addOption(new DropDownOption("second", "2"));
					$inc->addOption(new DropDownOption("third",  "3"));
					$inc->addOption(new DropDownOption("fourth", "4"));
					$inc->addOption(new DropDownOption("fifth",  "5"));
					$day_of_week_of_month->add($inc);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($dowom_radio));
					$inc->setName("recur_m_dowom_dow");
					$inc->addOption(new DropDownOption("Sunday",    "0"));
					$inc->addOption(new DropDownOption("Monday",    "1"));
					$inc->addOption(new DropDownOption("Tuesday",   "2"));
					$inc->addOption(new DropDownOption("Wednesday", "3"));
					$inc->addOption(new DropDownOption("Thursday",  "4"));
					$inc->addOption(new DropDownOption("Friday",    "5"));
					$inc->addOption(new DropDownOption("Saturday",  "6"));
					$day_of_week_of_month->add($inc);
					$days = new Text(" of every ");
					$days->setStyle($lower_label_style);
					$day_of_week_of_month->add($days);
					$blank = new SmallTextInput();
					$blank->addFocusGainedAction(new CheckAction($dowom_radio));
					$blank->setStyle(new Style("input"));
					$blank->setValue("1");
					$blank->setSize(2);
					$blank->setName("recur_m_dowom_num_months");
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$day_of_week_of_month->add($blank);
					$months = new Text(" month(s)");
					$months->setStyle($lower_label_style);
					$day_of_week_of_month->add($months);
					$month_recur_panel->add($day_of_week_of_month);


				// the year panel
				$year_recur_panel = new GridPanel(2);
				$year_recur_panel->getStyle()->setDisplayNone();
				$year_recur_panel->getCellStyle()->setPaddingTop(6);
				$year_recur_panel->setWidth("100%");
				$domoy_radio = new RadioInput();
				$domoy_radio->setName("recur_y_type");
				$domoy_radio->setValue(RECUR_YEARLY_DOM);
				$domoy_radio->setChecked(true);

				$dowomoy_radio = new RadioInput();
				$dowomoy_radio->setName("recur_y_type");
				$dowomoy_radio->setValue(RECUR_YEARLY_DOW);

				$year_recur_panel->add($domoy_radio);
					$day_of_month = new Panel();
					$day_of_month->setWidth("100%");
					$every = new Text("Every ");
					$every->setStyle($lower_label_style);
					$day_of_month->add($every);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($domoy_radio));
					$inc->setName("recur_y_domoy_month");
					$inc->addOption(new DropDownOption("January",  "01"));
					$inc->addOption(new DropDownOption("February", "02"));
					$inc->addOption(new DropDownOption("March",    "03"));
					$inc->addOption(new DropDownOption("April",    "04"));
					$inc->addOption(new DropDownOption("May",      "05"));
					$inc->addOption(new DropDownOption("June",     "06"));
					$inc->addOption(new DropDownOption("July",     "07"));
					$inc->addOption(new DropDownOption("August",   "08"));
					$inc->addOption(new DropDownOption("September","09"));
					$inc->addOption(new DropDownOption("October",  "10"));
					$inc->addOption(new DropDownOption("November", "11"));
					$inc->addOption(new DropDownOption("December", "12"));
					$inc->loadFormValue(array($inc->getName() => $month));
					$day_of_month->add($inc);
					$blank = new SmallTextInput();
					$blank->addFocusGainedAction(new CheckAction($domoy_radio));
					$blank->setStyle(new Style("input"));
					$blank->setValue($day);
					$blank->setSize(2);
					$blank->setName("recur_y_domoy_day");
					$blank->getStyle()->setTextAlign("right");
					$blank->addKeyPressAction(new NumberOnlyAction($blank));
					$day_of_month->add($blank);
					$year_recur_panel->add($day_of_month);
				$year_recur_panel->add($dowomoy_radio);
					$day_of_week_of_month = new Panel();
					$day_of_week_of_month->setWidth("100%");
					$the = new Text("The ");
					$the->setStyle($lower_label_style);
					$day_of_week_of_month->add($the);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($dowomoy_radio));
					$inc->setName("recur_y_dowomoy_week");
					$inc->addOption(new DropDownOption("first",  "1"));
					$inc->addOption(new DropDownOption("second", "2"));
					$inc->addOption(new DropDownOption("third",  "3"));
					$inc->addOption(new DropDownOption("fourth", "4"));
					$inc->addOption(new DropDownOption("fifth",  "5"));
					$day_of_week_of_month->add($inc);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($dowomoy_radio));
					$inc->setName("recur_y_dowomoy_dow");
					$inc->addOption(new DropDownOption("Sunday",    "0"));
					$inc->addOption(new DropDownOption("Monday",    "1"));
					$inc->addOption(new DropDownOption("Tuesday",   "2"));
					$inc->addOption(new DropDownOption("Wednesday", "3"));
					$inc->addOption(new DropDownOption("Thursday",  "4"));
					$inc->addOption(new DropDownOption("Friday",    "5"));
					$inc->addOption(new DropDownOption("Saturday",  "6"));
					$day_of_week_of_month->add($inc);
					$days = new Text(" of ");
					$days->setStyle($lower_label_style);
					$day_of_week_of_month->add($days);
					$inc = new DropDownInput();
					$inc->addFocusGainedAction(new CheckAction($dowomoy_radio));
					$inc->setName("recur_y_dowomoy_month");
					$inc->addOption(new DropDownOption("January",  "01"));
					$inc->addOption(new DropDownOption("February", "02"));
					$inc->addOption(new DropDownOption("March",    "03"));
					$inc->addOption(new DropDownOption("April",    "04"));
					$inc->addOption(new DropDownOption("May",      "05"));
					$inc->addOption(new DropDownOption("June",     "06"));
					$inc->addOption(new DropDownOption("July",     "07"));
					$inc->addOption(new DropDownOption("August",   "08"));
					$inc->addOption(new DropDownOption("September","09"));
					$inc->addOption(new DropDownOption("October",  "10"));
					$inc->addOption(new DropDownOption("November", "11"));
					$inc->addOption(new DropDownOption("December", "12"));
					$inc->loadFormValue(array($inc->getName() => $month));
					$day_of_week_of_month->add($inc);
					$year_recur_panel->add($day_of_week_of_month);


				$end_recur_panel = new GridPanel(2);
				$end_recur_panel->getStyle()->setDisplayNone();
				$end_recur_panel->getCellStyle()->setPaddingTop(6);
				$end_recur_panel->setWidth("100%");
				$end_after_radio = new RadioInput();
				$end_after_radio->setName("recur_end_type");
				$end_after_radio->setValue(RECUR_END_AFTER);
				$end_after_radio->setChecked(true);

				$end_by_radio = new RadioInput();
				$end_by_radio->setName("recur_end_type");
				$end_by_radio->setValue(RECUR_END_BY);

				$end_recur = new Text("End series:");
				$end_recur->getStyle()->setClassname($lower_label_style->getClassname());
				$end_recur->getStyle()->setDisplayNone();

				$end_by = new GridPanel(2);
				$end_by->getCellStyle()->setPaddingRight(4);
				$by = new Text("by ");
				$by->setStyle($lower_label_style);
				$end_by->add($by);
				$end_date = new DateInput($date);
				$end_date->addFocusGainedAction(new CheckAction($end_by_radio));
				$end_date->setName("recur_end_date");
				$end_by->add($end_date);

				// change the end recur date if the start date changes
				$sdate->addChangeAction(new MinDateAction($sdate, $end_date));


				$end_recur_panel->add($end_after_radio);
				$end_after_panel = new GridPanel(3);
				$end_after_panel->getCellStyle()->setPaddingRight(4);
				$end_after = new Text("end after");
				$end_after->setStyle($lower_label_style);
				$end_after_panel->add($end_after);
				$blank = new SmallTextInput();
				$blank->addFocusGainedAction(new CheckAction($end_after_radio));
				$blank->setName("recur_end_num");
				$blank->setSize(3);
				$blank->setStyle(new Style("input"));
				$blank->setValue("2");
				$blank->addKeyPressAction(new NumberOnlyAction($blank));
				$end_after_panel->add($blank);
				$events = new Text("events");
				$events->setStyle($lower_label_style);
				$end_after_panel->add($events);


				$end_recur_panel->add($end_after_panel);
				$end_recur_panel->add($end_by_radio);
				$end_recur_panel->add($end_by);

				$recur_lower->getStyle()->setPaddingTop(4);
				$recur_lower->getStyle()->setPaddingBottom(4);
				$recur_lower->getCellStyle()->setPadding(8);

				$option1 = new DropDownOption("none", "none");
				$option2 = new DropDownOption("daily", RECUR_DAILY);
				$option3 = new DropDownOption("weekly", RECUR_WEEKLY);
				$option4 = new DropDownOption("monthly", RECUR_MONTHLY);
				$option5 = new DropDownOption("yearly", RECUR_YEARLY);

				$recur_option = new DropDownInput();
				$recur_option->setName("recur_type");
				$recur_option->addOption($option1);
				$recur_option->addOption($option2);
				$recur_option->addOption($option3);
				$recur_option->addOption($option4);
				$recur_option->addOption($option5);

				$ddclearfunction = new NewFunctionAction("clear_dd");
				$ddclearfunction->addAction(new DisplayNoneAction($day_recur_panel));
				$ddclearfunction->addAction(new DisplayNoneAction($week_recur_panel));
				$ddclearfunction->addAction(new DisplayNoneAction($month_recur_panel));
				$ddclearfunction->addAction(new DisplayNoneAction($year_recur_panel));
				$ddclearfunction->addAction(new DisplayNoneAction($end_recur));
				$ddclearfunction->addAction(new DisplayNoneAction($end_recur_panel));
				$this->doc->addFunction($ddclearfunction);

				$ddshowrecurfunction = new NewFunctionAction("show_end_recur");
				$ddshowrecurfunction->addAction(new DisplayBlockAction($end_recur));
				$ddshowrecurfunction->addAction(new DisplayBlockAction($end_recur_panel));
				$this->doc->addFunction($ddshowrecurfunction);

				$ddaction = new DropDownAction($recur_option);
				$ddaction->addAction(RECUR_DAILY, new DisplayInlineAction($day_recur_panel));
				$ddaction->addAction(RECUR_DAILY, new CallFunctionAction("show_end_recur"));
				$ddaction->addAction(RECUR_WEEKLY, new DisplayInlineAction($week_recur_panel));
				$ddaction->addAction(RECUR_WEEKLY, new CallFunctionAction("show_end_recur"));
				$ddaction->addAction(RECUR_MONTHLY, new DisplayInlineAction($month_recur_panel));
				$ddaction->addAction(RECUR_MONTHLY, new CallFunctionAction("show_end_recur"));
				$ddaction->addAction(RECUR_YEARLY, new DisplayInlineAction($year_recur_panel));
				$ddaction->addAction(RECUR_YEARLY, new CallFunctionAction("show_end_recur"));

				$recur_option->addChangeAction(new CallFunctionAction("clear_dd"));
				$recur_option->addChangeAction($ddaction);
				$recur_options = new GridPanel(1);
				$recur_options_text = new Panel();
				$recur_this_event = new Text("Recur this event ");
				$recur_this_event->setStyle($lower_label_style);
				$recur_options_text->add($recur_this_event);
				$recur_options_text->add($recur_option);
				$recur_options->add($recur_options_text);
				$recur_options->add($day_recur_panel);
				$recur_options->add($week_recur_panel);
				$recur_options->add($month_recur_panel);
				$recur_options->add($year_recur_panel);
				$recur_options->add($end_recur);
				$recur_options->add($end_recur_panel);

				$recur_lower->add($recur_options);
				$lower_container->add($recur_lower);
				/*************************************************************************
				stuff
				**************************************************************************/
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

				$button_panel = new GridPanel(2);
				$button_panel->getCellStyle()->setPadding(7);
				$button_panel->add(new Text("<input style='border:1px solid black;' type='submit' value='Add Event'>"));

				$my_container->add($lower_container);
				$lower_container->add($button_panel);

				$big_container->add($my_container);

				$add_event_view = new Panel();
				$add_event_view->getStyle()->setWidth("100%");
				$add_event_view->setAlign("left");
				$add_event_view->getStyle()->setPaddingLeft(20);
				$add_event_view->add($big_container);
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to add events for the " . $cal->name() . " calendar."));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$add_event_view = $error;
			}


			return new module_bootstrap_data($add_event_view, "a gui component for the add_event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>