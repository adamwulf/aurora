<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class FilterInitAction extends NonKeyAction{

	protected static $global_id = 0;
	
	private $id;
	private $trigger;
	
	private $filters;
	
	public function __construct(Component $trigger, $max){
		$this->trigger = $trigger;
		if(!is_int($max) || $max < 0){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an int >= 0");
		}
		$this->max = $max;
		$this->filters = array();
		FilterInitAction::$global_id++;
		$this->id = FilterInitAction::$global_id;
	}

	public function toJS(){
		$trigger_id = $this->trigger->getId();
		$name = "yfilter_" . $this->id;
		$ret = $name . " = new yFilter('$trigger_id', " . $this->max . ");";
		foreach($this->filters as $f){
			if($f[2] === false){
				$ret .= $name . ".addItem(\"" . addslashes($f[0]) . "\",\"" . $f[1]->getId() . "\",\"\");";
			}else{
				$ret .= $name . ".addItem(\"" . addslashes($f[0]) . "\",\"" . $f[1]->getId() . "\",\"" . $f[2]->getId() . "\");";
			}
		}
		return $ret;
	}
	
	public function addItem($str, Component $match, $fail = false){
		if($fail === false || is_object($fail) && $fail instanceof Component){
			$this->filters[] = array($str, $match, $fail);
		}else{
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be either false or a Component");
		}
	}
}


?>