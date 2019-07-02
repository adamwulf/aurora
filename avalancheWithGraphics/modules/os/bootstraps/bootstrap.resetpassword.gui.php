<?

class module_bootstrap_os_resetpassword_gui extends module_bootstrap_module{

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

			$error = false;
			try{
				// if they submitted, then let's figure everything out
				if(isset($data_list["submit"])){
					$user_id = $this->avalanche->getActiveUser();
					
					$reader = new SmallTextInput();
					$reader->setName("password");
					$reader->loadFormValue($data_list);
					$password = $reader->getValue();
					$reader->setName("confirm");
					$reader->loadFormValue($data_list);
					$confirm = $reader->getValue();
					
					if($password == $confirm){
						// update the password
						$this->avalanche->getUser($user_id)->password($password);
					}else{
						throw new CannotEditUserException("Password and Confirm need to be the same word");
					}
					$data_list["subview"] = "done";
				}
			}catch(CannotEditUserException $e){
				$error = $e;
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
				$content->add(new Text("Your password has been reset.<br><br>"));
				$content->add(new Link("Continue", "index.php"));
				
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
				$form->addHiddenField("page", "index.php");
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
		$ret["title"] = new Text("Reset Your Password");
		$ret["content"] = new Text("It looks like your password needs to be reset. To get started, click the [Next] button below.");
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
		$content->add(new Text("Note: passwords <i>are</i> case sensitive."));
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