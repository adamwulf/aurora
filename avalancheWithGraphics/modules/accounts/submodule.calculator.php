<?



class TransactionCalculator{
	private $avalanche;
	private $transaction;
	
	function __construct($avalanche, $t){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an object");
		}
		if(!is_object($t)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an object");
		}
		$this->avalanche = $avalanche;
		$this->transaction = $t;
	}
	
	/**
	 * sets the transaction to the default choices
	 */
	public function setToDefault(){
		if($this->transaction->account()->isDemo()){
			$this->transaction->users(count($this->transaction->account()->getAvalanche()->getAllUsers()) - 1);
		}else{
			$this->transaction->users($this->transaction->account()->maxUsers());
		}
		$this->transaction->quantity(12);
		$accounts = $this->avalanche->getModule("accounts");
		$products = $accounts->getProducts();
		$maxppu = 0;
		$maxppm = 0;
		foreach($products as $p){
			$maxppu = max($maxppu, $p->pricePerUser());
			$maxppm = max($maxppm, $p->pricePerMonth());
		}
		// make the total larger than any other
		$total = $maxppm + $maxppu * $this->transaction->users() + 1;
		foreach($products as $p){
			$new_total = $p->pricePerMonth() + (max(0, $this->transaction->users() - $p->users())) * $p->pricePerUser();
			if($new_total < $total){
				$total = $new_total;
				$this->transaction->product($p);
				$this->transaction->total($total);
			}
		}
	}
	
	/**
	 * sets the transaction to the optimum choices given the account status
	 * ie, if the transaction wants 8 users for 12 months, this function
	 * will set the transaction product to the ideal product type
	 */
	public function setToOptimum(){
		$accounts = $this->avalanche->getModule("accounts");
		$products = $accounts->getProducts();
		$maxppu = 0;
		$maxppm = 0;
		foreach($products as $p){
			$maxppu = max($maxppu, $p->pricePerUser());
			$maxppm = max($maxppm, $p->pricePerMonth());
		}
		// make the total larger than any other
		$total = $maxppm + $maxppu * $this->transaction->users() + 1;
		foreach($products as $p){
			$new_total = $p->pricePerMonth() + (max(0, $this->transaction->users() - $p->users())) * $p->pricePerUser();
			if($new_total < $total){
				$total = $new_total;
				$this->transaction->product($p);
				$this->transaction->total($total);
			}
		}
	}
	/**
	 * returns true if the transaction will affect an account
	 * that is not disabled. ie, it will affect/upgrade time
	 * that has already been purchased
	 */
	public function isUpdate(){
		return $this->transaction->account()->getMonthsLeft() > 0;
	}
	
	
	public function calculateTotal(){
		$u = $this->transaction->users();
		$q = $this->transaction->quantity();
		$p = $this->transaction->product();
		
		$new_month_price = ($p->pricePerMonth() + max(0, $u - $p->users())*$p->pricePerUser());
		$new_months = $q * $new_month_price;
		
		$old_p = $this->transaction->account()->findCurrentProduct();
		$old_month_price = ($old_p->pricePerMonth() + ($this->transaction->account()->maxUsers() - $old_p->users()) * $old_p->pricePerUser());
		
		$curr_months = round(($new_month_price - $old_month_price) * $this->transaction->account()->getMonthsLeft(),2);

		if(!$this->transaction->account()->isDemo()){
			$total = $new_months + $curr_months;		
			if($this->applyDiscountHuh()){
				$d = $this->transaction->account()->discount()/100;
				$total = (1-$d)*$total;
			}
		}else{
			$total = $new_months;		
			if($q >= 12){
				$d = $this->transaction->account()->discount()/100;
				$total = (1-$d)*$total;
			}
		}
		
		$total = round($total,2);
		return (double)$total;
		
	}
	
	public function applyDiscountHuh(){
		$u = $this->transaction->users();
		$q = $this->transaction->quantity();
		$p = $this->transaction->product();
		
		$new_month_price = ($p->pricePerMonth() + max(0, $u - $p->users())*$p->pricePerUser());
		
		$old_p = $this->transaction->account()->findCurrentProduct();
		$old_month_price = ($old_p->pricePerMonth() + ($this->transaction->account()->maxUsers() - $old_p->users()) * $old_p->pricePerUser());
		
		$curr_months = round($new_month_price - $old_month_price,2);
		return !$this->transaction->account()->isDemo() && ($this->transaction->account()->getMonthsLeft() + $this->transaction->quantity()) >= 12 && $curr_months > 0 ||
		       $this->transaction->quantity() >= 12;
	}
}
?>
