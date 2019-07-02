<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class LoadPageAction extends NonKeyAction{
	private $url;
	private $frame;
	public function __construct($url, $frame = "_self"){
		if(!is_string($url)){
			throw new IllegalArgumentException("second argument to " . __METHOD__ . " must be of type string");
		}
		if(!is_string($frame)){
			throw new IllegalArgumentException("third argument to " . __METHOD__ . " must be of type string");
		}
		$this->url = $url;
		$this->frame = $frame;
	}
	
	public function toJS(){
		return "window.open(\"" . $this->url . "\",\"" . $this->frame . "\");\n";
	}
}

?>