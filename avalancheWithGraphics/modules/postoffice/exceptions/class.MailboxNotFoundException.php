<?



class MailboxNotFoundException extends Exception{

	public function __construct($message=false, $code=false){
		if(!is_int($message)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be an int");
		}
		$message = "Mailbox #$message could not be found";
		parent::__construct($message);
	}
}

?>