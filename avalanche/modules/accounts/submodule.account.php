<?



class module_accounts_account{


	private $avalanche;
	private $id;
	private $name;
	private $loaded;
	private $added_on;
	private $expires_on;
	private $domain;
	private $email;
	private $disabled;
	private $discount;

	// the number of users allowed in this account (discluding the guest)
	private $users;
	// the number of months purchased so far for this account
	private $months;

	function __construct($avalanche, $id){
		$this->avalanche = $avalanche;
		$this->loaded = false;
		if(is_array($id)){
			$this->id = $id["id"];
			$this->name = $id["name"];
			$this->loaded = true;
			$this->email = $id["email"];
			$this->added_on = $id["added_on"];
			$this->domain = $id["domain"];
			$this->disabled = $id["disabled"];
			$this->users = (int)$id["users"];
			$this->months = (int)$id["months"];
			$this->expires_on = $id["expires_on"];
			$this->discount = (int) $id["discount"];
		}else if(is_int($id)){
			$this->id = $id;
		}else{
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an array or int");
		}
	}

	public function getId(){
		return $this->id;
	}

	public function name(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->name;
	}

	public function discount(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->discount;
	}

	public function email(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->email;
	}

	public function addedOn(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->added_on;
	}

	public function expiresOn(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->expires_on;
	}

	public function domain(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->domain;
	}

	public function disabled(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->disabled ? true : false;
	}

