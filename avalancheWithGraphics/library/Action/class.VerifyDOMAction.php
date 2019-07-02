<?

/**
 * selects the value of a text input
 */
class VerifyDOMAction extends NonKeyAction{
	
	protected $d;
	protected $m;
	protected $y;
	
	public function __construct(TextInput $y, TextInput $m, TextInput $d){
		$this->y = $y;
		$this->m = $m;
		$this->d = $d;
	}
	
	public function toJS(){
		$y = "parseInt(xGetElementById(\"" . $this->y->getId() . "\").value,10)";
		$m = "parseInt(xGetElementById(\"" . $this->m->getId() . "\").value,10)";
		$max = "getMaxDOM($y, $m)";
		return "if(parseInt(xGetElementById(\"" . $this->d->getId() . "\").value,10) > $max){xGetElementById(\"" . $this->d->getId() . "\").value = $max};";
	}
}


?>