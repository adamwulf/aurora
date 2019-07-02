<?

class module_bootstrap_os_firsttime_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche About page to HTML");
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

			
			// if they submitted, then let's figure everything out
			if(isset($data_list["submit"])){
				$user_id = $this->avalanche->getActiveUser();
				
				$reader = new SmallTextInput();
				$reader->setName("title");
				$reader->loadFormValue($data_list);
				$name["title"] = $reader->getValue();
				$reader->setName("first");
				$reader->loadFormValue($data_list);
				$name["first"] = $reader->getValue();
				$reader->setName("middle");
				$reader->loadFormValue($data_list);
				$name["middle"] = $reader->getValue();
				$reader->setName("last");
				$reader->loadFormValue($data_list);
				$name["last"] = $reader->getValue();
				$reader->setName("email");
				$reader->loadFormValue($data_list);
				$mail = $reader->getValue();
				$reader->setName("sms");
				$reader->loadFormValue($data_list);
				$sms = $reader->getValue();
				$reader->setName("bio");
				$reader->loadFormValue($data_list);
				$bio = $reader->getValue();
				// update the name
				$this->avalanche->updateName($user_id, $name);
				// update email
				$this->avalanche->updateEmail($user_id, $mail);
				// update the sms
				$this->avalanche->getUser($user_id)->sms($sms);
				// update the bio
				$this->avalanche->getUser($user_id)->bio($bio);
				
				$reader->setName("password");
				$reader->loadFormValue($data_list);
				$password = $reader->getValue();
				$reader->setName("confirm");
				$reader->loadFormValue($data_list);
				$confirm = $reader->getValue();
				
				if($password == $confirm){
					// update the password
					$this->avalanche->getUser($user_id)->password($password);
				}
				
				// time preferences
				$reader->setName("timezone");
				$reader->loadFormValue($data_list);
				$strongcal->setUserVar($reader->getName(), $reader->getValue());

				$day_start = new TimeInput();
				$day_start->setName("day_start");
				$day_start->loadFormValue($data_list);
				$strongcal->setUserVar($day_start->getName(), $day_start->getValue());

				$day_end = new TimeInput();
				$day_end->setName("day_end");
				$day_end->loadFormValue($data_list);
				$strongcal->setUserVar($day_end->getName(), $day_end->getValue());
				
				// add the calendar
				$reader->setName("cal_name");
				$reader->loadFormValue($data_list);
				$name = $reader->getValue();
				$reader->setName("cal_color");
				$reader->loadFormValue($data_list);
				$color = $reader->getValue();
				if(strlen($name) == 0){
					$name = "no name";
				}
				$cal_id = $strongcal->addCalendar($name);
				$data = new module_bootstrap_data(array($cal_id), "the calendar id to get"); // send in false as the default value
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
				$data = $runner->run($data);
				$calendar_obj_list = $data->data();
				if(count($calendar_obj_list) > 0){
					$cal = $calendar_obj_list[0];
					$cal->color($color);
				}else{
					throw new Exception("Error adding calendar");
				}

				throw new RedirectException("index.php?view=first_login&subview=done&cal_id=$cal_id");
			}
			
			if(isset($data_list["subview"])){
				$subview = $data_list["subview"];
			}else{
				$subview = "form";
			}
			
			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/first_time.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			    initialize panels
			************************************************************************/
			
			$my_container = new GridPanel(1);
			$my_container->setStyle(new Style("first_time_outer_panel"));

			if($subview == "done"){
				$content = new GridPanel(1);
				$content->setCellStyle(new Style("first_time_content_text"));
				$content->getCellStyle()->setPadding(4);
				$content->add(new Text("All Done!"), new Style("first_time_title"));
				$content->add(new Text("You're all set up! To add a task or event, click the appropriate icon in the top right of the screen. You can also find your profile and Frequently Asked Questions in the sprocket menu in the top right.<br><br>"));
				if($this->avalanche->hasPermissionHuh($this->avalanche->getActiveUser(), "add_user")){
					$content->add(new Link("Add More Users", "index.php?view=inviteusers"));
				}else{
					$content->add(new Link("Go to Month view", "index.php?view=month"));
				}
				if(isset($data_list["cal_id"])){
					$panel = new Panel();
					$panel->setStyle(new Style("first_time_content_text"));
					$panel->setWidth("100%");
					$panel->add(new Text(" or "));
					$panel->add(new Link("add your first event!", "index.php?view=add_event_step_2&cal_id=" . $data_list["cal_id"]));
					$content->add($panel);
				}
				$my_container = new ErrorPanel($content);
				$my_container->setStyle(new Style("first_time_outer_panel"));
				$page = $my_container;
			}else{
				
				/************************************************************************
				************************************************************************/
	
				$title = new Panel();
				$title->setStyle(new Style("first_time_title"));
				$title->add(new Text("Welcome, " . $this->avalanche->getModule("os")->getUsername($this->avalanche->getActiveUser()) . "!"));
				
				$titles = new Panel();
				$titles->setStyle(new Style("first_time_titles"));
				
				$buttons = new BorderPanel();
				$buttons->setStyle(new Style("first_time_buttons"));
				$buttons_panel = new Panel();
				$buttons_panel->setStyle(new Style("first_time_buttons_content"));
				$buttons_panel->setAlign("right");
				$step_panel = new Panel();
				$step_panel->setStyle(new Style("first_time_buttons_content"));
				$buttons->setWest($step_panel);
				$buttons->setEast($buttons_panel);
				
				$content = new Panel();
				$content->setValign("top");
				$content->setStyle(new Style("first_time_content"));
				
				$steps = array();
				
				
				$next_buttons = new HashTable();
				$prev_buttons = new HashTable();
				$step_texts = new HashTable();
				$steps = $this->getSteps();
				for($i=0;$i<count($steps);$i++){
					$step = $steps[$i];
					$step_texts->put($i, new Text((string)($i+1)));
					if($i < (count($steps) - 1)){
						if(is_object($steps[$i]["next"])){
							$next = $steps[$i]["next"];
						}else{
							$next = new ButtonInput("Next");
						}
					}else{
						$next = new SubmitInput("Done");
					}
					$next->setStyle(new Style("first_time_button"));
					$next_buttons->put($i, $next);
					$prev = new ButtonInput("Prev");
					$prev->setStyle(new Style("first_time_button"));
					$prev_buttons->put($i, $prev);
					if($i != 0){
						$next->getStyle()->setDisplayNone();
						$prev->getStyle()->setDisplayNone();
					}else{
						$prev->getStyle()->setDisplayNone();
					}
				}
				
				for($i=0;$i<count($steps); $i++){
					$prev = $prev_buttons->get($i);
					$next = $next_buttons->get($i);
					
					if($i==1){
						$next_p = $next_buttons->get($i-1);
						$prev->addClickAction(new DisplayNoneAction($prev));
						$prev->addClickAction(new DisplayNoneAction($next));
						$prev->addClickAction(new DisplayInlineAction($next_p));
					}
					if($i>1){
						$prev_p = $prev_buttons->get($i-1);
						$next_p = $next_buttons->get($i-1);
						$prev->addClickAction(new DisplayNoneAction($prev));
						$prev->addClickAction(new DisplayNoneAction($next));
						$prev->addClickAction(new DisplayInlineAction($prev_p));
						$prev->addClickAction(new DisplayInlineAction($next_p));
					}
					if($i<(count($steps)-1)){
						$prev_n = $prev_buttons->get($i+1);
						$next_n = $next_buttons->get($i+1);
						$next->addClickAction(new DisplayNoneAction($prev));
						$next->addClickAction(new DisplayNoneAction($next));
						$next->addClickAction(new DisplayInlineAction($prev_n));
						$next->addClickAction(new DisplayInlineAction($next_n));
						$next->addClickAction(new DisplayNoneAction($steps[$i]["title"]));
						$next->addClickAction(new DisplayBlockAction($steps[$i+1]["title"]));
						$next->addClickAction(new DisplayNoneAction($steps[$i]["content"]));
						$next->addClickAction(new DisplayBlockAction($steps[$i+1]["content"]));
						$next->addClickAction(new DisplayNoneAction($step_texts->get($i)));
						$next->addClickAction(new DisplayInlineAction($step_texts->get($i+1)));
					}
					if($i > 0){
						$prev->addClickAction(new DisplayNoneAction($steps[$i]["title"]));
						$prev->addClickAction(new DisplayBlockAction($steps[$i-1]["title"]));
						$prev->addClickAction(new DisplayNoneAction($steps[$i]["content"]));
						$prev->addClickAction(new DisplayBlockAction($steps[$i-1]["content"]));
						$prev->addClickAction(new DisplayNoneAction($step_texts->get($i)));
						$prev->addClickAction(new DisplayInlineAction($step_texts->get($i-1)));
					}
				}
				
				$step_panel->add(new Text("("));
				for($i=0;$i<count($steps);$i++){
					if($i == 0){
						$buttons_panel->add($next_buttons->get($i));
					}else{
						$buttons_panel->add($prev_buttons->get($i));
						$buttons_panel->add($next_buttons->get($i));
						$steps[$i]["title"]->getStyle()->setDisplayNone();
						$steps[$i]["content"]->getStyle()->setDisplayNone();
						$step_texts->get($i)->getStyle()->setDisplayNone();
					}
					$step_panel->add($step_texts->get($i));
					$titles->add($steps[$i]["title"]);
					$content->add($steps[$i]["content"]);
				}
				$step_panel->add(new Text("/" . count($steps) . ")"));
				
				
				$my_container->add($title);
				$my_container->add($titles);
				$my_container->add($content);
				$my_container->add($buttons);
				
				$form = new FormPanel("index.php");
				$form->addHiddenField("view", "first_login");
				$form->addHiddenField("submit", "1");
				$form->add($my_container);
				
				$page = $form;
			}
			
			return new module_bootstrap_data($page, "a gui component for the first time login view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}
	
	private function getSteps(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$steps = array();
		
		// Step 1
		$ret = array();
		$ret["title"] = new Text("Welcome!");
		$ret["content"] = new Text("Thanks for logging in! Let's get started setting up your account!");
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 1.5
		$next = new ButtonInput("Next");
		$next->setDisabled(true);

		$password_field = new SmallTextInput();
		$password_field->getStyle()->setClassname("first_time_input");
		$password_field->setPassword(true);
		$password_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$password_field->setName("password");

		$confirm_field = new SmallTextInput();
		$confirm_field->getStyle()->setClassname("first_time_input");
		$confirm_field->setPassword(true);
		$confirm_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$confirm_field->setName("confirm");

		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("First, let's set up a new password. (Note: passwords <i>are</i> case sensitive.)"));
		$content->add(new Text("New Password"));
		$content->add($password_field);
		$content->add(new Text("Confirm New Password"));
		$content->add($confirm_field);

		$password_field->addKeyUpAction(new IfStrCompareThenAction($password_field, $confirm_field, "==", new EnableAction($next)));
		$confirm_field->addKeyUpAction(new IfStrCompareThenAction($password_field, $confirm_field, "==", new EnableAction($next)));
		$password_field->addKeyUpAction(new IfStrCompareThenAction($password_field, $confirm_field, "!=", new DisableAction($next)));
		$confirm_field->addKeyUpAction(new IfStrCompareThenAction($password_field, $confirm_field, "!=", new DisableAction($next)));
		$password_field->addKeyUpAction(new IfStrLenThenAction($password_field, "==", 0, new DisableAction($next)));
		$confirm_field->addKeyUpAction(new IfStrLenThenAction($confirm_field, "==", 0, new DisableAction($next)));
		
		$ret = array();
		$ret["title"] = new Text("Update Password");
		$ret["content"] = $content;
		$ret["next"] = $next;
		$steps[] = $ret;

		// Step 2
		$main_user = $this->avalanche->getUser($this->avalanche->getActiveUser());
		$title_input = new DropDownInput();
		$title_input->setName("title");
		$title_input->addOption(new DropDownOption("None", ""));
		$title_input->addOption(new DropDownOption("Mr.", "Mr."));
		$title_input->addOption(new DropDownOption("Ms.", "Ms."));
		$title_input->addOption(new DropDownOption("Mrs.", "Mrs."));
		$title_input->addOption(new DropDownOption("Dr.", "Dr."));
		$title_input->setValue($main_user->title());
		$title_input->getStyle()->setClassname("first_time_input");
		$first_input = new SmallTextInput();
		$first_input->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$first_input->setSize(10);
		$first_input->setName("first");
		$first_input->setValue($main_user->first());
		$first_input->getStyle()->setClassname("first_time_input");
		$middle_input = new SmallTextInput();
		$middle_input->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$middle_input->setSize(10);
		$middle_input->setName("middle");
		$middle_input->setValue($main_user->middle());
		$middle_input->getStyle()->setClassname("first_time_input");
		$last_input = new SmallTextInput();
		$last_input->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$last_input->setSize(10);
		$last_input->setName("last");
		$last_input->setValue($main_user->last());
		$last_input->getStyle()->setClassname("first_time_input");
		
		$name_input = new GridPanel(4);
		$name_input->setCellStyle(new Style("first_time_content_text"));
		$name_input->getCellStyle()->setPaddingBottom(3);
		$name_input->getCellStyle()->setPaddingRight(8);
		$name_input->add(new Text("Title"));
		$name_input->add(new Text("First"));
		$name_input->add(new Text("Middle"));
		$name_input->add(new Text("Last"));
		$name_input->add($title_input);
		$name_input->add($first_input);
		$name_input->add($middle_input);
		$name_input->add($last_input);
		
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("You can optionally set your name. This can help other users find you if they need you.<br><br>"));
		$content->add($name_input);
		

		$ret = array();
		$ret["title"] = new Text("Your Name");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 3
		$next = new ButtonInput("Next");

		if(strlen($main_user->email()) == 0){
			$next->setDisabled(true);
		}
		
		$email_input = new SmallTextInput();
		$email_input->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$email_input->setSize(40);
		$email_input->setName("email");
		$email_input->setValue($main_user->email());
		$email_input->getStyle()->setClassname("first_time_input");
		$sms_input = new SmallTextInput();
		$sms_input->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$sms_input->setSize(40);
		$sms_input->setName("sms");
		$sms_input->setValue($main_user->sms());
		$sms_input->getStyle()->setClassname("first_time_input");
		
		$small_font_style = new Style();
		$small_font_style->setFontSize(9);
		
		$contact_input = new GridPanel(2);
		$contact_input->setCellStyle(new Style("first_time_content_text"));
		$contact_input->getCellStyle()->setPaddingBottom(3);
		$contact_input->getCellStyle()->setPaddingRight(8);
		$contact_input->add(new Text("Email"));
		$contact_input->add($email_input);
		$contact_input->add(new Text("SMS"));
		$contact_input->add($sms_input);
		$contact_input->add(new Text(""));
		$contact_input->add(new Text("Example: 2815550983@messaging.sprintpcs.com"), $small_font_style);
		
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Here you can set your contact information. We require that you enter an email address, but a SMS address (for text messages to your phone) is optional.<br><br>"));
		$content->add($contact_input);
		
		//$email_input->addKeyUpAction(new IfStrLenThenAction($email_input, ">", 0, new EnableAction($next)));
		//$email_input->addKeyUpAction(new IfStrLenThenAction($email_input, "==", 0, new DisableAction($next)));
		$email_input->addKeyUpAction(new IfEmailThenAction($email_input, new EnableAction($next)));
		$email_input->addKeyUpAction(new IfNotEmailThenAction($email_input, new DisableAction($next)));
		
		$ret = array();
		$ret["title"] = new Text("Your Contact Information");
		$ret["content"] = $content;
		$ret["next"] = $next;
		$steps[] = $ret;

		// Step 4
		$bio_field = new TextAreaInput();
		$bio_field->setName("bio");
		$bio_field->setValue($main_user->bio());
		$bio_field->getStyle()->setClassname("first_time_input");
		
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Write up a short profile about yourself to share with other users on this calendar.")); 
		$content->add($bio_field);
		

		$ret = array();
		$ret["title"] = new Text("About You");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 4
		$timezones = module_bootstrap_os_preferences_gui::getTimezoneArray();
		$timezone_field = new DropDownInput();
		$timezone_field->setName("timezone");
		foreach($timezones as $timezone){
			$timezone_field->addOption(new DropDownOption($timezone["full_name"], (string)$timezone["offset"]));
		}
		$timezone_field->setValue((string)$strongcal->getUserVar("timezone"));
		
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Aurora Calendar can adjust event times to fit into your timezone. No matter where you are, home or abroad, you can view your schedule in your time.")); 
		$content->add(new Text("Please select your local time zone."));
		$content->add($timezone_field);
		

		$ret = array();
		$ret["title"] = new Text("Time Preferences");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 5
		$day_start_field = new TimeInput();
		$day_start_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$day_start_field->setName("day_start");
		$day_start_field->setValue((string)$strongcal->getUserVar("day_start"));

		$day_end_field = new TimeInput();
		$day_end_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$day_end_field->setName("day_end");
		$day_end_field->setValue((string)$strongcal->getUserVar("day_end"));

		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Aurora can customize views to fit your schedule. Simply fill in when your day starts and ends and Aurora won't show you the times you don't want to see."));
		$content->add(new Text("Beginning of Your Day"));
		$content->add($day_start_field);
		$content->add(new Text("End of Your Day"));
		$content->add($day_end_field);

		$ret = array();
		$ret["title"] = new Text("Time Preferences");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 6
		$default_color = "#CCCCCC";
		$name_field = new SmallTextInput();
		$name_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
		$name_field->setName("cal_name");
		$name_field->setValue("Personal");
		$name_field->setStyle(new Style("first_time_input"));

		$color_box = new Panel();
		$color_box->setStyle(new Style("first_time_main_color_box"));
		$color_box->getStyle()->setBackground($default_color);
		$color_input = new HiddenInput();
		$color_input->setName("cal_color");
		$color_input->setValue($default_color);
		
		$color_palette = module_bootstrap_strongcal_addcalendarview_gui::getColorPalatte($color_box, $color_input);

		$color_choose = new BorderPanel();
		$color_choose->setWidth("90%");
		$color_choose->setAlign("center");
		$color_choose->setValign("middle");
		$color_choose->setCenter($color_palette);
		$color_choose->setEast($color_box);
		
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Let's set up your first calendar."));
		$name_content = new GridPanel(2);
		$name_content->setCellStyle(new Style("first_time_content_text"));
		$name_content->getCellStyle()->setPaddingRight(5);
		$name_content->add(new Text("Calendar Name:"));
		$name_content->add($name_field);
		$content->add($name_content);
		$content->add(new Text("Calendar Color:"));
		$content->add($color_choose);
		$content->add($color_input);
		
		$ret = array();
		$ret["title"] = new Text("First Calendar");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 7
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("That's it! Just click [Done] and enjoy the calendar!"));
		
		$ret = array();
		$ret["title"] = new Text("Finished with Setup!");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		return $steps;
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
	private function getTimezoneArray(){
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