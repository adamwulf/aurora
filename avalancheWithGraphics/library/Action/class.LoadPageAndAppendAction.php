<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 * loads the url into the frame, and appends the value of the textbox to the
 * end of the url
 */
class LoadPageAndAppendAction extends NonKeyAction{
	private $url;
	private $frame;
	private $text;
	public function __construct($url, TextInput $t, $frame = "_self"){
		if(!is_string($url)){
			throw new IllegalArgumentException("second argument to " . __METHOD__ . " must be of type string");
		}
		if(!is_string($frame)){
			throw new IllegalArgumentException("third argument to " . __METHOD__ . " must be of type string");
		}
		$this->url = $url;
		$this->frame = $frame;
		$this->text = $t;
	}
	
	public function toJS(){
		return "window.open(\"" . $this->url . "\" + xGetElementById(\"" . $this->text->getId() ."\").value,\"" . $this->frame . "\");\n";
	}
}

?>