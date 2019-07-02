<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class RowPanel extends SimpleRowPanel{

	private $_row_height;
	
	function __construct(){
		parent::__construct();
		$this->setRowHeight("50");
	}
	
	
	/**
	 * gets the row height
	 */
	public function getRowHeight(){
		return $this->_row_height;
	}

	/**
	 * gets the row height
	 */
	public function setRowHeight($h){
		if(!is_string($h)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type string");
		}
		$this->_row_height = $h;
		
	}
}



?>
