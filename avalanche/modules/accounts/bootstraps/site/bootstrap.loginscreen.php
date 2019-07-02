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
class LoginScreen extends module_bootstrap_module{

	private $account;
	private $error;
	function __construct($avalanche, $account, $error = false){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}

		$this->setName("Primary Customer Account Management Loader");
		$this->setInfo("");
		$this->avalanche = $avalanche;
		$this->account = $account;
		$this->error = $error;
	}

	function run($data = false){
		if(is_object($data) && is_array($data->data())){
			$data_list = $data->data();
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a bootstrap data with surrounding an array");
		}

		// customer login
		$login_panel = new SimplePanel();

		if(is_object($this->error)){
			$error = new Text("Please enter an account name");
			$error->setStyle(new Style("loginPageError"));
			$login_panel->add($error);
		}

		$form_panel = new FormPanel("member.php");
		$form_panel->setStyle(new Style("loginPageForm"));
		$form_panel->addHiddenField("page", "members");
		$form_panel->addHiddenField("submit", "1");
		$form_panel->addHiddenField("login", "1");
		$form_panel->addHiddenField("testserver", (string) (isset($data_list["testserver"]) && $data_list["testserver"]));
		$form_panel->setAsPost();

		$acct_name = new SmallTextInput();
		$acct_name->setName("account");
		$acct_name->setSize(20);
		$acct_name->setStyle(new Style("loginPageInput"));
		if(is_object($this->account)){
			$acct_name->setValue($this->account->name());
		}

		$username = new SmallTextInput();
		$username->setName("username");
		$username->setSize(20);
		$username->setStyle(new Style("loginPageInput"));
		$password = new SmallTextInput();
		$password->setName("password");
		$password->setSize(20);
		$password->setPassword(true);
		$password->setStyle(new Style("loginPageInput"));

		$submit = new Text("<input type='image' src='images/login.gif' value='Log In' class='formButton'>");

		$acct_label_holder = new SimplePanel();
		$acct_label_holder->setStyle(new Style("loginPageLabel"));
		$acct_label_holder->add(new Text("Account Name: "));
		$username_label_holder = new SimplePanel();
		$username_label_holder->setStyle(new Style("loginPageLabel"));
		$username_label_holder->add(new Text("Username: "));
		$password_label_holder = new SimplePanel();
		$password_label_holder->setStyle(new Style("loginPageLabel"));
		$password_label_holder->add(new Text("Password: "));

		$pair = new SimplePanel();
		$pair->setStyle(new Style("formPair"));
		$pair->add($acct_label_holder);
		$pair->add($acct_name);
		$form_panel->add($pair);
		$pair = new SimplePanel();
		$pair->setStyle(new Style("formPair"));
		$pair->add($username_label_holder);
		$pair->add($username);
		$form_panel->add($pair);
		$pair = new SimplePanel();
		$pair->setStyle(new Style("formPair"));
		$pair->add($password_label_holder);
		$pair->add($password);
		$form_panel->add($pair);
		$form_panel->add($submit);

		$login_panel->add($form_panel);

		$memberContent = new SimplePanel();
		$memberContent->setStyle(new Style("memberContent"));
		$memberContent->add($login_panel);
		$stockPhotoContainer = new SimplePanel();
		$stockPhotoContainer->setStyle(new Style("stockPhotoContainer"));
		$icon = new Icon("images/login_stock.jpg");
		$icon->setStyle(new Style("stockPhoto"));
		$stockPhotoContainer->add($icon);

		$loginContentBlock = new SimplePanel();
		$loginContentBlock->setStyle(new Style("loginContentBlock"));
		$loginContentBlock->add($memberContent);


		return new module_bootstrap_data($loginContentBlock, "asdf");
	}
}
?>