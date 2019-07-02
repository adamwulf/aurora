<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class TrueAction extends BooleanAction{
	
	public function __construct(){
	}
	
	public function toJS(){

		return "(true)";
	}
}

?>