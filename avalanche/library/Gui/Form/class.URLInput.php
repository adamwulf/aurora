<?

/**
 * This class represents an form text input.
 */
class URLInput extends Input{
	
	/**
	 * the text input
	 */
	private $_text;
	/**
	 * the link input
	 */
	private $_link;
	/**
	 * the current DOW action
	 */
	private $_action;
	/**
	 * $value is a string of form "text\nlink"
	 */
	function __construct($value=""){
		parent::__construct();
		
		if(!is_string($value)){
			throw new IllegalArgumentException("Argument 1 to " . __METHOD__ . " must be a string");
		}
		$values = explode("\n", $value);
		if(count($values) == 2){
			$text = trim($values[0]);
			$link = trim($values[1]);
		}else{
			$text = "";
			$link = "";
		}
		
		$this->_text = new SmallTextInput($text);
		$this->_link = new SmallTextInput($link);
		
		$this->_text->setSize(20);
		$this->_link->setSize(20);
		
		$this->_text->getStyle()->setBorderWidth(1);
		$this->_text->getStyle()->setBorderColor("black");
		$this->_text->getStyle()->setBorderStyle("solid");
		$this->_text->getStyle()->setBackground("white");
		$this->_link->setStyle($this->_text->getStyle());
	}
	
	public function setName($str){
		parent::setName($str);
		$this->_text->setName($this->getName() . "_text");
		$this->_link->setName($this->getName() . "_link");
	}
	
	public function getValue(){
		$text = $this->_text->getValue();
		$link = $this->_link->getValue();
		return $text . "\n" . $link;
	}
	
	public function setValue($str){
		if(!is_string($str)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		$values = explode("\n", $value);
		if(count($values) == 2){
			$text = trim($values[0]);
			$link = trim($values[1]);
		}else{
			$text = "";
			$link = "";
		}
		$this->_text->setValue($text);
		$this->_link->setValue($link);
	}
	
	/**
	 * loads this fields value from form input if possible
	 */
	function loadFormValue($my_array){
		$text_name = $this->_text->getName();
		$link_name = $this->_link->getName();
		if(isset($my_array[$text_name]) && isset($my_array[$link_name])){
			$text = $my_array[$text_name];
			$link = $my_array[$link_name];
			if(get_magic_quotes_gpc()){
				$text = stripslashes($my_array[$text_name]);
				$link = stripslashes($my_array[$link_name]);
			}
			$this->_text->setValue($text);
			$this->_link->setValue($link);
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * getters for the three text components
	 */
	function getTextComponent(){
		$this->_text->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_text;
	}	
	
	function getLinkComponent(){
		$this->_link->getStyle()->setBackground($this->getStyle()->getBackground());
		return $this->_link;
	}
	
	/**
	 * pass on the read only...
	 */
	public function setReadOnly($ro){
		parent::setReadOnly($ro);
		$this->_text->setReadOnly($ro);
		$this->_link->setReadOnly($ro);
	}

	/**
	 * functions to pass on actions to the rest...
	 */
	public function addClickAction(NonKeyAction $a){
		parent::addClickAction($a);
		$this->_text->addClickAction($a);
		$this->_link->addClickAction($a);
	}

	public function removeClickAction(NonKeyAction $a){
		parent::removeClickAction($a);
		$this->_text->removeClickAction($a);
		$this->_link->removeClickAction($a);
	}
	
	public function addKeyPressAction(Action $a){
		parent::addKeyPressAction($a);
		$this->_text->addKeyPressAction($a);
		$this->_link->addKeyPressAction($a);
	}
	
	public function removeKeyPressAction(Action $a){
		parent::removeKeyPressAction($a);
		$this->_text->removeKeyPressAction($a);
		$this->_link->removeKeyPressAction($a);
	}

	public function addChangeAction(NonKeyAction $a){
		parent::addChangeAction($a);
		$this->_text->addChangeAction($a);
		$this->_link->addChangeAction($a);
	}

	public function removeChangeAction(NonKeyAction $a){
		parent::removeChangeAction($a);
		$this->_text->removeChangeAction($a);
		$this->_link->removeChangeAction($a);
	}

	public function addFocusGainedAction(NonKeyAction $a){
		parent::addFocusGainedAction($a);
		$this->_text->addFocusGainedAction($a);
		$this->_link->addFocusGainedAction($a);
	}

	public function removeFocusGainedAction(NonKeyAction $a){
		parent::removeFocusGainedAction($a);
		$this->_text->removeFocusGainedAction($a);
		$this->_link->removeFocusGainedAction($a);
	}

	public function addFocusLostAction(NonKeyAction $a){
		parent::addFocusLostAction($a);
		$this->_text->addFocusLostAction($a);
		$this->_link->addFocusLostAction($a);
	}

	public function removeFocusLostAction(NonKeyAction $a){
		parent::removeFocusLostAction($a);
		$this->_text->removeFocusLostAction($a);
		$this->_link->removeFocusLostAction($a);
	}
}
?>
