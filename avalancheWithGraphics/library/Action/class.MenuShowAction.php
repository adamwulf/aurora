<?

/**
 * an abstract javascript action. Assumes the use of the X javascript library
 */
class MenuShowAction extends NonKeyAction{
	public function __construct(Menu $e){
		$this->e = $e;
	}
	
	public function toJS(){
		$id = $this->e->getId();
		return "this.menu_$id.paint();xShow(this.menu_$id.ele)\n";
	}
}


?>