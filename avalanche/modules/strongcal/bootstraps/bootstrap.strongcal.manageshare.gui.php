<?

class module_bootstrap_strongcal_manageshare_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $calendar;

	function __construct($avalanche, Document $doc, $calendar){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_object($calendar)){
			throw new IllegalArgumentException("");
		}
		$this->setName("Aurora HTML to manage fields");
		$this->setInfo("this module takes raw form input");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->calendar = $calendar;
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

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_cal";
			}
			
			if(isset($data_list["subsubview"])){
				$subsubview = $data_list["subsubview"];
			}else{
				$subsubview = "event";
			}

			$main_cal_obj = $this->calendar;
			/** end initializing the input */			

			$input_style = new Style();
			$input_style->setBorderWidth(1);
			$input_style->setBorderColor("black");
			$input_style->setBorderStyle("solid");
			
			/************************************************************************
			************************************************************************/
			
			if($main_cal_obj->canWriteName() || $main_cal_obj->canWriteValidations()){

				$tabbed_body = new TabbedPanel();
				
				$this->addEventTab($tabbed_body, $main_cal_obj, $data_list);
				//$this->addFieldTab($tabbed_body, $main_cal_obj, $data_list);
				$this->addCommentTab($tabbed_body, $main_cal_obj, $data_list);
				//$this->addModeratorTab($tabbed_body, $main_cal_obj, $data_list);
				$this->addAdminTab($tabbed_body, $main_cal_obj, $data_list);
				
				$tabbed_body->setWidth("100%");
				$tabbed_body->setHolderStyle(new Style("cal_holder"));
				$tabbed_body->setContentStyle(new Style("sharing_content"));
				$this->doc->addFunction($tabbed_body->getCloseFunction());
				
				if($subsubview == "event"){
					$tabbed_body->selectTab(1);
				// }else if($subsubview == "field"){
					// $tabbed_body->selectTab(2);
				}else if($subsubview == "comment"){
					$tabbed_body->selectTab(2);
				// }else if($subsubview == "validation"){
					// $tabbed_body->selectTab(4);
				}else if($subsubview == "name"){
					$tabbed_body->selectTab(3);
				// }else{
					// $tabbed_body->selectTab(1);
				}
				$output_panel = $tabbed_body;
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to manage sharing this calendar."));
				$error = new ErrorPanel($content);
				$output_panel = $error;
			}
			
			/************************************************************************
			 put it all together
			************************************************************************/
			
			return new module_bootstrap_data($output_panel, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
	
	private function addEventTab($tab_panel, $calendar, $data_list){
		$open_button = new Button("Events and Tasks");
		$open_button->setStyle(new Style("cal_tab_light"));
		$close_button = new Button("Events and Tasks");
		$close_button->setStyle(new Style("cal_tab_dark"));
		
		
		$module = new module_bootstrap_strongcal_change_event_transparency_gui($this->avalanche, $this->doc, $calendar);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
		$cal_info = $data->data();

		$tab_panel->add($cal_info, $open_button, $close_button);
	}

	private function addCommentTab($tab_panel, $calendar, $data_list){
		$open_button = new Button("Comments");
		$open_button->setStyle(new Style("cal_tab_light"));
		$close_button = new Button("Comments");
		$close_button->setStyle(new Style("cal_tab_dark"));
		
		
		$module = new module_bootstrap_strongcal_change_comment_transparency_gui($this->avalanche, $this->doc, $calendar);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
		$cal_info = $data->data();

		$tab_panel->add($cal_info, $open_button, $close_button);
	}

	private function addFieldTab($tab_panel, $calendar, $data_list){
		$open_button = new Button("Fields");
		$open_button->setStyle(new Style("cal_tab_light"));
		$close_button = new Button("Fields");
		$close_button->setStyle(new Style("cal_tab_dark"));
		
		
		$module = new module_bootstrap_strongcal_change_custom_fields_gui($this->avalanche, $this->doc, $calendar);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
		$cal_info = $data->data();

		$tab_panel->add($cal_info, $open_button, $close_button);
	}

	private function addModeratorTab($tab_panel, $calendar, $data_list){
		$open_button = new Button("Moderators");
		$open_button->setStyle(new Style("cal_tab_light"));
		$close_button = new Button("Moderators");
		$close_button->setStyle(new Style("cal_tab_dark"));
		
		
		$module = new module_bootstrap_strongcal_change_validation_gui($this->avalanche, $this->doc, $calendar);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
		$cal_info = $data->data();

		$tab_panel->add($cal_info, $open_button, $close_button);
	}


	private function addAdminTab($tab_panel, $calendar, $data_list){
		$open_button = new Button("Administrators");
		$open_button->setStyle(new Style("cal_tab_light"));
		$close_button = new Button("Administrators");
		$close_button->setStyle(new Style("cal_tab_dark"));
		
		
		$module = new module_bootstrap_strongcal_change_administration_gui($this->avalanche, $this->doc, $calendar);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
		$cal_info = $data->data();

		$tab_panel->add($cal_info, $open_button, $close_button);
	}
}
?>