<?
// a confirmation window

class ConfirmWindow extends GridPanel{

	private $_ok_button;
	private $_no_button;
	private $_x_button;
	private $_main_panel;

	public function __construct(Component $item){
		parent::__construct(1);
		$cell_style = new Style();
		$cell_style->setPadding(4);
		$cell_style->setBackground("#FFFFFF");
		$cell_style->setBorderWidth(1);
		$cell_style->setBorderStyle("solid");
		$cell_style->setBorderColor("black");
		$cell_style->setPosition("absolute");
		$cell_style->setTop(250);
		$cell_style->setLeft(-700);
		$cell_style->setWidth("340px");

		$this->setAlign("left");
		$this->setValign("top");
		$this->setStyle($cell_style);
		$this->_x_button = new Button("<img src='" . HOSTURL . APPPATH . LIBRARY . "Gui/Window/xicon.gif' border=0>");
		$this->_x_button->setStyle(new Style());
		$this->_x_button->setAlign("left");
		$this->_x_button->addAction(new HorizontallySlideToAction($this, -700, 400));
		$this->_x_button->getStyle()->setPadding(2);

		$this->add($this->_x_button);
		$this->add($item);


		$this->_ok_button = new Button("Ok");
		$this->_ok_button->getStyle()->setBackground("#EEEEEE");
		$this->_ok_button->getStyle()->setMarginTop(3);
		$this->_ok_button->getStyle()->setFontFamily("verdana, sans-serif");
		$this->_ok_button->getStyle()->setFontSize(8);

		$this->_no_button = new Button("Cancel");
		$this->_no_button->setStyle($this->_ok_button->getStyle());
		$this->_no_button->addAction(new HorizontallySlideToAction($this, -700, 400));

		$button_panel = new GridPanel(2);
		$button_panel->getCellStyle()->setPadding(2);
		$button_panel->add($this->_ok_button);
		$button_panel->add($this->_no_button);

		$button_panel = new ErrorPanel($button_panel);
		$this->add($button_panel);
	}

	public function addOkAction(Action $a){
		$this->_ok_button->addAction($a);
	}

	public function removeOkAction(Action $a){
		return $this->_ok_button->removeAction($a);
	}

	public function addCancelAction(Action $a){
		$this->_no_button->addAction($a);
		$this->_x_button->addAction($a);
	}

	public function removeCancelAction(Action $a){
		$this->_no_action->removeAction($a);
		return $this->_x_action->removeAction($a);
	}
}
?>
