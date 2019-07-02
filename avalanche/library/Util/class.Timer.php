<?

/**
 * represents a simple Html Document
 */
class Timer{

	protected $start;
	protected $stop;

	function __construct(){
		$this->start = false;
		$this->stop = false;
	}

	function start(){
		 $this->start = (int)getmicrotime();
		 $this->stop = false;
	}

	function stop(){
		 $this->stop = (int)getmicrotime();
	}

	function read(){
		if(is_numeric($this->stop) &&
		   is_numeric($this->start) &&
		   ($this->stop > $this->start)){
			return ($this->stop - $this->start);
		}else
		if(is_numeric($this->start)){
			return (getmicrotime() - $this->start);
		}else{
			return 0;
		}
	}
}

?>