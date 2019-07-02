<?
/**
 * represents an element that has a style attribute
 */
abstract class StyledElement extends Element{

	private $style;

	public function __construct(){
		$this->style = false;
	}

	/**
	 * adds a Style to this element
	 */
	public function setStyle(Style $s){
		$this->style = $s;
	}

	/**
	 * removes a style from this element if the style is present, otherwise does nothing
	 */
	public function removeStyle(){
		$this->style = false;
	}

	/**
	 * returns all styles associated with this element
	 */	
	public function getStyle(){
		return $this->style;
	}
}

?>