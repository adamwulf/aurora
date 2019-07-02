<?

/**
 * represents an abstract document
 */
class Paragraph extends StyledElement{

	private $text;

	public function __construct($text = ""){
		parent::__construct();
		if(is_string($text)){
			$this->text = $text;
		}else{
			$this->text = "";
		}
	}

	/**
	 * sets this paragraph's text
	 */
	public function setText($text){
		if(is_string($text)){
			$this->text = $text;
		}else{
			throw new IllegalArgumentException("argument \$text must be a string to function Paragraph::setText(\$text)");
		}
	}

	/**
	 * @return the text of this paragraph
	 */
	public function getText(){
		return $this->text;
	}
}

?>