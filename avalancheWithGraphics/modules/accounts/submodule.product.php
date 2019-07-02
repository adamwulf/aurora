<?



class AccountProduct{
	private $avalanche;
	private $id;
	
	private $description;
	private $ppm;
	private $ppu;
	
	function __construct($avalanche, $vals){
		$this->avalanche = $avalanche;
		$accounts = $this->avalanche->getModule("accounts");
		if(is_array($vals)){
			// load the transaction
			$this->id = (int)$vals["id"];
			$this->description = $vals["description"];
			$this->ppm = (double) $vals["price_per_month"];
			$this->ppu = (double) $vals["price_per_user"];
			$this->users = (int) $vals["users"];
		}else{
			throw new IllegalArgumentException("2nd argument to " . __METHOD__ . " must be an associated array");
		}
	}
	
	public function getId(){
		return $this->id;
	}

	public function description($d = false){
		if(is_string($d)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_products SET description = '" . $d . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->description = $d;
			return $d;
		}else if($d === false){
			return (string)$this->description;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function pricePerMonth($p = false){
		if(is_double($p)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_products SET price_per_month = '" . $p . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->ppm = $p;
			return $p;
		}else if($p === false){
			return (double)$this->ppm;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a double");
		}
	}

	public function pricePerUser($p = false){
		if(is_double($p)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_products SET price_per_user = '" . $p . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->ppu = $p;
			return $p;
		}else if($p === false){
			return (double)$this->ppu;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}

	public function users($u = false){
		if(is_int($u)){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts_products SET users = '" . $u . "' WHERE id='" . $this->getId() . "'";
			$this->avalanche->mysql_query($sql);
			$this->users = $u;
			return $u;
		}else if($u === false){
			return (int)$this->users;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a integer");
		}
	}
}
?>
