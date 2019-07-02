<?

class module_bootstrap_os_preferences_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche General page to HTML");
		$this->setInfo("outputs a html page describing Avalanche");
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
			$taskman   = $this->avalanche->getModule("taskman");
			$notifier  = $this->avalanche->getModule("notifier");

			if(isset($data_list["subview"]) && (
				 $data_list["subview"] == "general" ||
				 $data_list["subview"] == "calendar" ||
				 $data_list["subview"] == "notifier" ||
				 $data_list["subview"] == "password")){
				$subview = $data_list["subview"];
			}else if(!isset($data_list["subview"])){
				$subview = "general";
			}else{
				throw new IllegalArgumentException("subview argument in form input must be set to general, calendar, or notifier");
			}


			$preferences_updated = false;
			$password_error = false;
			$password_success = false;
			if(isset($data_list["delete_note"]) && $data_list["delete_note"]){
				if(!isset($data_list["note_id"])){
					throw new IllegalArgumentException("argument \"note_id\" must be set in form input to delete a notification");
				}
				$notifier->deleteNotification((int)$data_list["note_id"]);

				header("Location: index.php?view=preferences&subview=$subview");
				exit;
			}
			if(isset($data_list["submit"]) && $data_list["submit"]){
				$preferences_updated = true;

				$add_notification = new SmallTextInput();
				$add_notification->setName("add_notification");
				$add_notification->loadFormValue($data_list);
				if($add_notification->getValue()){
					$note = $notifier->addNotificationFor($this->avalanche->loggedInHuh());

					$item = new SmallTextInput();
					$item->setName("item");
					$item->loadFormValue($data_list);
					$note->item((int)$item->getValue());

					if($note->item() == module_notifier_notification::$ITEM_TASK){
						$action_type = "task_action";
					}else{
						$action_type = "event_action";
					}

					$action = new SmallTextInput();
					$action->setName($action_type);
					$action->loadFormValue($data_list);
					$note->action((int)$action->getValue());

					$contact = new SmallTextInput();
					$contact->setName("contact");
					$contact->loadFormValue($data_list);
					$note->contactBy((int)$contact->getValue());

					$all_calendars = new SmallTextInput();
					$all_calendars->setName("all_calendars");
					$all_calendars->loadFormValue($data_list);
					$note->allCalendarsHuh((bool)$all_calendars->getValue());

					if(isset($data_list["calendars"]) && is_array($data_list["calendars"])){
						foreach($data_list["calendars"] as $cal_id){
							$cal = $strongcal->getCalendarFromDb((int)$cal_id);
							$note->addCalendar($cal);
						}
					}
				}

				if($this->avalanche->hasPermissionHuh($this->avalanche->getActiveUser(), "view_cp")){
					$site_title = new SmallTextInput();
					$site_title->setName("site_title");
					$site_title->loadFormValue($data_list);
					$this->avalanche->setOrganization($site_title->getValue());
				}

				if($this->avalanche->loggedInHuh()){
					$password = new SmallTextInput();
					$password->setName("password");
					$password->loadFormValue($data_list);
					$password = $password->getValue();

					$confirm = new SmallTextInput();
					$confirm->setName("confirm");
					$confirm->loadFormValue($data_list);
					$confirm = $confirm->getValue();

					if(strlen($password) && $password == $confirm){
						// update password
						$this->avalanche->updatePassword($this->avalanche->getActiveUser(), $password);
						$password_success = true;
					}else if(strlen($password)){
						// the password and confirm didn't match
						$password_error = true;
					}
				}

				$start_page = new SmallTextInput();
				$start_page->setName("start_page");
				$start_page->loadFormValue($data_list);
				$this->avalanche->setUserVar($start_page->getName(), $start_page->getValue());

				$start_page = new SmallTextInput();
				$start_page->setName("preferred_contact");
				$start_page->loadFormValue($data_list);
				$this->avalanche->setUserVar($start_page->getName(), $start_page->getValue());

				$gtimezone = new SmallTextInput();
				$gtimezone->setName("gtimezone");
				$gtimezone->loadFormValue($data_list);
				$strongcal->setUserVar("timezone", $gtimezone->getValue(), -1);

				$timezone = new SmallTextInput();
				$timezone->setName("timezone");
				$timezone->loadFormValue($data_list);
				$strongcal->setUserVar($timezone->getName(), $timezone->getValue());

				$day_start = new TimeInput();
				$day_start->setName("day_start");
				$day_start->loadFormValue($data_list);
				$strongcal->setUserVar($day_start->getName(), $day_start->getValue());

				$day_end = new TimeInput();
				$day_end->setName("day_end");
				$day_end->loadFormValue($data_list);
				$strongcal->setUserVar($day_end->getName(), $day_end->getValue());
			}



			/**
			 * let's make the panel's !!!
			 */
			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/

			/************************************************************************
			    initialize panels
			************************************************************************/

			$my_container = new GridPanel(1);


			$general_content = new Panel();
			$general_content->setWidth("100%");
			$calendar_content = new Panel();
			$calendar_content->setWidth("100%");
			$password_content = new Panel();
			$password_content->setWidth("100%");
			$notifier_content = new Panel();
			$notifier_content->setWidth("100%");

			/************************************************************************
			************************************************************************/

			$subview_input = new SmallTextInput();
			$subview_input->setName("subview");
			$subview_input->setValue($subview);
			$subview_input->getStyle()->setDisplayNone();

			$buttons = new RowPanel();
			$buttons->setRowHeight("26");
			$buttons->setStyle(new Style("preferences_buttons_panel"));
			$buttons->setAlign("left");
			$buttons->getStyle()->setWidth("410px");

			$open_general_button = new Button();
			$open_general_button->setStyle(new Style("preferences_tab_open"));
			$open_general_button->setAlign("left");
			$open_general_button->add(new Text("General"));

			$closed_general_button = new Button();
			$closed_general_button->setStyle(new Style("preferences_tab_closed"));
			$closed_general_button->setAlign("left");
			$closed_general_button->add(new Text("General"));

			$open_calendar_button = new Button();
			$open_calendar_button->setStyle(new Style("preferences_tab_open"));
			$open_calendar_button->setAlign("center");
			$open_calendar_button->add(new Text("Preferences"));

			$closed_calendar_button = new Button();
			$closed_calendar_button->setStyle(new Style("preferences_tab_closed"));
			$closed_calendar_button->setAlign("center");
			$closed_calendar_button->add(new Text("Preferences"));

			$open_password_button = new Button();
			$open_password_button->setStyle(new Style("preferences_tab_open"));
			$open_password_button->setAlign("center");
			$open_password_button->add(new Text("Password"));

			$closed_password_button = new Button();
			$closed_password_button->setStyle(new Style("preferences_tab_closed"));
			$closed_password_button->setAlign("center");
			$closed_password_button->add(new Text("Password"));

			$open_notifier_button = new Button();
			$open_notifier_button->setStyle(new Style("preferences_tab_open"));
			$open_notifier_button->setAlign("center");
			$open_notifier_button->add(new Text("Notifications"));

			$closed_notifier_button = new Button();
			$closed_notifier_button->setStyle(new Style("preferences_tab_closed"));
			$closed_notifier_button->setAlign("center");
			$closed_notifier_button->add(new Text("Notifications"));

			$open_general_button->getStyle()->setDisplayNone();
			$closed_general_button->getStyle()->setDisplayBlock();
			$open_calendar_button->getStyle()->setDisplayNone();
			$closed_calendar_button->getStyle()->setDisplayBlock();
			$open_password_button->getStyle()->setDisplayNone();
			$closed_password_button->getStyle()->setDisplayBlock();
			$open_notifier_button->getStyle()->setDisplayNone();
			$closed_notifier_button->getStyle()->setDisplayBlock();

			$general_content->getStyle()->setDisplayNone();
			$calendar_content->getStyle()->setDisplayNone();
			$notifier_content->getStyle()->setDisplayNone();
			$password_content->getStyle()->setDisplayNone();

			// set button visibility
			if($subview == "general"){
				$open_general_button->getStyle()->setDisplayBlock();
				$closed_general_button->getStyle()->setDisplayNone();
				$general_content->getStyle()->setDisplayBlock();
			}else if($subview == "calendar"){
				$open_calendar_button->getStyle()->setDisplayBlock();
				$closed_calendar_button->getStyle()->setDisplayNone();
				$calendar_content->getStyle()->setDisplayBlock();
			}else if($subview == "notifier"){
				$open_notifier_button->getStyle()->setDisplayBlock();
				$closed_notifier_button->getStyle()->setDisplayNone();
				$notifier_content->getStyle()->setDisplayBlock();
			}else if($subview == "password"){
				$open_password_button->getStyle()->setDisplayBlock();
				$closed_password_button->getStyle()->setDisplayNone();
				$password_content->getStyle()->setDisplayBlock();
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
			$password = new Panel();
			$password->add($open_password_button);
			$password->add($closed_password_button);
			$buttons->add($general);
			$buttons->add($calendar);
			$buttons->add($notifier);
			$buttons->add($password);

			// create visibility functions and set up actions
			$closefunction = new NewFunctionAction("close_general_tabs");
			$closefunction->addAction(new DisplayNoneAction($open_general_button));
			$closefunction->addAction(new DisplayNoneAction($general_content));
			$closefunction->addAction(new DisplayBlockAction($closed_general_button));
			$closefunction->addAction(new DisplayNoneAction($open_calendar_button));
			$closefunction->addAction(new DisplayNoneAction($calendar_content));
			$closefunction->addAction(new DisplayBlockAction($closed_calendar_button));
			$closefunction->addAction(new DisplayNoneAction($open_notifier_button));
			$closefunction->addAction(new DisplayNoneAction($notifier_content));
			$closefunction->addAction(new DisplayBlockAction($closed_notifier_button));
			$closefunction->addAction(new DisplayNoneAction($open_password_button));
			$closefunction->addAction(new DisplayNoneAction($password_content));
			$closefunction->addAction(new DisplayBlockAction($closed_password_button));
			$this->doc->addFunction($closefunction);

			$closed_general_button->addAction(new CallFunctionAction("close_general_tabs"));
			$closed_general_button->addAction(new DisplayNoneAction($closed_general_button));
			$closed_general_button->addAction(new DisplayBlockAction($open_general_button));
			$closed_general_button->addAction(new DisplayBlockAction($general_content));
			$closed_general_button->addAction(new SetValueAction($subview_input, "general"));

			$closed_calendar_button->addAction(new CallFunctionAction("close_general_tabs"));
			$closed_calendar_button->addAction(new DisplayNoneAction($closed_calendar_button));
			$closed_calendar_button->addAction(new DisplayBlockAction($open_calendar_button));
			$closed_calendar_button->addAction(new DisplayBlockAction($calendar_content));
			$closed_calendar_button->addAction(new SetValueAction($subview_input, "calendar"));

			$closed_notifier_button->addAction(new CallFunctionAction("close_general_tabs"));
			$closed_notifier_button->addAction(new DisplayNoneAction($closed_notifier_button));
			$closed_notifier_button->addAction(new DisplayBlockAction($open_notifier_button));
			$closed_notifier_button->addAction(new DisplayBlockAction($notifier_content));
			$closed_notifier_button->addAction(new SetValueAction($subview_input, "notifier"));

			$closed_password_button->addAction(new CallFunctionAction("close_general_tabs"));
			$closed_password_button->addAction(new DisplayNoneAction($closed_password_button));
			$closed_password_button->addAction(new DisplayBlockAction($open_password_button));
			$closed_password_button->addAction(new DisplayBlockAction($password_content));
			$closed_password_button->addAction(new SetValueAction($subview_input, "password"));


			// content pages
			$content_header_style = new Style();
			$content_header_style->setPadding(4);
			$content_header_style->setFontFamily("verdana, sans-serif");
			$content_header_style->setFontWeight("bold");
			$content_header_style->setFontSize(11);

			$content = new ScrollPanel();
			$content->setStyle(new Style("preferences_content"));
			$content->getStyle()->setWidth("350px");
			$content->getStyle()->setHeight("370px");

			$general = new GridPanel(1);
			$general->setWidth("100%");
			$general->getStyle()->setFontFamily("verdana, sans-serif");
			$general->getStyle()->setFontSize(10);
			$general->getStyle()->setFontColor("black");
			$general->add(new Text("General Preferences"), $content_header_style);
			$general->add($this->getGeneral($data_list));

			$calendar = new GridPanel(1);
			$calendar->setWidth("100%");
			$calendar->getCellStyle()->setFontFamily("verdana, sans-serif");
			$calendar->getCellStyle()->setFontSize(12);
			$calendar->getCellStyle()->setFontColor("black");
			$calendar->add(new Text("Calendar Preferences"), $content_header_style);
			$calendar->add($this->getCalendar($data_list));

			$notifier = new GridPanel(1);
			$notifier->setWidth("100%");
			$notifier->getCellStyle()->setFontFamily("verdana, sans-serif");
			$notifier->getCellStyle()->setFontSize(12);
			$notifier->getCellStyle()->setFontColor("black");
			$notifier->add(new Text("Notifications"), $content_header_style);
			$notifier->add($this->getNotifier($data_list));

			$password = new GridPanel(1);
			$password->setWidth("100%");
			$password->getCellStyle()->setFontFamily("verdana, sans-serif");
			$password->getCellStyle()->setFontSize(12);
			$password->getCellStyle()->setFontColor("black");
			$password->add(new Text("Password"), $content_header_style);
			$password->add($this->getPassword($data_list));

			$general_content->add($general);
			$calendar_content->add($calendar);
			$notifier_content->add($notifier);
			$password_content->add($password);

			$content->add($general_content);
			$content->add($calendar_content);
			$content->add($notifier_content);
			$content->add($password_content);

			$button = new Text("<input type='submit' value='Apply Changes' style='border: 1px solid black;'>");
			$submit = new Panel();
			$submit->add($button);
			$submit->setValign("bottom");
			$submit->getStyle()->setHeight("30px");

			$info = new GridPanel(1);
			$info->setAlign("center");
			$info->getStyle()->setHeight("400px");
			$info->getStyle()->setWidth("400px");
			if($preferences_updated){
				$saved = new Text("Your settings have been saved");
				$saved_style = new Style("footer_text");
				$saved_style->setFontColor("#CC0000");
				$saved_style->setPaddingBottom(6);
				$info->add($saved, $saved_style);
			}
			if($password_error){
				$saved = new Text("Your password was NOT updated.");
				$saved_style = new Style("footer_text");
				$saved_style->setFontColor("#CC0000");
				$saved_style->setPaddingBottom(6);
				$info->add($saved, $saved_style);
			}else if($password_success){
				$saved = new Text("Your password was updated.");
				$saved_style = new Style("footer_text");
				$saved_style->setFontColor("#CC0000");
				$saved_style->setPaddingBottom(6);
				$info->add($saved, $saved_style);
			}
			$info->add($content);
			$info->add($submit);

			$form = new FormPanel("index.php");
			$form->setAsPost();
			$form->addHiddenField("view", "preferences");
			$form->addHiddenField("submit", "1");
			$form->add($info);
			$form->add($subview_input);
			$form->getStyle()->setHeight("400px");
			$form->getStyle()->setWidth("400px");

			//$info = new ErrorPanel($form);
			$form->setStyle(new Style("preferences_footer"));

			/************************************************************************
			put it all together
			************************************************************************/
			$my_container->add($buttons);
			$my_container->add($form);

			$my_container->getStyle()->setMarginLeft(20);


			$title = "Settings";
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add(new Text($title));
			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			$grid->add($my_container);
			return new module_bootstrap_data($grid, "a gui component for the manage teams view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}

	private function getCalendar($data_list){
		$strongcal = $this->avalanche->getModule("strongcal");

		// timezone
		$timezone_text = new Text("Timezone ");
		$timezone_text->setStyle(new Style("preferences_text"));
		$timezone_text->getStyle()->setFontSize(10);

		$timezones = $this->getTimezoneArray();
		$timezone_field = new DropDownInput();
		$timezone_field->setName("timezone");
		foreach($timezones as $timezone){
			$timezone_field->addOption(new DropDownOption($timezone["full_name"], (string)$timezone["offset"]));
		}
		$timezone_field->setValue((string)$strongcal->getUserVar("timezone"));

		$timezone_about = new Panel();
		$timezone_about->setWidth("100%");
		$timezone_about->add(new Text("Select your local time zone."));
		$timezone_about->setStyle(new Style("preferences_text"));
		$timezone_about->getStyle()->setFontSize(8);
		$timezone_about->getStyle()->setMarginLeft(10);

		$timezone = new BorderPanel();
		$timezone->getStyle()->setPadding(4);
		$timezone->setAlign("left");
		$timezone->setWidth("100%");
		$timezone->setCenter($timezone_text);
		$timezone->setEast($timezone_field);
		$timezone->setSouth($timezone_about);


		// day start
		$day_start_text = new Text("Beginning of Your Day");
		$day_start_text->setStyle(new Style("preferences_text"));
		$day_start_text->getStyle()->setFontSize(10);

		$day_start_field = new TimeInput();
		$day_start_field->setName("day_start");
		$day_start_field->setValue((string)$strongcal->getUserVar("day_start"));

		$day_start_about = new Panel();
		$day_start_about->setWidth("100%");
		$day_start_about->add(new Text("Select the time when you start work."));
		$day_start_about->setStyle(new Style("preferences_text"));
		$day_start_about->getStyle()->setFontSize(8);
		$day_start_about->getStyle()->setMarginLeft(10);

		$day_start = new BorderPanel();
		$day_start->getStyle()->setPadding(4);
		$day_start->setAlign("left");
		$day_start->setWidth("100%");
		$day_start->setCenter($day_start_text);
		$day_start->setEast($day_start_field);
		$day_start->setSouth($day_start_about);


		// day end
		$day_end_text = new Text("End of Your Day");
		$day_end_text->setStyle(new Style("preferences_text"));
		$day_end_text->getStyle()->setFontSize(10);

		$day_end_field = new TimeInput();
		$day_end_field->setName("day_end");
		$day_end_field->setValue((string)$strongcal->getUserVar("day_end"));

		$day_end_about = new Panel();
		$day_end_about->setWidth("100%");
		$day_end_about->add(new Text("Select the time when you start work."));
		$day_end_about->setStyle(new Style("preferences_text"));
		$day_end_about->getStyle()->setFontSize(8);
		$day_end_about->getStyle()->setMarginLeft(10);

		$day_end = new BorderPanel();
		$day_end->getStyle()->setPadding(4);
		$day_end->setAlign("left");
		$day_end->setWidth("100%");
		$day_end->setCenter($day_end_text);
		$day_end->setEast($day_end_field);
		$day_end->setSouth($day_end_about);


		$calendar = new GridPanel(1);
		$calendar->setWidth("100%");
		$calendar->add($timezone);
		$calendar->add($day_start);
		$calendar->add($day_end);
		return $calendar;
	}

	private function getPassword($data_list){
		$strongcal = $this->avalanche->getModule("strongcal");

		// timezone
		$timezone_about = new Panel();
		$timezone_about->setWidth("100%");
		$timezone_about->add(new Text("To reset your password, please type and confirm your new password. Otherwise, leave these fields blank. Remember, passwords <i>are</i> case-sensitive."));
		$timezone_about->setStyle(new Style("preferences_text"));
		$timezone_about->getStyle()->setFontSize(8);
		$timezone_about->getStyle()->setMarginLeft(10);

		$timezone = new BorderPanel();
		$timezone->getStyle()->setPadding(4);
		$timezone->setAlign("left");
		$timezone->setWidth("100%");
		$timezone->setSouth($timezone_about);


		// day start
		$password_text = new Text("New Password");
		$password_text->setStyle(new Style("preferences_text"));
		$password_text->getStyle()->setFontSize(10);

		$password_field = new SmallTextInput();
		$password_field->setPassword(true);
		$password_field->setName("password");
		$password_field->getStyle()->setBorderWidth(1);
		$password_field->getStyle()->setBorderStyle("solid");
		$password_field->getStyle()->setBorderColor("black");

		$password_about = new Panel();
		$password_about->setWidth("100%");
		$password_about->add(new Text("Please type in your new password."));
		$password_about->setStyle(new Style("preferences_text"));
		$password_about->getStyle()->setFontSize(8);
		$password_about->getStyle()->setMarginLeft(10);

		$password = new BorderPanel();
		$password->getStyle()->setPadding(4);
		$password->setAlign("left");
		$password->setWidth("100%");
		$password->setCenter($password_text);
		$password->setEast($password_field);
		$password->setSouth($password_about);


		// day end
		$confirm_text = new Text("Confirm");
		$confirm_text->setStyle(new Style("preferences_text"));
		$confirm_text->getStyle()->setFontSize(10);

		$confirm_field = new SmallTextInput();
		$confirm_field->setPassword(true);
		$confirm_field->setName("confirm");
		$confirm_field->getStyle()->setBorderWidth(1);
		$confirm_field->getStyle()->setBorderStyle("solid");
		$confirm_field->getStyle()->setBorderColor("black");

		$confirm_about = new Panel();
		$confirm_about->setWidth("100%");
		$confirm_about->add(new Text("Please confirm your new password."));
		$confirm_about->setStyle(new Style("preferences_text"));
		$confirm_about->getStyle()->setFontSize(8);
		$confirm_about->getStyle()->setMarginLeft(10);

		$confirm = new BorderPanel();
		$confirm->getStyle()->setPadding(4);
		$confirm->setAlign("left");
		$confirm->setWidth("100%");
		$confirm->setCenter($confirm_text);
		$confirm->setEast($confirm_field);
		$confirm->setSouth($confirm_about);


		$calendar = new GridPanel(1);
		$calendar->setWidth("100%");
		$calendar->add($timezone);
		$calendar->add($password);
		$calendar->add($confirm);
		return $calendar;
	}

	private function getGeneral($data_list){
		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");

		$start_page_text = new Text("Start Page after Log In ");
		$start_page_text->setStyle(new Style("preferences_text"));
		$start_page_text->getStyle()->setFontSize(10);

		$start_page_field = new DropDownInput();
		$start_page_field->setName("start_page");
		$start_page_field->addOption(new DropDownOption("Last Visited", "last"));
		$start_page_field->addOption(new DropDownOption("Overview", "overview"));
		$start_page_field->addOption(new DropDownOption("This Month", "month"));
		$start_page_field->addOption(new DropDownOption("This Week", "week"));
		$start_page_field->addOption(new DropDownOption("Today", "today"));
		$start_page_field->setValue((string)$this->avalanche->getUserVar("start_page"));

		$start_page_about = new Panel();
		$start_page_about->setWidth("100%");
		$start_page_about->add(new Text("Choose the page that you want to be redirected to after you log in to Aurora."));
		$start_page_about->setStyle(new Style("preferences_text"));
		$start_page_about->getStyle()->setFontSize(8);
		$start_page_about->getStyle()->setMarginLeft(10);

		$start_page = new BorderPanel();
		$start_page->getStyle()->setPadding(4);
		$start_page->setAlign("left");
		$start_page->setWidth("100%");
		$start_page->setCenter($start_page_text);
		$start_page->setEast($start_page_field);
		$start_page->setSouth($start_page_about);

		$preferred_contact_text = new Text("Preferred to be Contacted by:");
		$preferred_contact_text->setStyle(new Style("preferences_text"));
		$preferred_contact_text->getStyle()->setFontSize(10);

		$preferred_contact_field = new DropDownInput();
		$preferred_contact_field->setName("preferred_contact");
		$preferred_contact_field->addOption(new DropDownOption("Email", (string)avalanche_user::$CONTACT_EMAIL));
		$preferred_contact_field->addOption(new DropDownOption("SMS", (string)avalanche_user::$CONTACT_SMS));
		$preferred_contact_field->addOption(new DropDownOption("Email and SMS", (string)(avalanche_user::$CONTACT_EMAIL | avalanche_user::$CONTACT_SMS)));
		$preferred_contact_field->setValue((string)$this->avalanche->getUserVar("preferred_contact"));

		$preferred_contact_about = new Panel();
		$preferred_contact_about->setWidth("100%");
		$preferred_contact_about->add(new Text("Choose how you want to be sent reminders and notices about your schedule."));
		$preferred_contact_about->setStyle(new Style("preferences_text"));
		$preferred_contact_about->getStyle()->setFontSize(8);
		$preferred_contact_about->getStyle()->setMarginLeft(10);

		$preferred_contact = new BorderPanel();
		$preferred_contact->getStyle()->setPadding(4);
		$preferred_contact->setAlign("left");
		$preferred_contact->setWidth("100%");
		$preferred_contact->setCenter($preferred_contact_text);
		$preferred_contact->setEast($preferred_contact_field);
		$preferred_contact->setSouth($preferred_contact_about);

		$site_title_text = new Text("Site Title:");
		$site_title_text->setStyle(new Style("preferences_text"));
		$site_title_text->getStyle()->setFontSize(10);

		$site_title_field = new SmallTextInput();
		$site_title_field->setSize(30);
		$site_title_field->getStyle()->setBorderWidth(1);
		$site_title_field->getStyle()->setBorderStyle("solid");
		$site_title_field->getStyle()->setBorderColor("black");
		$site_title_field->setName("site_title");
		$site_title_field->setValue((string)$this->avalanche->getVar("ORGANIZATION"));

		$site_title_about = new Panel();
		$site_title_about->setWidth("100%");
		$site_title_about->add(new Text("Choose the title you want to appear on the top left of every page. (This applies to all users)."));
		$site_title_about->setStyle(new Style("preferences_text"));
		$site_title_about->getStyle()->setFontSize(8);
		$site_title_about->getStyle()->setMarginLeft(10);

		$site_title = new BorderPanel();
		$site_title->getStyle()->setPadding(4);
		$site_title->setAlign("left");
		$site_title->setWidth("100%");
		$site_title->setCenter($site_title_text);
		$site_title->setEast($site_title_field);
		$site_title->setSouth($site_title_about);

		// timezone
		$timezone_text = new Text("Timezone ");
		$timezone_text->setStyle(new Style("preferences_text"));
		$timezone_text->getStyle()->setFontSize(10);

		$timezones = $this->getTimezoneArray();
		$timezone_field = new DropDownInput();
		$timezone_field->setName("gtimezone");
		foreach($timezones as $timezone){
			$timezone_field->addOption(new DropDownOption($timezone["full_name"], (string)$timezone["offset"]));
		}
		$timezone_field->setValue((string)$strongcal->getUserVar("timezone",-1));

		$timezone_about = new Panel();
		$timezone_about->setWidth("100%");
		$timezone_about->add(new Text("Select the default timezone for your guests."));
		$timezone_about->setStyle(new Style("preferences_text"));
		$timezone_about->getStyle()->setFontSize(8);
		$timezone_about->getStyle()->setMarginLeft(10);

		$timezone = new BorderPanel();
		$timezone->getStyle()->setPadding(4);
		$timezone->setAlign("left");
		$timezone->setWidth("100%");
		$timezone->setCenter($timezone_text);
		$timezone->setEast($timezone_field);
		$timezone->setSouth($timezone_about);



		$general = new GridPanel(1);
		$general->setWidth("100%");
		$general->add($start_page);
		$general->add($preferred_contact);
		if($this->avalanche->hasPermissionHuh($this->avalanche->getActiveUser(), "view_cp")){
			$general->add($site_title);
			$general->add($timezone);
		}
		return $general;
	}


	private function getTextForItem($note){
		if($note->item() == module_notifier_notification::$ITEM_EVENT){
			return "Event";
		}else if($note->item() == module_notifier_notification::$ITEM_TASK){
			return "Task";
		}else if($note->item() == module_notifier_notification::$ITEM_COMMENT){
			return "Comment";
		}
	}

	private function getTextForAction($note){
		if($note->action() == module_notifier_notification::$ACTION_ADDED){
			return "Added";
		}else if($note->action() == module_notifier_notification::$ACTION_EDITED){
			return "Edited";
		}else if($note->action() == module_notifier_notification::$ACTION_DELETED){
			return "Deleted";
		}else if($note->action() == module_notifier_notification::$ACTION_STATUS){
			return "Changed Status";
		}else if($note->action() == module_notifier_notification::$ACTION_DELEGATED){
			return "Delegated to Me";
		}else if($note->action() == module_notifier_notification::$ACTION_COMPLETED){
			return "Completed";
		}else if($note->action() == module_notifier_notification::$ACTION_CANCELLED){
			return "Cancelled";
		}
	}

	private function getTextForCalendars($note){
		if($note->allCalendarsHuh()){
			return "Any Calendar";
		}else{
			$cals = $note->getCalendars();
			if(count($cals) > 0){
				$text = "";
				while(count($cals)){
					if(count($cals) == 1){
						$text .= $cals[0]->name();
						array_splice($cals, 0, 1);
					}else if(count($cals) == 2){
						$text .= $cals[0]->name() . " or " . $cals[1]->name();
						array_splice($cals, 0, 2);
					}else if(count($cals) > 2){
						$text .= $cals[0]->name() . ", ";
						array_splice($cals, 0, 1);
					}
				}
				return $text;
			}else{
				return "No Calendars";
			}
		}
	}

	private function getTextForContact($note){
		if($note->contactBy() == module_notifier_notification::$CONTACT_EMAIL){
			return "Email";
		}else if($note->contactBy() == module_notifier_notification::$CONTACT_SMS){
			return "SMS";
		}else if($note->contactBy() == module_notifier_notification::$CONTACT_EMAIL_SMS){
			return htmlspecialchars("Email & SMS");
		}else if($note->contactBy() == module_notifier_notification::$CONTACT_MESSAGE){
			return "Message";
		}else if($note->contactBy() == module_notifier_notification::$CONTACT_NONE){
			return "Don't Contact";
		}else{
			return "[Undefined Contact Method]";
		}
	}
	private function getNotifier($data_list){
		$strongcal = $this->avalanche->getModule("strongcal");
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$notifier = $this->avalanche->getModule("notifier");

		$main_panel = new GridPanel(1);
		$main_panel->setWidth("100%");
		if($this->avalanche->loggedInHuh()){
			$title_style = new Style();
			$title_style->setFontFamily("verdana, sans-serif");
			$title_style->setFontColor("black");
			$title_style->setFontSize(9);
			$title_style->setPadding(3);

			$input_style = new Style();
			$input_style->setFontFamily("verdana, sans-serif");
			$input_style->setFontColor("black");
			$input_style->setFontSize(7);

			// list of notifications panel
			$add_note_input = new SmallTextInput();
			$add_note_input->setName("add_notification");
			$add_note_input->setValue("0");
			$add_note_input->getStyle()->setDisplayNone();

			$notes_style1 = new Style();
			$notes_style1->setFontSize(8);
			$notes_style1->setFontFamily("verdana, sans-serif");
			$notes_style1->setFontColor("black");
			$notes_style2 = clone $notes_style1;
			$notes_style2->setBackground("#FFFFFF");

			$notes_panel = new GridPanel(1);
			$notes_panel->setWidth("100%");
			$notes_panel->setStyle(new Style("notes_list"));

			$notes = $notifier->getNotificationsFor($this->avalanche->loggedInHuh());
			$button_style = new Style("button");
			$cell_style = new Style();
			$cell_style->setPaddingRight(3);
			$cell_style->setPaddingTop(1);
			$cell_style->setPaddingBottom(2);
			$toggle = true;
			foreach($notes as $note){
				$text = "<b>" . $this->getTextForContact($note) . "</b> me when a(n) <b>" . $this->getTextForItem($note) . "</b> is <b>" . $this->getTextForAction($note) . "</b> in:<br> <b>" . $this->getTextForCalendars($note) . "</b>";
				$note_panel = new GridPanel(1);
				$note_panel->setWidth("100%");

				$delete_link = new Button("delete");
				$delete_link->setStyle($button_style);
				$cancel_link = new Button("cancel");
				$cancel_link->setStyle($button_style);
				$links = new GridPanel(2);
				$links->setCellStyle($cell_style);
				$links->getStyle()->setDisplayNone();
				$links->add($cancel_link);
				$links->add($delete_link);
				$cancel_link->addAction(new DisplayNoneAction($links));
				$delete_link->addAction(new LoadPageAction("index.php?view=preferences&subview=notifier&delete_note=1&note_id=" . $note->getId()));
				$text = new Link($text, "javascript:;");
				$text->getStyle()->setFontColor("#333333");
				$text->addAction(new DisplayBlockAction($links));
				$note_panel->add($text, $toggle ? $notes_style1 : $notes_style2);
				$note_panel->add($links, $toggle ? $notes_style1 : $notes_style2);

				$notes_panel->add($note_panel);
				$toggle = !$toggle;
			}

			$list_notes_panel = new Panel();
			$list_notes_panel->setWidth("100%");
			$list_notes_panel->setAlign("center");
			$list_notes_panel->add($notes_panel);

			// add notification panel
			$add_note_panel = new GridPanel(1);
			$add_note_panel->setWidth("100%");
			$add_note_panel->getStyle()->setDisplayNone();
			$add_note_panel->getCellStyle()->setPaddingLeft(15);
			$add_note_panel->getCellStyle()->setPaddingTop(2);
			$add_note_panel->getCellStyle()->setPaddingBottom(2);

			$contact_select = new DropDownInput();
			$contact_select->setName("contact");
			$contact_select->setStyle($input_style);
			$contact_select->addOption(new DropDownOption("Email", (string) module_notifier_notification::$CONTACT_EMAIL));
			$contact_select->addOption(new DropDownOption("SMS", (string) module_notifier_notification::$CONTACT_SMS));
			$contact_select->addOption(new DropDownOption(htmlspecialchars("Email & SMS"), (string)module_notifier_notification::$CONTACT_EMAIL_SMS));

			$item_select = new DropDownInput();
			$item_select->setName("item");
			$item_select->setStyle($input_style);
			$item_select->addOption(new DropDownOption("Event", (string) module_notifier_notification::$ITEM_EVENT));
			$item_select->addOption(new DropDownOption("Task", (string) module_notifier_notification::$ITEM_TASK));
//			$item_select->addOption(new DropDownOption("Comment", (string)module_notifier_notification::$ITEM_COMMENT));

			$eaction_select = new DropDownInput();
			$eaction_select->setName("event_action");
			$eaction_select->setStyle($input_style);
			$eaction_select->addOption(new DropDownOption("Added", (string) module_notifier_notification::$ACTION_ADDED));
			$eaction_select->addOption(new DropDownOption("Edited", (string) module_notifier_notification::$ACTION_EDITED));
			$eaction_select->addOption(new DropDownOption("Deleted", (string)module_notifier_notification::$ACTION_DELETED));

			$taction_select = new DropDownInput();
			$taction_select->setName("task_action");
			$taction_select->setStyle(clone $input_style);
			$taction_select->getStyle()->setDisplayNone();
			$taction_select->addOption(new DropDownOption("Added", (string) module_notifier_notification::$ACTION_ADDED));
			$taction_select->addOption(new DropDownOption("Edited", (string) module_notifier_notification::$ACTION_EDITED));
			$taction_select->addOption(new DropDownOption("Deleted", (string)module_notifier_notification::$ACTION_DELETED));
			$taction_select->addOption(new DropDownOption("Changed Status", (string)module_notifier_notification::$ACTION_STATUS));
			$taction_select->addOption(new DropDownOption("Completed", (string)module_notifier_notification::$ACTION_COMPLETED));
			$taction_select->addOption(new DropDownOption("Delegated to Me", (string)module_notifier_notification::$ACTION_DELEGATED));
			$taction_select->addOption(new DropDownOption("Cancelled", (string)module_notifier_notification::$ACTION_CANCELLED));

			$a = new DropDownAction($item_select);
			$a->addAction((string) module_notifier_notification::$ITEM_EVENT, new DisplayBlockAction($eaction_select));
			$a->addAction((string) module_notifier_notification::$ITEM_EVENT, new DisplayNoneAction($taction_select));
			$a->addAction((string) module_notifier_notification::$ITEM_TASK, new DisplayBlockAction($taction_select));
			$a->addAction((string) module_notifier_notification::$ITEM_TASK, new DisplayNoneAction($eaction_select));
			$item_select->addChangeAction($a);

			$action_select = new Panel();
			$action_select->add($eaction_select);
			$action_select->add($taction_select);

			$calendar_choices = new ScrollPanel();
			$calendar_choices->getStyle()->setWidth("150");
			$calendar_choices->getStyle()->setBorderWidth(1);
			$calendar_choices->getStyle()->setBorderColor("black");
			$calendar_choices->getStyle()->setBorderStyle("solid");
			$calendar_choices->getStyle()->setBackground("white");
			$max_height = 75;

			$data = false;
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendars = $data->data();
			$height = count($calendars) * 15 + 15;
			if($height > $max_height) $height = $max_height;
			$calendar_choices->getStyle()->setHeight($height);

			$calendar_grid = new GridPanel(1);
			$cal_row = new GridPanel(3);
			$all_check = new CheckInput();
			$all_check->setName("all_calendars");
			$all_check->setChecked(true);
			$all_check->setValue("1");
			$cal_row->getStyle()->setFontSize(8);
			$cal_row->add($all_check);
			$cal_row->add(new Text(""));
			$cal_row->add(new Text("[Any Calendar]"));
			$calendar_grid->add($cal_row);
			foreach($calendars as $cal){
				$icon = new Panel();
				$icon->setStyle(new Style("aurora_view_icon"));
				$icon->getStyle()->setBackground($cal->color());

				$check = new CheckInput();
				$check->setDisabled(true);
				$check->setName("calendars[]");
				$check->setValue((string)$cal->getId());
				$cal_row = new GridPanel(3);
				$cal_row->getStyle()->setFontSize(8);
				$cal_row->add($check);
				$cal_row->add($icon);
				$cal_row->add(new Text($cal->name()));
				$calendar_grid->add($cal_row);

				$all_check->addChangeAction(new ToggleEnabledAction($check));
			}
			$calendar_choices->add($calendar_grid);

			$top_row = new Panel();
			$top_row->getStyle()->setFontSize(8);
			$top_row->setWidth("100%");
			$top_row->add($contact_select);
			$top_row->add(new Text(" me when a(n) "));
			$top_row->add($item_select);

			$mid_row = new GridPanel(4);
			$mid_row->setValign("top");
			$mid_row->getCellStyle()->setPaddingRight(4);
			$mid_row->getStyle()->setFontSize(8);
			$mid_row->add(new Text(" is "));
			$mid_row->add($action_select);
			$mid_row->add(new Text(" in: "));
			$mid_row->add($calendar_choices);
			$mid_row->add($add_note_input);

			$cancel_button = new Button("cancel");
			$cancel_button->getStyle()->setFontSize(8);
			$cancel_button->getStyle()->setBackground("#CCCCCC");

			$text = new Text("[click apply changes below to add notification]");
			$text->getStyle()->setFontSize(8);


			$buttons = new GridPanel(1);
			$buttons->getCellStyle()->setPaddingBottom(4);
			$buttons->add($text);
			$buttons->add($cancel_button);

			$add_note_panel->add($top_row);
			$add_note_panel->add($mid_row);
			$add_note_panel->add($buttons);

			// bring it all together
			$normal_add = new Text("Add Notification");
			$normal_add->getStyle()->setDisplayNone();
			$link_add = new Link("<u>Add Notification</u>", "javascript:;");
			$link_add->getStyle()->setFontColor("black");
			$link_add->addAction(new DisplayBlockAction($add_note_panel));
			$link_add->addAction(new DisplayBlockAction($normal_add));
			$link_add->addAction(new DisplayNoneAction($link_add));
			$link_add->addAction(new SetValueAction($add_note_input, "1"));
			$cancel_button->addAction(new DisplayNoneAction($add_note_panel));
			$cancel_button->addAction(new DisplayNoneAction($normal_add));
			$cancel_button->addAction(new DisplayBlockAction($link_add));
			$cancel_button->addAction(new SetValueAction($add_note_input, "0"));
			$links = new Panel();
			$links->getStyle()->setFontSize(9);
			$links->add($normal_add);
			$links->add($link_add);
			$main_panel->add($links, $title_style);
			$main_panel->add($add_note_panel);
			$main_panel->add(new Text("Notifications"), $title_style);
			$main_panel->add($list_notes_panel);
		}else{
			$main_panel->add(new Text("you must be logged in to set notifications"));
		}


		$general = new GridPanel(1);
		$general->setWidth("100%");
		$general->add($main_panel);
		return $general;
	}


	/**
	 * returns array with items of form
	 * array{ "offset" => hour offset from GMT,
	 *	  "small_name" => small acronym for timezone, ie CST,
	 *	  "full_name" => full name of timezone,
	 *	  "location" => continent where applicable,
	 *	  )
	 *
	 */
	static public function getTimezoneArray(){
		// timezones found at http://www.timeanddate.com/library/abbreviations/timezones/na/
		$timezones = array();

		$timezones[] = array("offset" => -3.5,
				     "small_name" => "NST",
				     "full_name" => "Newfoundland Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -4,
				     "small_name" => "AST",
				     "full_name" => "Atlantic Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -5,
				     "small_name" => "EST",
				     "full_name" => "Eastern Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -6,
				     "small_name" => "CST",
				     "full_name" => "Central Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -7,
				     "small_name" => "MST",
				     "full_name" => "Mountain Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -8,
				     "small_name" => "PST",
				     "full_name" => "Pacific Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -9,
				     "small_name" => "AKST",
				     "full_name" => "Alaska Standard Time",
				     "location" => "North America");

		$timezones[] = array("offset" => -10,
				     "small_name" => "HAST",
				     "full_name" => "Hawaii-Aleutian Standard Time",
				     "location" => "North America");

		return $timezones;
	}
}
?>
