<?

/**
 * This class represents an form text input.
 */
class TimeInput extends Input{
	
	/**
	 * the hour text input
	 */
	private $_hour;
	/**
	 * the minute text input
	 */
	private $_minute;
	/**
	 * the ampm field
	 */
	private $_ampm;

	
	private $_a;

	/**
	 * $value is a string of form "HH-mm"
	 * it's in 24 hour time. ie, 23:52 is 11:52pm
	 */
	function __construct($value="00:00:00"){
		parent::__construct();
		
		if(!is_string($value)){
			throw new IllegalArgumentException("Argument 1 to " . __METHOD__ . " must be a string");
		}
		$this->_a = new NumberOnlyAction();
		// initialize the form fields
		$this->_hour   = new SmallTextInput();
		$this->_minute = new SmallTextInput();
		$this->_ampm   = new SmallTextInput();

		// initialize the form field values
		$this->setValue($value);		
		
		// set up style and look and feel
		$this->_hour->setMaxLength(2);
		$this->_hour->setSize(2);
		$this->_hour->getStyle()->setBorderWidth(0);
		$this->_hour->addKeyPressAction($this->_a);
		$if = new CompareIntBoxAction($this->_hour, 0, "==");
		$then = new SetValueAction($this->_hour, "1");
		$a = new IfThenAction($if,$then);
		$this->_hour->addChangeAction($a);
		$if = new CompareIntBoxAction($this->_hour, 12, ">");
		$then = new SetValueAction($this->_hour, "12");
		$a = new IfThenAction($if,$then);
		$this->_hour->addChangeAction($a);
		
		$this->_minute->setMaxLength(2);
		$this->_minute->setSize(2);
		$this->_minute->getStyle()->setBorderWidth(0);
		$this->_minute->addKeyPressAction($this->_a);
		$if = new CompareIntBoxAction($this->_minute, 59, ">");
		$then = new SetValueAction($this->_minute, "59");
		$a = new IfThenAction($if,$then);
		$this->_minute->addChangeAction($a);
		$if = new CompareIntBoxAction($this->_minute, 0, "==");
		$then = new SetValueAction($this->_minute, "00");
		$a = new IfThenAction($if,$then);
		$this->_minute->addChangeAction($a);
		
		$this->_ampm->setSize(2);
		$this->_ampm->setMaxLength(2);
		$this->_ampm->setReadOnly(true);
		$select_all_action = new SelectAction($this->_ampm);
		$this->_ampm->getStyle()->setborderWidth(0);
		$this->_ampm->addClickAction($select_all_action);
		$this->_ampm->addKeyPressAction(new AmPmAction($this->_ampm));
		$this->_ampm->addKeyPressAction($select_all_action);
		
		$this->getStyle()->setBorderWidth(1);
		$this->getStyle()->setBorderColor("black");
		$this->getStyle()->setBorderStyle("solid");
		$this->getStyle()->setBackground("white");
	}
	
	public function setName($str){
		parent::setName($str);
		$this->_hour->setName($this->getName() . "_hour");
		$this->_minute->setName($this->getName() . "_minute");
		$this->_ampm->setName($this->getName() . "_ampm");
	}
	
	public function getValue(){
		$hour   = (int) $this->_hour->getValue();
		$minute = (int) $this->_minute->getValue();
		$ampm   = $this->_ampm->getValue();
		if($hour == 12 && (strcasecmp($ampm, "am") === 0)){
			$hour = 0;
			$ampm = "";
		}else if(($hour != 12) && (strcasecmp($ampm, "pm") === 0)) $hour += 12;
		
		// now, $hour and $minute have the right number values, let's make sure they're strings.
		
		if($hour < 10){
			$hour = "0" . $hour;
		}
		if($minute < 10){
			$minute = "0" . $minute;
		}
		
		return $hour . ":" . $minute . ":00";
	}
	
	public function setValue($str){
		if(!is_string($str) || is_string($str) && strlen($str) < 5){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string of length 5 in HH:mm format");
		}
		$hour = (int) substr($str, 0, 2);
		$min  = (int) substr($str, 3, 2);
		$ampm = "am";
		if($hour >= 12 && $hour != 24){
			$ampm = "pm";
		}
		if($hour > 12) $hour -= 12;
		if($hour == 0) $hour = 12;
		
		
		if($hour < 10) $hour = "0" . $hour;
		if($min  < 10) $min  = "0" . $min;
		$hour = (string) $hour;
		$min  = (string) $min;
		
		
		$this->_hour->setValue($hour);
		$this->_minute->setValue($min);
		$this->_ampm->setValue($ampm);
	}
	
