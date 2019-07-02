<?

class module_bootstrap_os_adduserview_gui extends module_bootstrap_module{

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
		
		$this->setName("Add user view for Avalanche");
		$this->setInfo("adds an user to avalanche.");
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

			/************************************************************************
			get modules
			************************************************************************/
			$strongcal = $this->avalanche->getModule("strongcal");
			$buffer = $this->avalanche->getSkin("buffer");

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/add_cal_style.css"));
			$this->doc->addStyleSheet($css);

			$error = false;
			// check form input to see if we need to add a team....
			if(isset($data_list["submit"])){
				try{
					if(!isset($data_list["submit"]) ||
					   !isset($data_list["submit"])){
						throw new IllegalArgumentException("arguments \$name and \$color must be sent in via GET or POST to add a calendar");
					}else{
						$name = array();
						$name["title"] = $data_list["title"];
						$name["first"] = $data_list["first"];
						$name["middle"] = $data_list["middle"];
						$name["last"] = $data_list["last"];
	
						$username = $data_list["username"];
						$pass = $data_list["password"];
						$conf = $data_list["confirm"];
						$mail = $data_list["email"];
						if($pass != $conf){
							throw new CannotAddUserException("Password and Confirmation must be the same");
						}
						if(strlen($username) > 0 && strlen($mail) > 0){
							$user_id = $this->avalanche->addUser($username, $pass, $mail);
							$this->avalanche->updateName($user_id, $name);
						}else if(strlen($username) <= 0){
							throw new CannotAddUserException("Username must not be blank when trying to add user");
						}else if(strlen($mail) <= 0){
							throw new CannotAddUserException("Email must not be blank when trying to add user");
						}
						
						if($user_id !== false){
							// redirect the page
							throw new RedirectException("index.php?view=manage_users&user_id=$user_id");
						}else{
							throw new Exception("Error adding user");
						}
					}
				}catch(CannotAddUserException $e){
					$error = $e;
				}
			}
			
			
			

			/************************************************************************
			    initialize panels
			************************************************************************/
			$my_form = new FormPanel("index.php");
			$my_form->addHiddenField("view", "manage_users");
			$my_form->addHiddenField("subview", "add_user");
			$my_form->addHiddenField("submit", "1");
			$my_form->setAsGet();
			$my_container = new GridPanel(1);
			
			$username_choose = new GridPanel(1);
			$description_main = new GridPanel(1);
			$button_panel = new GridPanel(1);
			
			$button_row = new Panel();
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			$my_container->setValign("top");
			
			$description_main->getStyle()->setClassname("edit");
			$description_main->getCellStyle()->setPadding(4);
			$description_main->getStyle()->setWidth("450px");
			
			$username_choose->getStyle()->setClassname("edit");
			$username_choose->getStyle()->setHeight("50px");
			$username_choose->getStyle()->setWidth("450px");
			$username_choose->getCellStyle()->setPadding(4);
			$username_choose->setValign("top");
			
			$button_panel->getStyle()->setClassname("edit");
			$button_panel->getStyle()->setHeight("50px");
			$button_panel->getStyle()->setWidth("450px");
			$button_panel->getCellStyle()->setPadding(4);
			$button_panel->setValign("middle");
			$button_panel->setAlign("center");
			
			
			/************************************************************************
			    add necessary text and html
			************************************************************************/
			
			$cal_name_input = new SmallTextInput();
			$cal_name_input->setName("username");
			$cal_name_input->setSize(30);
			$cal_name_input->getStyle()->setClassname("calendar_input");
			if(isset($data_list["username"])){
				$cal_name_input->setValue($data_list["username"]);
			}
			
			$username_choose->add(new Text("Username:"));
			$username_choose->add($cal_name_input);
			
			$name_inputs = new GridPanel(4);
			$name_inputs->getStyle()->setClassname("edit_text");
			$name_inputs->getCellStyle()->setPaddingRight(2);
			$name_inputs->getCellStyle()->setPaddingBottom(1);
			$name_inputs->add(new Text("Title"));
			$name_inputs->add(new Text("First"));
			$name_inputs->add(new Text("Middle"));
			$name_inputs->add(new Text("Last"));
			$title_input = new DropDownInput();
			$title_input->setName("title");
			$title_input->addOption(new DropDownOption("None", ""));
			$title_input->addOption(new DropDownOption("Mr.", "Mr."));
			$title_input->addOption(new DropDownOption("Ms.", "Ms."));
			$title_input->addOption(new DropDownOption("Mrs.", "Mrs."));
			$title_input->addOption(new DropDownOption("Dr.", "Dr."));
			$title_input->getStyle()->setClassname("calendar_input");
			if(isset($data_list["title"])){
				$title_input->setValue($data_list["title"]);
			}
			$first_input = new SmallTextInput();
			$first_input->setSize(10);
			$first_input->setName("first");
			$first_input->getStyle()->setClassname("calendar_input");
			if(isset($data_list["first"])){
				$first_input->setValue($data_list["first"]);
			}
			$middle_input = new SmallTextInput();
			$middle_input->setSize(10);
			$middle_input->setName("middle");
			$middle_input->getStyle()->setClassname("calendar_input");
			if(isset($data_list["middle"])){
				$middle_input->setValue($data_list["middle"]);
			}
			$last_input = new SmallTextInput();
			$last_input->setSize(10);
			$last_input->setName("last");
			$last_input->getStyle()->setClassname("calendar_input");
			if(isset($data_list["last"])){
				$last_input->setValue($data_list["last"]);
			}
			
			$name_inputs->add($title_input);
			$name_inputs->add($first_input);
			$name_inputs->add($middle_input);
			$name_inputs->add($last_input);


			$password_input = new SmallTextInput();
			$password_input->setPassword(true);
			$password_input->setName("password");
			$password_input->setSize(30);
			$password_input->getStyle()->setClassname("calendar_input");
			
			$description_main->add(new Text("Name: "));
			$description_main->add($name_inputs);
			$description_main->add(new Text("Password: "));
			$description_main->add($password_input);
			
			$confirm_input = new SmallTextInput();
			$confirm_input->setPassword(true);
			$confirm_input->setName("confirm");
			$confirm_input->setSize(30);
			$confirm_input->getStyle()->setClassname("calendar_input");
			$description_main->add(new Text("Confirm: "));
			$description_main->add($confirm_input);
			
			$email_input = new SmallTextInput();
			$email_input->setName("email");
			$email_input->setSize(30);
			$email_input->getStyle()->setClassname("calendar_input");
			$description_main->add(new Text("Email: "));
			$description_main->add($email_input);
			
			$button_panel->add(new Text("<input type='submit' value='Go!' class='go_button'>"));
			
			/************************************************************************
			put it all together
			************************************************************************/
			
			if(is_object($error)){
				$my_container->add(new Text($error->getMessage()));
			}
			$my_container->add($username_choose);
			$my_container->add($description_main);
			$my_container->add($button_panel);
			
			$my_form->add($my_container);
			
			return new module_bootstrap_data($my_form, "a gui component for the add calendar view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>