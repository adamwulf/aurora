<?
/**
 * is a menu that can be attached to a component via a MenuAction
 */
 class Menu extends Component{
	 
	// an array of menu items
	private $_items;
	 
	// the button it is attached to
	private $_button;
	 
	// the style for each non submenu item
	private $_item_style;
	 
	// the style for the menus div that wraps the button
	private $_label_style;
	 
	/**
	 * @param $b the button to attach this menu to
	 * @param $d the Document that contains this menu
	 */
	public function __construct(Component $b, Document $d){
		parent::__construct();
		$this->_items = array();
		$this->_button = $b;
		$this->_item_style = new Style();
		$this->_label_style = new Style();
		
		$load_action = new MenuInitAction($this);
		$show_action = new MenuShowAction($this);
		$d->addAction($load_action);
		$d->addAction($show_action);
		$d->addAction($show_action, Document::$onResize);
		
	}

	/**
	 * gets the component that this menu is attached to
	 */
	public function getComponent(){
		return $this->_button;
	}
	 
	public function add(Component $comp){
		if(!is_object($comp)){
			throw new IllegalArgumentException("argument to add must not be null");
		}
		$this->_items[] = $comp;
	}
	
	public function remove(Component $comp){
		$index = array_search($comp, $this->_items, true);
		if(isset($this->_items[$index])){
			$this->_items = array_slice($this->_items, $index, 1);
		}
	}
	
	public function getComponents(){
		return $this->_items;
	}
	
	
	
	//**********************************************************************
	// (label style) sets the style of each item in the menu (that is, non sub menu items)
	//**********************************************************************
	/**
	 * adds a Style to this element
	 */
	public function setLabelStyle(Style $s){
		$this->_label_style = $s;
	}

	/**
	 * removes a style from this element if the style is present, otherwise does nothing
	 */
	public function removeLabelStyle(){
		$this->_label_style = false;
	}

	/**
	 * returns all styles associated with this element
	 */	
	public function getLabelStyle(){
		return $this->_label_style;
	}
	//**********************************************************************
	// (done label style)
	//**********************************************************************

	
	//**********************************************************************
	// (item style) sets the style of each item in the menu (that is, non sub menu items)
	//**********************************************************************

	/**
	 * adds a Style to this element
	 */
	public function setItemStyle(Style $s){
		$this->_item_style = $s;
	}

	/**
	 * removes a style from this element if the style is present, otherwise does nothing
	 */
	public function removeItemStyle(){
		$this->_item_style = false;
	}

	/**
	 * returns all styles associated with this element
	 */	
	public function getItemStyle(){
		return $this->_item_style;
	}
	//**********************************************************************
	// (done item style)
	//**********************************************************************
 }
 
?>
