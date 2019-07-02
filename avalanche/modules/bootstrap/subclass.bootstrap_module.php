<?

/*
 * an abstract module
 */
abstract class module_bootstrap_module{

	private $_name = "Default Bootstrap! Module";
	private $_info = "This is the abstract case for the Bootstrap! module.";


	/* setter for name */
	final protected function setName($n){
		$this->_name = $n;
	}

	/* setter for info */
	final protected function setInfo($i){
		$this->_info = $i;
	}

	/* getter for name */
	final public function name(){
		return $this->_name;
	}

	/* getter for info */
	final public function info(){
		return $this->_info;
	}


	/* override this function for functionality */
	/* the $data should be of type module_bootstrap_data */
	abstract function run($data = false);




	/* standard visitor pattern
	 */
	function execute(avalanche_interface_visitor $visitor){
		$visitor->visit($this);
	}
}



/* these classes are created specifically for test purposes only */
class module_bootstrap_module_testcase extends module_bootstrap_module{
  /* noop, just use base class for test cases */

  function run($data=false){
  }
}


class module_bootstrap_module_add1 extends module_bootstrap_module{

	function __construct(){
		$this->setName("add1");
		$this->setInfo("adds 1 to the integer input");
	}

	function run($data = false){
		if(!($data === false || $data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if($data === false){
			return new module_bootstrap_data(1, "an int");
		}else
		if(is_int($data->data())){
			return new module_bootstrap_data($data->data() + 1, $data->info());
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an integer.<br>");
		}
	}

}
?>