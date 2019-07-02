<?

class module_bootstrap_strongcal_managecals_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List to HTML");
		$this->setInfo("this module takes as input an array of calendar objects. the output is a very basic
				html list of the calendars.");
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

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_cal";
			}
			
			if(isset($data_list["subsubview"])){
				$subsubview = $data_list["subsubview"];
			}else{
				$subsubview = "simple";
			}
			
			/** end initializing the input */			

			/**
			 * get the list of calendars
			 */
			$data = false; // send in false as the default value
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_obj_list = $data->data();
			
			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/manage_cals.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			
			$nobg = new Style("nobg");
			$info_style = new Style("info");
			
			$title_style = new Style();
			$title_style->setFontFamily("verdana, sans-serif");
			$title_style->setFontSize(10);
			$title_style->setFontWeight("bold");

			/************************************************************************
			    initialize panels
			************************************************************************/
			
			$cal_info_panel = new BorderPanel();
			
			/************************************************************************
			************************************************************************/
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			/** done making calendar list for the left side **/
			
			
			$cal_info_panel->getStyle()->setWidth("450px");
			$cal_info_panel->getStyle()->setHeight("450px");
			$cal_info_panel->getStyle()->setBorderWidth(1);
			$cal_info_panel->getStyle()->setBorderStyle("solid");
			$cal_info_panel->getStyle()->setBorderColor("black");
			
			if($subview == "hide_cal"){
				$module = new module_bootstrap_strongcal_hidecalendarview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				// this will have redirected us so we should throw an error if that didn't happen
				throw new Exception("hide/show calendar should have redirected, but did not");
			}
			if($subview == "delete_cal" && isset($data_list["cal_id"])){
				$module = new module_bootstrap_strongcal_deletecalendarview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$data = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				// this will have redirected us so we should throw an error if that didn't happen
				throw new Exception("delete calendar should have redirected, but did not");
			}
			if($subview == "edit_cal" && isset($data_list["cal_id"])){
				
				$module = new module_bootstrap_strongcal_editcalendarview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$edit_cal_form = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				$content_panel = $edit_cal_form->data();
			}else if($subview == "add_cal"){
				$module = new module_bootstrap_strongcal_addcalendarview_gui($this->avalanche, $this->doc);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$add_cal_form = $runner->run(new module_bootstrap_data($data_list, "the form input"));
				$content_panel = $add_cal_form->data();
			}else if((count($calendar_obj_list) > 0) && isset($data_list["cal_id"])){
					/**
					 * manage this calendar
					 */
					$data = new module_bootstrap_data($data_list);
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_managecal_gui($this->avalanche, $this->doc));
					$data = $runner->run($data);
					$content_panel = $data->data();
			}else{
				if(count($calendar_obj_list) > 0){
					/**
					 * get the list of calendars
					 */
					$data = new module_bootstrap_data($data_list);
					$runner = $bootstrap->newDefaultRunner();
					$runner->add(new module_bootstrap_strongcal_managecals_list_gui($this->avalanche, $this->doc));
					$data = $runner->run($data);
					$content_panel = $data->data();
				}else{

					// there are no calendars in the list
					$content = new Panel();
					$content->getStyle()->setClassname("error_panel");
					$content->add(new Text("There are no calendars<br> to manage yet."));

					$left_button_panel = new BorderPanel();
					$left_button_panel->setWidth("100%");
					$add_button = new Button("add new");
					$add_button->getStyle()->setClassname("manage_cals_button");
					$add_button->addAction(new LoadPageAction("index.php?view=manage_cals&subview=add_cal"));			
					$left_button_panel->setCenter(new Text("&nbsp;"));
					if($strongcal->canAddCalendar()){
						$left_button_panel->setEast($add_button);
						$c = new GridPanel(1);
						$c->add($content);
						$c->add($left_button_panel);
						$content = $c;
					}
					
					$content_panel = new ErrorPanel($content);
					$content_panel->getStyle()->setHeight("300px");
				}
			}
			/************************************************************************
			put it all together
			************************************************************************/
			
			
			$manage_view = $content_panel;
			return new module_bootstrap_data($manage_view, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	private function getCalendar($calendar_obj_list, $cal_id){
		/**
		 * get the main calendar
		 */
		$main_cal_obj = false;
		foreach($calendar_obj_list as $cal){
			if($cal->getId() == $cal_id){
				$main_cal_obj = $cal;
				break;
			}
		}
		if(!is_object($main_cal_obj) && is_object($calendar_obj_list[0])){
			// they specified an incorrect calendar id
			// just use the first calendar
			$main_cal_obj = $calendar_obj_list[0];
		}
		return $main_cal_obj;
	}
}
?>