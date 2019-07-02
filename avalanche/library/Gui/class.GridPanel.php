<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class GridPanel extends Panel{

	private $_cols;
	private $_cell_style;

	private $_individual_cell_styles;
	
	function __construct($cols){
		if(!is_integer($cols)){
			throw new IllegalArgumentException("the first argument to " . __METHOD__ . " must be an int");
		}
		parent::__construct();
		$this->_components = array();
		$this->_cols = $cols;
		$this->setCellStyle(new Style());
	}
	
	public function add(Component $comp, $opt_style = false){
		parent::add($comp);
		if($opt_style instanceof Style){
			$this->_individual_cell_style[$comp->getId()] = $opt_style;
		}
	}
	
	public function remove(Component $comp){
		parent::remove($comp);
		if(isset($this->_individual_cell_style[$comp->getId()])){
			unset($this->_individual_cell_style[$comp->getId()]);
		}
	}
	
	public function setColumns($cols){
		if(!is_integer($cols)){
			throw new IllegalArgumentException("the first argument to " . __METHOD__ . " must be an int");
		}
		$this->_cols = $cols;
	}
	
	public function getColumns(){
		return $this->_cols;
	}

	/**
	 * adds a Style to this element
	 */
	public function setCellStyle(Style $s){
		$this->_cell_style = $s;
	}

	/**
	 * removes a style from this element if the style is present, otherwise does nothing
	 */
	public function removeCellStyle(){
		$this->_cell_style = false;
	}

	/**
	 * returns all styles associated with this element
	 */	
	public function getCellStyle($cell = false){
		if(is_object($cell) && $cell instanceof Component && isset($this->_individual_cell_style[$cell->getId()])){
			return $this->_individual_cell_style[$cell->getId()];
		}else{
			return $this->_cell_style;
		}
	}
}



?>
