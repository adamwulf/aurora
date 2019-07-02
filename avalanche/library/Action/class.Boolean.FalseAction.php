<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library.
 */
class FalseAction extends BooleanAction{
	
	public function __construct(){
	}
	
	public function toJS(){

		return "(false)";
	}
}

?>