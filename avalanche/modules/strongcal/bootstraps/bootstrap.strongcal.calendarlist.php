<?

class module_bootstrap_strongcal_calendarlist extends module_bootstrap_module{

	private $allowed_loaders;
	private $avalanche;

	function __construct($avalanche){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List Getter");
		$this->setInfo("this module returns an array of calendar objects. the input must either by false, which
				indicates that all available calendars should be returned. or the input can be an array
				of calendar id's, indicating which calendars should be returned (if possible).
				this module throws exceptions for poorly formated input");
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
			$ret_array = array();
			for($i=0;$i<count($cal_list);$i++){
				$cal = $strongcal->getCalendarFromDb($cal_list[$i]);
				if(is_object($cal) && ($cal instanceof module_strongcal_calendar)){
					$ret_array[] = $cal;
				}else{
					throw new module_bootstrap_exception("Error: Request for nonexistant calendar id # " . $cal_list[$i]);
				}
			}
			return new module_bootstrap_data($ret_array, "a calendar list");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be either false, or an array of calendar ids.<br>");
		}
	}
}
?>