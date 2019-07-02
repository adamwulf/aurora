<?
/**
 * reprents an element that can have a visitor execute over it
 */
abstract class Element{

	public function execute(ElementVisitor $v){
		return $v->accept($this);
	}
}

?>