<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class SetCookieAction extends NonKeyAction{
	private $name;
	private $value;
	public function __construct($name, $value){
		$this->name = $name;
		$this->value = $value;
	}
	
	public function toJS(){
		return "xSetCookie(\"" . $this->name . "\",\"" . $this->value . "\");\n";
	}
}


?>