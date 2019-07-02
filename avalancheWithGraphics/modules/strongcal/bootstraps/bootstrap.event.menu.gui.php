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
class StrongcalEventMenu extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $trigger;
	private $cal_id;
	private $event_id;
	
	private static $menus;
	
	function __construct($avalanche, Document $doc, Component $trigger, $cal_id, $event_id){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("OS User Menu");
		$this->setInfo("returns the component for the usermenu");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->trigger = $trigger;
		$this->cal_id = $cal_id;
		$this->event_id = $event_id;
		if(!is_object(StrongcalEventMenu::$menus)){
			StrongcalEventMenu::$menus = new HashTable();
		}
	}

	function run($data = false){
		if($data === false){
			$strongcal = $this->avalanche->getModule("strongcal");
			$cal = $strongcal->getCalendarFromDb($this->cal_id);
			$event = $cal->getEvent($this->event_id);
			
			/************************************************************************
			    initialize menu
			************************************************************************/
			if(!is_object(StrongcalEventMenu::$menus->get($this->cal_id . "_" . $this->event_id))){
				$profile = new BorderPanel();
				$profile_link = new Button("View Event");
				$profile_link->setAlign("left");
				$profile_link->setStyle(new Style("menu_button"));
				$profile_link->getStyle()->setWidth("120px");
				$profile_link->addAction(new LoadPageAction("index.php?view=event&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id));
				$profile->setCenter($profile_link);
				// $profile_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/profile.gif");
				// $profile->setWest($profile_icon);
				
				$email = new BorderPanel();
				$email_link = new Button("Edit Event");
				$email_link->setAlign("left");
				$email_link->setStyle(new Style("menu_button"));
				$email_link->getStyle()->setWidth("120px");
				$email_link->addAction(new LoadPageAction("index.php?view=edit_event&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id));
				$email->setCenter($email_link);
				// $email_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/email.gif");
				// $email->setWest($email_icon);
				
				// $sms = new BorderPanel();
				// $sms_link = new Button("Delete Event");
				// $sms_link->setAlign("left");
				// $sms_link->setStyle(new Style("menu_button"));
				// $sms_link->getStyle()->setWidth("120px");
				// $profile_link->addAction(new LoadPageAction("index.php?view=delete_event&cal_id=" . $this->cal_id . "&event_id=" . $this->event_id));
				// $sms->setCenter($sms_link);
				// // $sms_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/phone.gif");
				// // $sms->setWest($sms_icon);
				
				$menu_panel = new GridPanel(1);
				$menu_panel->add($profile);
				if($cal->canWriteEvent($this->event_id)){
					$menu_panel->add($email);
					// $menu_panel->add($sms);
				}
				$menu_panel->setStyle(new Style("xMenu"));
				$menu_panel->getStyle()->setWidth("120px");
				$menu_panel->getStyle()->setBackground("silver");
				$this->doc->addHidden($menu_panel);
				StrongcalEventMenu::$menus->put($this->cal_id . "_" . $this->event_id, $menu_panel);
			}else{
				$menu_panel = StrongcalEventMenu::$menus->get($this->cal_id . "_" . $this->event_id);
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