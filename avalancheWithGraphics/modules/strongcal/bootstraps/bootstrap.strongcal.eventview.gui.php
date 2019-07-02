<?

class module_bootstrap_strongcal_eventview_gui extends module_bootstrap_module{

	private $avalanche;
	private $cal_id;
	private $event_id;
	private $doc;

	private $data_list;

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
		$this->setName("Aurora Calendar List to HTML");
		$this->setInfo("this module takes as input an array of calendar objects. the output is a very basic
				html list of the calendars.");
		$this->cal_id = $cal_id;
		$this->event_id = $event_id;
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->data_list = array();
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			$this->data_list = $data_list;
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");
			$os = $this->avalanche->getModule("os");

			/**
			 * end initialization and checking
			 */

			$cal = $strongcal->getCalendarFromDb($this->cal_id);
			if(!is_object($cal)){
				$event_view = new Text("calendar " . $this->cal_id . " could not be found");
				$event_view->getStyle()->setFontFamily("verdana, sans-serif");
				$event_view->getStyle()->setFontSize(9);
				return new module_bootstrap_data(new ErrorPanel($event_view), "error: couldn't find calendar");
			}

			$event = $cal->getEvent($this->event_id);
			if(is_object($event) && $cal->canReadEvent($event->getId())){
				if(!is_object($event)){
					$event_view = new Text("event "  . $this->event_id . " could not be found");
					$event_view->getStyle()->setFontFamily("verdana, sans-serif");
					$event_view->getStyle()->setFontSize(9);
					return new module_bootstrap_data(new ErrorPanel($event_view), "error: couldn't find event");
				}

				/**
				 * i now have an $event object for my current event to display
				 */

				$subview = "event";
				if(isset($data_list["subview"])){
					$subview = $data_list["subview"];
				}

				if($subview == "add_comment"){
					if(isset($data_list["title"]) && isset($data_list["message"])){
						$reader = new SmallTextInput();
						$reader->setName("title");
						$reader->loadFormValue($data_list);
						$title = $reader->getValue();
						$reader->setName("message");
						$reader->loadFormValue($data_list);
						$message = $reader->getValue();
						if(strlen($title) > 0 || strlen($message) > 0){
							$event->comment($title, $message);
						}
						header("Location: index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId());
					}else{
						throw new IllegalArgumentException("title and message must be sent in form input to add comment");
					}
				}else
				if($subview == "edit_comment"){
					if(isset($data_list["title"]) && isset($data_list["message"]) && isset($data_list["comment_id"])){
						$title = $data_list["title"];
						$message = $data_list["message"];
						$comment_id = $data_list["comment_id"];
						if(strlen($title) > 0 && strlen($message) > 0){
							$event->comment($title, $message, $comment_id);
						}
						header("Location: index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId());
					}else{
						throw new IllegalArgumentException("title and message and comment_id must be sent in form input to edit comment");
					}
				}else
				if($subview == "delete_comment"){
					if(isset($data_list["comment_id"])){
						$comment_id = $data_list["comment_id"];
						$event->removeComment($comment_id);
						header("Location: index.php?view=event&cal_id=" . $cal->getId() . "&event_id=" . $event->getId());
					}else{
						throw new IllegalArgumentException("title and message and comment_id must be sent in form input to edit comment");
					}
				}



				/**
				 * add the style sheet to the document for this page
				 */
				$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/event_view.css"));
				$this->doc->addStyleSheet($css);


				if(isset($data_list["commit"]) && $data_list["commit"] == "1" &&
				   (isset($data_list["subview"]) && $data_list["subview"] == "delete" ||
				    isset($data_list["subview"]) && $data_list["subview"] == "delete_series")){
					$delevent_view = new Panel();
					$delevent_view->getStyle()->setClassname("big_container");
					$delevent_view->getStyle()->setWidth("100%");
					$delevent_view->getStyle()->setHeight("400");
					$delevent_view->setAlign("center");
					$delevent_view->setValign("middle");

					$notice = new Panel();
					$notice->setAlign("center");
					$notice->setStyle(new Style("calendar_form"));

					if(isset($data_list["subview"]) && $data_list["subview"] == "delete_series"){
						$event = $event->returnRecurrance(false);
					}
					if($cal->removeEvent($this->event_id)){
						$view = $strongcal->getUserVar("highlight");
						throw new RedirectException("index.php?view=$view");
					}
					$text = new Text("Event " . $this->event_id . " has NOT been deleted.");

					$text->setStyle(new Style("form_header"));
					$notice->add($text);
					$delevent_view->add($notice);
					return new module_bootstrap_data($delevent_view, "the delete event view");
				}

				$container_style = new Style("container");
				$container_style->setWidth("450px");
				$container_style->setHeight("425px");

				$left_cell_style = new Style("panel");
				$left_cell_style->setWidth("50px");
				$left_cell_style->setHeight("50px");

				$right_cell_style = new Style("title");
				$right_cell_style->setWidth("370px");

				$date_style = new Style("date_style");
				$date_style->setWidth("420px");

				$content_style = new Style("content_style");
				$content_style->setWidth("420px");
				$content_style->setHeight("275px");

				$attendee_content_style = new Style("content_style");
				$attendee_content_style->setWidth("420px");
				$attendee_content_style->setHeight("315px");

				$reminder_content_style = new Style("content_style");
				$reminder_content_style->setWidth("420px");
				$reminder_content_style->setHeight("315px");

				$content_style = new Style("content_style");
				$content_style->setWidth("420px");
				$content_style->setHeight("315px");

				$image_style = new Style();
				//$image_style->setWidth("420px");

				$comment_style = new Style("comments");
				$comment_style->setWidth("420px");
				$comment_style->setHeight("200px");
				$comment_style->setBackground("#D5DAD0");

				$bottom_style = new Style("bottom");
				$bottom_style->setWidth("450px");
				$bottom_style->setHeight("220px");

				$color_style = new Style("container");
				$color_style->setWidth("35px");
				$color_style->setHeight("35px");
				$color_style->setBackground($event->calendar()->color());

				$border_bottom_style = new Style("border_bottom");
				/**
				 * end defining styles
				 */


				/**
				 * begin constructing container
				 */

				$my_container = new GridPanel(1);
				$my_container->setValign("top");
				$my_container->setStyle($container_style);

				$title = new Text("<b>Delete Event?</b><br>");
				$text = new Text("Delete the event <i>" . $event->getDisplayValue("title") . "</i>?<br>");
				$warning = new Text("(All related information will be lost. This cannot be reversed.)");
				$warning->getStyle()->setFontSize(8);
				$delete_confirm_window = new SimpleWindow($title);
				$delete_confirm_window->add($text);
				$delete_confirm_window->add($warning);
				$yes_action = new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=event&subview=delete&commit=1&event_id=" . $this->event_id . "&cal_id=" . $this->cal_id);
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
				$delete_confirm_window->add($buttons);
				$buttons->add($yes_button);

				if(is_object($event->stealRecurrance())){
					$title = new Text("<b>Delete Series?</b><br>");
					$text = new Text("Delete the series containing: <i>" . $event->getDisplayValue("title") . "</i>?<br>");
					$warning = new Text("(All events and information will be lost. This cannot be reversed.)");
					$warning->getStyle()->setFontSize(8);
					$text->getStyle()->setFontFamily("verdana, sans-serif");
					$text->getStyle()->setFontSize(9);
					$delete_series_confirm_window = new SimpleWindow($title);
					$delete_series_confirm_window->add($text);
					$delete_series_confirm_window->add($warning);
					$yes_action = new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=event&subview=delete_series&commit=1&event_id=" . $this->event_id . "&cal_id=" . $this->cal_id);
					$no_action = new MoveToAction($delete_series_confirm_window, -1000, -1000);

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
					$delete_series_confirm_window->add($buttons);

					$delete_series_button = new Button("delete series");
					$delete_series_button->getStyle()->setPaddingTop(4);
					$delete_series_button->getStyle()->setPaddingBottom(4);
					$delete_series_button->getStyle()->setClassname("event_button");
					$delete_series_button->addAction(new MoveToCenterAction($delete_series_confirm_window));
				}

				$delete_button = new Button("delete");
				$delete_button->getStyle()->setPaddingTop(4);
				$delete_button->getStyle()->setPaddingBottom(4);
				$delete_button->getStyle()->setClassname("event_button");
				$delete_button->addAction(new MoveToCenterAction($delete_confirm_window));

				$edit_button = new Button("edit");
				$edit_button->getStyle()->setPaddingTop(4);
				$edit_button->getStyle()->setPaddingBottom(4);
				$edit_button->getStyle()->setClassname("event_button");
				$edit_button->addAction(new LoadPageAction("index.php?view=edit_event&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id));


				$button_panel = new GridPanel(5);
				$button_panel->getStyle()->setMarginLeft(10);
				if($cal->canWriteEntries() && $event->author() == $this->avalanche->getActiveUser() || $cal->canWriteName()){
					$button_panel->add($edit_button);
					$button_panel->add($delete_button);
					if(is_object($event->stealRecurrance())){
						$button_panel->add($delete_series_button);
						$this->doc->addHidden($delete_series_confirm_window);
					}
				}
				$this->doc->addHidden($delete_confirm_window);
				$top_row = new BorderPanel();
				$color_panel = new Panel();
				$color_panel->setStyle($left_cell_style);
				$color_panel2 = new Panel();
				$color_panel2->setStyle($color_style);
				$real_title_panel = new Panel();
				$real_title_panel->setStyle($right_cell_style);
				$color_panel->add($color_panel2);
				$color_panel->setAlign("center");
				$color_panel->setValign("middle");
				$username = $os->getUsername($cal->author());
				$cal_panel = new Panel();
				$cal_panel->add(new Text("on "));
				$link = new Link(htmlspecialchars($username), "javascript:;");
				$this->createUserMenu($link, $cal->author());
				$cal_panel->add($link);
				$cal_panel->add(new Text(htmlspecialchars("'s " . $cal->name() . " calendar.")));
				$cal_panel->setStyle(new Style("cal_title"));
				$event_title = $event->getDisplayValue("title");
				if(strlen(trim($event_title)) == 0){
					$event_title = "<i>no title</i>";
				}
				$real_title_panel->add(new Text($event_title));

				$grid = new GridPanel(1);
				$grid->setWidth("100%");
				$grid->add($real_title_panel);
				$grid->add($cal_panel);
				$top_row->setWest($color_panel);
				$top_row->setCenter($grid);
				// $top_row->setEast($button_panel);
				$top_row->setEastWidth("200");
				$top_row->setAlign("center");

				$panel_style = new Style();
				$panel_style->setHeight("325px");

				// event panel will hold all basic event info
				$event_panel = new GridPanel(1);
				$event_panel->setStyle(clone $panel_style);
				$event_panel->setAlign("center");
				$event_panel->setWidth("100%");

				// attendee panel
				$attendee_panel = new GridPanel(1);
				$attendee_panel->setStyle(clone $panel_style);
				$attendee_panel->setAlign("left");
				$attendee_panel->setWidth("100%");
				$content_panel = new ScrollPanel();
				$content_panel->setStyle($attendee_content_style);
				$panel = $this->getAttendeePanel($event);
				$content_panel->add($panel);
				$attendee_panel->add($content_panel);

				// reminder panel
				$reminder_panel = new GridPanel(1);
				$reminder_panel->setStyle(clone $panel_style);
				$reminder_panel->setAlign("center");
				$reminder_panel->setWidth("100%");
				$content_panel = new ScrollPanel();
				$content_panel->setStyle($reminder_content_style);
				$panel = $this->getReminderPanel($event);
				$content_panel->add($panel);
				$reminder_panel->add($content_panel);

				// create tabs for top view
				$tab_panel = new GridPanel(1);
				$tab_panel->setWidth("100%");
				$tab_panel->getStyle()->setWidth("450px");
				$tab_panel->add($this->getTabs($subview, $event_panel, $attendee_panel, $reminder_panel, $event));


				// format the start/end times/dates
				$start_time = $event->getDisplayValue("start_time");
				$start_hour = substr($start_time, 0, 2);
				$start_min  = substr($start_time, 3, 2);

				$start_date  = $event->getDisplayValue("start_date");
				$start_year  = substr($start_date, 0, 4);
				$start_month = substr($start_date, 5, 2);
				$start_day   = substr($start_date, 8, 2);

				$end_time = $event->getDisplayValue("end_time");
				$end_hour = substr($end_time, 0, 2);
				$end_min  = substr($end_time, 3, 2);

				$end_date  = $event->getDisplayValue("end_date");
				$end_year  = substr($end_date, 0, 4);
				$end_month = substr($end_date, 5, 2);
				$end_day   = substr($end_date, 8, 2);

				$start_stamp = mktime($start_hour, $start_min, 0, $start_month, $start_day, $start_year);
				$end_stamp   = mktime($end_hour, $end_min, 0, $end_month, $end_day, $end_year);

				$start_date = "Begins: " .
				date("g:i A ", $start_stamp) .
				" on " . "<a href='index.php?view=day&date=" . date("Y-m-d", $start_stamp) . "'>" .
				date("l, F jS, Y", $start_stamp) . "</a>";

				$end_date =   "Ends: " .
				date("g:i A ", $end_stamp) .
				" on " . "<a href='index.php?view=day&date=" . date("Y-m-d", $end_stamp) . "'>" .
				date("l, F jS, Y", $end_stamp) . "</a>";

				if(date("Y-m-d", $start_stamp) == date("Y-m-d", $end_stamp)){
					$all_day =   "All Day: " .
					"<a href='index.php?view=day&date=" . date("Y-m-d", $start_stamp) . "'>" .
					date("l, F jS, Y", $end_stamp) . "</a>";
				}else{
					$all_day =   "Begins: " .
					"<a href='index.php?view=day&date=" . date("Y-m-d", $start_stamp) . "'>" .
					date("l, F jS, Y", $start_stamp) . "</a><br>&nbsp;&nbsp;" .
					"Ends: " .
					"<a href='index.php?view=day&date=" . date("Y-m-d", $end_stamp) . "'>" .
					date("l, F jS, Y", $end_stamp) . "</a>";
				}

				$second_row = new GridPanel(1);
				$date_panel = new Panel();
				$date_panel->setStyle($date_style);
				if(!$event->isAllDay()){
					$date_panel->add(new Text("$start_date<br>&nbsp; $end_date"));
				}else{
					$date_panel->add(new Text("$all_day"));
				}
				$date_panel->setValign("top");
				$second_row->add($date_panel);

				$third_row = new GridPanel(1);
				$content_panel = new ScrollPanel();
				$content_panel->setStyle($content_style);

				// set icon for priority
				if(strcasecmp($event->getDisplayValue("priority"), "high") == 0){
					$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $strongcal->folder() . "/gui/os/exclamation.gif");
					$icon->setWidth(35);
					$icon->setHeight(35);
					$color_panel2->add($icon);
				}
				// end setting icon


				$field_list = $event->fields();
				foreach($field_list as $field){
					if($field->field() != "start_date" &&
					$field->field() != "end_date" &&
					$field->field() != "start_time" &&
					$field->field() != "end_time" &&
					$field->field() != "title"){
						$field_txt = $field->display_value();
						if($field_txt !== false){
							if(strlen($field_txt) == 0){
								$field_txt = "<i>no data</i>";
							}
							$field_panel = new QuotePanel(20);
							$field_panel->setStyle(new Style("content_font"));
							if($field->type() == "url"){
								$values = explode("\n", $field_txt);
								if(count($values) == 2){
									if(strlen(trim($values[0])) == 0){
										$field_panel->add(new Text("<i>no data</i>"));
									}else{
										$link = new Link(htmlspecialchars($values[0], ENT_QUOTES), htmlspecialchars($values[1], ENT_QUOTES));
										$link->setTarget("_new");
										$field_panel->add($link);
									}
								}else{
									$field_txt = str_replace("\n", "<br>", $field_txt);
									$field_panel->add(new Text($field_txt));
								}
							}else{
								$field_txt = str_replace("\n", "<br>", $field_txt);
								$field_panel->add(new Text($field_txt));
							}

							$field_title = new Text($field->prompt());
							$field_title->setStyle(new Style("content_title"));
							$content_panel->add($field_title);
							$content_panel->add($field_panel);
						}
					}
				}
				$content_panel->setAlign("left");
				$content_panel->setValign("top");
				$third_row->add($content_panel);

				$bottom_panel = new Panel();  // used in row 5. defined here for use by action
				$fifth_row = new GridPanel(1);
				$fourth_row = new BorderPanel();

				$image_panel = new GridPanel(2);
				$image_panel->setAlign("right");
				$export_button = new Button("");
				$export_button->setStyle(new Style("export_event_button"));
				$export_button->addAction(new LoadPageAction("?view=export&range=event&cal_id=" . $event->calendar()->getId() . "&event_id=" . $event->getId()));
				$comment_button = new Button("");
				$comment_button->setStyle(new Style("comment_button"));
				$comment_button->addAction(new DisplayInlineAction($fifth_row));
				$comment_button->addAction(new HeightAction($fifth_row, 230));
				$image_panel->add($export_button);
				$image_panel->add($comment_button);
				$image_panel->setStyle($image_style);
				$image_panel->getCellStyle()->setPaddingTop(4);
				$fourth_row->setCenter($button_panel);
				$fourth_row->setEast($image_panel);

				$fifth_row->setAlign("center");
				$fifth_row->setValign("middle");
				$fifth_row_style = new Style("comment_row");
				$fifth_row_style->setWidth("100%");
				$fifth_row_style->setHeight("0px");
				$fifth_row->setStyle($fifth_row_style);
				$bottom_panel->setStyle($bottom_style);
				$comment_panel = new ScrollPanel();
				$comment_panel->setStyle($comment_style);
				$comment_panel->getStyle()->setWidth("420px");
				$comment_panel->getStyle()->setHeight("180px");
				$comment_panel->setValign("top");

				$comment_panel_holder = new Panel();
				$comment_panel_holder->setValign("top");
				$comment_panel_holder_style = new Style("comments_holder");
				$comment_panel_holder_style->setWidth("420px");
				$comment_panel_holder_style->setHeight("200px");
				$comment_panel_holder->setStyle($comment_panel_holder_style);
				$comment_panel_holder->add($comment_panel);


				$add_comment_panel = new GridPanel(2);
				$add_comment_panel->setStyle($border_bottom_style);
				$add_comment_panel->getStyle()->setDisplayNone();
				$add_comment_panel->setValign("top");
				$add_comment_panel->getCellStyle()->setFontFamily("verdana, sans-serif");
				$add_comment_panel->getCellStyle()->setFontSize(10);
				$add_comment_panel->setWidth("100%");
				$add_comment_panel->add(new Text("<b>Add Comment</b>"));
				$add_comment_panel->add(new Text("&nbsp;"));
				$add_comment_panel->add(new Text("title"));
				$title_input = new SmallTextInput();
				$title_input->setSize(30);
				$title_input->setName("title");
				$title_input->setStyle(new Style("comment_title_input"));
				$add_comment_panel->add($title_input);
				$add_comment_panel->add(new Text("message"));
				$message_input = new TextAreaInput();
				$message_input->setName("message");
				$message_input->setCols(35);
				$message_input->setRows(3);
				$message_input->setStyle(new Style("comment_message_input"));
				$add_comment_panel->add($message_input);
				$submit = new Text("<input type='submit' value='Add' style='border: 1px solid black;'>");
				$add_comment_panel->add(new Text("&nbsp;"));
				$add_comment_panel->add($submit);

				$add_comment_form = new FormPanel("index.php");
				$add_comment_form->setAsGet();
				$add_comment_form->addHiddenField("view","event");
				$add_comment_form->addHiddenField("subview", "add_comment");
				$add_comment_form->addHiddenField("cal_id", (string) $cal->getId());
				$add_comment_form->addHiddenField("event_id",  (string) $event->getId());
				$add_comment_form->add($add_comment_panel);
				$add_comment_form->setWidth("100%");

				$reset = new NewFunctionAction("reset_comments");
				$this->doc->addFunction($reset);
				$call_reset = new CallFunctionAction("reset_comments");

				$add_comment_link = new Link("Click to Add Comment", "javascript:;");
				$add_comment_link->getStyle()->setFontFamily("verdana, sans-serif");
				$add_comment_link->getStyle()->setFontSize(10);
				$add_comment_link->addAction($call_reset);
				$add_comment_link->addAction(new DisplayNoneAction($add_comment_link));
				$add_comment_link->addAction(new DisplayInlineAction($add_comment_panel));

				$reset->addAction(new DisplayInlineAction($add_comment_link));
				$reset->addAction(new DisplayNoneAction($add_comment_panel));

				if($cal->canWriteComments()){
					$comment_panel->add($add_comment_link);
					$comment_panel->add($add_comment_form);
				}

				$bottom_panel->add($comment_panel_holder);
				//$bottom_panel->setStyle($comment_style);
				$comment_list = $event->comments();
				$author_style = new Style("comment_author");
				$title_style = new Style("comment_title");
				$date_style = new Style("comment_date");
				$light = true;

				if(count($comment_list) == 0){
					$temp_panel = new Panel();
					$temp_style = new Style("comments_dark");
					$temp_style->setWidth("100%");
					$temp_panel->setStyle($temp_style);
					$temp_panel->add(new Text("no comments"));
					$comment_panel->add($temp_panel);
					$fifth_row_style->setDisplayNone();
				}else{
					$bottom_panel->getStyle()->setDisplayInline();
					$fifth_row->getStyle()->setHeight("230px");
				}

				foreach($comment_list as $comment){
					if(strlen($comment["title"]) == 0){
						$comment["title"] = "<i>no title</i>";
					}
					$temp_panel = new FormPanel("index.php");
					$temp_panel->setAsGet();
					$temp_panel->addHiddenField("view", "event");
					$temp_panel->addHiddenField("subview", "edit_comment");
					$temp_panel->addHiddenField("cal_id",  (string)$cal->getId());
					$temp_panel->addHiddenField("event_id",  (string)$event->getId());
					$temp_panel->addHiddenField("comment_id",  (string)$comment["id"]);
					$title_panel = new Panel();
					if($light){
						$temp_style = new Style("comments_light");
						$button_style = new Style("buttons_dark");
					}else{
						$temp_style = new Style("comments_dark");
						$button_style = new Style("buttons_light");
					}
					$temp_style->setWidth("100%");
					$temp_panel->setStyle($temp_style);
					$title_panel->setStyle($temp_style);

					$author = new Text($os->getUsername((int)$comment["author"]));
					$author->setStyle($author_style);

					$title = new Text($comment["title"]);
					$title->setStyle($title_style);
					$title_input = new SmallTextInput();
					$title_input->setValue($comment["title"]);
					$title_input->setSize(min(strlen($comment["title"]), 30));
					$title_input->setName("title");
					$title_input->setStyle(new Style("comment_title_input"));
					$title_input->getStyle()->setDisplayNone();

					$date = $comment["date"];
					$date = explode(" ", $date);
					$time = $date[1];
					$date = $date[0];
					$date = explode("-", $date);
					$time = explode(":", $time);
					$date = mktime($time[0],$time[1],$time[2], $date[1], $date[2], $date[0]);
					$date = date("M jS g:ia", $date);

					$date = new Text($date);
					$date->setStyle($date_style);

					$body = new Text(str_replace("\n", "<br>", $comment["body"]));
					$body_input = new TextAreaInput();
					$body_input->setName("message");
					$body_input->setValue($comment["body"]);
					$body_input->setCols(50);
					$body_input->setRows(max(4, substr_count($comment["body"], "\n")));
					$body_input->setStyle(new Style("comment_message_input"));
					$body_input->getStyle()->setDisplayNone();

					$submit = new Text("<input type='submit' value='Edit' style='border: 1px solid black;'>");
					$submit->getStyle()->setDisplayNone();

					$title_panel->add($title);
					$title_panel->add($title_input);
					$title_panel->add(new Text(" by "));
					$title_panel->add($author);
					$title_panel->add(new Text(" on "));
					$title_panel->add($date);
					if($comment["author"] == $this->avalanche->loggedInHuh() && $cal->canWriteComments() ||
					   $cal->canWriteName()){
						$buttons = new BorderPanel();
						$buttons->setCenter($title_panel);
						$edit_del = new GridPanel(3);
						$buttons->setEast($edit_del);
						$edit_button = new Button("edit");
						$edit_button->setStyle($button_style);
						$cancel_button = new Button("cancel");

						$cancel_button->setStyle(new Style());
						$cancel_button->getStyle()->setClassname($button_style->getClassname());
						$cancel_button->getStyle()->setDisplayNone();
						$del_button = new Button("delete");
						$del_button->setStyle($button_style);

						$reset->addAction(new DisplayNoneAction($title_input));
						$reset->addAction(new DisplayInlineAction($title));
						$reset->addAction(new DisplayNoneAction($body_input));
						$reset->addAction(new DisplayNoneAction($submit));
						$reset->addAction(new DisplayInlineAction($body));
						$reset->addAction(new DisplayNoneAction($cancel_button));
						$reset->addAction(new DisplayBlockAction($edit_button));
						$reset->addAction(new DisplayBlockAction($del_button));

						$edit_button->addAction($call_reset);
						$edit_button->addAction(new DisplayNoneAction($title));
						$edit_button->addAction(new DisplayInlineAction($title_input));
						$edit_button->addAction(new DisplayNoneAction($body));
						$edit_button->addAction(new DisplayInlineAction($body_input));
						$edit_button->addAction(new DisplayBlockAction($submit));
						$edit_button->addAction(new DisplayNoneAction($edit_button));
						$edit_button->addAction(new DisplayNoneAction($del_button));
						$edit_button->addAction(new DisplayBlockAction($cancel_button));

						$cancel_button->addAction($call_reset);
						$cancel_button->addAction(new DisplayNoneAction($cancel_button));
						$cancel_button->addAction(new DisplayBlockAction($edit_button));
						$cancel_button->addAction(new DisplayBlockAction($del_button));

						$title = new Text("<b>Delete Comment?</b><br>");
						$text = new Text("Are you sure you want to delete this comment?");
						$text->getStyle()->setFontFamily("verdana, sans-serif");
						$text->getStyle()->setFontSize(9);
						$delete_confirm_window = new SimpleWindow($title);
						$delete_confirm_window->add($text);
						$yes_action = new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=event&subview=delete_comment&event_id=" . $this->event_id . "&cal_id=" . $this->cal_id . "&comment_id=" . $comment["id"]);
						$no_action = new MoveToAction($delete_confirm_window, -1000, -1000);
						$no_button = new Button("Never Mind");
						$no_button->setStyle(new Style("confirm_window_no"));
						$no_button->addAction($no_action);
						$delete_confirm_window->add($no_button);

						$yes_button = new Button("Delete");
						$yes_button->setStyle(new Style("confirm_window_yes"));
						$yes_button->addAction($yes_action);
						$yes_button->addAction($no_action);
						$delete_confirm_window->add($yes_button);

						$del_button->addAction($call_reset);
						$del_button->addAction(new MoveToCenterAction($delete_confirm_window, 500));

						$edit_del->add($edit_button);
						$edit_del->add($cancel_button);
						$edit_del->add($del_button);
						$temp_panel->add($buttons);
						$this->doc->addHidden($delete_confirm_window);
					}else{
						$temp_panel->add($title_panel);
					}
					if(strlen($comment["body"]) > 0){
						$body_panel = new QuotePanel();
						$body_panel->setStyle($temp_style);
						$body_panel->setWidth("100%");
						$body_panel->add($body);
						$body_panel->add($body_input);
						$body_panel->add($submit);
						$temp_panel->add($body_panel);
					}

					$comment_panel->add($temp_panel);
					$light = !$light;
				}
				$bottom_panel->setAlign("center");
				$fifth_row->add($bottom_panel);

				$event_panel->add($third_row);

				$content = new Panel();
				$content->add($event_panel);
				$content->add($attendee_panel);
				$content->add($reminder_panel);

				$my_container->add($top_row);
				$my_container->add($second_row);
				$my_container->add($tab_panel);
				$my_container->add($content);
				$my_container->add($fourth_row);
				$my_container->add($fifth_row);
				$my_container->setAlign("center");


				$event_view = new Panel();

				$event_style = new Style("big_event_container");
				$event_style->setPaddingTop(70);
				$event_style->setPaddingLeft(110);
				$event_style->setWidth("100%");
				$event_view->setStyle($event_style);
				$event_view->setAlign("left");
				$event_view->add($my_container);
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You are not allowed to view this event."));
				$error = new ErrorPanel($content);
				$error->getStyle()->setHeight("400px");
				$event_view = $error;
			}

			return new module_bootstrap_data($event_view, "a gui component for the event view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}


	private function getTabs($subview, $event_panel, $attendee_panel, $reminder_panel, $event){
		$reminder = $this->avalanche->getModule("reminder");
		$view = "event";
		$subview = "event";
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
		$open_general_button->add(new Text("Event"));

		$closed_general_button = new Button();
		$closed_general_button->setStyle(new Style("preferences_tab_closed"));
		$closed_general_button->setAlign("left");
		$closed_general_button->add(new Text("Event"));

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
		$event_panel->getStyle()->setDisplayNone();
		$attendee_panel->getStyle()->setDisplayNone();
		$reminder_panel->getStyle()->setDisplayNone();
		if($subview == "event"){
			$open_general_button->getStyle()->setDisplayBlock();
			$closed_general_button->getStyle()->setDisplayNone();
			$event_panel->getStyle()->setDisplayBlock();
		}else if($subview == "attendees"){
			if(isset($this->data_list["add_attendee"]) && $this->data_list["add_attendee"]){
				if(!isset($this->data_list["user_id"])){
					// fail silently
					throw new RedirectException("index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id);
				}else if(isset($this->data_list["add_to_all"]) && $this->data_list["add_to_all"]){
					if(is_object($event->stealRecurrance())){
						$events = $event->calendar()->getEventsIn($event->stealRecurrance());
						foreach($events as $e){
							$e->addAttendee((int)$this->data_list["user_id"]);
						}
					}else{
						$event->addAttendee((int)$this->data_list["user_id"]);
					}
				}else{
					$event->addAttendee((int)$this->data_list["user_id"]);
				}
				throw new RedirectException("index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id . "&user_added[]=" . $this->data_list["user_id"]);
			}
			if(isset($this->data_list["add_attendees"]) && $this->data_list["add_attendees"]){
				if(!isset($this->data_list["group_id"])){
					// fail silently
					throw new RedirectException("index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id);
				}
				$group_id = (int)$this->data_list["group_id"];
				$group = $this->avalanche->getUsergroup($group_id);
				$users = $group->getAllUsersIn();
				if(isset($this->data_list["add_to_all"]) && $this->data_list["add_to_all"]){
					if(is_object($event->stealRecurrance())){
						$events = $event->calendar()->getEventsIn($event->stealRecurrance());
						foreach($events as $e){
							foreach($users as $user){
								$e->addAttendee($user->getId());
							}
						}
					}else{
						foreach($users as $user){
							$event->addAttendee($user->getId());
						}
					}
				}else{
					foreach($users as $user){
						$event->addAttendee($user->getId());
					}
				}
				$user_ids = "";
				foreach($users as $user){
					$user_ids .= "&user_added[]=" . $user->getId();
				}
				throw new RedirectException("index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id . $user_ids);
			}
			if(isset($this->data_list["remove_attendee"]) && $this->data_list["remove_attendee"]){
				if(!isset($this->data_list["user_id"])){
					throw new IllegalArgumentException("user_id must be sent as form input to remove an attendee");
				}
				if(isset($this->data_list["remove_from_all"]) && $this->data_list["remove_from_all"]){
					if(is_object($event->stealRecurrance())){
						$events = $event->calendar()->getEventsIn($event->stealRecurrance());
						foreach($events as $e){
							$e->removeAttendee((int)$this->data_list["user_id"]);
						}
					}else{
						$event->removeAttendee((int)$this->data_list["user_id"]);
					}
				}else{
					$event->removeAttendee((int)$this->data_list["user_id"]);
				}
				header("Location: index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id);
				exit;
			}
			if(isset($this->data_list["contact_attendees"]) && $this->data_list["contact_attendees"]){
				$subj = new SmallTextInput();
				$subj->setName("message_subject");
				$subj->loadFormValue($this->data_list);
				$subj = $subj->getValue();
				$body = new TextAreaInput();
				$body->setName("message_body");
				$body->loadFormValue($this->data_list);
				$body = $body->getValue();
				$attendees = $event->attendees();
				foreach($attendees as $a){
					$user = $this->avalanche->getUser($a->userId());
					$user->contact($this->avalanche->getUser($this->avalanche->loggedInHuh()), $subj, $body);
				}
				header("Location: index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id . "&message_sent=1");
				exit;
			}
			$open_calendar_button->getStyle()->setDisplayBlock();
			$closed_calendar_button->getStyle()->setDisplayNone();
			$attendee_panel->getStyle()->setDisplayBlock();
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
				if(isset($this->data_list["all_attendees"]) && $this->data_list["all_attendees"]){
					$reminder->type(module_reminder_reminder::$TYPE_EVENT_ATTENDEES, $event);
				}else{
					$reminder->type(module_reminder_reminder::$TYPE_EVENT, $event);
				}
				$reminder->day((int)$this->data_list["days_before"]);
				$reminder->hour((int)$this->data_list["hours_before"]);
				$reminder->minute((int)$this->data_list["minutes_before"]);

				if(isset($this->data_list["user_id"]) && is_array($this->data_list["user_id"])){
					$list = $this->data_list["user_id"];
					foreach($list as $user_id){
						if($user_id != $reminder->author()){
							$reminder->addUser((int)$user_id);
						}
					}
				}

				$subject = new SmallTextInput();
				$subject->setName("subject");
				$subject->loadFormValue($this->data_list);
				$reminder->subject($subject->getValue());

				$body = new SmallTextInput();
				$body->setName("body");
				$body->loadFormValue($this->data_list);
				$reminder->body($body->getValue());

				header("Location: index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id . "&reminder_added=" . $reminder->getId());
				exit;
			}
			if(isset($this->data_list["remove_reminder"]) && $this->data_list["remove_reminder"]){
				if(!isset($this->data_list["reminder_id"])){
					throw new IllegalArgumentException("reminder_id must be sent as form input to delete a reminder");
				}
				$reminder->deleteReminder((int)$this->data_list["reminder_id"]);
				header("Location: index.php?view=$view&subview=$subview&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id);
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
		$calendar = new Panel();
		$calendar->add($open_calendar_button);
		$calendar->add($closed_calendar_button);
		$notifier = new Panel();
		$notifier->add($open_notifier_button);
		$notifier->add($closed_notifier_button);
		$buttons->add($general);
		$buttons->add($calendar);
		$buttons->add($notifier);

		// create visibility functions and set up actions
		$closefunction = new NewFunctionAction("close_general_tabs");
		$closefunction->addAction(new DisplayNoneAction($open_general_button));
		$closefunction->addAction(new DisplayNoneAction($event_panel));
		$closefunction->addAction(new DisplayBlockAction($closed_general_button));
		$closefunction->addAction(new DisplayNoneAction($open_calendar_button));
		$closefunction->addAction(new DisplayNoneAction($attendee_panel));
		$closefunction->addAction(new DisplayBlockAction($closed_calendar_button));
		$closefunction->addAction(new DisplayNoneAction($open_notifier_button));
		$closefunction->addAction(new DisplayNoneAction($reminder_panel));
		$closefunction->addAction(new DisplayBlockAction($closed_notifier_button));
		$this->doc->addFunction($closefunction);

		$closed_general_button->addAction(new CallFunctionAction("close_general_tabs"));
		$closed_general_button->addAction(new DisplayNoneAction($closed_general_button));
		$closed_general_button->addAction(new DisplayBlockAction($open_general_button));
		$closed_general_button->addAction(new DisplayBlockAction($event_panel));

		$closed_calendar_button->addAction(new CallFunctionAction("close_general_tabs"));
		$closed_calendar_button->addAction(new DisplayNoneAction($closed_calendar_button));
		$closed_calendar_button->addAction(new DisplayBlockAction($open_calendar_button));
		$closed_calendar_button->addAction(new DisplayBlockAction($attendee_panel));

		$closed_notifier_button->addAction(new CallFunctionAction("close_general_tabs"));
		$closed_notifier_button->addAction(new DisplayNoneAction($closed_notifier_button));
		$closed_notifier_button->addAction(new DisplayBlockAction($open_notifier_button));
		$closed_notifier_button->addAction(new DisplayBlockAction($reminder_panel));

		return $buttons;
	}

	private function getAttendeePanel($event){
		$os = $this->avalanche->getModule("os");
		$tab_panel = new TabbedPanel();
		$tab_panel->setWidth("100%");

		$title_style = new Style();
		$title_style->setFontFamily("verdana, sans-serif");
		$title_style->setFontSize(10);
		$title_style->setFontWeight("bold");


		// list attendees tab
		$users = $event->attendees();
		$data = new module_bootstrap_data($users, "send in the users");
		$runner = new module_bootstrap_runner();
		$runner->add(new module_bootstrap_strongcal_attendeesorter());
		$data = $runner->run($data);
		$users = $data->data();
		$user_text = new GridPanel(5);
		$user_text->setCellStyle(new Style("content_font"));
		$user_text->getCellStyle()->setPaddingRight(20);
		if(count($users) == 0){
			$user_text = new Panel();
			$user_text->setStyle(new Style("content_font"));
			$user_text->setWidth("100%");
			$user_text->add(new Text("<br><i>There are no attendees registered for this event. Click the [Invite User] tab to invite users to this event.</i>"));
		}else{
			$user_text->add(new Text("<b>Username</b>"));
			$user_text->add(new Text("<b>Name</b>"));
			$user_text->add(new Text(""));
			$user_text->add(new Text(""));
			$user_text->add(new Text(""));
		}
		foreach($users as $attendee){
			$user = $this->avalanche->getUser($attendee->userId());
			$name = $this->avalanche->getName($user->getId());
			$username = $user->username();
			$name = $name["title"] . " " . $name["first"] . " " . $name["middle"] . " " . $name["last"];
			$added = "";
			if(isset($this->data_list["user_added"]) && is_array($this->data_list["user_added"]) && in_array($user->getId(), $this->data_list["user_added"])){
				$username = "<b>$username</b>";
				$name = "<b>$name</b>";
				$added = "<b>(just added)</b>";
			}
			$link = new Link($username, "javascript:;");
			$link->getStyle()->setFontColor("black");
			$this->createUserMenu($link, $attendee->userId());
			$user_text->add($link);
			$link = new Link($name, "javascript:;");
			$link->getStyle()->setFontColor("black");
			$this->createUserMenu($link, $attendee->userId());
			$user_text->add($link);
			if($this->avalanche->loggedInHuh() == $event->author() ||
			   $event->calendar()->canWriteEntries()){
				$user_text->add(new Link("remove", "index.php?view=event&subview=attendees&user_id=" . $attendee->userId() . "&remove_attendee=1&cal_id=" . $this->cal_id  . "&event_id=" . $this->event_id));
			}else{
				$user_text->add(new Text("&nbsp;"));
			}
			if(is_object($event->stealRecurrance()) && ($this->avalanche->loggedInHuh() == $event->author() ||
			   $event->calendar()->canWriteEntries())){
				$user_text->add(new Link("remove in series", "index.php?view=event&subview=attendees&remove_from_all=1&user_id=" . $attendee->userId() . "&remove_attendee=1&cal_id=" . $this->cal_id  . "&event_id=" . $this->event_id));
			}else{
				$user_text->add(new Text("&nbsp;"));
			}
			$user_text->add(new Text($added));

		}
		if(count($users)){
			$panel = new GridPanel(1);
			$panel->setWidth("100%");
			$panel->setCellStyle(new Style("content_font"));
			$panel->add(new Text("This is the list of attendees for this event."));
			$panel->add($user_text);
			$user_text = $panel;
		}

		$content = new GridPanel(1);
		$content->setWidth("100%");
		$content->setStyle(new Style("content_font"));

		$s = clone $title_style;
		$s->setPaddingBottom(6);
		$content->add(new Text("List of Attendees"), $s);
		$content->add($user_text);

		$open_button = new Button("Attendees");
		$open_button->setStyle(new Style("event_tab_light"));
		$close_button = new Button("Attendees");
		$close_button->setStyle(new Style("event_tab_dark"));
		$tab_panel->add($content, $open_button, $close_button);


		// add user tab
		if($event->calendar()->canWriteEntries() ||
		   $this->avalanche->loggedInHuh() == $event->author()){

			$name_input = new SmallTextInput();
			$name_input->getStyle()->setBorderWidth(1);
			$name_input->getStyle()->setBorderStyle("solid");
			$name_input->getStyle()->setBorderColor("black");
			$name_input->setSize(20);
			$dd = new DropDownInput();
			$dd->setName("user_id");
			$clone = new DropDownInput();
			$clone->getStyle()->setDisplayNone();

			$users = $this->avalanche->getAllUsers();
			$dd->setSize(min(5, count($users)));
			$first_huh = true;
			foreach($users as $user){
				$name = $os->getUsername($user->getId());
				if(strlen($name)){
					$name = $name . " (" . $user->username() . ")";
				}else{
					$name = $user->username();
				}
				$option = new DropDownOption($name, (string)$user->getId());
				$dd->addOption($option);
				$clone->addOption($option);
				if($first_huh){
					$option->setSelected(true);
					$first_huh = false;
				}
			}

			$name_input->addKeyUpAction(new FilterDropDownAction($dd, $clone, $name_input));

			$add_user_form = new GridPanel(3);
			$add_user_form->setCellStyle(new Style("content_font"));
			$add_user_form->getCellStyle()->setPaddingRight(4);
			if(is_object($event->stealRecurrance())){
				// then we add the checkbox for (add attendees to all events in series)
				$add_user_form->add(new Text(""));
				$add_to_all = new CheckInput("Add invitee to all events in the series");
				$add_to_all->setName("add_to_all");
				$add_to_all->setValue("1");
				$add_to_all->setChecked(true);
				$add_to_all->getStyle()->setMarginBottom(5);
				$add_user_form->add($add_to_all);
				$add_user_form->add(new Text(""));
			}

			$add_user_form->add(new Text("Search: "));
			$add_user_form->add($name_input);
			$add_user_form->add(new Text(""));
			$add_user_form->add($clone);
			$add_user_form->add($dd);
			$add_user_form->add(new Text("<input type='submit' value='Invite' style='border: 1px solid black'>"));

			$actual_form = new FormPanel("index.php");
			$actual_form->setAsGet();
			$actual_form->addHiddenField("view", "event");
			$actual_form->addHiddenField("subview", "attendees");
			$actual_form->addHiddenField("cal_id",  (string)$this->cal_id);
			$actual_form->addHiddenField("event_id",  (string)$this->event_id);
			$actual_form->addHiddenField("add_attendee", "1");
			$actual_form->add($add_user_form);

			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(new Style("content_font"));

			$content->add(new Text("Invite a User"), $title_style);
			$content->add(new Text("You can add an attendee to this event by selecting the user from the list below and clicking [Invite]. To search for a user, type the full name or username to filter the list of users.<br><br>"));
			$content->add($actual_form);

			$open_button = new Button("Invite User");
			$open_button->setStyle(new Style("event_tab_light"));
			$close_button = new Button("Invite User");
			$close_button->setStyle(new Style("event_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}

		// add group tab
		if($event->calendar()->canWriteEntries() ||
		   $this->avalanche->loggedInHuh() == $event->author()){

			$name_input = new SmallTextInput();
			$name_input->getStyle()->setBorderWidth(1);
			$name_input->getStyle()->setBorderStyle("solid");
			$name_input->getStyle()->setBorderColor("black");
			$name_input->setSize(20);
			$dd = new DropDownInput();
			$dd->setName("group_id");
			$clone = new DropDownInput();
			$clone->getStyle()->setDisplayNone();

			$groups = $this->avalanche->getAllUsergroups($this->avalanche->loggedInHuh());
			$filtered_list = array();
			foreach($groups as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC &&
				   ($group->getId() != $this->avalanche->getVar("GUESTGROUP")) ||
				   $group->type() == avalanche_usergroup::$PERSONAL &&
				   $group->author() == $this->avalanche->getActiveUser()){
					$filtered_list[] = $group;
				}
			}
			$groups = $filtered_list;
			$sorter = new MDASorter();
			$groups = $sorter->sortDESC($groups, new OSUsergroupComparator());
			$dd->setSize(min(5, count($groups)));
			$first_huh = true;
			foreach($groups as $group){

				$name = $group->name();
				$option = new DropDownOption($name, (string)$group->getId());
				$dd->addOption($option);
				$clone->addOption($option);
				if($first_huh){
					$option->setSelected(true);
					$first_huh = false;
				}
			}

			$name_input->addKeyUpAction(new FilterDropDownAction($dd, $clone, $name_input));

			$add_group_form = new GridPanel(3);
			$add_group_form->setCellStyle(new Style("content_font"));
			$add_group_form->getCellStyle()->setPaddingRight(4);
			if(is_object($event->stealRecurrance())){
				// then we add the checkbox for (add attendees to all events in series)
				$add_group_form->add(new Text(""));
				$add_to_all = new CheckInput("Invite group to all events in the series");
				$add_to_all->setName("add_to_all");
				$add_to_all->setValue("1");
				$add_to_all->setChecked(true);
				$add_to_all->getStyle()->setMarginBottom(5);
				$add_group_form->add($add_to_all);
				$add_group_form->add(new Text(""));
			}

			$add_group_form->add(new Text("Search: "));
			$add_group_form->add($name_input);
			$add_group_form->add(new Text(""));
			$add_group_form->add($clone);
			$add_group_form->add($dd);
			$add_group_form->add(new Text("<input type='submit' value='Invite' style='border: 1px solid black'>"));

			$actual_form = new FormPanel("index.php");
			$actual_form->setAsGet();
			$actual_form->addHiddenField("view", "event");
			$actual_form->addHiddenField("subview", "attendees");
			$actual_form->addHiddenField("cal_id",  (string)$this->cal_id);
			$actual_form->addHiddenField("event_id",  (string)$this->event_id);
			$actual_form->addHiddenField("add_attendees", "1");
			$actual_form->add($add_group_form);

			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(new Style("content_font"));

			$content->add(new Text("Invite a Group"), $title_style);
			$content->add(new Text("You can invite a group to this event by selecting the group from the list below and clicking [Invite]. To search for a group, type the group name to filter the list of groups.<br><br>"));
			$content->add($actual_form);

			$open_button = new Button("Invite Group");
			$open_button->setStyle(new Style("event_tab_light"));
			$close_button = new Button("Invite Group");
			$close_button->setStyle(new Style("event_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}

		// send message tab
		if($event->calendar()->canWriteEntries() ||
		   $this->avalanche->loggedInHuh() == $event->author()){
			$subject_input = new SmallTextInput();
			$subject_input->setName("message_subject");
			$subject_input->setValue("Regarding our meeting");
			$subject_input->getStyle()->setBorderWidth(1);
			$subject_input->getStyle()->setBorderStyle("solid");
			$subject_input->getStyle()->setBorderColor("black");
			$subject_input->setSize(45);

			$body_input = new TextAreaInput();
			$body_input->setName("message_body");
			$body_input->setValue("Regarding our meeting \"" . $event->getDisplayValue("title") . "\"...");
			$body_input->setStyle($subject_input->getStyle());
			$body_input->setCols(40);
			$body_input->setRows(5);

			$contact_user_form = new GridPanel(2);
			$contact_user_form->setCellStyle(new Style("content_font"));
			$contact_user_form->getCellStyle()->setPaddingRight(4);
			$contact_user_form->add(new Text("Subject: "));
			$contact_user_form->add($subject_input);
			$contact_user_form->add(new Text("Body:"));
			$contact_user_form->add($body_input);
			$contact_user_form->add(new Text(""));

			$actual_form = new FormPanel("index.php");
			$actual_form->setAsGet();
			$actual_form->addHiddenField("view", "event");
			$actual_form->addHiddenField("subview", "attendees");
			$actual_form->addHiddenField("cal_id",  (string)$this->cal_id);
			$actual_form->addHiddenField("event_id",  (string)$this->event_id);
			$actual_form->addHiddenField("contact_attendees", "1");
			$actual_form->add($contact_user_form);

			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(new Style("content_font"));

			$error_style = new Style("content_font");
			$error_style->setFontColor("#CC0000");

			$content->add(new Text("Contact Attendees"), $title_style);
			$content->add(new Text("Use the form below to send a message to all of the attendees of this event. Simply type in the subject and body of the message and click [Send Message].<br><br>"));
			if(strlen($this->avalanche->getUser($this->avalanche->getActiveUser())->email()) == 0){
				$content->add(new Text("You cannot send this message because you do not have an email address set up in your profile. To set up your email, click the sprocket in the top right of the page and click \"My Profile.\"<br><br>"), $error_style);
				$disabled = " DISABLED";
			}else{
				$disabled = "";
			}
			$contact_user_form->add(new Text("<input type='submit' value='Send Message' style='border: 1px solid black' $disabled>"));
			$content->add($actual_form);

			if(isset($this->data_list["message_sent"]) && $this->data_list["message_sent"]){
				$tab_panel->selectTab(3);

				$content->getStyle()->setDisplayNone();
				$message = new GridPanel(1);
				$message->setStyle(new Style("attendee_message"));
				$message->setCellStyle(new Style("content_font"));
				$message->getCellStyle()->setPadding(4);
				$message->setAlign("center");
				$message->add(new Text("Message Sent Successfully"));
				$button = new Button("Ok");
				$button->setStyle(new Style("buttons_light"));
				$button->addAction(new DisplayNoneAction($message));
				$button->addAction(new DisplayBlockAction($content));
				$message->add($button);

				$message_panel = new Panel();
				$message_panel->getStyle()->setPaddingTop(60);
				$message_panel->getStyle()->setPaddingLeft(110);
				$message_panel->add($message);

				$new_content = new Panel();
				$new_content->setWidth("100%");
				$new_content->setAlign("center");
				$new_content->setValign("middle");
				$new_content->add($content);
				$new_content->add($message_panel);

				$content = $new_content;
			}

			$open_button = new Button("Send Message");
			$open_button->setStyle(new Style("event_tab_light"));
			$close_button = new Button("Send Message");
			$close_button->setStyle(new Style("event_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}


		$tab_panel->setHolderStyle(new Style("event_holder"));
		$tab_panel->setContentStyle(new Style("attendee_content"));
		$this->doc->addFunction($tab_panel->getCloseFunction());


		$content = new GridPanel(1);
		$content->setWidth("100%");
		$s = clone($title_style);
		$s->setFontSize(12);
		$s->setPaddingBottom(3);
		$content->add(new Text("Attendees"), $s);
		$content->add($tab_panel);

		return $content;
	}



	private function getReminderPanel($event){
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
		$rs = $reminder->getRemindersFor($event);
		$reminder_text = new GridPanel(3);
		$reminder_text->setCellStyle(new Style("content_font"));
		$reminder_text->getCellStyle()->setPaddingRight(20);

		$error_style = new Style("content_font");
		$error_style->setFontColor("#CC0000");

		$rs_me = array();
		$rs_all = array();
		foreach($rs as $r){
			if($r->type() == module_reminder_reminder::$TYPE_EVENT_ATTENDEES){
				$rs_all[] = $r;
			}else if(in_array((int)$this->avalanche->loggedInHuh(), $r->getUsers())){
				$rs_me[] = $r;
			}
		}

		if(count($rs_me) == 0 && count($rs_all) == 0){
			$reminder_text = new Panel();
			$reminder_text->setStyle(new Style("content_font"));
			$reminder_text->setWidth("100%");
			$reminder_text->add(new Text("<br><i>There are no reminders registered for this event. Click the [Create for Me] tab to create a reminder for yourself.</i>"));
		}

		if(count($rs_me)){
			$reminder_text->add(new Text("<b>Reminders for Me</b>"));
			$reminder_text->add(new Text(""));
			$reminder_text->add(new Text(""));
		}
		foreach($rs_me as $r){
			$added = "";
			$day = ($r->day() ? ($r->day() . " day" . ($r->day() > 1 ? "s":"")): "");
			$hour = (($r->day() && $r->hour())? " and " : "") . ($r->hour() ? ($r->hour() . " hour" . ($r->hour() > 1 ? "s":"")): "");
			$minute = ((($r->day() || $r->hour()) && $r->minute()) ? " and " : "") . ($r->minute() ? ($r->minute() . " minute" . ($r->minute() > 1 ? "s":"")): "");
			if(strlen($day) || strlen($hour) || strlen($minute)){
				$text = $day . $hour . $minute . " before the event";
			}else{
				$text = "When the event starts";
			}
			if(isset($this->data_list["reminder_added"]) && $r->getId() == $this->data_list["reminder_added"]){
				$text = "<b>$text</b>";
				$added = "<b>(just added)</b>";
			}
			$reminder_text->add(new Text((string)$text));
			if($r->sentOn() == "0000-00-00 00:00:00"){
				if($this->avalanche->loggedInHuh() == $event->author() ||
				   $event->calendar()->canWriteEntries() || $r->author() == $this->avalanche->loggedInHuh()){
					$reminder_text->add(new Link("remove", "index.php?view=event&subview=reminders&reminder_id=" . $r->getId() . "&remove_reminder=1&cal_id=" . $this->cal_id  . "&event_id=" . $this->event_id));
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
		if(count($rs_all)){
			$reminder_text->add(new Text("<b>Reminders for All Attendees</b>"));
			$reminder_text->add(new Text(""));
			$reminder_text->add(new Text(""));
		}
		foreach($rs_all as $r){
			$added = "";
			$day = ($r->day() ? ($r->day() . " day" . ($r->day() > 1 ? "s":"")): "");
			$hour = ($r->hour() ? " and " : "") . ($r->hour() ? ($r->hour() . " hour" . ($r->hour() > 1 ? "s":"")): "");
			$minute = ($r->day() || $r->hour() ? " and " : "") . ($r->minute() ? ($r->minute() . " minute" . ($r->minute() > 1 ? "s":"")): "");
			if(strlen($day) || strlen($hour) || strlen($minute)){
				$text = $day . $hour . $minute . " before the event";
			}else{
				$text = "When the event starts";
			}
			if(isset($this->data_list["reminder_added"]) && $r->getId() == $this->data_list["reminder_added"]){
				$text = "<b>$text</b>";
				$added = "<b>(just added)</b>";
			}
			$reminder_text->add(new Text((string)$text));
			if($this->avalanche->loggedInHuh() == $event->author() ||
			   $event->calendar()->canWriteEntries() || $r->author() == $this->avalanche->loggedInHuh()){
				$reminder_text->add(new Link("remove", "index.php?view=event&subview=reminders&reminder_id=" . $r->getId() . "&remove_reminder=1&cal_id=" . $this->cal_id  . "&event_id=" . $this->event_id));
			}else{
				$reminder_text->add(new Text("&nbsp;"));
			}
			$reminder_text->add(new Text($added));
		}
		if(count($rs_me) || count($rs_all)){
			$panel = new GridPanel(1);
			$panel->setWidth("100%");
			$panel->setCellStyle(new Style("content_font"));
			$panel->add(new Text("This is the list of reminders for this event."));
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
		$open_button->setStyle(new Style("event_tab_light"));
		$close_button = new Button("Reminders");
		$close_button->setStyle(new Style("event_tab_dark"));
		$tab_panel->add($content, $open_button, $close_button);


		// remind me tab
		if($event->calendar()->canReadEvent($event->getId()) ||
		   $this->avalanche->loggedInHuh() == $event->author()){
			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(clone $content_style);

			$form = new FormPanel("index.php");
			$form->setAsGet();
			$form->addHiddenField("view", "event");
			$form->addHiddenField("cal_id", (string)$this->cal_id);
			$form->addHiddenField("event_id", (string)$this->event_id);
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
			$inputs->add(new Text(" minutes before this event."));

			$subject = new SmallTextInput();
			$subject->setName("subject");
			$subject->setValue($event->getDisplayValue("title"));
			$subject->setStyle($input_style);
			$subject->setSize(30);

			$body = new TextAreaInput();
			$body->setName("body");

			$date = $event->getDisplayValue("start_date");
			$time = $event->getDisplayValue("start_time");
			$time = mktime(substr($time, 0, 2), substr($time, 3, 2), substr($time, 6, 2), substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

			$text = "This is a reminder for the \"" . $event->calendar()->name() . "\" event \"" . $event->getDisplayValue("title") . "\"";
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
			$content->add(new Text("Never miss an important meeting again. Just set up a reminder to automatically contact you before the event.<br><br>"));
			if(strlen($this->avalanche->getUser($this->avalanche->getActiveUser())->email()) == 0){
				$content->add(new Text("You cannot set up this reminder because you do not have an email address set up in your profile. To set up your email, click the sprocket in the top right of the page and click \"My Profile.\"<br><br>"), $error_style);
				$disabled = " DISABLED";
			}else{
				$disabled = "";
			}
			$form_body->add(new Text("<input type='submit' value='Add Reminder' style='border:1px solid black' $disabled>"));
			$content->add($form);

			$open_button = new Button("Create for Me");
			$open_button->setStyle(new Style("event_tab_light"));
			$close_button = new Button("Create for Me");
			$close_button->setStyle(new Style("event_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}

		// remind everyone tab
		if($event->calendar()->canWriteEntries() ||
		   $this->avalanche->loggedInHuh() == $event->author()){
			$content = new GridPanel(1);
			$content->setWidth("100%");
			$content->setStyle(clone $content_style);

			$form = new FormPanel("index.php");
			$form->setAsGet();
			$form->addHiddenField("view", "event");
			$form->addHiddenField("cal_id", (string)$this->cal_id);
			$form->addHiddenField("event_id", (string)$this->event_id);
			$form->addHiddenField("subview", "reminders");
			$form->addHiddenField("add_reminder", "1");
			$form->addHiddenField("all_attendees", "1");

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
			$inputs->add(new Text(" minutes before this event."));

			$subject = new SmallTextInput();
			$subject->setName("subject");
			$subject->setValue($event->getDisplayValue("title"));
			$subject->setStyle($input_style);
			$subject->setSize(30);

			$body = new TextAreaInput();
			$body->setName("body");

			$date = $event->getDisplayValue("start_date");
			$time = $event->getDisplayValue("start_time");
			$time = mktime(substr($time, 0, 2), substr($time, 3, 2), substr($time, 6, 2), substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));



			$text = "This is a reminder for the \"" . $event->calendar()->name() . "\" event \"" . $event->getDisplayValue("title") . "\"";
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
			$form_body->add(new Text("<input type='submit' value='Add Reminder' style='border:1px solid black'>"));

			$form->add($form_body);

			$content->add(new Text("New Reminder for All Attendees"), $title_style);
			$content->add(new Text("Set up a reminder to contact all attendees before the event.<br><br>"));
			$content->add($form);

			$open_button = new Button("Create for Attendees");
			$open_button->setStyle(new Style("event_tab_light"));
			$close_button = new Button("Create for Attendees");
			$close_button->setStyle(new Style("event_tab_dark"));
			$tab_panel->add($content, $open_button, $close_button);
		}


		$tab_panel->setHolderStyle(new Style("event_holder"));
		$tab_panel->setContentStyle(new Style("attendee_content"));
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