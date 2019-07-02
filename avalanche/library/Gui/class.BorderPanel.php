<?
/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class BorderPanel extends Panel{

	private $west;
	private $east;
	private $north;
	private $south;
	private $center;
	
	private $west_width;
	private $east_width;
	private $north_height;
	private $south_height;
	
	/**
	 * basic constructor
	 */
	function __construct(){
		parent::__construct();
	}
	
	public function getComponents(){
		$ret = array();
		if(is_object($this->west)) $ret[] = $this->west;
		if(is_object($this->west)) $ret[] = $this->east;
		if(is_object($this->west)) $ret[] = $this->north;
		if(is_object($this->west)) $ret[] = $this->south;
		if(is_object($this->west)) $ret[] = $this->center;
		return $ret;
	}
	
	// HEIGHT AND WIDTH FUNCTIONS
	public function setWestWidth($w){
		$this->west_width = $w;
	}
	
	public function setEastWidth($w){
		$this->east_width = $w;
	}
	
	public function setNorthHeight($h){
		$this->north_height = $h;
	}
	
	public function setSouthHeight($h){
		$this->south_height = $h;
	}
	
	public function getWestWidth(){
		return $this->west_width;
	}
	
	public function getEastWidth(){
		return $this->east_width;
	}
	
	public function getNorthHeight(){
		return $this->north_height;
	}
	
	public function getSouthHeight(){
		return $this->south_height;
	}
	
	
	
	public function setWest(Component $comp){
		$this->west = $comp;
	}
	
	public function getWest(){
		return $this->west;
	}
	
	public function setEast(Component $comp){
		$this->east = $comp;
	}
	
	public function getEast(){
		return $this->east;
	}
	
	public function setNorth(Component $comp){
		$this->north = $comp;
	}
	
	public function getNorth(){
		return $this->north;
	}
	
	public function setSouth(Component $comp){
		$this->south = $comp;
	}
	
	public function getSouth(){
		return $this->south;
	}
	
	public function setCenter(Component $comp){
		$this->center = $comp;
	}

	public function getCenter(){
		return $this->center;
	}
}
?>
