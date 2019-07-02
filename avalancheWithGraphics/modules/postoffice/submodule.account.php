<?



class module_postoffice_account{
	private $avalanche;
	private $id;
	
	private $unlocked;
	private $key;
	
	private $inbox_id;
	private $sentbox_id;
	private $draftbox_id;
	private $deletedbox_id;
	
	private $boxes;
	
	function __construct($avalanche, $id){
		$this->avalanche = $avalanche;
		$this->loaded = false;
		$this->unlocked = false;
		$this->boxes = new HashTable();
		if(is_array($id)){
			$this->id = (int)$id["id"];

			$this->inbox_id = (int)$id["inbox_id"];
			$this->sentbox_id = (int)$id["sentbox_id"];
			$this->draftbox_id = (int)$id["draftbox_id"];
			$this->deletedbox_id = (int)$id["deletedbox_id"];
			
			$this->loaded = true;
		}else if(is_int($id)){
			$this->id = $id;
			$this->loaded = false;
		}else{
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an array or int");
		}
	}
	
	// returns the avalanche associated with this account
	public function avalanche(){
		return $this->avalanche;
	}
	
	// before you can access boxes, you have to have a key and unlock the account
	public function unlock($key){
		$id = $this->id;
		$account_table = $this->avalanche()->PREFIX() . "postoffice_accounts";
		$sql = "SELECT * FROM $account_table WHERE id='$id' AND the_key='$key'";
		$result = $this->avalanche()->mysql_query($sql);
		if($myrow = mysql_fetch_array($result)){
			$this->key = $key;
			$this->unlocked = true;
			$this->load();
			return true;
		}else{
			return false;
		}
	}
	
	
	// locking an account returns the key
	public function lock(){
		$key = $this->getKey();
		$this->key = false;
		$this->unlocked = false;
		return $key;
	}
	
	// returns true if the account is unlocked
	public function unlocked(){
		return $this->unlocked;
	}
	
	// you can get your key from a box if its unlocked
	public function getKey(){
		if($this->unlocked()){
			return $this->key;
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice account.");
		}
	}
	
	private function load(){
		$id = $this->id;
		$key = $this->key;
		if(!$this->loaded){
			$account_table = $this->avalanche()->PREFIX() . "postoffice_accounts";
			$sql = "SELECT * FROM $account_table WHERE id='$id' AND the_key='$key'";
			$result = $this->avalanche()->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->inbox_id = (int)$id["inbox_id"];
				$this->sentbox_id = (int)$id["sentbox_id"];
				$this->draftbox_id = (int)$id["draftbox_id"];
				$this->deletedbox_id = (int)$id["deletedbox_id"];
				
				$this->loaded = true;
			}else{
				throw new MailAccountNotFoundException($id);
			}
		}
	}
	
	function getInbox(){
		if($this->unlocked()){
			if(!$this->loaded){
				$this->load();
			}
			return $this->getBox($this->inbox_id);
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice box.");
		}
	}
	
	function getSentbox(){
		if($this->unlocked()){
			if(!$this->loaded){
				$this->load();
			}
			return $this->getBox($this->sentbox_id);
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice box.");
		}
	}
	
	function getDraftbox(){
		if($this->unlocked()){
			if(!$this->loaded){
				$this->load();
			}
			return $this->getBox($this->draftbox_id);
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice box.");
		}
	}
	
	function getDeletedbox(){
		if($this->unlocked()){
			if(!$this->loaded){
				$this->load();
			}
			return $this->getBox($this->deletedbox_id);
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice box.");
		}
	}
	
	private function getBox($box_id){
		$box_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
		if(!is_int($box_id)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an int");
		}else{
			if(is_object($this->boxes->get($box_id))){
				return $this->boxes->get($box_id);
			}else{
				$sql = "SELECT * FROM $box_table WHERE id='$box_id'";
				$result = $this->avalanche()->mysql_query($sql);
				if($myrow = mysql_fetch_array($result)){
					$box = new module_postoffice_mailbox($this->avalanche(), $myrow);
					$this->boxes->put($box_id, $box);
					return $box;
				}else{
					throw new MailboxNotFoundException($box_id);
				}
			}
		}
	}
	
	public function compose(){
		if($this->unlocked()){
			$message_table = $this->avalanche()->PREFIX() . "postoffice_messages";
			$sql = "INSERT INTO $message_table (`from_id`) VALUES ('" . $this->getId() . "')";
			$result = $this->avalanche()->mysql_query($sql);
			$message = new module_postoffice_message($this->avalanche(), $this->avalanche()->mysql_insert_id());
			$rel_table = $this->avalanche()->PREFIX() . "postoffice_message_rel";
			$sql = "INSERT INTO $rel_table (`box_id`,`message_id`) VALUES ('" . $this->draftbox_id . "','" . $message->getId() . "')";
			$result = $this->avalanche()->mysql_query($sql);
			
			$draftbox = $this->getBox($this->draftbox_id);
			$draftbox->reload();
		}else{
			throw new AccountIsLockedException("You cannot access a locked postoffice box.");
		}
	}
	
	public function getId(){
		return (int)$this->id;
	}
}
?>
