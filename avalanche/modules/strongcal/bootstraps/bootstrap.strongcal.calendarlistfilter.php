<?

class module_bootstrap_strongcal_filter_calendars extends module_bootstrap_module{

	private $allowed_loaders;
	private $avalanche;

	function __construct($avalanche){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List Filter");
		$this->setInfo("this module filters out all selected calendars from input array and returns the new array.");
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		$strongcal = $this->avalanche->getModule("strongcal");

		if(!($data === false || $data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if($data === false){
			$ret_array = array();
			$cal_list = $strongcal->getCalendarList();
			for($i=0;$i<count($cal_list);$i++){
				$ret_array[] = $cal_list[$i]["calendar"];
			}
			return new module_bootstrap_data($ret_array, "a calendar list");
		}else
		if(is_array($data->data())){
			$cal_list = $data->data();
			$new_list = array();
			foreach($cal_list as $cal){
				if(!$strongcal->selected($cal)){
					$new_list[] = $cal;
				}
			}
			return new module_bootstrap_data($new_list, "a filtered calendar list");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be either false, or an array of calendar ids.<br>");
		}
	}
}
?>