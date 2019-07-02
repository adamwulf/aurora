<?

/**
 * This class represents an form text input.
 */
class DateInput extends Input{
	
	/**
	 * the year text input
	 */
	private $_year;
	/**
	 * the month text input
	 */
	private $_month;
	/**
	 * the day text input
	 */
	private $_day;
	/**
	 * the dow text input
	 */
	private $_dow;
	/**
	 * the current DOW action
	 */
	private $_action;
	
	// the number only action
	private $_a;
	/**
	 * $value is a string of form "yyyy-mm-dd"
	 */
	function __construct($value="0000-00-00"){
		parent::__construct();
		
		if(!is_string($value)){
			throw new IllegalArgumentException("Argument 1 to " . __METHOD__ . " must be a string");
		}
		$this->_a = new NumberOnlyAction();
		$year = substr($value, 0, 4);
		$month = substr($value, 5, 2);
		$day = substr($value, 8, 2);
		
		$this->_year  = new SmallTextInput($year);
		$this->_month = new SmallTextInput($month);
		$this->_day   = new SmallTextInput($day);
		
		$this->_year->setMaxLength(4);
		$this->_year->setSize(4);
		$this->_year->getStyle()->setBorderWidth(0);
		$this->_year->addKeyPressAction($this->_a);
		$if = new CompareIntBoxAction($this->_year, 2030, ">");
		$then = new SetValueAction($this->_year, "2030");
		$a = new IfThenAction($if,$then);
		$this->_year->addChangeAction($a);
		$if = new CompareIntBoxAction($this->_year, 1960, "<");
		$then = new SetValueAction($this->_year, "1960");
		$a = new IfThenAction($if,$then);
		$this->_year->addChangeAction($a);
		
		$this->_month->setMaxLength(2);
		$this->_month->setSize(2);
		$this->_month->getStyle()->setBorderWidth(0);
		$this->_month->addKeyPressAction($this->_a);
		$if = new CompareIntBoxAction($this->_month, 1, "<");
		$then = new SetValueAction($this->_month, "1");
		$a = new IfThenAction($if,$then);
		$this->_month->addChangeAction($a);
		$if = new CompareIntBoxAction($this->_month, 12, ">");
		$then = new SetValueAction($this->_month, "12");
		$a = new IfThenAction($if,$then);
		$this->_month->addChangeAction($a);
		
		$this->_day->setMaxLength(2);
		$this->_day->setSize(2);
		$this->_day->getStyle()->setBorderWidth(0);
		$this->_day->addKeyPressAction($this->_a);
		$if = new CompareIntBoxAction($this->_day, 1, "<");
		$then = new SetValueAction($this->_day, "1");
		$a = new IfThenAction($if,$then);
		$this->_day->addChangeAction($a);

		$a = new VerifyDOMAction($this->_year, $this->_month, $this->_day);
		$this->_day->addChangeAction($a);
		$this->_month->addChangeAction($a);
		$this->_year->addChangeAction($a);
		
		$this->getStyle()->setBorderWidth(1);
		$this->getStyle()->setBorderColor("black");
		$this->getStyle()->setBorderStyle("solid");
		$this->getStyle()->setBackground("white");


		$dow_array = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
		try{
			$dow = $dow_array[date("w",mktime(0,0,0,$month,$day,$year))];
		}catch(Exception $e){
			$dow = "Er";
		}
		$this->_dow = new SmallTextInput($dow);
		$this->_dow->setReadOnly(true);
		$this->_dow->setMaxLength(2);
		$this->_dow->setSize(2);
		$this->_dow->getStyle()->setBorderWidth(0);
		$this->addChangeAction($this->getChangeAction());
	}
	
	public function getChangeAction(){
		return new ManualAction("xGetElementById(\"" . $this->_dow->getId() . "\").value = xGetDOW(new Date(xGetElementById(\"" . $this->_year->getId() . "\").value, xGetElementById(\"" . $this->_month->getId() . "\").value-1, xGetElementById(\"" . $this->_day->getId() . "\").value,0,0,0).getDay())");
	}
	
	public function setName($str){
		parent::setName($str);
		$this->_year->setName($this->getName() . "_year");
		$this->_month->setName($this->getName() . "_month");
		$this->_day->setName($this->getName() . "_day");
		$this->_dow->setName($this->getName() . "_dow");
	}
	
	public function getValue(){
		$year = (int) $this->_year->getValue();
		$month = (int) $this->_month->getValue();
		$day = (int) $this->_day->getValue();
		if($year < 1000 || $year > 9999){
			$year = (string) date("Y");
		}else{
			$year = (string) $year;
		}
		if($month < 10){
			$month = "0" . $month;
		}else{
			$month = (string) $month;
		}
		if($day < 10){
			$day = "0" . $day;
		}else{
			$day = (string) $day;
		}
		return $year . "-" . $month . "-" . $day;
	}
	
