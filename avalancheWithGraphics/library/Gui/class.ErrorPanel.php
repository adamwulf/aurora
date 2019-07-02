<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class ErrorPanel extends Panel{

	/**
	 * basic constructor
	 */
	function __construct(Component $comp){
		parent::__construct();
		$this->getStyle()->setWidth("100%");
		$this->getStyle()->setHeight("100%");
		$this->setAlign("center");
		$this->setValign("middle");
		$this->add($comp);
	}
}



?>
