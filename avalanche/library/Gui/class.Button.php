<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class Button extends Panel{

	private $_icon;
	
	function __construct($text = false){
		parent::__construct();
		if($text !== false){
			$text = new Text($text);
			$text->setAlign("center");
			$text->getStyle()->setTextAlign("center");
			$this->add($text);
		}
		$this->_icon = false;
		
		$this->setAlign("center");
		$this->setValign("middle");
		
		$this->getStyle()->setPaddingTop(1);
		$this->getStyle()->setPaddingBottom(1);
		$this->getStyle()->setPaddingLeft(3);
		$this->getStyle()->setPaddingRight(3);
		$this->getStyle()->setBorderWidth(1);
		$this->getStyle()->setBorderColor("black");
		$this->getStyle()->setBorderStyle("solid");
	}

	
	public function setIcon(Icon $i){
		$this->_icon = $i;
	}
	
	public function getIcon(){
		return $this->_icon;
	}
}
?>