	public function disable($val){
		if(!is_bool($val)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
		if($this->avalanche->loggedInHuh() || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_cp")){
			$this->_disable($val);
		}
	}

	private function _disable($val){
		if(!is_bool($val)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
		$val = $val == 0 ? 0 : 1;
		$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `disabled` = '$val' WHERE `id` = '" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);
		$this->disabled = $val;
	}

	private function load(){
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts WHERE id='" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysql_fetch_array($result)){
			$vals = $myrow;
		}
		if(!is_array($vals)){
			throw new DatabaseException("cannot load account id: #" . $this->getId() . ". Record not found");
		}
		$this->name = $vals["name"];
		$this->email = $vals["email"];
		$this->added_on = $vals["added_on"];
		$this->domain = $vals["domain"];
		$this->disabled = $vals["disabled"];
		$this->users = (int)$vals["users"];
		$this->months = (int)$vals["months"];
		$this->expires_on = $vals["expires_on"];
		$this->discount = (int)$vals["discount"];
	}

	private function loaded(){
		return $this->loaded;
	}

	public function reload(){
		if($this->loaded()){
			$this->loaded = false;
		}
	}

	private $avalanche_loaded = false;
	private function avalanche_loaded(){
		return $this->avalanche_loaded;
	}
	private function avalanche_load(){
		if(defined("ISTEST")){
			$root = $this->avalanche->ROOT() . "../" . $this->name() . "/";
			$hosturl = $this->avalanche->HOSTURL() . "../" . $this->name() . "/";
			$apppath = "../thewulfs/" . $this->avalanche->APPPATH();
		}else{
			$root = $this->avalanche->ROOT() . $this->name() . "/";
			$hosturl = $this->avalanche->HOSTURL() . $this->name() . "/";
			$apppath = "../" . $this->avalanche->APPPATH();
		}
		$databasename = "invers_acct" . $this->name();
		$prefix = "avalanche_";
		$domain = $this->domain();
		$this->account_avalanche = new avalanche_class($root, $this->avalanche->PUBLICHTML(), $hosturl, $domain, $apppath, $this->avalanche->SECURE(), $this->avalanche->INCLUDEPATH(), $this->avalanche->JAVASCRIPT(), $this->avalanche->MODULES(), $this->avalanche->SKINS(), $this->avalanche->LIBRARY(), $this->avalanche->CLASSLOADER(), $this->avalanche->HOST(), $this->avalanche->ADMIN(), $this->avalanche->PASS(), $databasename, $prefix, $this);
		$this->avalanche_loaded = true;
	}

	public function getAvalanche(){
		if(!$this->avalanche_loaded()){
			$this->avalanche_load();
		}
		return $this->account_avalanche;
	}

	/**
	 * returns the maximum number of users allowed by the system
	 */
	public function maxUsers(){
		if(!$this->loaded()){
			$this->load();
		}
		return $this->users;
	}
	
	public function getLastActive(){
		$avalanche = $this->getAvalanche();
		$sql = "SELECT last_active FROM " . $avalanche->PREFIX() . "loggedinusers ORDER BY last_active DESC LIMIT 1";
		$result = $avalanche->mysql_query($sql);
		if($row = mysql_fetch_array($result)){
			return $row["last_active"];
		}
		return "0000-00-00 00:00:00";
	}

	public function usesRemindersHuh(){
		$avalanche = $this->getAvalanche();
		$sql = "SELECT COUNT(*) AS count FROM " . $avalanche->PREFIX() . "reminder_reminders";
		$result = $avalanche->mysql_query($sql);
		if($row = mysql_fetch_array($result)){
			return (int)$row["count"];
		}
		return 0;
	}
	
	/**
	 * returns the number of months purchased so far
	 * ie, the number of months between the addedOn date and expiration date
	 */
	public function monthsSoFar(){
		$expires = new MMDateTime($this->expiresOn());
		$added = new MMDateTime($this->addedOn());

		return ($expires->getTimeStamp() - $added->getTimeStamp())/(60*60*24*30);
	}


	/**
	 * returns the fraction of months left for this account
	 * difference between now and expiration date
	 * (could be negative)
	 */
	public function getMonthsLeft(){
		$strongcal = $this->getAvalanche()->getModule("strongcal");
		$now = new MMDateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
		$expires = new MMDateTime($this->expiresOn());
		$timeleft = $expires->getTimeStamp() - $now->getTimeStamp();
		$months_left = $timeleft / (60*60*24*30);
		return $months_left;
	}


	/**
	 * returns the account's pending transaction
	 */
	 public function getPendingTransaction(){
		 $sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts_transactions WHERE account_name = '" . $this->name() . "' AND pending='1' ORDER BY purchase_date DESC";
		 $result = $this->avalanche->mysql_query($sql);
		 $ret = false;
		 while($myrow = mysql_fetch_array($result)){
			 $ret = new AccountTransaction($this->avalanche, (int)$myrow["id"]);
		 }
		 if($ret === false){
			 $ret = new AccountTransaction($this->avalanche);
			 $ret->pending(true);
			 $ret->account($this);
			 $ret->users($this->maxUsers());
			 $calc = new TransactionCalculator($this->avalanche, $ret);
			 // sets up the transaction to the default options
			 $calc->setToDefault();
			 return $ret;
		 }
		 return $ret;
	 }


	/**
	 * returns all transactions for this account
	 */
	 public function getTransactions(){
		 $sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts_transactions WHERE account_name = '" . $this->name() . "' AND pending='0' ORDER BY purchase_date DESC";
		 $result = $this->avalanche->mysql_query($sql);
		 $ret = array();
		 while($myrow = mysql_fetch_array($result)){
			 $ret[] = new AccountTransaction($this->avalanche, (int)$myrow["id"]);
		 }
		 return $ret;
	 }


	 public function isDemo(){
		$trans = $this->getTransactions();
		if(count($trans)){
			if($this->monthsSoFar() == 1){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	 }

	 public function process($t){
		if(!is_object($t) || !($t instanceof AccountTransaction)){
			 throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a AccountTransaction object");
		}
		// update month's purchased
		$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `months` = '" . ($this->monthsSoFar() + $t->quantity()) . "' WHERE `id` = '" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);

		// update expiration date
		if($this->getMonthsLeft() < 0){
			// i'm expired, reset expiration date based on now
			$strongcal = $this->getAvalanche()->getModule("strongcal");
			$now = new MMDateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
			$now->month($now->month() + $t->quantity());
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `expires_on` = '" . ($now->toString()) . "' WHERE `id` = '" . $this->getId() . "'";
		}else{
			// i'm not expired, add to expiration date
			$expires = new MMDateTime($this->expiresOn());
			$expires->month($expires->month() + $t->quantity());
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `expires_on` = '" . ($expires->toString()) . "' WHERE `id` = '" . $this->getId() . "'";
		}
		$result = $this->avalanche->mysql_query($sql);

		if($this->disabled() && $t->quantity() > 0){
			$this->_disable(false);
		}

		// update num users allowed
		$u = max($t->product()->users(),$t->users());
		$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `users` = '" . $u . "' WHERE `id` = '" . $this->getId() . "'";
		$result = $this->avalanche->mysql_query($sql);
		$this->loaded = false;
	 }

	 public function findCurrentProduct(){
		$u = $this->maxUsers();

		$accounts = $this->avalanche->getModule("accounts");
		$products = $accounts->getProducts();
		// at least set it as something large to start with...
		$maxppu = 99;
		foreach($products as $p){
			$maxppu = max($maxppu, $p->pricePerUser());
		}
		// make the total larger than any other
		$total = $maxppu * ($u + 1);
		$best = false;
		foreach($products as $p){
			$new_total = $p->pricePerMonth() + (max(0, $u - $p->users())) * $p->pricePerUser();
			if($new_total < $total){
				$total = $new_total;
				$best = $p;
			}
		}
		return $best;
	}

}
?>
