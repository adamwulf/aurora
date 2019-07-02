<?

/**
 * selects the value of a text input
 */
class MinDateAction extends NonKeyAction{
	
	protected $sd;
	protected $st;
	protected $ed;
	protected $et;
	
	public function __construct(DateInput $sd, DateInput $ed){
		$this->sd = $sd;
		$this->ed = $ed;
	}
	
	public function toJS(){
		// if sd.year > ed.year || sd.year == ed.year && sd.month > ed.month ||
		$sdyear  = "xGetElementById(\"" . $this->sd->getYearComponent()->getId() . "\")";
		$sdmonth = "xGetElementById(\"" . $this->sd->getMonthComponent()->getId() . "\")";
		$sdday   = "xGetElementById(\"" . $this->sd->getDayComponent()->getId() . "\")";
		$edyear  = "xGetElementById(\"" . $this->ed->getYearComponent()->getId() . "\")";
		$edmonth = "xGetElementById(\"" . $this->ed->getMonthComponent()->getId() . "\")";
		$edday   = "xGetElementById(\"" . $this->ed->getDayComponent()->getId() . "\")";
		return "if(parseInt($sdyear.value,10) > parseInt($edyear.value,10) || parseInt($sdyear.value,10) == parseInt($edyear.value,10) && 
		          (parseInt($sdmonth.value,10) > parseInt($edmonth.value,10) || parseInt($sdmonth.value,10) == parseInt($edmonth.value,10) && 
			  (parseInt($sdday.value,10) > parseInt($edday.value,10)))){
			  $edyear.value = $sdyear.value;
			  $edmonth.value = $sdmonth.value;
			  $edday.value = $sdday.value;	  
			}\n";
	}
}


?>