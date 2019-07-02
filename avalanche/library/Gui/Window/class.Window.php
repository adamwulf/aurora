<?
// a confirmation window

class Window extends GridPanel{

	private $_x_button;
	private $_main_panel;
	private $_title;
	private $_title_panel;

	public function __construct(Component $item){
		parent::__construct(1);
		$this->_title = "";
		$cell_style = new Style();
		$cell_style->setPadding(4);
		$cell_style->setBackground("#FFFFFF");
		$cell_style->setBorderWidth(1);
		$cell_style->setBorderStyle("solid");
		$cell_style->setBorderColor("black");
		$cell_style->setPosition("absolute");
		$cell_style->setTop(250);
		$cell_style->setLeft(-700);


		$this->setValign("top");
		$this->setStyle($cell_style);
		$this->_x_button = new Button("<img src='" . HOSTURL . APPPATH . LIBRARY . "Gui/Window/xicon.gif' border=0>");
		$this->_x_button->setStyle(new Style());
		$this->_x_button->setAlign("left");
		$this->_x_button->addAction(new HorizontallySlideToAction($this, -700, 400));
		$this->_x_button->getStyle()->setPadding(2);

		$this->_title_panel = new BorderPanel();
		$this->_title_panel->getStyle()->setMarginTop(2);
		$this->_title_panel->getStyle()->setMarginBottom(2);
		$this->_title_panel->setWidth("100%");
		$this->_title_panel->setWest($this->_x_button);

		$this->add($this->_title_panel);
		$this->add($item);
	}

	public function setTitle($title){
		if(!is_string($title)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$title = new Text($title);
		$title->getStyle()->setFontFamily("verdana, sans-serif");
		$title->getStyle()->setFontSize(11);
		$title->getStyle()->setFontColor("black");
		$this->_title_panel->setCenter($title);
		$this->_title = $title;
	}

	public function getTitle(){
		return $this->_title;
	}
}
?>