	/**
	 * loads this fields value from form input if possible
	 */
	function loadFormValue($my_array){
		$hour_name = $this->_hour->getName();
		$minute_name = $this->_minute->getName();
		$ampm_name = $this->_ampm->getName();
		if(isset($my_array[$hour_name]) && isset($my_array[$minute_name]) && isset($my_array[$ampm_name])){
			$this->_hour->loadFormValue($my_array);
			$this->_minute->loadFormValue($my_array);
			$this->_ampm->loadFormValue($my_array);
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * getters for the three text components
	 */
	function getHourComponent(){
		$this->_hour->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_hour;
	}	
	
	function getMinuteComponent(){
		$this->_minute->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_minute;
	}
	
	function getAMPMComponent(){
		$this->_ampm->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_ampm;
	}
	
	
	/**
	 * pass on the readonly action
	 */
	public function setReadOnly($ro){
		parent::setReadOnly($ro);
		$this->_hour->setReadOnly($ro);
		$this->_minute->setReadOnly($ro);
	}

	/**
	 * functions to pass on actions to the rest...
	 */
	public function addClickAction(NonKeyAction $a){
		parent::addClickAction($a);
		$this->_hour->addClickAction($a);
		$this->_minute->addClickAction($a);
		$this->_ampm->addClickAction($a);
	}

	public function removeClickAction(NonKeyAction $a){
		parent::removeClickAction($a);
		$this->_hour->removeClickAction($a);
		$this->_minute->removeClickAction($a);
		$this->_ampm->removeClickAction($a);
	}
	
	public function addKeyPressAction(Action $a){
		parent::addKeyPressAction($a);
		$this->_hour->removeKeyPressAction($this->_a);
		$this->_minute->removeKeyPressAction($this->_a);
		$this->_hour->addKeyPressAction($a);
		$this->_minute->addKeyPressAction($a);
		$this->_ampm->addKeyPressAction($a);
		$this->_hour->addKeyPressAction($this->_a);
		$this->_minute->addKeyPressAction($this->_a);
	}
	
	public function removeKeyPressAction(Action $a){
		parent::removeKeyPressAction($a);
		$this->_hour->removeKeyPressAction($a);
		$this->_minute->removeKeyPressAction($a);
		$this->_ampm->removeKeyPressAction($a);
	}

	public function addChangeAction(NonKeyAction $a){
		parent::addChangeAction($a);
		$this->_hour->addChangeAction($a);
		$this->_minute->addChangeAction($a);
		$this->_ampm->addChangeAction($a);
	}

	public function removeChangeAction(NonKeyAction $a){
		parent::removeChangeAction($a);
		$this->_hour->removeChangeAction($a);
		$this->_minute->removeChangeAction($a);
		$this->_ampm->removeChangeAction($a);
	}

	public function addFocusGainedAction(NonKeyAction $a){
		parent::addFocusGainedAction($a);
		$this->_hour->addFocusGainedAction($a);
		$this->_minute->addFocusGainedAction($a);
		$this->_ampm->addFocusGainedAction($a);
	}

	public function removeFocusGainedAction(NonKeyAction $a){
		parent::removeFocusGainedAction($a);
		$this->_hour->removeFocusGainedAction($a);
		$this->_minute->removeFocusGainedAction($a);
		$this->_ampm->removeFocusGainedAction($a);
	}

	public function addFocusLostAction(NonKeyAction $a){
		parent::addFocusLostAction($a);
		$this->_hour->addFocusLostAction($a);
		$this->_minute->addFocusLostAction($a);
		$this->_ampm->addFocusLostAction($a);
	}

	public function removeFocusLostAction(NonKeyAction $a){
		parent::removeFocusLostAction($a);
		$this->_hour->removeFocusLostAction($a);
		$this->_minute->removeFocusLostAction($a);
		$this->_ampm->removeFocusLostAction($a);
	}
}
?>
