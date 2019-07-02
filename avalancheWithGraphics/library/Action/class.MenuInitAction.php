<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class MenuInitAction extends NonKeyAction{

	private $trigger;
	private $menu;
	
	public function __construct(Component $trigger, Component $menu){
		$this->trigger = $trigger;
		$this->menu = $menu;
	}

	public function toJS(){
		$trigger_id = $this->trigger->getId();
		$menu_id = $this->menu->getId();
		return "new xMenu1('$trigger_id', '$menu_id', 10);";
	}


	// public function __construct(Menu $e){
		// $this->e = $e;
	// }
	// 
	// public function toJS(){
		// $id =  $this->e->getId();
		// $label_style = $this->e->getLabelStyle();
		// $item_style = $this->e->getItemStyle();
		// $menu_style = $this->e->getStyle();
		// return "  // Create menu 1
			  // this.menu_$id = new xMenu('$id',         // element id
			    // 'horizontal', 2, -6,                // mnuType, verOfs, hrzOfs
			    // -12, -16, -20, -4,                  // lbl selection area clipping (top,right,bottom,left)
			    // -32, null, null, null,              // box selection area clipping
			    // 'myHMBarLbl', 'myHMBarLbl',       // barLblOutStyle, barLblOvrStyle
			    // 'myHMBarLbl',                // barLblOvrClosedStyle
			    // 'myMLbl', 'myMLbl',             // lblOutStyle, lblOvrStyle
			    // 'myHMBar', 'myMBox');            // barStyle, boxStyle
			// 
			  // this.menu_$id.load();
			  // 
			  // // Set menu z's
			  // xZIndex(this.menu_$id.ele, 30);
			// ";
	// }
}


?>