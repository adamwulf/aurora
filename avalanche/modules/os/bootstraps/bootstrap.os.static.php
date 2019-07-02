<?

class OsGuiHelper {

	public static function createToolTip($obj){
		if(! $obj instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		$p1 = new SimplePanel();
		$p1->getStyle()->setClassname("tooltip");
		$p1->getStyle()->setPosition("relative");
		$p1->getStyle()->setRight(6);
		$p1->getStyle()->setBottom(6);
		$p1->add($obj);
		$p2 = new SimplePanel();
		$p2->getStyle()->setClassname("dropshadow");
		$p2->add($p1);
		return $p2;
	}
}



?>
