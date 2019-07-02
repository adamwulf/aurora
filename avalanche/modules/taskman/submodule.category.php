<?



class module_taskman_category{
	
	private $id;
	
	private $avalanche;
	public function __construct($avalanche, $id, $cal_id, $name){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("First argument to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_int($id)){
			throw new IllegalArgumentException("Second argument to " . __METHOD__ . " must be an int");
		}
		$this->id = $id;
		if(!is_int($cal_id)){
			throw new IllegalArgumentException("Third argument to " . __METHOD__ . " must be an int");
		}
		$this->cal_id = $cal_id;
		if(!is_string($name)){
			throw new IllegalArgumentException("Fourth argument to " . __METHOD__ . " must be an string");
		}
		$this->name = $name;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function calId(){
		return $this->cal_id;
	}
	
	public function name(){
		return $this->name;
	}
	
	
}
?>
