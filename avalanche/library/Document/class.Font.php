<?
/**
 * represents an font
 */
class Font{

	public static $BOLD      = 0;
	public static $ITALIC    = 1;
	public static $UNDERLINE = 2;


	private $face;
	private $size;
	private $attributes;

	public function __construct($face, $size, $attributes = array()){
		if(is_array($attributes)){
			foreach($attributes as $attribute){
				if(!(is_int($attribute)&& $attribute < 3 && $attribute >= 0)){
					throw new IllegalArgumentException("Third argument of Font(\$face, \$size, \$attributes) needs to be an array of FONT::\$BOLD, FONT::\$ITALIC, or FONT::\$UNDERLINE");
				}
			}
		}else{
			throw new IllegalArgumentException("Third argument of Font(\$face, \$size, \$attributes) needs to be an array");
		}
		$this->face=$face;
		$this->size=$size;
		$this->attributes=$attributes;
	}

	/**
	 * gets the face of this font
	 */
	public function getFace(){
		return $this->face;
	}

	/**
	 * sets the size attribute for this style
	 */
	public function getSize(){
		return $this->size;
	}

	/**
	 * @return true if this font is bold, false otherwise
	 */
	public function isBold(){
		return in_array(FONT::$BOLD, $this->attributes);
	}

	/**
	 * @return true if this font is italic, false otherwise
	 */
	public function isItalic(){
		return in_array(FONT::$ITALIC, $this->attributes);
	}

	/**
	 * @return true if this font is bold, false otherwise
	 */
	public function isUnderline(){
		return in_array(FONT::$UNDERLINE, $this->attributes);
	}
}



?>