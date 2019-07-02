<?



class AccountIsLockedException extends Exception{

	public function __construct($message=false, $code=false){
		if(!is_string($message)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an string");
		}
		$message = "Account is Locked: $message";
		parent::__construct($message);
	}
}

?>