<?



class AccountTransaction{
	private $avalanche;
	private $id;
	private $processed;
	private $pending;
	private $email;
	private $phone;
	private $country;
	private $state;
	private $city;
	private $zip;
	private $street;
	private $name;
	private $description;
	private $quantity;
	private $users;
	private $purchaed_on;
	private $order_id;
	private $product;
	private $account;
	private $total;
	
	function __construct($avalanche, $id=0){
		$this->avalanche = $avalanche;
		$accounts = $this->avalanche->getModule("accounts");
		if($id == 0){
			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "accounts_transactions () VALUES ()";
			$result = $this->avalanche->mysql_query($sql);
			$id = mysql_insert_id();
			$this->id = $id;
			$this->processed = false;
			$this->pending = false;
			$this->email = "";
			$this->phone = "";
			$this->country = "";
			$this->state = "";
			$this->city = "";
			$this->street = "";
			$this->zip = "";
			$this->name = "";
			$this->description = "";
			$this->quantity = 0;
			$this->users = 0;
			$this->purchased_on = "0000-00-00 00:00:00";
			$this->order_id = "";
			$this->product = false;
			$this->account = false;
			$this->total = 0;
		}else{
			// load the transaction
			$this->id = $id;
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts_transactions WHERE id='$id'";
			$result = $this->avalanche->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->id = $id;
				$this->processed = (bool) $myrow["processed"];
				$this->pending = (bool) $myrow["pending"];
				$this->email = $myrow["card_email"];
				$this->phone = $myrow["card_phone"];
				$this->country = $myrow["card_country"];
				$this->state = $myrow["card_state"];
				$this->street = $myrow["card_street"];
				$this->name = $myrow["card_name"];
				$this->city = $myrow["card_city"];
				$this->zip = $myrow["card_zip"];
				$this->description = $myrow["description"];
				$this->quantity = $myrow["quantity"];
				$this->users = $myrow["users"];
				$this->purchased_on = $myrow["purchase_date"];
				$this->order_id = $myrow["2co_order_id"];
				$this->product = $accounts->getProduct((int)$myrow["product_id"]);
				if(strlen($myrow["account_name"]) > 0){
					$this->account = $accounts->getAccount($myrow["account_name"]);
				}else{
					$this->account = false;
				}
				$this->total = $myrow["total"];
			}else{
				throw new Exception("cannot load transaction #$id");
			}
		}
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function processed($p = 0){
		if(is_bool($p)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET processed = '" . ($p ? "1" : "0") . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->processed = $p;
			return $p;
		}else if($p === 0){
			return (bool)$this->processed;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
	}
	
	public function pending($p = 0){
		if(is_bool($p)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET pending = '" . ($p ? "1" : "0") . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->pending = $p;
			return $p;
		}else if($p === 0){
			return (bool)$this->pending;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
	}
	
	public function email($e = 0){
		if(is_string($e)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_email = '" . $e . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->email = $e;
			return $e;
		}else if($e === 0){
			return (string)$this->email;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function phone($p = 0){
		if(is_string($p)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_phone = '" . $p . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->phone = $p;
			return $p;
		}else if($p === 0){
			return (string)$this->phone;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function country($c = 0){
		if(is_string($c)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_country = '" . $c . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->country = $c;
			return $c;
		}else if($c === 0){
			return (string)$this->country;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function state($s = 0){
		if(is_string($s)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_state = '" . $s . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->state = $s;
			return $s;
		}else if($s === 0){
			return (string)$this->state;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function city($c = 0){
		if(is_string($c)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_city = '" . $c . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->city = $c;
			return $c;
		}else if($c === 0){
			return (string)$this->city;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function zip($z = 0){
		if(is_string($z)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_zip = '" . $z . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->zip = $z;
			return $z;
		}else if($z === 0){
			return (string)$this->zip;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}
	public function street($s = 0){
		if(is_string($s)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_street = '" . $s . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->street = $s;
			return $s;
		}else if($s === 0){
			return (string)$this->street;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function name($n = 0){
		if(is_string($n)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET card_name = '" . $n . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->name = $n;
			return $n;
		}else if($n === 0){
			return (string)$this->name;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function description($d = 0){
		if(is_string($d)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET description = '" . $d . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->description = $d;
			return $d;
		}else if($d === 0){
			return (string)$this->description;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
	}

	public function quantity($q = false){
		if(is_int($q)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET quantity = '" . $q . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->quantity = $q;
			return $q;
		}else if($q === false){
			return (int)$this->quantity;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	/**
	 * the number of total users they want
	 */
	public function users($u = false){
		if(is_int($u)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET users = '" . $u . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->users = $u;
			return $u;
		}else if($u === false){
			return (int)$this->users;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function purchasedOn($d = false){
		if(module_taskman_task::isDateTime($d)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET purchase_date = '" . $d . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->purchased_on = $d;
			return $d;
		}else if($d === false){
			return (string)$this->purchased_on;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function orderId($o = false){
		if(is_string($o)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET 2co_order_id = '" . $o . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->order_id = $o;
			return $o;
		}else if($o === false){
			return (string)$this->order_id;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function total($t = false){
		if(is_double($t)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET total = '" . $t . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->total = $t;
			return $t;
		}else if($t === false){
			return (double)$this->total;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function product($p = 0){
		if($p === false || $p instanceof AccountProduct){
			if($p instanceof AccountProduct){
				$id = $p->getId();
			}else{
				$id = 0;
			}
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET product_id = '" . $id . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->product = $p;
			return $p;
		}else if($p === 0){
			return $this->product;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function account($p = 0){
		if($p === false || is_object($p)){
			if(is_object($p)){
				$id = $p->name();
			}else{
				$id = "";
			}
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_transactions SET account_name = '" . $id . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->account = $p;
			return $p;
		}else if($p === 0){
			return $this->account;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an account object");
		}
	}
}
?>
