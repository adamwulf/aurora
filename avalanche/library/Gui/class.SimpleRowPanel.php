<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the
 * items in the panel is determined by the panel type.
 */
class SimpleRowPanel extends Panel{

	private $_cell_style;
	private $_curr_row;

	private $end_style;

	function __construct(){
		parent::__construct();
		$this->_components = array();
		$this->setCellStyle(new Style());
		$this->_components[] = array();
		$this->_curr_row = 0;
		$this->_on_dbl_click_for_rows = array();
		$this->end_style = new Style();
	}

	public function nextRow(){
		$this->_curr_row++;
		$this->_components[$this->_curr_row] = array();
	}

	public function add(Component $comp, $rs = 1, $two=false){
		if(!is_object($comp)){
			throw new IllegalArgumentException("argument one to " . __METOD__ . " must not be null");
		}
		if(!is_integer($rs)){
			throw new IllegalArgumentException("argument two to " . __METHOD__ . " must be an integer");
		}
		$this->_components[$this->_curr_row][] = new Pair($comp, $rs);
	}



	public function remove(Component $comp, $one=false, $two=false){
		$index = array_search($comp, $this->_components[$this->_curr_row], true);
		if(isset($this->_components[$this->_curr_row][$index])){
			$this->_components = array_slice($this->_components[$this->_curr_row], $index, 1);
		}
	}

	public function getComponents(){
		$ret = array();
		foreach($this->_components as $a){
			$ret = array_merge($ret, $a);
		}
		$ret = array_map(create_function('$n', 'return $n->getFirst();'), $ret);
		return $ret;
	}

	/* returns an array of arrays of components.
	 * each inner array is an array of Pairs of components and ints (rowspans)
	 */
	public function getRowComponents(){
		return $this->_components;
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
	public function getCellStyle(){
		return $this->_cell_style;
	}

	/**
	 * adds a Style to this element
	 */
	public function setEndStyle(Style $s){
		$this->end_style = $s;
	}

	/**
	 * returns all styles associated with this element
	 */
	public function getEndStyle(){
		return $this->end_style;
	}








	/**
	 * adds an action that will be invoked when this component is double clicked
	 */
	public function addRowDblClickAction(NonKeyAction $a){
		if(!isset($this->_on_dbl_click_for_rows[$this->_curr_row]) || !is_array($this->_on_dbl_click_for_rows[$this->_curr_row])){
			$this->_on_dbl_click_for_rows[$this->_curr_row] = array();
		}
		$this->_on_dbl_click_for_rows[$this->_curr_row][] = $a;
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeRowDblClickAction(NonKeyAction $a){
		$index = array_search($a, $this->_on_dbl_click_for_rows[$this->_curr_row]);
		if(isset($this->_on_dbl_click_for_rows[$this->_curr_row][$index])){
			array_splice($this->_on_dbl_click_for_rows[$this->_curr_row], $index, 1);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getRowDblClickActions($row_num){
		if(isset($this->_on_dbl_click_for_rows[$row_num]) && is_array($this->_on_dbl_click_for_rows[$row_num])){
			return $this->_on_dbl_click_for_rows[$row_num];
		}else{
			return array();
		}
	 }

}



?>
