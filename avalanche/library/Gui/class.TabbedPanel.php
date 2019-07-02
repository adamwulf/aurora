<?


/**
 * this class represents an Gui Panel. this type of panel is tabbed. content components are
 * added with open and closed tabs.
 */
class TabbedPanel extends Panel{

	// an array of Pairs.
	// Pair->first is open
	// Pair->second is closed
	private $_tabs;
	
	private $_holder_style;
	private $_content_style;
	
	// the index+1 of the tab to show
	private $_tab;
	
	function __construct(){
		parent::__construct();
		$this->_components = array();
		$this->_tabs = array();
		$this->_holder_style = new Style();
		$this->_content_style = new Style();
		$this->_tab = 1;
	}
	
	
	// out of bounds errors are ignored
	public function selectTab($i){
		if(!is_int($i)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$this->_tab = $i;
	}
	
	public function tabSelected(){
		if(count($this->_tabs)){
			if($this->_tab > 0 && $this->_tab <= count($this->_tabs)){
				return $this->_tab;
			}else{
				return 1;
			}
		}else{
			return 0;
		}
	}
	
	
	
	
	// $comp the content for the tab
	// $open_tab the content for the tab that will be displayed when the tab is open
	// $closed_tab the content for the tab that will be displayed when teh tab is closed
	// returns the tab number
	public function add(Component $comp, $open_tab, $closed_tab){
		parent::add($comp);
		$this->_tabs[] = new Pair($open_tab, $closed_tab);
		return count($this->_tabs) - 1;
	}
	
	// $comp, the content page to remove
	// $index, the tab number to remove
	public function remove(Component $comp, $index){
		parent::remove($comp);
		array_splice($this->_tabs, $index, 1);
	}
	
	// returns an array of Pairs for the tabs
	// first is open tab
	// second is closed tab
	public function getTabs(){
		return $this->_tabs;
	}
	
	public function getCloseFunction(){
		$closefunction = new NewFunctionAction("close_tabs_" . $this->getId());
		$tabs = $this->getTabs();
		$contents = $this->getComponents();
		for($i=0;$i<count($tabs);$i++){
			$closefunction->addAction(new DisplayNoneAction($tabs[$i]->getFirst()));
			$closefunction->addAction(new DisplayBlockAction($tabs[$i]->getSecond()));
			$closefunction->addAction(new DisplayNoneAction($contents[$i]));
		}
		return $closefunction;
	}
	
	
	public function getHolderStyle(){
		return $this->_holder_style;
	}
	
	public function setHolderStyle(Style $s){
		$this->_holder_style = $s;
	}

	public function getContentStyle(){
		return $this->_content_style;
	}
	
	public function setContentStyle(Style $s){
		$this->_content_style = $s;
	}
}



?>
