<?

class module_bootstrap_strongcal_eventlist extends module_bootstrap_module{

	private $allowed_loaders;

	private $start_date;
	private $end_date;
	private $end_num;

	function __construct($start_date, $end_date = false){
		$this->setName("Aurora Event List Getter");
		$this->setInfo("the constructor for this module is a start date and an (optional) end date. if no end date
				is specified, then the start date is assumed to be both the end date and the start date.
				the format for the parameters is yyyy-mm-dd.
				
				you can pass in a number as the second argument which specifies the maximum number of events to
				load for each calendar.

				this module returns an array of event objects lying within the start date and end date inclusive.
				the input must be an array of calendar objects, indicating which calendars' events should be
				returned (if allowed).
				
				this module throws exceptions for poorly formated input");

		$this->start_date = $start_date;
		$this->end_date = false;
		$this->end_num = false;
		if(is_numeric($end_date)){
		  $this->end_num = $end_date;
		}else
		  if($this->verify($end_date)){
		    $this->end_date = $end_date;
		  }else
		    if($end_date === false){
		      $this->end_date = $start_date;
		    }else{
		      throw new module_bootstrap_exception("\$end_date input strongcal event list module must be either false, numeric, or a date");
		    }

		$this->verify($this->start_date);
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
		
		if(!($data === false || $data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$ret_array = array();
			$cal_list = $data->data();
			for($i=0;$i<count($cal_list);$i++){
				if(!is_object($cal_list[$i]) || !($cal_list[$i] instanceof module_strongcal_calendar)){
					throw new module_bootstrap_exception("input to method run in " . $this->name() . " must be an array of calendar objects.<br>");
				}
				$cal_list[$i]->start_date($this->start_date);
				if($this->end_date !== false){
				  $cal_list[$i]->end_date($this->end_date);
				}else{
				  $cal_list[$i]->end_after($this->end_num);
				}
				$cal_list[$i]->reload();
				$ret_array = array_merge($ret_array, $cal_list[$i]->events());
			}
			return new module_bootstrap_data($ret_array, "an unsorted list of events");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be either false, or an array of calendar ids.<br>");
		}
	}
}
?>