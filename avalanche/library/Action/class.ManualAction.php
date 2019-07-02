<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class ManualAction extends NonKeyAction{
	private $js;
	public function __construct($raw_js_text){
		if(!is_string($raw_js_text)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a string");
		}
		$this->js = $raw_js_text;
	}
	
	public function toJS(){
		return $this->js . ";\n";
	}
}


?>