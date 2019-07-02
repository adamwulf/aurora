<?

class module_bootstrap_strongcal_hidecalendarview_gui extends module_bootstrap_module{

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
		
		$this->setName("Add event view for Aurora");
		$this->setInfo("hides a calendar. expecs \$cal_id to come as an integer input.");
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
				throw new IllegalArgumentException("hiding or showing a calendar requires \$cal_id to be sent as form input");
			}else{
				$cal_id = $data_list["cal_id"];
			}

			if(!isset($data_list["view"])){
				$view = "";
			}else{
				$view = $data_list["view"];
			}
			if(!isset($data_list["subview"])){
				$subview = "";
			}else{
				$subview = $data_list["subview"];
			}
			
			$data = new module_bootstrap_data(array($cal_id), "the calendar id to get"); // send in false as the default value
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$data = $runner->run($data);
			$calendar_obj_list = $data->data();
			if(count($calendar_obj_list) > 0){
				$cal = $calendar_obj_list[0];
			}else{
				throw new Exception("calendar $cal_id does not exist");
			}
			
			if(!isset($data_list["show"])){
				// the calendar is currently shown, so we need to hide it
				$subview = "";
				$strongcal->select($cal_id);
			}else{
				// show the calendar
				$subview = "";
				$strongcal->unselect($cal_id);
			}

			
			header("Location: index.php?view=$view&subview=$subview&cal_id=$cal_id");
			exit;
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
	
	
	/************************************************************************
	include avalanche
	and initialize necessary variables
	************************************************************************/

	function getColorPalatte($comp, $inp){
		
		
		$color_palette = new GridPanel(9);
		$color_palette->getStyle()->setHeight("225px");
		$color_palette->getStyle()->setWidth("420px");
		$color_palette->setStyle(new Style("editlite"));

			
		$width = 20;
		$height = 14;
		
		// first the grayscale row
		for($i=0;$i<9;$i++){
			$color = $this->getBW($i);
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
				$color1 = $this->getColor1($i);
				$color2 = $this->getColor2($i);
				$color = $this->makeColor($c, $color1, $color2);
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

	
	function getBW($j){
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
	
	function getColor1($j){
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
	
	function getColor2($c){
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
	
	function makeColor($style, $color1, $color2){
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