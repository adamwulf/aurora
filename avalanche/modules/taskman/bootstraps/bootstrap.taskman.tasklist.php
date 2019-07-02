<?

class module_bootstrap_taskman_tasklist extends module_bootstrap_module{

	private $avalanche;
	private $end_date;
	

	function __construct($avalanche, $end_date){
		$this->setName("Taskman Task List Getter");
		$this->setInfo("");

		$this->end_date = $end_date;
		$this->avalanche = $avalanche;
		$this->verify($this->end_date);
	}

	/**
	 * this function return true if the input is a
	 * string in the form of yyyy-mm-dd, (date form)
	 */
	private function verify($date){
		if(strlen($date) != 10){
			return false;
		}
		$year  = (int)substr($date, 0, 4);
		$month = (int)substr($date, 5, 2);
		$day   = (int)substr($date, 8, 2);
		$seperator1 = substr($date, 4, 1);
		$seperator2 = substr($date, 7, 1);
		
		return (($seperator1 == "-") && ($seperator2 == "-") &&
		       ($year < 2038) && ($year > 1901) && ($month > 0) &&
		       ($month <= 12) && ($day > 0) && ($day <= 31));

	}

	function run($data = false){
		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		
		if(!($data === false || $data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$ret_array = array();
			$cal_list = $data->data();
			$tasks = $taskman->getTasks($this->avalanche->getActiveUser());
			$cal_ids = array();
			for($i=0;$i<count($cal_list);$i++){
				if(!is_object($cal_list[$i]) || !($cal_list[$i] instanceof module_strongcal_calendar)){
					throw new module_bootstrap_exception("input to method run in " . $this->name() . " must be an array of calendar objects.<br>");
				}
				$cal_ids[] = $cal_list[$i]->getId();
			}
			
			foreach($tasks as $task){
				if(in_array($task->calId(), $cal_ids) && substr($task->due(),0,10) <= $this->end_date){
					$ret_array[] = $task;
				}
			}
			return new module_bootstrap_data($ret_array, "an unsorted list of tasks");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be either false, or an array of calendar ids.<br>");
		}
	}
}
?>