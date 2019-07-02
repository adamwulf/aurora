<?

class module_bootstrap_strongcal_calendar_header_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $calendar;

	function __construct($avalanche, Document $doc, module_strongcal_calendar $cal){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora visibility permission options to HTML");
		$this->setInfo("this module displays information for the visibility options of a clalendar.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->calendar = $cal;
	}

	function run($data = false){
		if(!($data === false)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be false.<br>");
		}else
		if($data === false){
			$os = $this->avalanche->getModule("os");
			$main_cal_obj = $this->calendar;
			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			
			$nobg = new Style("nobg");
			
			/************************************************************************
			    initialize panels
			************************************************************************/
			/**
			cal info should have whatever you want it to have. at the very least the owner (maybe his avatar, too)
			the calendar color, the calendar name, and those three buttons i've got included.
			**/
			
			$width = "650px";
			
			// the top row of the header
			$top_row = new BorderPanel();
			$top_row->getStyle()->setWidth($width);
			$top_row->setValign("top");
			
			// the main color box the for the calendar
			$color_box = new Panel();
			$color_box->setStyle(new Style("main_color_box"));
			$color_box->getStyle()->setBackground($main_cal_obj->color());
			
			// the calendar name
			$cal_info = new BorderPanel();
			$cal_info->setWest($color_box);
			$str = $main_cal_obj->name();
			$cal_info->setCenter(new Text($str));
			$cal_info->setStyle($nobg);
			
			// the owner avatar
			$avatar = new Icon($this->avalanche->HOSTURL() . $this->avalanche->getAvatar($main_cal_obj->author()));
			$avatar->getStyle()->setBorderWidth(1);
			$avatar->getStyle()->setBorderColor("black");
			$avatar->getStyle()->setBorderStyle("solid");
			
			$avatar = new ErrorPanel($avatar);
			$avatar->getStyle()->setPaddingRight(20);
			$avatar->getStyle()->setPaddingTop(10);
			
			$top_row->setEast($avatar);
			
			$header = new Panel();
			$style = new Style("page_header");
			$header->setStyle($style);
			$header->setWidth("100%");
			$title = "Calendar Management";
			$header->add(new Link($title, "index.php?view=manage_cals"));
			if(isset($main_cal_obj) && is_object($main_cal_obj)){
				// $header->add(new Text(" &gt;&gt; "));
				// $header->add(new Text($main_cal_obj->name()));
			}
			$top_row->setCenter($header);
			
			// make username link
			$link = new Link($os->getUsername($main_cal_obj->author()), "javascript:;");
			$link->getStyle()->setFontColor("black");
			$this->createUserMenu($link, $main_cal_obj->author());
			$link = $link->execute(new HTMLElementVisitor());
			
			// the header
			$cal_info_header = new GridPanel(1);
			$cal_info_header->add($top_row);
			$cal_info_header->add(new Text("
			<table border='0' cellpadding='0' cellspacing='0' height='40'>
			<tr>
			<td height='7' width='12'></td>
			<td rowspan='3' height='40'><div style='width: 40px; height: 40px; background-color: " . $main_cal_obj->color() . "; border: 1px solid black;'></div></td>
			<td height='7' width='598' colspan='2'></td>
			</tr>
			<tr>
			<td height='26' bgcolor='#ADB8D0' style='border-bottom:1px solid #8A9BB3;border-top:1px solid #8A9BB3;'>
			&nbsp;
			</td>
			<td height='26' bgcolor='#ADB8D0' id='ownedby' style='border-bottom:1px solid #8A9BB3;border-top:1px solid #8A9BB3; font-family: verdana; font-size: 12pt;'>
			&nbsp;&nbsp;" . $main_cal_obj->name() . "
			</td>
			<td nowrap height='26' align='right' bgcolor='#ADB8D0' style='border-bottom:1px solid #8A9BB3;border-top:1px solid #8A9BB3; padding-left:18px;font-family: verdana; font-size: 9pt;'>
			by $link&nbsp;&nbsp;
			</td>
			</tr>
			<tr>
			<td height='7' bgcolor='#ffffff'></td>
			<td height='7' bgcolor='#FFFFFF' colspan='2'></td>
			</tr>
			</table>"
			));
			
			return new module_bootstrap_data($cal_info_header, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	private function createUserMenu($trigger, $user_id){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = false;
		$module = new OSUserMenu($this->avalanche, $this->doc, $trigger, $user_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}	
}
?>