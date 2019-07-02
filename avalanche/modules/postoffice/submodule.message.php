<?



class module_postoffice_message{
	private $avalanche;
	private $id;
	
	private $_from;
	private $_subject;
	private $_body;
	
	function __construct($avalanche, $id){
		$this->avalanche = $avalanche;
		$this->loaded = false;
		if(is_array($id)){
			$this->id = (int)$id["id"];
			$this->_from = (int)$id["from_id"];
			$this->_body = (string)$id["body"];
			$this->_subject = (string)$id["subject"];
			$this->loaded = true;
		}else if(is_int($id)){
			$this->id = $id;
		}else{
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an array or int");
		}
	}
	
	public function getId(){
		return (int)$this->id;
	}
	
	public function avalanche(){
		return $this->avalanche;
	}
	
	public function load(){
		if(!$this->loaded()){
			$message_table = $this->avalanche()->PREFIX() . "postoffice_messages";
			$sql = "SELECT * FROM $message_table WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche()->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->_from = (int)$id["from_id"];
				$this->_body = (string)$id["body"];
				$this->_subject = (string)$id["subject"];
				$this->loaded = true;
			}else{
				throw new MailboxNotFoundException($box_id);
			}
		}
	}
	
	public function loaded(){
		return $this->loaded;
	}
	
	function from(){
		return (int)$this->_from;
	}

	function subject($subject=false){
		if(!$subject){
			return (string)$this->_subject;
		}else{
			if(!is_string($subject)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an string");
			}
			$sql_subject = addslashes($subject);
			$message_table = $this->avalanche()->PREFIX() . "postoffice_messages";
			$my_id = $this->getId();
			$sql = "UPDATE $message_table SET subject = '$sql_subject' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_subject = $subject;
				return (string)$this->_subject;
			}else{
				return (string)$this->_subject;
			}
		}
	}

	function body($body=false){
		if(!$body){
			return (string)$this->_body;
		}else{
			if(!is_string($body)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an string");
			}
			$sql_body = addslashes($body);
			$message_table = $this->avalanche()->PREFIX() . "postoffice_messages";
			$my_id = $this->getId();
			$sql = "UPDATE $message_table SET body = '$sql_body' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_body = $body;
				return (string)$this->_body;
			}else{
				return (string)$this->_body;
			}
		}
	}
	
	
	public function addRecipient($r){
		if(! $r instanceof module_postoffice_account){
			throw new IllegalArgumentException("argument to " . __METHOD__  . " must be of type module_postoffice_account");
		}
		if(!$this->hasRecipient($r)){
			$rel_table = $this->avalanche()->PREFIX() . "postoffice_send_rel";
			$sql = "INSERT INTO $rel_table (`to_id`,`message_id`) VALUES ('" . $r->getId() . "','" . $this->getId() . "')";
			$result = $this->avalanche()->mysql_query($sql);
			$this->_recipients->put($r->getId(), $r);
		}
	}
	
	public function removeRecipient($r){
		if(! $r instanceof module_postoffice_account){
			throw new IllegalArgumentException("argument to " . __METHOD__  . " must be of type module_postoffice_account");
		}
		if($this->hasRecipient($r)){
			$rel_table = $this->avalanche()->PREFIX() . "postoffice_send_rel";
			$sql = "DELETE FROM $rel_table WHERE message_id='" . $this->getId() . "' AND to_id='" . $r->getId() . "'";
			$result = $this->avalanche()->mysql_query($sql);
			$this->_recipients->clear($r->getId());
		}
	}
	
	public function getRecipients(){
		if(!is_object($this->_recipients)){
			$this->_recipients = new HashTable();
			$postoffice = $this->avalanche()->getModule("postoffice");
			$rel_table = $this->avalanche()->PREFIX() . "postoffice_send_rel";
			$sql = "SELECT * FROM $rel_table WHERE message_id='" . $this->getId() . "'";
			$result = $this->avalanche()->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				$acct = $postoffice->getAccount((int)$myrow["to_id"]);
				$this->_recipients->put($acct->getId(), $acct);
			}
		}
		return $this->_recipients->enum();
	}

	
	public function hasRecipient($acct){
		// load recipients
		$this->getRecipients();
		return is_object($this->_recipients->get($acct->getId()));
	}
	
}
?>
