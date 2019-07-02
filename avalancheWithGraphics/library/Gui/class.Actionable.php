<?
interface ActionAble{


	public function addAction(NonKeyAction $a);
	public function removeAction(NonKeyAction $a);
	public function getActions();

	public function addMouseOverAction(NonKeyAction $a);
	public function removeMouseOverAction(NonKeyAction $a);
	public function getMouseOverActions();

	public function addMouseOutAction(NonKeyAction $a);
	public function removeMouseOutAction(NonKeyAction $a);
	public function getMouseOutActions();

	public function addDblClickAction(NonKeyAction $a);
	public function removeDblClickAction(NonKeyAction $a);
	public function getDblClickActions();

}
?>
