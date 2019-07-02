<?

class module_bootstrap_strongcal_addcalendarview_gui extends module_bootstrap_module{

	/** the avalanche object */
	private $avalanche;
	/** the document were in */
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}

		$this->avalanche = $avalanche;
		$this->doc = $doc;

		$this->setName("Add event view for Aurora");
		$this->setInfo("adds an event to a calendar. expecs \$cal_id to come as an integer input.");
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


			if($strongcal->canAddCalendar()){
				if(isset($data_list["submit"])){
					if(!isset($data_list["submit"]) ||
					   !isset($data_list["submit"])){
						throw new IllegalArgumentException("arguments \$name and \$color must be sent in via GET or POST to add a calendar");
					}else{
						$text = new SmallTextInput();
						$text->setName("name");
						$text->loadFormValue($data_list);
						$name = $text->getValue();
						$color = $data_list["color"];
						if(strlen($name) == 0){
							$name = "no name";
						}
						$name = strip_tags($name);
						$cal_id = $strongcal->addCalendar($name);

						$data = new module_bootstrap_data(array($cal_id), "the calendar id to get"); // send in false as the default value
						$runner = $bootstrap->newDefaultRunner();
						$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
						$data = $runner->run($data);
						$calendar_obj_list = $data->data();
						if(count($calendar_obj_list) > 0){
							$cal = $calendar_obj_list[0];
							$cal->color($color);


							if(isset($data_list["description"])){
								// set the (optional) description
								$text = new SmallTextInput();
								$text->setName("description");
								$text->loadFormValue($data_list);
								$description = $text->getValue();
								$cal->description($description);
							}

							header("Location: index.php?view=manage_cals&cal_id=$cal_id");
							exit;
						}else{
							throw new Exception("Error adding calendar");
						}
					}
				}




				/************************************************************************
				    initialize panels
				************************************************************************/
				$my_form = new FormPanel("index.php");
				$my_form->addHiddenField("view", "manage_cals");
				$my_form->addHiddenField("subview", "add_cal");
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
				$default_color = "#aaaaaa";

				$cal_info_panel = new BorderPanel();

				/************************************************************************
				    apply styles to created panels
				************************************************************************/

				$cal_info_panel->getStyle()->setWidth("450px");
				$cal_info_panel->getStyle()->setHeight("450px");
				$cal_info_panel->getStyle()->setBorderWidth(1);
				$cal_info_panel->getStyle()->setBorderStyle("solid");
				$cal_info_panel->getStyle()->setBorderColor("black");

				$my_container->setValign("top");

				$color_main->getStyle()->setClassname("edit");
				$color_main->getCellStyle()->setPadding(4);
				$color_main->getStyle()->setWidth("450px");


				$title_choose->getStyle()->setClassname("edit");
				$title_choose->getStyle()->setHeight("50px");
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
				$cal_name_input->getStyle()->setClassname("calendar_input");

				$cal_desc_input = new TextAreaInput();
				$cal_desc_input->setName("description");
				$cal_desc_input->setCols(40);
				$cal_desc_input->setRows(3);
				$cal_desc_input->getStyle()->setClassname("calendar_input");

				$title_choose->add(new Text("Calendar Title:"));
				$title_choose->add($cal_name_input);

				$title_choose->add(new Text("Description:"));
				$title_choose->add($cal_desc_input);

				$color_input = new HiddenInput();
				$color_input->setName("color");
				$color_input->setValue($default_color);

				$color_palette = $this->getColorPalatte($color_box, $color_input);

				$back = new ButtonInput("Cancel");
				$back->getStyle()->setBorderWidth(1);
				$back->getStyle()->setBorderColor("black");
				$back->getStyle()->setBorderStyle("solid");
				$back->addClickAction(new LoadPageAction("index.php?view=manage_cals"));

				$button_panel->add(new Text("<input type='submit' value='Add' class='go_button'>"));
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
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You are not allowed to add new calendars."));
				$error = new ErrorPanel($content);
				$my_form = $error;
			}

			$header_text = new Text("Add Calendar");
			$header_text->getStyle()->setClassname("page_header");
			$header = new ErrorPanel($header_text);
			$header->getStyle()->setHeight("30px");

			$cal_info_panel->setValign("top");
			$cal_info_panel->setNorth($header);
			$cal_info_panel->setCenter($my_form);


			$content_panel = new Panel();
			$content_panel->setWidth("100%");
			$content_panel->setAlign("center");
			$content_panel->getStyle()->setPaddingTop(60);
			$content_panel->getStyle()->setPaddingBottom(30);
			$content_panel->add($cal_info_panel);

			return new module_bootstrap_data($content_panel, "a gui component for the add calendar view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}


	/************************************************************************
	include avalanche
	and initialize necessary variables
	************************************************************************/

	static function getColorPalatte($comp, $inp){


		$color_palette = new GridPanel(9);
		$color_palette->getStyle()->setHeight("225px");
		$color_palette->getStyle()->setWidth("420px");
		$color_palette->setStyle(new Style("editlite"));


		$width = 20;
		$height = 14;

		// first the grayscale row
		for($i=0;$i<9;$i++){
			$color = module_bootstrap_strongcal_addcalendarview_gui::getBW($i);
			$color = "#" . $color . $color . $color;
			$updateAction = new BackgroundAction($comp, $color);
			$valueAction  = new SetValueAction($inp, $color);
			$panel = new Button();
			$panel->addAction($updateAction);
			$panel->addAction($valueAction);
			$panel->setStyle(new Style());
			$panel->getStyle()->setWidth($width . "px");
			$panel->getStyle()->setHeight($height . "px");
			$panel->getStyle()->setBorderWidth(1);
			$panel->getStyle()->setBorderStyle("solid");
			$panel->getStyle()->setBorderColor("black");
			$panel->getStyle()->setMarginRight(1);
			$panel->getStyle()->setMarginBottom(1);
			$panel->getStyle()->setBackground($color);
			$color_palette->add($panel);
		}

		// now for the fancy stuff
		for($c=0;$c<6;$c++){
			for($i=0;$i<9;$i++){
				$color1 = module_bootstrap_strongcal_addcalendarview_gui::getColor1($i);
				$color2 = module_bootstrap_strongcal_addcalendarview_gui::getColor2($i);
				$color = module_bootstrap_strongcal_addcalendarview_gui::makeColor($c, $color1, $color2);
				$updateAction = new BackgroundAction($comp, $color);
				$valueAction  = new SetValueAction($inp, $color);
				$panel = new Button();
				$panel->addAction($updateAction);
				$panel->addAction($valueAction);
				$panel->setStyle(new Style());
				$panel->getStyle()->setWidth($width . "px");
				$panel->getStyle()->setHeight($height . "px");
				$panel->getStyle()->setBorderWidth(1);
				$panel->getStyle()->setBorderStyle("solid");
				$panel->getStyle()->setBorderColor("black");
				$panel->getStyle()->setMarginRight(1);
				$panel->getStyle()->setMarginBottom(1);
				$panel->getStyle()->setBackground($color);
				$color_palette->add($panel);
			}
		}

		$orange = array("#442200",
				"#663300",
				"#993300",
				"#CC6600",
				"#FF6600",
				"#FF6633",
				"#FF9933",
				"#FFCC66",
				"#FFDD99");
		// now orange
		for($i=0;$i<9;$i++){
			$color = $orange[$i];
			$updateAction = new BackgroundAction($comp, $color);
			$valueAction  = new SetValueAction($inp, $color);
			$panel = new Button();
			$panel->addAction($updateAction);
			$panel->addAction($valueAction);
			$panel->setStyle(new Style());
			$panel->getStyle()->setWidth($width . "px");
			$panel->getStyle()->setHeight($height . "px");
			$panel->getStyle()->setBorderWidth(1);
			$panel->getStyle()->setBorderStyle("solid");
			$panel->getStyle()->setBorderColor("black");
			$panel->getStyle()->setMarginRight(1);
			$panel->getStyle()->setMarginBottom(1);
			$panel->getStyle()->setBackground($color);
			$color_palette->add($panel);
		}
		return $color_palette;
	}


	static function getBW($j){
		switch($j){
			case 0: return "00"; break;
			case 1: return "33"; break;
			case 2: return "66"; break;
			case 3: return "88"; break;
			case 4: return "99"; break;
			case 5: return "AA"; break;
			case 6: return "CC"; break;
			case 7: return "EE"; break;
			case 8: return "FF"; break;
		}
	}

	static function getColor1($j){
		switch($j){
			case 0: return "33"; break;
			case 1: return "66"; break;
			case 2: return "99"; break;
			case 3: return "CC"; break;
			case 4: return "FF"; break;
			case 5: return "FF"; break;
			case 6: return "FF"; break;
			case 7: return "FF"; break;
			case 8: return "FF"; break;
		}
	}

	static function getColor2($c){
		switch($c){
			case 0: return "00"; break;
			case 1: return "00"; break;
			case 2: return "00"; break;
			case 3: return "00"; break;
			case 4: return "00"; break;
			case 5: return "33"; break;
			case 6: return "66"; break;
			case 7: return "99"; break;
			case 8: return "CC"; break;
		}
	}

	static function makeColor($style, $color1, $color2){
		switch($style){
			case 0: return "#" . $color2 . $color2 . $color1; break;
			case 1: return "#" . $color2 . $color1 . $color2; break;
			case 2: return "#" . $color1 . $color2 . $color2; break;
			case 3: return "#" . $color2 . $color1 . $color1; break;
			case 4: return "#" . $color1 . $color2 . $color1; break;
			case 5: return "#" . $color1 . $color1 . $color2; break;
		}
	}

}
?>