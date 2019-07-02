<?

class module_bootstrap_taskman_tasksorter extends module_bootstrap_module{

	private $allowed_loaders;

	private $start_date;
	private $end_date;

	function __construct(){
		$this->setName("Aurora Task List Sorter");
		$this->setInfo("the constructor for this module takes no input. 

                                this module will return a sorted array of tasks. it uses the TaskmanTaskComparator to sort.
				the input must be an array of task objects to sort.
				
				this module throws exceptions for poorly formated input");
	}

	function run($data = false){
		global $avalanche;
		$strongcal = $avalanche->getModule("strongcal");

		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$task_list = $data->data();
			for($i=0;$i<count($task_list);$i++){
				if(!is_object($task_list[$i]) || !($task_list[$i] instanceof module_taskman_task)){
					throw new module_bootstrap_exception("input to method run in " . __METHOD__ . " must be an array of task objects: " . (is_object($task_list[$i]) ? get_class($task_list[$i]) : gettype($task_list[$i])) . " found<br>");
				}
			}

			$sorter = new MDASorter();
			$comp   = new TaskmanTaskComparator();
			$sorted_list = $sorter->sort($data->data(), $comp);

			return new module_bootstrap_data($sorted_list, "a sorted list of tasks");
		}else{
		  throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be an array of task objects");
		}
	}
}
?>