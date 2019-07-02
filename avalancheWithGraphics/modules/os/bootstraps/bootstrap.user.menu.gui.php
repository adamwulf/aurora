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
class OSUserMenu extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $trigger;
	private $user_id;
	
	private static $menus;
	
	function __construct($avalanche, Document $doc, Component $trigger, $user_id){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("OS User Menu");
		$this->setInfo("returns the component for the usermenu");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->trigger = $trigger;
		$this->user_id = $user_id;
		if(!is_object(OSUserMenu::$menus)){
			OSUserMenu::$menus = new HashTable();
		}
	}

	function run($data = false){
		if($data === false){
			$os = $this->avalanche->getModule("os");
			$main_user = $this->avalanche->getUser($this->user_id);

			/************************************************************************
			    initialize menu
			************************************************************************/
			if(!is_object(OSUserMenu::$menus->get($this->user_id))){
				$profile = new BorderPanel();
				$profile_link = new Button("Profile");
				$profile_link->setAlign("left");
				$profile_link->setStyle(new Style("menu_button"));
				$profile_link->getStyle()->setWidth("110px");
				$profile_link->addAction(new LoadPageAction("index.php?view=manage_users&user_id=" . $this->user_id));
				$profile->setCenter($profile_link);
				$profile_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/profile.gif");
				$profile_icon = new ErrorPanel($profile_icon);
				$profile_icon->getStyle()->setWidth("24px");
				$profile_icon->getStyle()->setClassname("menu_icon");
				$profile->setWest($profile_icon);
				
				$email = new BorderPanel();
				$email_link = new Button("Email");
				$email_link->setAlign("left");
				$email_link->setStyle(new Style("menu_button"));
				$email_link->getStyle()->setWidth("110px");
				$email_link->addAction(new LoadPageAction("index.php?view=manage_users&user_id=" . $this->user_id . "&subview=email"));
				$email->setCenter($email_link);
				$email_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/email.gif");
				$email_icon = new ErrorPanel($email_icon);
				$email_icon->getStyle()->setWidth("24px");
				$email_icon->getStyle()->setClassname("menu_icon");
				$email->setWest($email_icon);
				
				$sms = new BorderPanel();
				$sms_link = new Button("SMS");
				$sms_link->setAlign("left");
				$sms_link->setStyle(new Style("menu_button"));
				$sms_link->getStyle()->setWidth("110px");
				$sms_link->addAction(new LoadPageAction("index.php?view=manage_users&user_id=" . $this->user_id . "&subview=text"));
				$sms->setCenter($sms_link);
				$sms_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/phone.gif");
				$sms_icon = new ErrorPanel($sms_icon);
				$sms_icon->getStyle()->setWidth("24px");
				$sms_icon->getStyle()->setClassname("menu_icon");
				$sms->setWest($sms_icon);
				
				$menu_panel = new GridPanel(1);
				$menu_panel->add($profile);
				if(strlen($main_user->email())){
					$menu_panel->add($email);
				}
				if(strlen($main_user->sms())){
					$menu_panel->add($sms);
				}
				$menu_panel->setStyle(new Style("xMenu"));
				$menu_panel->getStyle()->setWidth("120px");
				$menu_panel->getStyle()->setBackground("#EEEEEE");
				$this->doc->addHidden($menu_panel);
				OSUserMenu::$menus->put($this->user_id, $menu_panel);
			}else{
				$menu_panel = OSUserMenu::$menus->get($this->user_id);
			}
			
			$this->trigger->getStyle()->setClassname("xTrigger");
			$menu_action = new MenuInitAction($this->trigger, $menu_panel);
			$this->doc->addAction($menu_action);
			
			return false;
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be false.<br>");
		}
	}

}
?>