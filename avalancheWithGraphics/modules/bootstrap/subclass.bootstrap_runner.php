<?

/*
 * an abstract runner. the basic case
 */
class module_bootstrap_runner{

	protected $_modules;

	function __construct(){
		$this->_modules = array();
	}

	function add(module_bootstrap_module $module){
		$this->_modules[] = $module;
	}

	function run($data=false){
		if($data === false && 0 < count($this->_modules)){
			$data = $this->_modules[0]->run();
		}else
		if($data instanceof module_bootstrap_data  && 0 < count($this->_modules)){
			$data = $this->_modules[0]->run($data);
		}else{
			throw new module_bootstrap_exception("input to run() must be of type module_bootstrap_data.<br>");
		}
		
		for($i=1;$i<count($this->_modules);$i++){
			if(!($data instanceof module_bootstrap_data)){
				throw new module_bootstrap_exception("input to run() must be of type module_bootstrap_data.<br>");
			}
			$data = $this->_modules[$i]->run($data);
		}
		return $data;
	}






	/* standard visitor pattern
	 */
	function execute(avalanche_interface_visitor $visitor){
		$visitor->visit($this);
	}

}

?>