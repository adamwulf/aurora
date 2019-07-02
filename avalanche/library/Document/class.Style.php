<?
/**
 * a style can represent a link to a class defined in a style sheet,
 * or define it's own style. or both.
 */
class Style extends Element{

	private $class;

	private $position;

	private $width;
	private $height;

	private $top;
	private $left;
	private $right;
	private $bottom;

	private $vertical_align;
	private $text_align;

	private $cursor;

	private $background;
	private $backgroundi;
	private $display;

	private $border_width;
	private $border_color;
	private $border_style;

	private $text_indent;

	private $padding;
	private $padding_top;
	private $padding_bottom;
	private $padding_right;
	private $padding_left;

	private $margin;
	private $margin_top;
	private $margin_bottom;
	private $margin_right;
	private $margin_left;

	private $font_family;
	private $font_size;
	private $font_color;
	private $font_weight;

	private $overflow;

	public function __construct($classname = false){
		if(!($classname === false ||
		is_string($classname))){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either false for a string");
		}
		$this->class = $classname;
		$this->font_family = false;
		$this->font_size = false;
		$this->font_color = false;
		$this->font_weight = false;
		$this->top = false;
		$this->left = false;
		$this->width = false;
		$this->height = false;
		$this->text_align=false;
		$this->vertical_align=false;
		$this->cursor = false;
		$this->background = false;
		$this->backgroundi = "";
		$this->display = false;
		$this->border_width = false;
		$this->border_color = false;
		$this->border_style = false;
		$this->text_indent = false;
		$this->padding = false;
		$this->padding_top = false;
		$this->padding_bottom = false;
		$this->padding_right = false;
		$this->padding_left = false;
		$this->margin = false;
		$this->margin_top = false;
		$this->margin_bottom = false;
		$this->margin_right = false;
		$this->margin_left = false;
		$this->repeat = false;
		$this->overflow = false;
		$this->position = false;
	}

	public function setOverflowHidden(){
		$this->overflow = "hidden";
	}

	public function removeOverflow(){
		$this->overflow = false;
	}

	public function setOverflowScroll(){
		$this->overflow = "scroll";
	}

	public function getOverflow(){
		return $this->overflow;
	}

	public function setClassname($classname){
		if(!($classname === false ||
		is_string($classname))){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either false for a string");
		}
		$this->class = $classname;
	}

	public function getClassname(){
		return $this->class;
	}

	/**
	 * background functions
	 */
	public function setBackground($background){
		$this->background = $background;
	}

	public function getBackground(){
		return $this->background;
	}

	public function setBackgroundImage($background, $extra=""){
		$this->backgroundi = $background;
		$this->backgrounde = $extra;
	}

	public function getBackgroundExtra(){
		return $this->backgrounde;
	}

	public function getBackgroundImage(){
		return $this->backgroundi;
	}

	public function repeatBackground(){
		$this->repeat = "repeat";
	}

	public function repeatBackgroundHorizontally(){
		$this->repeat = "repeat-x";
	}

	public function repeatBackgroundVertically(){
		$this->repeat = "repeat-y";
	}

	public function repeatBackgroundNone(){
		$this->repeat = "no-repeat";
	}

	public function getRepeatBackground(){
		return $this->repeat;
	}

	/**
	 * align functions
	 */
	public function setTextAlign($align){
		$this->text_align = $align;
	}

	public function setVerticalAlign($align){
		$this->vertical_align = $align;
	}

	public function getTextAlign(){
		return $this->text_align;
	}

	public function getVerticalAlign(){
		return $this->vertical_align;
	}


	/**
	 * width/height functions
	 */
	public function setWidth($width){
		$this->width = $width;
	}

	public function setHeight($height){
		$this->height = $height;
	}

	public function getWidth(){
		return $this->width;
	}

	public function getHeight(){
		return $this->height;
	}


	public function setTop($top){
		$this->top = $top;
	}

	public function setLeft($left){
		$this->left = $left;
	}

	public function setBottom($b){
		$this->bottom = $b;
	}

	public function setRight($r){
		$this->right = $r;
	}

	public function getBottom(){
		return $this->bottom;
	}

	public function getRight(){
		return $this->right;
	}

	public function getTop(){
		return $this->top;
	}

	public function getLeft(){
		return $this->left;
	}

	public function setPosition($str){
		if($str != "absolute" && $str != "relative"){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be either 'absolute' or 'relative'");
		}
		$this->position = $str;
	}

	public function getPosition(){
		return $this->position;
	}


