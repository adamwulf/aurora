<?

class module_bootstrap_os_manageusers_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche User List to HTML");
		$this->setInfo("outputs a html list of users");
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
			$os = $this->avalanche->getModule("os");

			if(isset($data_list["user_id"])){
				$user_id = (int) $data_list["user_id"];
			}else{
				$user_id = false;
			}


			if(isset($data_list["search"])){
				$search_default = (string) $data_list["search"];
			}else{
				$search_default = "";
			}


			if(isset($data_list["action"])){
				$action = (string) $data_list["action"];
			}else{
				$action = "";
			}

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "";
			}

			if(isset($data_list["sent_ok"])){
				$sent_ok = (string) $data_list["sent_ok"];
			}else{
				$sent_ok = false;
			}
			/** end initializing the input */

			/**
			 * get the list of users and filter out so we only have USERs, not SYSTEMs or MODULEs
			 */

			if($subview == "delete_user"){
				if(isset($data_list["user_id"])){
					// casting is ok, since we're coming from form input
					$this->avalanche->deleteUser((int)$data_list["user_id"]);
					throw new RedirectException("index.php?view=manage_users");
				}else{
					throw new IllegalArgumentException("user_id must be sent as form input to delete a user");
				}
			}


			$user_list = $this->avalanche->getAllUsers();

			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/manage_users.css"));
			$this->doc->addStyleSheet($css);
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/style.os.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/

			$column = new Style("column");
			$column_results = new Style("column_results");
			$result_style = new Style("result_style");
			$active_result_style = new Style("active_result_style");
			$search_box_style = new Style("search_box_style");
			$header_style = new Style("user_dir_header");
			$small_icon_style = new Style("small_square_icon");
			$icon_style = new Style("normal_square_icon");
			$error_box_style = new Style("error_panel");
			$profile_header_style = new Style("profile_header_style");
			$profile_header_text = new Style("profile_header_text");
			$profile_icons_style = new Style("profile_icons_style");
			$profile_icons_holder_style = new Style("profile_icons_holder");
			$profile_info_style = new Style("profile_info_style");
			$profile_info_holder_style = new Style("profile_info_holder");
			$info_panel_style = new Style("info_panel");
			$facts_style = new Style("facts_style");
			$invite_style = new Style("invite_style");
			$search_style = new Style("search_style");
			$bottom_style = new Style("bottom_style");
			$button_style = new Style("button_style");
			$invite_main_sytle = new Style("invite_main_style");
			$faded_input_style = new Style("faded_input_style");
			$faded_input_style->setHandCursor();

			/************************************************************************
			    initialize panels
			************************************************************************/

			$search_panel = new Panel();
			$results_panel = new Panel();
			$info_panel = new Panel();

			$right = new BorderPanel();
			$right->setValign("top");
			$right->setWest($results_panel);
			$right->setCenter($info_panel);

			$my_container = new BorderPanel();
			$my_container->setValign("top");
			$my_container->setWest($search_panel);
			$my_container->setCenter($right);

			/************************************************************************
			   init constants
			************************************************************************/

			// max results to show at a time
			$max_to_show = 10;

			/************************************************************************
			    apply styles to created panels
			************************************************************************/

			$my_container->setWidth("100%");
			$my_container->setHeight("100%");

			$info_panel->setValign("top");
			$info_panel->setWidth("100%");
			$info_panel->setHeight("100%");
			$info_panel->setStyle($info_panel_style);

			$search_panel->setStyle($column);
			$results_panel->setStyle($column_results);
			$search_panel->setValign("top");
			$results_panel->setValign("top");
			$results_panel->setHeight("100%");
			$info_panel->setValign("top");

			/************************************************************************
			     add the headers
			************************************************************************/
			$results_header = new Panel();
			$results_header->add(new Text("User Matches<br>"));
			$results_header->setStyle(clone $header_style);
			$results_header->getStyle()->setPaddingRight(4);
			$results_panel->add($results_header);

			$info_header = new Panel();
			$info_header->add(new Text("Profile<br>"));
			$info_header->setStyle($header_style);
			$info_panel->add($info_header);

			/************************************************************************
			     build the panels
			************************************************************************/

			if($subview == "invite"){
				$errors = array();
				$new_users = array();
				if(isset($data_list["submit"])){
					$user_id = $this->avalanche->getActiveUser();

					$reader = new SmallTextInput();

					// create info for email
					$from = $this->avalanche->getUser($this->avalanche->getActiveUser());
					$acct = is_object($this->avalanche->ACCOUNTOBJ()) ? $this->avalanche->ACCOUNTOBJ()->name() : "www";
					$site = $this->avalanche->DOMAIN() . "/" . $acct . "/";
					$the_user_name = $os->getUsername($this->avalanche->getActiveUser());
					$subj = "You have been invited to " . $the_user_name . "'s online calendar!";
					// add users and send email
					$reader->setName("username");
					$reader->loadFormValue($data_list);
					$username = $reader->getValue();
					$reader->setName("email");
					$reader->loadFormValue($data_list);
					$email = $reader->getValue();
					try{
						if(strlen($username) > 0){
							$pass = substr(md5(md5($username . $email . rand()) . rand()), 0, 8);
							$user_id = $this->avalanche->addUser($username, $pass, $email);
							$user = $this->avalanche->getUser($user_id);

							$body = $the_user_name . " has invited you to their online calendar at:<br>\r\n";
							$body .= "  <a href='http://$site'>http://$site</a><br><br>\n\n";
							$body .= " You can log in with the following username and password:<br><br>\r\n\r\n";
							$body .= " username: " . $user->username() . "<br>\r\n";
							$body .= " password: " . $pass . "<br><br>\r\n\r\n";
							$body .= "To log in, simple visit <a href='http://$site'>http://$site</a> and enter your username and password. ";
							$body .= "Log in to manage and share your own events and tasks - even set up email reminders!<br><br>\r\n\r\n";
							$body .= "Happy Scheduling,<br>\r\n";
							$body .= "The Inversion Team<br><br><br>\r\n\r\n\r\n";
							$body .= "(This is an automatic e-mail on behalf of " . $the_user_name . ")";
							$user->contactEmail($from, $subj, $body);
							$new_users[] = $user;
						}
					}catch(CannotAddUserException $e){
						$errors[] = $e;
					}

					$data_list["subview"] = "done";
				}

				// build the search panel
				$search_text_panel = new Panel();
				$search_text_panel->setWidth("100%");
				$search_text_panel->setStyle(clone $invite_style);
				$search_text_panel->getStyle()->setHandCursor();

				$search_header = new Text("Search<br>");
				$search_header->setStyle(new Style("faded_user_dir_header"));
				$search_header->getStyle()->setFontSize(14);
				$search_text_panel->add($search_header);

				$search = new SmallTextInput();
				$search->setReadOnly(true);
				$search->setStyle($faded_input_style);
				$search->setSize(15);
				$search->setValue($search_default);

				$text = new Text("<br>search for:<br>");
				$search_text_panel->add($text);
				$search_text_panel->add($search);
				$search_text_panel->addAction(new LoadPageAndAppendAction("?view=manage_users&&search=", $search));

				$search_panel->add($search_text_panel);

				$filter = new FilterInitAction($search, $max_to_show);
				$this->doc->addAction($filter);


				// build the invite panel
				$search_panel->add(new Text("<br><br><br><br>"));
				$invitee_panel = new Panel();
				$invitee_panel->setStyle($search_style);
				$invitee_panel->setWidth("100%");

				$search_header = new Text("Invite<br>");
				$search_header->setStyle($invite_style);
				$search_header->getStyle()->setFontSize(14);
				$invitee_panel->add($search_header);

				$text = new Text("<br>fill out the form to invite a user<br>");
				$invitee_panel->add($text);
				$search_panel->add($invitee_panel);


				$right = new Panel();
				$right->setValign("top");
				$right->setStyle($invite_main_sytle);

				$invite_header = new Text("Invite User<br>");
				$invite_header->setStyle($header_style);
				$invite_header->getStyle()->setFontSize(14);
				$right->add($invite_header);

				if(isset($data_list["subview"]) && $data_list["subview"] == "done"){
					if(count($new_users)){
						$t = new Text("<br><br>The following users have been added:<br>");
						$t->getStyle()->setFontWeight("bold");
						$right->add($t);
						foreach($new_users as $u){
							$t = new Text($u->username() . "<br>");
							$t->getStyle()->setPaddingLeft(15);
							$right->add($t);
						}
					}else{
						$t = new Text("<br><br>A new user has not been invited<br><br>");
						$t->getStyle()->setFontWeight("bold");
						$right->add($t);
					}
					if(count($errors)){
						$t = new Text("The following errors have occurred:<br>");
						$t->getStyle()->setFontWeight("bold");
						$right->add($t);
						foreach($errors as $e){
							$t = new Text($e->getMessage() . "<br>");
							$t->getStyle()->setPaddingLeft(15);
							$right->add($t);
						}
					}
					$right->add(new Text("<br><br>"));
					$right->add(new Link("Invite another user", "index.php?view=manage_users&subview=invite&search=$search_default"));
				}else{
					// actual form to invite a user

					$form = new FormPanel("index.php");
					$form->setAsGet();
					$form->addHiddenField("view", "manage_users");
					$form->addHiddenField("subview", "invite");
					$form->addHiddenField("submit", "1");
					$form->addHiddenField("search", "$search_default");

					$spacer = new GridPanel(1);
					$spacer->setWidth("500");
					$spacer->getCellStyle()->setPaddingTop(20);
					$spacer->setStyle(new Style("profile_text"));

					$inputs = new GridPanel(2);
					$inputs->getStyle()->setPaddingTop(10);
					$inputs->getCellStyle()->setPaddingRight(4);
					$inputs->getCellStyle()->setPaddingBottom(2);
					$inputs->getCellStyle()->setFontFamily("verdanda, sans-serif");
					$inputs->getCellStyle()->setFontSize(10);
					$inputs->getCellStyle()->setFontWeight("bold");
					$inputs->getCellStyle()->setFontColor("#4b4b4b");

					$inputs->add(new Text("username"));
					$inputs->add(new Text("email"));

					$username_field = new SmallTextInput();
					$username_field->getStyle()->setClassname("first_time_input");
					$username_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
					$username_field->setSize(15);
					$username_field->setName("username");

					$email_field = new SmallTextInput();
					$email_field->getStyle()->setClassname("first_time_input");
					$email_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
					$email_field->setSize(35);
					$email_field->setName("email");

					$inputs->add($username_field);
					$inputs->add($email_field);

					$invite = new SubmitInput("Invite User");
					$invite->setDisabled(true);
					// validation actions
					$action = new TrueAction();
					$username_ok_action = new StrLenEqualsAction($username_field, ">", 0);
					$email_ok_action = new IsEmailAction($email_field);
					$ok_action = new AndAction($username_ok_action, $email_ok_action);
					$valid_action = new IfThenAction($ok_action, new EnableAction($invite));
					$not_valid_action = new IfThenAction(new NotAction($ok_action), new DisableAction($invite));

					$username_field->addKeyUpAction($valid_action);
					$username_field->addKeyUpAction($not_valid_action);
					$username_field->addKeyPressAction(new LowerAlphaNumericOnlyAction());
					$email_field->addKeyUpAction($valid_action);
					$email_field->addKeyUpAction($not_valid_action);


					$spacer->add(new Text("Inviting a user is easy! Simple choose a username for the new user, and enter his or her email address. An email will automatically be sent to the invitee with their new username and password."));
					$spacer->add($inputs);
					$spacer->add($invite);
					$form->add($spacer);
					$right->add($form);
				}
				$my_container->setCenter($right);
			}else{
				// build the search panel
				$search_text_panel = new Panel();
				$search_text_panel->setWidth("100%");
				$search_text_panel->setStyle($search_style);

				$search_header = new Text("Search<br>");
				$search_header->setStyle($header_style);
				$search_text_panel->add($search_header);

				$search = new SmallTextInput();
				$search->setStyle($search_box_style);
				$search->setSize(15);
				$search->setValue($search_default);

				$text = new Text("<br>search for:<br>");
				$search_text_panel->add($text);
				$search_text_panel->add($search);

				$search_panel->add($search_text_panel);

				$filter = new FilterInitAction($search, $max_to_show);
				$this->doc->addAction($filter);


				// build the invite panel
				$search_panel->add(new Text("<br><br><br><br>"));
				$invitee_panel = new Panel();
				$invitee_panel->setWidth("100%");
				$invitee_panel->setStyle($invite_style);
				$invitee_panel->getStyle()->setHandCursor();
				$user_url = "";
				if($user_id !== false){
					$user_url = "user_id=" . $user_id . "&";
				}
				$invitee_panel->addAction(new LoadPageAndAppendAction("?view=manage_users&subview=invite&" . $user_url . "search=", $search));

				$search_header = new Text("Invite<br>");
				$search_header->setStyle(new Style("faded_user_dir_header"));
				$search_header->getStyle()->setFontSize(14);
				$invitee_panel->add($search_header);

				$text = new Text("<br>click here to invite more users to your account<br>");
				$invitee_panel->add($text);
				$search_panel->add($invitee_panel);

				// build the results panel
				$results_inner = new Panel();
				$results_inner->setStyle(new Style("result_style_container"));
				$count_shown = 0;
				foreach($user_list as $user){

					$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->getAvatar($user->getId()));
					$icon->setStyle($small_icon_style);

					$current_row = new BorderPanel();
					$current_row->setWest($icon);
					$current_row->setValign("middle");
					if($user_id === $user->getId()){
						$current_row->setStyle(clone $active_result_style);
					}else{
						$current_row->setStyle(clone $result_style);
					}
					$user_name = new Text($os->getUsername($user->getId()) . "<br>(" . $user->username() . ")");
					$user_name->getStyle()->setPaddingLeft(2);
					$current_row->setCenter($user_name);
					$results_inner->add($current_row);

					$user_text = $os->getUsername($user->getId()) . " " . $user->username();

					$current_row->getStyle()->setHandCursor();

					$current_row->addAction(new LoadPageAndAppendAction("?view=manage_users&user_id=" . $user->getId() . "&search=", $search));

					$filter->addItem($user_text, $current_row);


					if(strlen($search_default) == 0 || strlen($search_default) && stripos($user_text, $search_default) !== false){
						$count_shown++;
					}else{
						$current_row->getStyle()->setDisplayNone();
					}

					if($count_shown > $max_to_show){
						$current_row->getStyle()->setDisplayNone();
					}


				}
				$results_panel->add($results_inner);

				// build the info panel

				if($subview == "edit_user"){
					$module = new module_bootstrap_os_edituserview_gui($this->avalanche, $this->doc);
					$bootstrap = $this->avalanche->getModule("bootstrap");
					$runner = $bootstrap->newDefaultRunner();
					$runner->add($module);
					$add_cal_form = $runner->run(new module_bootstrap_data($data_list, "the form input"));
					$add_cal_form = $add_cal_form->data();

					$info_panel->add($add_cal_form);
				}else
				if($user_id !== false){
					$main_user = $this->avalanche->getUser($user_id);

					if($action == "email"){
						$text = new SmallTextInput();
						$text->setName("subject");
						$text->loadFormValue($data_list);
						$subj = $text->getValue();
						$text->setName("body");
						$text->loadFormValue($data_list);
						$body = $text->getValue();
						$text->setName("search");
						$text->loadFormValue($data_list);
						$search_default = $text->getValue();

						$from_user = $this->avalanche->getUser($this->avalanche->getActiveUser());
						$sent_ok = $main_user->contactEmail($from_user, $subj, $body);
						throw new RedirectException("index.php?view=manage_users&subview=email&sent_ok=$sent_ok&user_id=" . $main_user->getId() . "&search=" . $search_default);
					}else if($action == "text"){
						$text = new SmallTextInput();
						$text->setName("subject");
						$text->loadFormValue($data_list);
						$subj = $text->getValue();
						$text->setName("body");
						$text->loadFormValue($data_list);
						$body = $text->getValue();

						$from_user = $this->avalanche->getUser($this->avalanche->getActiveUser());
						$sent_ok = $main_user->contactSMS($from_user, $subj, $body);
						throw new RedirectException("index.php?view=manage_users&subview=text&sent_ok=$sent_ok&user_id=" . $main_user->getId() . "&search=" . $search_default);
					}

					// show a real user profile
					$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->getAvatar($user_id));
					$icon->setStyle(clone $icon_style);
					$icon->getStyle()->setMarginRight(20);
					$title = new Panel();
					$title->setWidth("100%");
					$title->setStyle($profile_header_text);
					$header = new BorderPanel();
					$header->setStyle($profile_header_style);
					$header->setWest($icon);
					$header->setCenter($title);
					$title->add(new Text($os->getUsername($user_id)));
					$nombre = new Text("<br>(" . $main_user->username() . ")");
					$nombre->getStyle()->setFontSize(11);
					$title->add($nombre);

					$icons = new GridPanel(3);
					$icons->setAlign("center");
					$icons->setWidth("100%");
					$icons->setStyle($profile_icons_style);
					$icons->getCellStyle()->setPadding(4);
					$profile = new Link("profile", "javascript:;");
					$email = new Link("e-mail", "javascript:;");
					$text = new Link("text", "javascript:;");
					$icons->add($profile);
					$icons->add($email);
					$icons->add($text);

					if(strlen($main_user->sms()) === 0){
						$text->getStyle()->setDisplayNone();
					}

					$icons_holder = new Panel();
					$icons_holder->setStyle($profile_icons_holder_style);
					$icons_holder->add($icons);

					$info = new Panel();
					$info->setWidth("100%");
					$info->setValign("top");
					$info->setStyle($profile_info_style);
					$info->getStyle()->setPadding(4);

					$profile_panel = $this->getProfilePanel($main_user);
					$email_panel = $this->getEmailPanel($main_user, $search, $subview, $sent_ok);
					$text_panel = $this->getTextPanel($main_user, $search, $subview, $sent_ok);

					if($subview == "email"){
						$profile_panel->getStyle()->setDisplayNone();
						$text_panel->getStyle()->setDisplayNone();
					}else if($subview == "text"){
						$profile_panel->getStyle()->setDisplayNone();
						$email_panel->getStyle()->setDisplayNone();
					}else{
						$email_panel->getStyle()->setDisplayNone();
						$text_panel->getStyle()->setDisplayNone();
					}

					$profile->addAction(new DisplayBlockAction($profile_panel));
					$profile->addAction(new DisplayNoneAction($email_panel));
					$profile->addAction(new DisplayNoneAction($text_panel));

					$email->addAction(new DisplayBlockAction($email_panel));
					$email->addAction(new DisplayNoneAction($profile_panel));
					$email->addAction(new DisplayNoneAction($text_panel));

					$text->addAction(new DisplayBlockAction($text_panel));
					$text->addAction(new DisplayNoneAction($email_panel));
					$text->addAction(new DisplayNoneAction($profile_panel));

					$info->add($profile_panel);
					$info->add($email_panel);
					$info->add($text_panel);

					$info_holder = new Panel();
					$info_holder->setStyle($profile_info_holder_style);
					$info_holder->add($info);


					$facts = new Panel();
					$facts->setValign("top");
					$facts->setWidth("100%");
					$facts->getStyle()->setPadding(4);
					$facts->setStyle($facts_style);

					$bottom = new BorderPanel();
					$bottom->setStyle($bottom_style);
					$bottom->setCenter($facts);

					$info_panel->add($header);
					$info_panel->add($icons_holder);
					$info_panel->add($info_holder);
					$info_panel->add($bottom);

					try{
						$lastActive = new DateTime($main_user->lastActive());
						$lastActive->hour((int)($lastActive->hour() + $strongcal->timezone()));
						$lastActive->minute((int)(($strongcal->timezone() - round($strongcal->timezone(),0))*60));
						$lastActive = $lastActive->toString();
					}catch(IllegalArgumentException $e){
						$lastActive = "never";
					}
					try{
						$lastLoggedIn = new DateTime($main_user->lastLoggedIn());
						$lastLoggedIn->hour((int)($lastLoggedIn->hour() + $strongcal->timezone()));
						$lastLoggedIn->minute((int)(($strongcal->timezone() - round($strongcal->timezone(),0))*60));
						$lastLoggedIn = $lastLoggedIn->toString();
					}catch(IllegalArgumentException $e){
						$lastLoggedIn = "never";
					}

					try{
						$lastLoggedOut = new DateTime($main_user->lastLoggedOut());
						$lastLoggedOut->hour((int)($lastLoggedOut->hour() + $strongcal->timezone()));
						$lastLoggedOut->minute((int)(($strongcal->timezone() - round($strongcal->timezone(),0))*60));
						$lastLoggedOut = $lastLoggedOut->toString();
					}catch(IllegalArgumentException $e){
						$lastLoggedOut = "never";
					}

					$facts->add(new Text("last active: " . $lastActive . "<br>"));
					$facts->add(new Text("last logged in: " . $lastLoggedIn . "<br>"));
					$facts->add(new Text("last logged out: " . $lastLoggedOut . "<br>"));

					// the delete button
					$title = new Text("<b>Delete User?</b><br>");
					$text = new Text("Delete the user <i>" . $main_user->username() . "</i>?<br>");
					$warning = new Text("(All related information will be lost. This cannot be reversed.)");
					$warning->getStyle()->setFontSize(8);
					$delete_confirm_window = new SimpleWindow($title);
					$delete_confirm_window->add($text);
					$delete_confirm_window->add($warning);
					$yes_action = new LoadPageAction("index.php?view=manage_users&subview=delete_user&user_id=" . $main_user->getId());
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

					$delete_button = new Button("delete");
					$delete_button->setStyle($button_style);
					$delete_button->addAction(new MoveToCenterAction($delete_confirm_window, 500));

					$edit_button = new Button("edit");
					$edit_button->setStyle($button_style);
					$edit_button->addAction(new LoadPageAndAppendAction("?view=manage_users&subview=edit_user&user_id=" . $main_user->getId() . "&search=", $search));


					$buttons = new GridPanel(2);
					$buttons->getCellStyle()->setPaddingLeft(3);
					$bottom->setEast($buttons);
					if($main_user->getId() >= 0 && ($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_user") ||
					   $main_user->getId() == $this->avalanche->loggedInHuh())){
						$buttons->add($edit_button);
					}
					$default_user = $this->avalanche->getVar("USER");
					if($main_user->getId() >= 0 && ($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "del_user") &&
					   $main_user->getId() != $default_user &&
					   $main_user->getId() != $this->avalanche->loggedInHuh())){
						   $buttons->add($delete_button);
						   $this->doc->addHidden($delete_confirm_window);
					}

				}else{
					// show instructions
					$msg = new Panel();
					$msg->setStyle($error_box_style);
					$msg->add(new Text("Please select a user from the list <br>on the left. Use the search box<br> to narrow your choices."));
					$profile = new ErrorPanel($msg);

					$info_panel->setHeight("70%");
					$info_panel->add($profile);
				}
			}


			// finish up and add the title
			$title = "User Directory";
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$header->add(new Text($title));
			$grid = new GridPanel(1);
			$grid->setWidth("100%");
			$grid->add($header);
			$grid->add($my_container);

			$manage_view = $grid;
			return new module_bootstrap_data($manage_view, "a gui component for the manage users view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}


	private function getEmailPanel($main_user, $search, $action, $sent_ok){
		$os = $this->avalanche->getModule("os");

		$email_cell_style = new Style("profile_text");
		$email_cell_style->setPadding(4);

		$form_panel = new FormPanel("index.php");
		$form_panel->setWidth("100%");
		$form_panel->setAsGet();
		$form_panel->setStyle(clone $email_cell_style);
		$form_panel->addHiddenField("action", "email");
		$form_panel->addHiddenField("user_id", (string)$main_user->getId());
		$form_panel->addHiddenField("view", "manage_users");

		$email_panel = new GridPanel(2);
		$email_panel->setCellStyle($email_cell_style);

		$title = new Text("E-mail");
		$title->getStyle()->setFontSize(14);

		$searchlhs = new HiddenInput();
		$searchlhs->setValue($search->getValue());
		$searchlhs->setName("search");
		$search->addKeyUpAction(new AssignValueAction($searchlhs, $search));
		$form_panel->add($searchlhs);

		$subject = new SmallTextInput();
		$subject->setName("subject");

		$body = new TextAreaInput();
		$body->setName("body");

		$email_panel->add(new Text("To:"));
		$email_panel->add(new Text($os->getUsername($main_user->getId())));
		$email_panel->add(new Text("From:"));
		$email_panel->add(new Text($os->getUsername($this->avalanche->getActiveUser())));
		$email_panel->add(new Text("Subject:"));
		$email_panel->add($subject);
		$email_panel->add(new Text("Body:"));
		$email_panel->add($body);
		$email_panel->add(new SubmitInput("Send"));

		$form_panel->add($title);
		$form_panel->add($email_panel);

		if(is_string($sent_ok) && $action == "email"){
			if($sent_ok){
				$message = new Text("<br><br><br>Your e-mail has been sent successfully<br><br><br>");
			}else{
				$message = new Text("<br><br><br>Your e-mail has NOT been sent successfully<br><br><br>");
			}
			$button = new ButtonInput("OK");
			$button->addClickAction(new DisplayNoneAction($message));
			$button->addClickAction(new DisplayNoneAction($button));
			$button->addClickAction(new DisplayBlockAction($email_panel));
			$email_panel->getStyle()->setDisplayNone();
			$form_panel->add($message);
			$form_panel->add($button);
		}

		return $form_panel;
	}

	private function getTextPanel($main_user, $search, $subview, $sent_ok){
		$os = $this->avalanche->getModule("os");

		$text_cell_style = new Style("profile_text");
		$text_cell_style->setPadding(4);

		$form_panel = new FormPanel("index.php");
		$form_panel->setWidth("100%");
		$form_panel->setAsGet();
		$form_panel->setStyle(clone $text_cell_style);
		$form_panel->addHiddenField("action", "text");
		$form_panel->addHiddenField("user_id", (string)$main_user->getId());
		$form_panel->addHiddenField("view", "manage_users");

		$text_panel = new GridPanel(2);
		$text_panel->setCellStyle(clone $text_cell_style);

		$title = new Text("Text Message");
		$title->getStyle()->setFontSize(14);

		$searchlhs = new HiddenInput();
		$searchlhs->setValue($search->getValue());
		$searchlhs->setName("search");
		$search->addKeyUpAction(new AssignValueAction($searchlhs, $search));
		$form_panel->add($searchlhs);

		$subject = new SmallTextInput();
		$subject->setName("subject");

		$body = new TextAreaInput();
		$body->setName("body");

		$text_panel->add(new Text("To:"));
		$text_panel->add(new Text($os->getUsername($main_user->getId())));
		$text_panel->add(new Text("From:"));
		$text_panel->add(new Text($os->getUsername($this->avalanche->getActiveUser())));
		$text_panel->add(new Text("Subject:"));
		$text_panel->add($subject);
		$text_panel->add(new Text("Body:"));
		$text_panel->add($body);
		$text_panel->add(new SubmitInput("Send"));

		$form_panel->add($title);
		$form_panel->add($text_panel);

		if(is_string($sent_ok) && $subview == "text"){
			if($sent_ok){
				$message = new Text("<br><br><br>your message has been sent successfully<br><br><br>");
			}else{
				$message = new Text("<br><br><br>your message has NOT been sent successfully<br><br><br>");
			}
			$button = new ButtonInput("OK");
			$button->addClickAction(new DisplayNoneAction($message));
			$button->addClickAction(new DisplayNoneAction($button));
			$button->addClickAction(new DisplayBlockAction($text_panel));
			$text_panel->getStyle()->setDisplayNone();
			$form_panel->add($message);
			$form_panel->add($button);
		}

		return $form_panel;
	}

	private function getProfilePanel($main_user){
		if(strlen($main_user->bio())){
			return new Text(nl2br($main_user->bio()));
		}else{
			return new Text("<i>This user has not set up any bio information.</i>");
		}
	}
}
?>