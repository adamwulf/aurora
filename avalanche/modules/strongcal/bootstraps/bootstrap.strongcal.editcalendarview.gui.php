<?

class module_bootstrap_strongcal_editcalendarview_gui extends module_bootstrap_module{

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
		
		$this->setName("Edit a Calendar for Aurora");
		$this->setInfo("edits a calendar. expecs \$cal_id to come as an integer input.");
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

			if(!isset($data_list["cal_id"])){
				throw new IllegalArgumentException("cal_id is expected as form input to edit calendar"); 
			}
			$cal_id = (int) $data_list["cal_id"];

			/**
			 * get the calendar
			 */
			try{
				$data = new module_bootstrap_data(array($cal_id), "the calendar to get"); // send in false as the default value
				$runner = $bootstrap->newDefaultRunner();
				$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
				$data = $runner->run($data);
				$calendar_obj_list = $data->data();
				$main_cal_obj = $calendar_obj_list[0];
			}catch(Exception $e){
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("Calendar #$cal_id does not exist."));
				$error = new ErrorPanel($content);
				return new module_bootstrap_data($error, "an error occured");
			}

			// check if they're editing			
			if(isset($data_list["submit"])){
				if(!isset($data_list["submit"]) ||
				   !isset($data_list["submit"])){
					throw new IllegalArgumentException("arguments \$name and \$color must be sent in via GET or POST to add a calendar");
				}else{
					$cal_id = $data_list["cal_id"];
					$text = new SmallTextInput();
					$text->setName("name");
					$text->loadFormValue($data_list);
					$name = $text->getValue();
					if(strlen($name) == 0){
						$name = "no name";
					}
					$text = new SmallTextInput();
					$text->setName("description");
					$text->loadFormValue($data_list);
					$description = $text->getValue();
					$color = $data_list["color"];
					
					
					$main_cal_obj->color($color);
					$main_cal_obj->name($name);
					$main_cal_obj->description($description);
					header("Location: index.php?view=manage_cals&cal_id=$cal_id");
					exit;
				}
			}
			
			/************************************************************************
			    initialize panels
			************************************************************************/
			$cal_info_panel = new BorderPanel();
			$cal_info_panel->getStyle()->setWidth("450px");
			$cal_info_panel->getStyle()->setHeight("450px");
			$cal_info_panel->getStyle()->setBorderWidth(1);
			$cal_info_panel->getStyle()->setBorderStyle("solid");
			$cal_info_panel->getStyle()->setBorderColor("black");

			$my_form = new FormPanel("index.php");
			$my_form->addHiddenField("view", "manage_cals");
			$my_form->addHiddenField("subview", "edit_cal");
			$my_form->addHiddenField("cal_id", (string)$cal_id);
			$my_form->addHiddenField("submit", "1");
			$my_form->setAsGet();
			$my_container = new GridPanel(1);
			
			$color_choose = new BorderPanel();
			$color_box = new Panel();
			$mini_color_box = new Panel();
				
			$title_choose = new GridPanel(1);
			$color_main = new GridPanel(1);
			$button_panel = new GridPanel(2);
			
			$button_row = new Panel();
			$default_color = $main_cal_obj->color();
			$default_name = $main_cal_obj->name();
			$default_desc = $main_cal_obj->description();
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			
			$my_container->setValign("top");
			
			$color_main->getStyle()->setClassname("edit");
			$color_main->getCellStyle()->setPadding(4);
			$color_main->getStyle()->setWidth("450px");
			
			
			$title_choose->getStyle()->setClassname("edit");
			$title_choose->getStyle()->setHeight("100px");
			$title_choose->getStyle()->setWidth("450px");
			$title_choose->getCellStyle()->setPadding(4);
			$title_choose->setValign("top");
			
			$button_panel->getStyle()->setClassname("edit");
			$button_panel->getStyle()->setHeight("50px");
			$button_panel->getStyle()->setWidth("450px");
			$button_panel->getCellStyle()->setPadding(4);
			$button_panel->setValign("middle");
			$button_panel->setAlign("center");
			
			
			$color_box->setStyle(new Style("main_color_box2"));
			$color_box->getStyle()->setBackground($default_color);
			
			$color_choose->setWidth("90%");
			$color_choose->setAlign("center");
			$color_choose->setValign("middle");
			
			
			
			/************************************************************************
			    add necessary text and html
			************************************************************************/
			
			$cal_name_input = new SmallTextInput();
			$cal_name_input->setName("name");
			$cal_name_input->setSize(20);
			$cal_name_input->setValue($default_name);
			$cal_name_input->getStyle()->setClassname("calendar_input");
			
			$cal_desc_input = new TextAreaInput();
			$cal_desc_input->setName("description");
			$cal_desc_input->setCols(40);
			$cal_desc_input->setRows(3);
			$cal_desc_input->setValue($default_desc);
			$cal_desc_input->getStyle()->setClassname("calendar_input");

			$title_choose->add(new Text("Calendar Title:"));
			$title_choose->add($cal_name_input);
			
			$title_choose->add(new Text("Description:"));
			$title_choose->add($cal_desc_input);
			
			$color_input = new HiddenInput();
			$color_input->setName("color");
			$color_input->setValue($default_color);
			
			$color_palette = module_bootstrap_strongcal_addcalendarview_gui::getColorPalatte($color_box, $color_input);
			
			$back = new ButtonInput("Cancel");
			$back->getStyle()->setBorderWidth(1);
			$back->getStyle()->setBorderColor("black");
			$back->getStyle()->setBorderStyle("solid");
			$back->addClickAction(new LoadPageAction("index.php?view=manage_cals&cal_id=$cal_id"));

			$button_panel->add(new Text("<input type='submit' value='Save' class='go_button'>"));
			$button_panel->add($back);
			
			/************************************************************************
			put it all together
			************************************************************************/
			$color_box->add($color_input);
			
			$color_choose->setCenter($color_box);
			$color_choose->setEast($color_palette);
			$color_main->add(new Text("Choose a Calendar Color:"));
			$color_main->add($color_choose);
			
			
			$my_container->add($title_choose);
			$my_container->add($color_main);
			$my_container->add($button_panel);
			
			$my_form->add($my_container);
			
			$header_text = new Text("Edit Calendar");
			$header_text->getStyle()->setClassname("page_header");
			$header = new ErrorPanel($header_text);
			$header->getStyle()->setHeight("30px");
			

			$cal_info_panel->setValign("top");
			$cal_info_panel->setNorth($header);
			$cal_info_panel->setCenter($my_form);
			$content_panel = new ErrorPanel($cal_info_panel);
			$content_panel->getStyle()->setPaddingTop(30);
			$content_panel->getStyle()->setPaddingBottom(30);

			return new module_bootstrap_data($content_panel, "a gui component for the add calendar view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>