	/* font */
	public function setFontFamily($fam){
		if(!is_string($fam)){
			throw new IllegalArgumentException ("argument to " . __METHOD__ . " must be a string");
		}
		$this->font_family = $fam;
	}

	public function setFontSize($size){
		if(!is_int($size)){
			throw new IllegalArgumentException ("argument to " . __METHOD__ . " must be a integer");
		}
		$this->font_size = $size;
	}

	public function setFontColor($color){
		if(!is_string($color)){
			throw new IllegalArgumentException ("argument to " . __METHOD__ . " must be a string");
		}
		$this->font_color = $color;
	}

	public function setFontWeight($weight){
		if(!is_string($weight)){
			throw new IllegalArgumentException ("argument to " . __METHOD__ . " must be a string");
		}
		$this->font_weight = $weight;
	}

	public function getFontFamily(){
		return $this->font_family;
	}

	public function getFontSize(){
		return $this->font_size;
	}

	public function getFontColor(){
		return $this->font_color;
	}

	public function getFontWeight(){
		return $this->font_weight;
	}

	public function setHandCursor(){
		$this->cursor = "pointer";
	}

	public function getCursor(){
		return $this->cursor;
	}


	/* set display */
	public function setDisplayNone(){
		$this->display = "none";
	}
	public function setDisplayInline(){
		$this->display = "inline";
	}
	public function setDisplayBlock(){
		$this->display = "block";
	}
	public function getDisplay(){
		return $this->display;
	}

	/* set border */
	public function setBorderWidth($w){
		if(!is_integer($w)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->border_width = $w;
	}
	public function getBorderWidth(){
		return $this->border_width;
	}

	public function setBorderStyle($s){
		if(!is_string($s)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an string.");
		}
		$this->border_style = $s;
	}
	public function getBorderStyle(){
		return $this->border_style;
	}

	public function setBorderColor($c){
		if(!is_string($c)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an string.");
		}
		$this->border_color = $c;
	}

	public function getBorderColor(){
		return $this->border_color;
	}

	/* overall padding, takes precedence over top/right/left/bottom */
	public function setTextIndent($i){
		if(!is_integer($i)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->text_indent = $i;
	}

	public function getTextIndent(){
		return $this->text_indent;
	}

	/* overall padding, takes precedence over top/right/left/bottom */
	public function setPadding($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->padding = $p;
	}

	public function getPadding(){
		return $this->padding;
	}

	/* set padding top */
	public function setPaddingTop($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->padding_top = $p;
	}

	/* returns padding top if padding is not set */
	public function getPaddingTop(){
		return ($this->getPadding() ? $this->getPadding() : $this->padding_top);
	}

	/* set padding bottom */
	public function setPaddingBottom($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->padding_bottom = $p;
	}

	/* returns padding bottom if padding is not set */
	public function getPaddingBottom(){
		return ($this->getPadding() ? $this->getPadding() : $this->padding_bottom);
	}

	/* set padding right */
	public function setPaddingRight($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->padding_right = $p;
	}

	/* returns padding top if padding is not set */
	public function getPaddingRight(){
		return ($this->getPadding() ? $this->getPadding() : $this->padding_right);
	}

	public function setPaddingLeft($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->padding_left = $p;
	}

	/* returns padding top if padding is not set */
	public function getPaddingLeft(){
		return ($this->getPadding() ? $this->getPadding() : $this->padding_left);
	}


	/* overall margin, takes precedence over top/right/left/bottom */
	public function setMargin($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->margin = $p;
	}

	public function getMargin(){
		return $this->margin;
	}

	/* set margin top */
	public function setMarginTop($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->margin_top = $p;
	}

	/* returns margin top if margin is not set */
	public function getMarginTop(){
		return ($this->getMargin() ? $this->getMargin() : $this->margin_top);
	}

	/* set margin bottom */
	public function setMarginBottom($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->margin_bottom = $p;
	}

	/* returns margin bottom if margin is not set */
	public function getMarginBottom(){
		return ($this->getMargin() ? $this->getMargin() : $this->margin_bottom);
	}

	/* set margin right */
	public function setMarginRight($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->margin_right = $p;
	}

	/* returns margin top if margin is not set */
	public function getMarginRight(){
		return ($this->getMargin() ? $this->getMargin() : $this->margin_right);
	}

	public function setMarginLeft($p){
		if(!is_integer($p)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer.");
		}
		$this->margin_left = $p;
	}

	/* returns margin top if margin is not set */
	public function getMarginLeft(){
		return ($this->getMargin() ? $this->getMargin() : $this->margin_left);
	}
}

?>