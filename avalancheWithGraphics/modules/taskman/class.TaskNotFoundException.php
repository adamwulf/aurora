<?

class TaskNotFoundException extends AvalancheException {

	private $CODE = 5;

	public function __construct($id=false, $code=false){
		$message = "Task #$id could not be found";
		parent::__construct($message, $this->CODE);

	}

}


?>