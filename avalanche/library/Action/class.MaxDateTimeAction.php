<?

/**
 * selects the value of a text input
 */
class MaxDateTimeAction extends NonKeyAction{
	
	protected $sd;
	protected $st;
	protected $ed;
	protected $et;
	
	public function __construct(DateInput $sd, TimeInput $st, DateInput $ed, TimeInput $et){
		$this->sd = $sd;
		$this->ed = $ed;
		$this->st = $st;
		$this->et = $et;
	}
	
	public function toJS(){
		// if sd.year > ed.year || sd.year == ed.year && sd.month > ed.month ||
		$sdyear  = "xGetElementById(\"" . $this->sd->getYearComponent()->getId() . "\")";
		$sdmonth = "xGetElementById(\"" . $this->sd->getMonthComponent()->getId() . "\")";
		$sdday   = "xGetElementById(\"" . $this->sd->getDayComponent()->getId() . "\")";
		$sthour  = "xGetElementById(\"" . $this->st->getHourComponent()->getId() . "\")";
		$stmin   = "xGetElementById(\"" . $this->st->getMinuteComponent()->getId() . "\")";
		$stam    = "xGetElementById(\"" . $this->st->getAMPMComponent()->getId() . "\")";
		$edyear  = "xGetElementById(\"" . $this->ed->getYearComponent()->getId() . "\")";
		$edmonth = "xGetElementById(\"" . $this->ed->getMonthComponent()->getId() . "\")";
		$edday   = "xGetElementById(\"" . $this->ed->getDayComponent()->getId() . "\")";
		$ethour  = "xGetElementById(\"" . $this->et->getHourComponent()->getId() . "\")";
		$etmin   = "xGetElementById(\"" . $this->et->getMinuteComponent()->getId() . "\")";
		$etam    = "xGetElementById(\"" . $this->et->getAMPMComponent()->getId() . "\")";
		return "if(parseInt($sdyear.value,10) > parseInt($edyear.value,10) || parseInt($sdyear.value,10) == parseInt($edyear.value,10) && 
		          (parseInt($sdmonth.value,10) > parseInt($edmonth.value,10) || parseInt($sdmonth.value,10) == parseInt($edmonth.value,10) && 
			  (parseInt($sdday.value,10) > parseInt($edday.value,10) || parseInt($sdday.value,10) == parseInt($edday.value,10) && 
			  ($stam.value > $etam.value || $stam.value == $etam.value &&
			  (parseInt($sthour.value,10) > parseInt($ethour.value,10) &&  parseInt($sthour.value,10) != 12 && parseInt($ethour.value,10) != 12 || parseInt($sthour.value,10) != 12 && parseInt($ethour.value,10) == 12 || parseInt($sthour.value,10) == parseInt($ethour.value,10) &&
			  (parseInt($stmin.value,10) > parseInt($etmin.value,10))))))){
			  $sdyear.value = $edyear.value;
			  $sdmonth.value = $edmonth.value;
			  $sdday.value = $edday.value;
			  if($stam.value == \"am\" && $etam.value == \"am\"){
				  $etam.value = \"pm\";
			  }else{
				  $stam.value = $etam.value;
				  $sthour.value = $ethour.value;
				  $stmin.value = $etmin.value;
			  }
			};\n";
	}
}


?>