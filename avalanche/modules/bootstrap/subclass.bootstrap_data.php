<?


/* an representation of "data" */
/* this wrapper holds a data object, and a string representing the data */
 class module_bootstrap_data{

	/**
	 * the actual data
	 */
	private $_data;

	/**
	 * a string describing the data (optional, suggested)
	 */
	private $_info;


	/**
	 * initialize my variables
	 */
	function __construct($d, $i = ""){
		$this->_data = $d;
		$this->_info = $i;
	}

	/* returns the data */
	function data(){
		return $this->_data;
	}
	
	/* returns the info about the data */
	public function info(){
		return $this->_info;
	}
 }




?>