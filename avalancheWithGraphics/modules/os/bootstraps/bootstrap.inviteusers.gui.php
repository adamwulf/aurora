<?

class module_bootstrap_os_inviteusers_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Wizard to invite more users");
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
			$os   = $this->avalanche->getModule("os");

			$errors = array();
			$new_users = array();
			// if they submitted, then let's figure everything out
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
				for($i=0;$i<count($data_list["username"]);$i++){
					$data_list["fake_username"] = $data_list["username"][$i];
					$reader->setName("fake_username");
					$reader->loadFormValue($data_list);
					$username = $reader->getValue();
					$data_list["fake_email"] = $data_list["email"][$i];
					$reader->setName("fake_email");
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
				}

				$data_list["subview"] = "done";
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
				$content->getCellStyle()->setPadding(2);
				$content->add(new Text("All Done!"), new Style("first_time_title"));
				if(count($new_users)){
					$t = new Text("The following users have been added:");
					$t->getStyle()->setFontWeight("bold");
					$content->add($t);
					foreach($new_users as $u){
						$t = new Text($u->username());
						$t->getStyle()->setPaddingLeft(15);
						$content->add($t);
					}
				}else{
					$t = new Text("No users have been added.");
					$t->getStyle()->setFontWeight("bold");
					$content->add($t);
				}
				if(count($errors)){
					$t = new Text("The following errors have occurred:");
					$t->getStyle()->setFontWeight("bold");
					$content->add($t);
					foreach($errors as $e){
						$t = new Text($e->getMessage());
						$t->getStyle()->setPaddingLeft(15);
						$content->add($t);
					}
				}
				$content->add(new Link("Invite 5 more", "index.php?view=inviteusers"));

				$my_container = new ErrorPanel($content);
				$my_container->setStyle(new Style("first_time_outer_panel"));
				$page = $my_container;
			}else{

				/************************************************************************
				************************************************************************/

				$title = new Panel();
				$title->setStyle(new Style("first_time_title"));
				$title->add(new Text("Invite More Users"));

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
				$form->addHiddenField("page", "index.php");
				$form->addHiddenField("view", "inviteusers");
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
		$ret["title"] = new Text("Welcome, " . $this->avalanche->getModule("os")->getUsername($this->avalanche->getActiveUser()) . "!");
		$ret["content"] = new Text("Additional users really allow you to take advantage of Aurora's advanced features like group reminders and calendar sharing. To get started adding users, click the [Next] button below.");
		$ret["next"] = false;
		$steps[] = $ret;

		// Step 1.5
		$next = new ButtonInput("Next");
		$next->setDisabled(true);

		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("Just fill in a username and email for each user that you wish to add. If you need to add more than 5 users, you will get a chance to add more after these first 5. "));

		$inputs = new GridPanel(3);
		$inputs->getStyle()->setPaddingTop(10);
		$inputs->getCellStyle()->setPaddingRight(2);
		$inputs->getCellStyle()->setPaddingLeft(2);
		$inputs->getCellStyle()->setPaddingBottom(2);
		$inputs->getCellStyle()->setFontFamily("verdanda, sans-serif");
		$inputs->getCellStyle()->setFontSize(10);
		$inputs->getCellStyle()->setFontWeight("bold");

		$inputs->add(new Text("&nbsp;"));
		$inputs->add(new Text("username"));
		$inputs->add(new Text("email"));

		$users = array();
		$emails = array();

		for($i=0;$i<5;$i++){
			$username_field = new SmallTextInput();
			$username_field->getStyle()->setClassname("first_time_input");
			$username_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
			$username_field->setSize(15);
			$username_field->setName("username[$i]");

			$email_field = new SmallTextInput();
			$email_field->getStyle()->setClassname("first_time_input");
			$email_field->addKeyPressAction(new OnKeyAction(13, new ManualAction("return false;")));
			$email_field->setSize(35);
			$email_field->setName("email[$i]");

			$users[] = $username_field;
			$emails[] = $email_field;

			$inputs->add(new Text(($i+1) . "."));
			$inputs->add($username_field);
			$inputs->add($email_field);
		}
		$action = new TrueAction();
		for($i=0;$i<count($users);$i++){
			$strlen_username_action = new StrLenEqualsAction($users[$i], "==", 0);
			$strlen_email_action = new StrLenEqualsAction($emails[$i], "==", 0);
			$strlen_action = new AndAction($strlen_username_action, $strlen_email_action);

			$username_ok_action = new StrLenEqualsAction($users[$i], ">", 0);
			$email_ok_action = new IsEmailAction($emails[$i]);
			$ok_action = new AndAction($username_ok_action, $email_ok_action);

			$user_action = new OrAction($strlen_action, $ok_action);
			$action = new AndAction($user_action, $action);
		}
		$valid_action = new IfThenAction($action, new EnableAction($next));
		$not_valid_action = new IfThenAction(new NotAction($action), new DisableAction($next));

		for($i=0;$i<count($users);$i++){
			$users[$i]->addKeyUpAction($valid_action);
			$users[$i]->addKeyUpAction($not_valid_action);
			$users[$i]->addKeyPressAction(new LowerAlphaNumericOnlyAction());
			$emails[$i]->addKeyUpAction($valid_action);
			$emails[$i]->addKeyUpAction($not_valid_action);
		}
		$content->add($inputs);

		$ret = array();
		$ret["title"] = new Text("New User Info");
		$ret["content"] = $content;
		$ret["next"] = $next;
		$steps[] = $ret;

		// Step 7
		$content = new GridPanel(1);
		$content->setCellStyle(new Style("first_time_content_text"));
		$content->getCellStyle()->setPaddingBottom(3);
		$content->add(new Text("That's it! Just click [Done] and enjoy the calendar!"));

		$ret = array();
		$ret["title"] = new Text("Finished!");
		$ret["content"] = $content;
		$ret["next"] = false;
		$steps[] = $ret;

		return $steps;
	}
}
?>