<?



class AccountNotFoundException extends Exception{

	public function __construct($message=false, $code=false){
		if(!is_string($message) || strlen($message) == 0){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string with length >= 1");
		}
		$message = "Account $message could not be found";
		parent::__construct($message);

	}}

?>