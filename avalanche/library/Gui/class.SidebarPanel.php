<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class SidebarPanel extends Panel{
	private $color;
	
	private $open_width;
	private $open_height;

	private $close_width;
	private $close_height;
	
	private $open_button;
	private $close_button;
	
	private $open_icon;
	private $close_icon;
	
	private $backgroundi;
	private $close_status;
	/**
	 * this is to ensure that the buttons are only
	 * regenerated after calls to set methods.
	 * after a call to a set method, both buttons
	 * will be regenerated after a call to get***Button();
	 */
	private $_generated;
	/**
	 * basic constructor
	 */
	function __construct(){
		parent::__construct(1);
		$this->close_status = false;
		$this->setButtonColor("gray");
		$this->getStyle()->setBackground("silver");
		
		$this->setAlign("left");
		$this->setValign("top");
		$this->getStyle()->setWidth("100%");
		$this->getStyle()->setHeight("100%");
		
		$this->close_button = new Button();
		$this->open_button = new Button();
		
		$this->open_icon = false;
		$this->close_icon = false;
		
		$this->setOpenWidth(150);
		$this->setOpenHeight(20);
		$this->setCloseWidth(20);
		$this->setCloseHeight(150);
		$this->_generated = false;
		
		$this->backgroundi = false;
	}
	
	public function setClosed($b){
		if(!is_bool($b)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a boolean");
		}
		$this->close_status = $b;
	}
	
	public function isClosed(){
		return $this->close_status;
	}
	
	public function setOpenIcon(Icon $i){
		$this->open_icon = $i;
	}
	public function setCloseIcon(Icon $i){
		$this->close_icon = $i;
	}
	public function getOpenIcon(){
		return $this->open_icon;
	}
	public function getCloseIcon(){
		return $this->close_icon;
	}
	
	private function generateButtons(){
		$this->open_button = new Button();
		$this->close_button = new Button();
		$this->generateCloseButton();
		$this->generateOpenButton();
		if($this->getOpenIcon()){
			$this->open_button->setIcon($this->getOpenIcon());
			$this->open_button->getStyle()->setPaddingTop(0);
			$this->open_button->getStyle()->setPaddingBottom(0);
			$this->open_button->getStyle()->setPaddingLeft(0);
			$this->open_button->getStyle()->setPaddingRight(0);
			$this->open_button->getStyle()->setBorderWidth(0);
		}
		if($this->getCloseIcon()){
			$this->close_button->setIcon($this->getCloseIcon());
			$this->close_button->getStyle()->setPaddingTop(0);
			$this->close_button->getStyle()->setPaddingBottom(0);
			$this->close_button->getStyle()->setPaddingLeft(0);
			$this->close_button->getStyle()->setPaddingRight(0);
			$this->close_button->getStyle()->setBorderWidth(0);
		}
		$this->_generated = true;
	}
	
	private function generateCloseButton(){
		$this->setCloseWidth($this->getCloseWidth());
		$this->setCloseHeight($this->getCloseHeight());
		$this->close_button->getStyle()->setBackground("gray");
		if(strlen($this->getClosedBackgroundImage())){
			$this->close_button->addAction(new BackgroundAction($this, $this->getStyle()->getBackground(), $this->getClosedBackgroundImage()));
		}else{
			$this->close_button->addAction(new BackgroundAction($this, $this->getStyle()->getBackground()));
		}
		$this->close_button->addAction(new DisplayBlockAction($this->open_button)); 
		$this->close_button->addAction(new DisplayNoneAction($this->close_button));
		$this->close_button->addAction(new WidthAction($this, $this->getOpenWidth()));
		//$this->close_button->addAction(new ParentHeightAction($this));
	}
	
	private function generateOpenButton(){
		$this->setOpenWidth($this->getOpenWidth());
		$this->setOpenHeight($this->getOpenHeight());
		$this->open_button->getStyle()->setDisplayNone();
		$this->open_button->getStyle()->setBackground($this->getStyle()->getBackground());
		if(strlen($this->getStyle()->getBackgroundImage())){
			$this->open_button->addAction(new BackgroundAction($this, $this->getStyle()->getBackground(), $this->getStyle()->getBackgroundImage()));
		}else{
			$this->open_button->addAction(new BackgroundAction($this, $this->getStyle()->getBackground()));
		}
		$this->open_button->addAction(new DisplayBlockAction($this->close_button)); 
		$this->open_button->addAction(new DisplayNoneAction($this->open_button));
		$this->open_button->addAction(new WidthAction($this, $this->getCloseWidth()));
		//$this->open_button->addAction(new ParentHeightAction($this));
	}

	/**
	 * the color of the button when open,
	 * and the color of the background when closed
	 */
	function setButtonColor($color){
		$this->color = $color;
		$this->_generated = false;
	}

	function getButtonColor(){
		return $this->color;
	}
	
	/**
	 * the width of the button when open
	 */
	function setOpenWidth($w){
		if(!is_integer($w)){
			throw new IllegalArugmentException("Argument to " . __METHOD__ . " must be a integer");
		}
		$this->open_width = $w;
		$this->open_button->getStyle()->setWidth($w);
		$this->_generated = false;
	}
	
	function getOpenWidth(){
		return $this->open_width;
	}
	
	/**
	 * the width of the button when open
	 */
	function setOpenHeight($h){
		if(!is_integer($h)){
			throw new IllegalArugmentException("Argument to " . __METHOD__ . " must be a integer");
		}
		$this->open_height = $h;
		$this->open_button->getStyle()->setHeight($h);
		$this->_generated = false;
	}
	
	function getOpenHeight(){
		return $this->open_height;
	}

	/**
	 * the width of the button when closed
	 */
	function setCloseWidth($w){
		if(!is_integer($w)){
			throw new IllegalArugmentException("Argument to " . __METHOD__ . " must be an integer");
		}
		$this->close_width = $w;
		$this->close_button->getStyle()->setWidth($w);
		$this->_generated = false;
	}
	function getCloseWidth(){
		return $this->close_width;
	}

	/**
	 * the width of the button when closed
	 */
	function setCloseHeight($h){
		if(!is_integer($h)){
			throw new IllegalArugmentException("Argument to " . __METHOD__ . " must be a integer");
		}
		$this->close_height = $h;
		$this->close_button->getStyle()->setHeight($h);
		$this->_generated = false;
	}
	
	function getCloseHeight(){
		return $this->close_height;
	}

	function getOpenButton(){
		if(!$this->_generated){
			$this->generateButtons();
		}
		return $this->open_button;
	}
	
	function getCloseButton(){
		if(!$this->_generated){
			$this->generateButtons();
		}
		return $this->close_button;
	}
	
	
	public function setClosedBackgroundImage($background){
		$this->backgroundi = $background;
	}
	
	public function getClosedBackgroundImage(){
		return $this->backgroundi;
	}
	

}



?>
