<?
// a confirmation window

class SimpleWindow extends SimplePanel{

	private $window;

	public function __construct(Component $item){
		parent::__construct();

		$this->window = new SimplePanel();
		$this->window->setStyle(new Style("window_box_style"));
		$this->window->add($item);

		$this->setStyle(new Style("window_shadow_style"));
		$this->addOld($this->window);
	}

	public function add(Component $item, $one=false, $two=false){
		return $this->window->add($item);
	}

	private function addOld($item){
		return parent::add($item);
	}
}
?>
