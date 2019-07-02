<?
/**
 * This module is in charge of loading the entire page.
 *
 * it will end up running 2 bootstraps. the first will load the os header.
 * the second will load the content. the os header bootstrap will be passed in the URL,
 * as will the content loader.
 *
 * this loader will return an html page. it will be a table with two rows, one cell each.
 * the top cell will be the os header. the bottom cell will be the content.
 */
class module_bootstrap_os_login_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $d){
		$this->setName("Login Panel");
		$this->setInfo("returns the gui component for the OS Login Panel");
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->doc = $d;
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		$timer = new Timer();
		if(is_array($data->data())){
			$data_list = $data->data();
			$login_error = $this->avalanche->getCookie("login_error");
			$cell_style = new Style("login_main");
			$main_panel = new Panel();
			$main_panel->setWidth("100%");
			$main_panel->setValign("top");
			$main_panel->setAlign("center");
			$main_panel->setStyle($cell_style);
			
			// show error
			if($login_error){
				$error_msg = new Text("wrong username or password<br>");
				$error_msg->getStyle()->setFontColor("#990000");
				$login_title = new Text("&nbsp;<b>Whoops! Please Log In.</b>");
			}else if(isset($data_list["email"]) && isset($data_list["success"]) && $data_list["success"]){
				$error_msg = new Text("Your new password has been emailed to you.<br>");
				$login_title = new Text("&nbsp;<b>Welcome! Please Log In.</b>");
			}else{
				$error_msg = new Text("&nbsp;<br>");
				$login_title = new Text("&nbsp;<b>Welcome! Please Log In.</b>");
			}
			$error_msg->getStyle()->setFontFamily("verdana, sans-serif");
			$error_msg->getStyle()->setFontSize(7);
			$login_title->getStyle()->setFontFamily("verdana, sans-serif");
			$login_title->getStyle()->setFontSize(8);
			$main_panel->add($login_title);
			
			$logo_panel = new GridPanel(3);
			$logo_panel->setAlign("center");
			$logo_panel->setValign("middle");
			$logo_panel->setWidth("100%");
			$logo_panel->getStyle()->setHeight("116px");
			
			$bar = new Panel();
			$bar->setStyle(new Style("login_bar_style"));
			
			$logo = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/logo.jpg");
			$logo->setStyle(new Style("login_logo_style"));
			$logo_panel->add($bar);
			$logo_panel->add($logo);
			$logo_panel->add($bar);
			$main_panel->add($logo_panel);
			
			
			
			$find_password_panel = new GridPanel(1);
			$find_password_panel_form = new FormPanel("index.php");
			$login_panel = new GridPanel(1);
			$login_panel->getStyle()->setWidth("320px");
			$login_panel->setAlign("center");
			$login_panel_form = new FormPanel("index.php");
			$login_panel_form->setWidth("100%");
			$login_panel_form->setName("login_form");
			$login_panel_form->setValign("top");
			$login_panel_form->setAlign("center");
			$login_panel_form->getStyle()->setFontFamily("verdana, sans-serif");
			$login_panel_form->getStyle()->setFontSize(8);
			$login_panel_form->add($login_panel);
			
			// add hidden fields
			if(!isset($data_list["to_page"])){
				$login_panel_form->addHiddenField("page", $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"]);
			}else{
				$login_panel_form->addHiddenField("page", $data_list["to_page"]);
			}
			if($this->avalanche->loggedInHuh()){
				$login_panel_form->addHiddenField("logout", "1");
			}else{
				$login_panel_form->addHiddenField("view", "login");
			}
			$forgot_link = new Link("forgot your password?", "javascript:;");
			$forgot_link->addAction(new DisplayNoneAction($login_panel_form));
			$forgot_link->addAction(new DisplayBlockAction($find_password_panel_form));
			$login_panel_form->add($forgot_link);
			
			$find_password_panel->getStyle()->setWidth("320px");
			$find_password_panel->setAlign("center");
			$find_password_panel_form->getStyle()->setDisplayNone();
			$find_password_panel_form->getStyle()->setWidth("320px");
			$find_password_panel_form->getStyle()->setFontFamily("verdana, sans-serif");
			$find_password_panel_form->getStyle()->setFontSize(8);
			$find_password_panel_form->setName("forgot_form");
			$find_password_panel_form->setValign("top");
			$find_password_panel_form->setAlign("center");
			$find_password_panel_form->add($find_password_panel);
			$find_password_panel_form->addHiddenField("view", "login");
			
			if(isset($data_list["email"]) && isset($data_list["success"]) && !$data_list["success"]){
				$login_panel_form->getStyle()->setDisplayNone();
				$find_password_panel_form->getStyle()->setDisplayBlock();
				$text = new Text("Your email address is not in our system.<br>");
			}else{
				$text = new Text("Tell me your email address and I will send you your password.<br>");
			}
			$text->getStyle()->setFontFamily("verdana, sans-serif");
			$text->getStyle()->setFontSize(7);
			$find_password_panel->add($text);
			$email = new SmallTextInput();
			$email->setSize(26);
			$email->setName("email");
			$email->setStyle(new Style("login_input"));
			if(isset($data_list["email"])){
				$email->setValue($data_list["email"]);
			}
			
			$forgot_inputs = new GridPanel(2);
			$forgot_inputs->getStyle()->setMarginTop(12);
			$forgot_inputs->getStyle()->setHeight("48px");
			$forgot_inputs->getCellStyle()->setPadding(2);
			$forgot_inputs->getCellStyle()->setFontFamily("verdana, sans-serif");
			$forgot_inputs->getCellStyle()->setFontSize(9);
			$forgot_inputs->setAlign("right");
			$forgot_inputs->add(new Text("email:"));
			$forgot_inputs->add($email);
			$find_password_panel->add($forgot_inputs);
			
			$cancel_link = new Link("cancel", "javascript:;");
			$cancel_link->addAction(new DisplayNoneAction($find_password_panel_form));
			$cancel_link->addAction(new DisplayBlockAction($login_panel_form));
			$find_password_panel->add(new Text("<input style='border: 1px solid black;' type='submit' name='find' value='find it'>"));
			$find_password_panel_form->add($cancel_link);
			
			
			$login_panel->add($error_msg);
			$username = new SmallTextInput();
			$username->setSize(16);
			$username->setName("user");
			$username->setStyle(new Style("login_input"));
			if(isset($data_list["user"])){
				$username->setValue($data_list["user"]);
			}
			$this->doc->addAction(new FocusAction($username));
			
			$password = new SmallTextInput();
			$password->setSize(16);
			$password->setPassword(true);
			$password->setName("pass");
			$password->setStyle(new Style("login_input"));
			
			$inputs = new GridPanel(2);
			$inputs->getStyle()->setMarginTop(12);
			$inputs->getStyle()->setHeight("48px");
			$inputs->getCellStyle()->setPadding(2);
			$inputs->getCellStyle()->setFontFamily("verdana, sans-serif");
			$inputs->getCellStyle()->setFontSize(9);
			$inputs->setAlign("right");
			$inputs->add(new Text("username:"));
			$inputs->add($username);
			$inputs->add(new Text("password:"));
			$inputs->add($password);
			
			if($this->avalanche->loggedInHuh()){
				$login_panel->add(new Text("<input style='border: 1px solid black;' type='submit' name='submit' value='logout'>"));
			}else{
				$login_panel->add($inputs);
				$login_panel->add(new Text("<input style='border: 1px solid black;' type='submit' name='login' value='login'>"));
			}
			
			$main_panel->add($login_panel_form);
			$main_panel->add($find_password_panel_form);
			$main_panel = new ErrorPanel($main_panel);
			$main_panel->getStyle()->setHeight("420px");
			return new module_bootstrap_data($main_panel, "the login panel gui component");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be an array of form input.<br>");
		}
	}
}
?>