<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
abstract class Action{
	public function __construct(){
	}
	
	abstract public function toJS();
}


?>