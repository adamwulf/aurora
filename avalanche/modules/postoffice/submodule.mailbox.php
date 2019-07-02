<?



class module_postoffice_mailbox{
	private $avalanche;
	private $id;
	
	private $_name;
	private $_quota;
	
	private $messages;
	
	function __construct($avalanche, $id){
		$this->avalanche = $avalanche;
		if(is_array($id)){
			$this->id = $id["id"];
			$this->_name = $id["name"];
			$this->_quota = (int)$id["quota"];
			$this->loaded = true;
		}else if(is_int($id)){
			$this->id = $id;
			$this->loaded = false;
		}else{
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an array or int");
		}
	}
	
	public function avalanche(){
		return $this->avalanche;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function loaded(){
		return $this->loaded;
	}
	
	public function load(){
		if(!$this->loaded()){
			$box_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
			$sql = "SELECT * FROM $box_table WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche()->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->_name = $myrow["name"];
				$this->_quota = (int)$myrow["quota"];
				$this->loaded = true;
			}else{
				throw new MailboxNotFoundException($box_id);
			}
		}
	}
	
	public function reload(){
		$this->loaded = false;
		$this->messages = false;
	}
	
	public function getMessages(){
		if(!is_object($this->messages)){
			$this->messages = new HashTable();
			$rel_table = $this->avalanche()->PREFIX() . "postoffice_message_rel";
			$message_table = $this->avalanche()->PREFIX() . "postoffice_messages";
			$sql = "SELECT `$message_table`.* FROM `$message_table`, `$rel_table` WHERE `$rel_table`.box_id='" . $this->getId() . "' AND `$rel_table`.message_id = `$message_table`.id";
			$result = $this->avalanche()->mysql_query($sql);
			while($myrow = mysql_fetch_array($result)){
				$message = new module_postoffice_message($this->avalanche(), $myrow);
				$this->messages->put((int)$myrow["id"], $message);
			}
		}
		return $this->messages->enum();
	}

	function name($name=false){
		if(!$name){
			return (string)$this->_name;
		}else{
			$sql_name = addslashes($name);
			$message_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
			$my_id = $this->getId();
			$sql = "UPDATE $message_table SET name = '$sql_name' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_name = $name;
				return (string)$this->_name;
			}else{
				return (string)$this->_name;
			}
		}
	}

	
	function quota($quota=false){
		if(!$quota){
			return (int)$this->_quota;
		}else{
			if(!is_int($quota)){
				throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
			}
			$message_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
			$my_id = $this->getId();
			$sql = "UPDATE $message_table SET quota = '$quota' WHERE id = '$my_id'";
			$result = $this->avalanche->mysql_query($sql);
			if($result){
				$this->_quota = $quota;
				return (int)$this->_quota;
			}else{
				return (int)$this->_quota;
			}
		}
	}

}
?>
