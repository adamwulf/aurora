<?
class module_bootstrap_strongcal_pageheader extends module_bootstrap_module{
	
	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Header");
		$this->setInfo("returns the Gui Component for the Aurora Header.
				(include links to views etc)");
				
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			$module = false;
			$strongcal = $this->avalanche->getModule("strongcal");
			$os = $this->avalanche->getModule("os");
			
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/header_style.css"));
			$this->doc->addStyleSheet($css);
			
			
			/*************************************************************************
			get the variables from the form input
			this code might come in handy later
			**************************************************************************/
			$nothing = new Text("");
			$nothing->getStyle()->setDisplayNone();
			$nothing = new module_bootstrap_data($nothing, "a blank text");
			
			if(!isset($data_list["date"])){
				$data_list["date"] = date("Y-m-d", $strongcal->localtimestamp());
			}
			$date = $data_list["date"];
			$date_stamp = mktime(0,0,0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
			
			if(!isset($data_list["view"])){
				$data_list["view"] = "month";
			}
			$view = $data_list["view"];
			
			if(isset($data_list["page"])){
				return $nothing;
			}else if($view == "inviteusers"){
				return $nothing;
			}else if($view == "month"){
				$date_formatted = date("F, Y", $date_stamp);
			}else if($view == "day"){
				$date_formatted = date("l, F jS, Y", $date_stamp);
			}else if($view == "week"){
				$first_of_week = mktime(0,0,0,date("m", $date_stamp), date("d", $date_stamp) - date("w", $date_stamp), date("Y", $date_stamp));
				$last_of_week = mktime(0,0,0,date("m", $first_of_week), date("d", $first_of_week) + 6, date("Y", $first_of_week));
				
				$date_formatted  = date("l, F jS", $first_of_week);
				$date_formatted .= " through ";
				$date_formatted .= date("l, F jS", $last_of_week);
			}else if($view == "event"){
				return $nothing;
			}else if($view == "first_login"){
				return $nothing;
			}else if($view == "overview"){
				$date_formatted = "Hello, " . $os->getUsername($this->avalanche->getActiveUser()) . "!";
			}else if($view == "preferences"){
				$date_formatted = "Preferences";
			}else if($view == "faq"){
				$date_formatted = "Frequently Asked Questions";
			}else if($view == "login"){
				return $nothing;
			}else if($view == "about"){
				return $nothing;
			}else if($view == "task"){
				return $nothing;
			}else if($view == "edit_event"){
				return $nothing;
			}else if($view == "edit_task"){
				return $nothing;
			}else if($view == "add_event_step_1"){
				return $nothing;
			}else if($view == "add_event_step_2"){
				return $nothing;
			}else if($view == "add_event_step_3"){
				return $nothing;
			}else if($view == "add_task_step_1"){
				return $nothing;
			}else if($view == "add_task_step_2"){
				return $nothing;
			}else if($view == "add_task_step_3"){
				return $nothing;
			}else if($view == "search"){
				$date_formatted = "Search Results";
			}else if($view == "manage_cals"){
				$date_formatted = "Calendar Management";
			}else if($view == "manage_teams"){
				$date_formatted = "Group Management";
			}else if($view == "user_profile"){
				return $nothing;
			}else if($view == "manage_users"){
				$date_formatted = "User Management";
			}else if($view == "delete_event_step_1"){
				return $nothing;
			}else if($view == "delete_event_step_2"){
				return $nothing;
			}else{
				return $nothing;
			}
			
			/*************************************************************************
			end getting the variables from the form input
			**************************************************************************/

			/*************************************************************************
			initialize components
			**************************************************************************/
			$my_container = new Panel();

			/*************************************************************************
			initialize styles
			**************************************************************************/
			$style = new Style("page_header");
			
			/*************************************************************************
			apply styles
			**************************************************************************/
			$my_container->setStyle($style);
			$my_container->setWidth("100%");
			
			/*************************************************************************
			add content
			**************************************************************************/
			$my_container->add(new Text($date_formatted));
			
			
			return new module_bootstrap_data($my_container, "the gui component for the aurora header");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}
}
?>