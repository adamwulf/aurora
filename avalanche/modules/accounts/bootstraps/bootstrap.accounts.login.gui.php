<?

class module_bootstrap_accounts_login_gui extends module_bootstrap_module{

	private $time_inc;
	private $column_width;
	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("Aurora Day View");
		$this->setInfo("returns the day view of this calendar");
		$this->time_inc = 30;
		$this->column_width = "120px";
		
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

			
			
			$name_error = false;
			$title_error = false;
			$email_error = false;
			if(isset($data_list["username"]) && isset($data_list["password"])){
				$this->avalanche->logIn($data_list["username"], $data_list["password"]);
				header("Location: " . $this->avalanche->HOSTURL() . "accounts/index.php");
				exit;
			}else if(isset($data_list["logout"])){
				$this->avalanche->logOut();
				header("Location: " . $this->avalanche->HOSTURL() . "accounts/index.php");
				exit;
			}
			
			
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/gui/os/loginstyle.css"));
			$this->doc->addStyleSheet($css);
			
			
			// styles
			$title_bar_style = new Style("header_cell");
			
			$input_style = new Style();
			$input_style->setBorderWidth(1);
			$input_style->setBorderStyle("solid");
			$input_style->setBorderColor("black");
			
			// panels

			
			$login = new Panel();
			$login->setAlign("right");
			$login->setWidth("100%");
			$login->setStyle($title_bar_style);
			
			
			$form = new FormPanel("index.php");
			
			if(!$this->avalanche->loggedInHuh()){
				$inputs = new GridPanel(3);
				$inputs->getCellStyle()->setPaddingRight(4);
				
				$username = new SmallTextInput();
				$username->setStyle($input_style);
				$username->setSize(8);
				$username->setValue("username");
				$username->setName("username");
				
				$password = new SmallTextInput();
				$password->setPassword(true);
				$password->setStyle($input_style);
				$password->setSize(8);
				$password->setValue("password");
				$password->setName("password");
				
				$inputs->add($username);
				$inputs->add($password);
				$inputs->add(new Text("<input type='submit' value='login' style='border: 1px solid black;'>"));
			}else{
				$inputs = new GridPanel(3);
				$inputs->getCellStyle()->setPaddingRight(4);
				$username = $this->avalanche->getUsername($this->avalanche->loggedInHuh());
				$inputs->add(new Text("<input type='submit' value='logout:$username' style='border: 1px solid black;'>"));
				
				$form->addHiddenField("logout", "1");
			}
			
			$form->add($inputs);
			
			$login->add($form);
			
			return new module_bootstrap_data($login, "a gui component for the login");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>