<?

class module_bootstrap_strongcal_selectcalendarview_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $post_to;
	private $filter;

	function __construct($avalanche, Document $doc, $post_to, $filter = "all"){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_string($post_to)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be of type string");
		}
		if(!is_string($filter)){
			throw new IllegalArgumentException("argument 4 to " . __METHOD__ . " must be of type string");
		}
		if($filter != "all" &&
		$filter != "read_event" &&
		$filter != "write_event"){
			throw new IllegalArgumentException("\$filter in form input must be set to \"all\" or \"read\" or \"write\""); 
		}
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->post_to = $post_to;
		$this->filter = $filter;
		
		$this->setName("Calendar List Selector Display");
		$this->setInfo("displays a list of calendars for the user to select from. posts to a page defined by input");
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

			
			/** end initializing the input */			

			/**
			 * get the calendar
			 */
			$data = false;
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$runner->add(new module_bootstrap_strongcal_filter_calendars($this->avalanche));
			$data = $runner->run($data);
			$calendar_list = $data->data();

			// short circuit this page if there's only 1 calendar
			if(count($calendar_list) == 1){
				$cal = $calendar_list[0];
				$cal_id = $cal->getId();
				$post_to = $this->post_to;
				$post_to = parse_url($post_to);
				$additions = "cal_id=$cal_id";
				if(isset($data_list["date"])){
					$additions .= "&date=" . $data_list["date"];
				}
				if(isset($data_list["time"])){
					$additions .= "&time=" . $data_list["time"];
				}
				if(isset($post_to["query"]) && strlen($post_to["query"])){
					$post_to["query"] .= "&" . $additions;
				}else{
					$post_to["query"] = $additions;
				}
				$url = "";
				if(isset($post_to["scheme"])){
					$url .= $post_to["scheme"] . "://"; 
				}
				if(isset($post_to["username"])){
					$url = $url . $post_to["username"]; 
					if(isset($post_to["password"])){
						$url = $url . ":" . $post_to["password"];
					}
					$url = $url . "@";
				}
				if(isset($post_to["host"])){
					$url = $url . $post_to["host"];
				}
				if(isset($post_to["path"])){
					$url = $url . $post_to["path"];
				}
				if(isset($post_to["query"])){
					$url = $url . "?" . $post_to["query"];
				}
				$this->post_to = $url;
				
				throw new RedirectException($this->post_to);
			}
			
			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/cal_form_style.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			get modules
			************************************************************************/
			$buffer = $this->avalanche->getSkin("buffer");
			
			$form = new FormPanel($this->post_to);
			if(isset($data_list["date"])){
				$form->addHiddenField("date", $data_list["date"]);
			}
			if(isset($data_list["time"])){
				$form->addHiddenField("time", $data_list["time"]);
			}
			
			
			$form->setAsPost();
			$form->setStyle(new Style("calendar_form"));
			
			$options = "";
			$count = 0;
			for($i=0;$i<count($calendar_list);$i++){
				$temp_cal = $calendar_list[$i];
				if($this->filter == "write_event" && $temp_cal->canWriteEntries() ||
				   $this->filter == "read_event"  && $temp_cal->canReadEntries() ||
				   $this->filter == "all"){
					   $count++;
					   $options .= $buffer->option($temp_cal->getId(), htmlentities($temp_cal->name(), ENT_QUOTES), "");
				}
			}
			$select  = $buffer->select($options, "name='cal_id'");
			$select = new Text($select);
			$submit = new Text("<input style='border: 1px solid black' type='submit' id='submit' value='Go'>");
			
			$cal_form = new Panel();
			$cal_form->getStyle()->setClassname("big_container");
			$cal_form->getStyle()->setWidth("100%");
			$cal_form->getStyle()->setHeight("400");
			$cal_form->setAlign("center");
			$cal_form->setValign("middle");

			$title = new Text("");
			if($count > 0){
				$title = new Text("Please select a calendar first...");
				$title->setStyle(new Style("form_header"));
				
				$content = new GridPanel(1);
				$content->setCellStyle(new Style("form_cell"));
				$content->getStyle()->setWidth("100%");
				$content->add($title);
				$content->add($select);
				$content->add($submit);
				
				$form->add($content);
				$cal_form->add($form);
			}else{
				$title = new Text("You do not have permission to edit any calendars");
				$title->setStyle(new Style("form_header"));
				$cal_form->add($title);
			}
			
			return new module_bootstrap_data($cal_form, "a gui form to select a calendar");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>