	public function setValue($str){
		if(!is_string($str) || is_string($str) && strlen($str) < 10){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string of length 10");
		}
		$year = substr($str, 0, 4);
		$month = substr($str, 5, 2);
		$day = substr($str, 8, 2);
		
		$dow = substr(date("D", mktime(0,0,0,$month,$day,$year)), 0, 2);
		
		$this->_year->setValue($year);
		$this->_month->setValue($month);
		$this->_day->setValue($day);
		$this->_dow->setValue($dow);
	}
	
	/**
	 * loads this fields value from form input if possible
	 */
	function loadFormValue($my_array){
		$year_name = $this->_year->getName();
		$month_name = $this->_month->getName();
		$day_name = $this->_day->getName();
		if(isset($my_array[$year_name]) && isset($my_array[$month_name]) && isset($my_array[$day_name])){
			$this->_year->setValue($my_array[$year_name]);
			$this->_month->setValue($my_array[$month_name]);
			$this->_day->setValue($my_array[$day_name]);
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * getters for the three text components
	 */
	function getYearComponent(){
		$this->_year->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_year;
	}	
	
	function getMonthComponent(){
		$this->_month->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_month;
	}
	
	function getDayComponent(){
		$this->_day->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_day;
	}
	
	function getDOWComponent(){
		$this->_dow->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_dow;
	}
	
	
	/**
	 * pass on the read only...
	 */
	public function setReadOnly($ro){
		parent::setReadOnly($ro);
		$this->_year->setReadOnly($ro);
		$this->_month->setReadOnly($ro);
		$this->_day->setReadOnly($ro);
	}

	/**
	 * functions to pass on actions to the rest...
	 */
	public function addClickAction(NonKeyAction $a){
		parent::addClickAction($a);
		$this->_year->addClickAction($a);
		$this->_month->addClickAction($a);
		$this->_day->addClickAction($a);
		$this->_dow->addClickAction($a);
	}

	public function removeClickAction(NonKeyAction $a){
		parent::removeClickAction($a);
		$this->_year->removeClickAction($a);
		$this->_month->removeClickAction($a);
		$this->_day->removeClickAction($a);
		$this->_dow->removeClickAction($a);
	}
	
	public function addKeyPressAction(Action $a){
		parent::addKeyPressAction($a);
		$this->_year->removeKeyPressAction($this->_a);
		$this->_month->removeKeyPressAction($this->_a);
		$this->_day->removeKeyPressAction($this->_a);
		$this->_year->addKeyPressAction($a);
		$this->_month->addKeyPressAction($a);
		$this->_day->addKeyPressAction($a);
		$this->_dow->addKeyPressAction($a);
		$this->_year->addKeyPressAction($this->_a);
		$this->_month->addKeyPressAction($this->_a);
		$this->_day->addKeyPressAction($this->_a);
	}
	
	public function removeKeyPressAction(Action $a){
		parent::removeKeyPressAction($a);
		$this->_year->removeKeyPressAction($a);
		$this->_month->removeKeyPressAction($a);
		$this->_day->removeKeyPressAction($a);
		$this->_dow->removeKeyPressAction($a);
	}

	public function addChangeAction(NonKeyAction $a){
		parent::addChangeAction($a);
		$this->_year->addChangeAction($a);
		$this->_month->addChangeAction($a);
		$this->_day->addChangeAction($a);
		$this->_dow->addChangeAction($a);
	}

	public function removeChangeAction(NonKeyAction $a){
		parent::removeChangeAction($a);
		$this->_year->removeChangeAction($a);
		$this->_month->removeChangeAction($a);
		$this->_day->removeChangeAction($a);
		$this->_dow->removeChangeAction($a);
	}

	public function addFocusGainedAction(NonKeyAction $a){
		parent::addFocusGainedAction($a);
		$this->_year->addFocusGainedAction($a);
		$this->_month->addFocusGainedAction($a);
		$this->_day->addFocusGainedAction($a);
		$this->_dow->addFocusGainedAction($a);
	}

	public function removeFocusGainedAction(NonKeyAction $a){
		parent::removeFocusGainedAction($a);
		$this->_year->removeFocusGainedAction($a);
		$this->_month->removeFocusGainedAction($a);
		$this->_day->removeFocusGainedAction($a);
		$this->_dow->removeFocusGainedAction($a);
	}

	public function addFocusLostAction(NonKeyAction $a){
		parent::addFocusLostAction($a);
		$this->_year->addFocusLostAction($a);
		$this->_month->addFocusLostAction($a);
		$this->_day->addFocusLostAction($a);
		$this->_dow->addFocusLostAction($a);
	}

	public function removeFocusLostAction(NonKeyAction $a){
		parent::removeFocusLostAction($a);
		$this->_year->removeFocusLostAction($a);
		$this->_month->removeFocusLostAction($a);
		$this->_day->removeFocusLostAction($a);
		$this->_dow->removeFocusLostAction($a);
	}
}
?>
