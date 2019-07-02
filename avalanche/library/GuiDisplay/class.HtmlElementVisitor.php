<?
/**
 * abstractly visits a Element
 */
class HtmlElementVisitor extends AElementVisitor{

	/**
	 * create this HtmlVisitor
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * this is the catch all function. if no other function can handle the input object,
	 * and no other catch all is defined, then this function will be called.
	 *
	 */
	public function ElementCase(Element $e){
		throw new Exception("no behavior defined for an Element of type \"" . get_class($e) . "\" in " . __METHOD__);
	}

	/**
	 * visits a Document
	 */
	public function DocumentCase(Document $e){
		$elements = $e->getElements();
		$body = "";
		$es = $e->getElements();
		for($i=0;$i<count($es);$i++){
			$body .= $es[$i]->execute($this);
		}

		$body .= "<div>";
		$es = $e->getHiddenElements();
		for($i=0;$i<count($es);$i++){
			$body .= $es[$i]->execute($this);
		}
		$body .= "</div>";


		$style = $e->getStyle();
		$style = $style ? $style->execute($this) : "";
		$style = "<style = \"text/css\"><!--\n" . $style . "--></style>";

		$stylesheets = "";
		$ss = $e->getStyleSheets();
		for($i=0;$i<count($ss);$i++){
			$stylesheets .= $ss[$i]->execute($this);
		}

		$onload_actions = "";
		$as = $e->getActions(Document::$onLoad);
		for($i=0;$i<count($as);$i++){
			$onload_actions .= $as[$i]->toJS();
		}

		$onload_actions .= "if(xIE4Up){
								var png = new PNGFIX();
								xTimer.set('timeout', png, 'correctPNG', 1000, false);
							}";

		$onresize_actions = "";
		$as = $e->getActions(Document::$onResize);
		for($i=0;$i<count($as);$i++){
			$onresize_actions .= $as[$i]->toJS();
		}

		$functions = "";
		$fs = $e->getFunctions();
		for($i=0;$i<count($fs);$i++){
			$functions .= $fs[$i]->toJS();
		}

		$output = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
		$output .= "<html><head>";
		$output .= "<title>" . htmlspecialchars($e->getTitle()) . "</title>";
		$output .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">";
		$output .= "<style type=\"text/css\">
				body {
					behavior:url(\"csshover.htc\");
				}
			    </style>";
		$output .= $stylesheets . $style;
		$js = $e->getJS();
		foreach($js as $j){
			$output .= "<script type='text/javascript' src='" . $j->getLocation() . "'></script>\n";
		}
		$output .= "\n\n<!--[if gte IE 5.5000]>\n
			<script type=\"text/javascript\" src=\"" . HOSTURL . APPPATH . JAVASCRIPT . "png/pngfix.js\"></script>\n
			<![endif]-->\n";
		$output .= "<script type='text/javascript'>
			    //<!--
		            window.onload = winOnload;
			    if(window.onresize == null) window.onresize = winResize;
			    function winOnload() {
				$onload_actions
			    }
			    function winResize() {
				$onresize_actions
			    }
			    $functions
				//--></script>";
		$output .= "</head><body>";
		$output .= $body;
		$output .= "</body></html>";
		return $output;
	}

	/**
	 * visits a Paragraph
	 */
	public function ParagraphCase(Paragraph $e){
		return "<p>" . $e->getText() . "</p>";
	}

	/**
	 * visits a CSS
	 */
	public function CSSCase(CSS $e){
		return "<link rel='STYLESHEET' href='" . $e->getLocation()->getLocation() . "' type='text/css'>";
	}

	/**
	 * visits a Style
	 */
	public function StyleCase(Style $e){
		$style = "." . $e->getClassname() . "{\n";
		if($e->getWidth()){
			$style .= " width: " . $e->getWidth() . ";\n";
		}
		if($e->getHeight()){
			$style .= " height: " . $e->getHeight() . ";\n";
		}
		$style .= "}\n";
		return $style . "\n";
	}

	/**
	 * FORM ELEMENT CASES
	 */

	/**
	 * visits a SubmitInput
	 */
	public function FileInputCase(FileInput $e){
		$ret = "<input id='" . $e->getId() . "' type='file' ";

		$ret .= " size='" . $e->getSize() . "' ";

		if($e->isReadOnly()){
			$ret .= "READONLY ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= "/>";

		return $ret;
	}

	/**
	 * visits a SubmitInput
	 */
	public function SubmitInputCase(SubmitInput $e){

		$ret = "<input id='" . $e->getId() . "' type='submit' ";

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . htmlspecialchars($e->getValue(),ENT_QUOTES) . "' ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= "/>";

		return $ret;
	}
	/**
	 * visits a ButtonInput
	 */
	public function ButtonInputCase(ButtonInput $e){

		$ret = "<input id='" . $e->getId() . "' type='button' ";

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . htmlspecialchars($e->getValue(),ENT_QUOTES) . "' ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= "/>";

		return $ret;
	}

	/**
	 * visits a SmallTextInput
	 */
	public function SmallTextInputCase(SmallTextInput $e){
		if($e->isPassword()){
			$type = "password";
		}else{
			$type = "text";
		}

		$ret = "<input id='" . $e->getId() . "' type='$type' ";

		$ret .= " size='" . $e->getSize() . "' ";

		if($e->getMaxLength()){
			$ret .= " maxlength='" . $e->getMaxLength() . "' ";
		}

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . htmlspecialchars($e->getValue(),ENT_QUOTES) . "' ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= "/>";

		return $ret;
	}

	/**
	 * visits a HiddenInput
	 */
	public function HiddenInputCase(HiddenInput $e){
		$ret = "<input id='" . $e->getId() . "' type='hidden' ";

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . htmlentities($e->getValue()) . "' ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= "/>";

		return $ret;
	}

	/**
	 * visits a TextAreaInput
	 */
	public function TextAreaInputCase(TextAreaInput $e){
		$ret = "<textarea id='" . $e->getId() . "' ";
		$ret .= " rows='" . $e->getRows() . "' ";
		$ret .= " cols='" . $e->getCols() . "' ";
		if($e->wordWrap()){
			$ret .= " wrap='virtual'";
		}else{
			$ret .= " wrap='off'";
		}
		$ret .= $this->getInputProperties($e);

		$ret .= ">";
		if(strlen($e->getValue()) > 0){
			$ret .= $e->getValue();
		}
		$ret .= "</textarea>";


		return $ret;
	}

	/**
	 * visits a CheckInput
	 */
	public function CheckInputCase(CheckInput $e){
		$ret = "<input id='" . $e->getId() . "' type='checkbox' ";

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . $e->getValue() . "' ";
		}

		if($e->isChecked()){
			$ret .= " CHECKED ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= ">";

		if(strlen($e->getLabel()) > 0){
			$label = new Text("<label for='" . $e->getId() . "'>" . $e->getLabel() . "</label>");
			$label->setStyle($e->getStyle());
			$ret .= $label->execute($this);
		}

		return $ret;
	}

	/**
	 * visits a RadioInput
	 */
	public function RadioInputCase(RadioInput $e){
		$ret = "<input id='" . $e->getId() . "' type='radio' ";

		if(strlen($e->getValue()) > 0){
			$ret .= " value='" . $e->getValue() . "' ";
		}

		if($e->isChecked()){
			$ret .= " CHECKED ";
		}

		$ret .= $this->getInputProperties($e);

		$ret .= ">";

		if(strlen($e->getLabel()) > 0){
			$label = new Text("<label for='" . $e->getId() . "'>" . $e->getLabel() . "</label>");
			$label->setStyle($e->getStyle());
			$ret .= $label->execute($this);
		}

		return $ret;
	}

	/**
	 * visits a DropDownInput
	 */
	public function DropDownInputCase(DropDownInput $e){
		$ret = "<select id='" . $e->getId() . "' ";
		$ret .= $this->getInputProperties($e);
		$ret .= " size='" . $e->getSize() . "'";
		$ret .= ">";

		$opts = $e->getOptions();
		foreach($opts as $opt){
			if($opt->isSelected()){
				$selected = "SELECTED";
			}else{
				$selected = "";
			}

			$ret .= "<option value='" . htmlspecialchars($opt->getValue(),ENT_QUOTES) . "' $selected>" . $opt->getDisplay() . "</option>";
		}

		$ret .= "</select>";

		return $ret;
	}

	/**
	 * visits a DateInput
	 */
	public function DateInputCase(DateInput $e){
		$slash = new Text(" / ");
		$slash->setStyle(clone $e->getStyle());
		$slash->getStyle()->setBorderWidth(0);

		$space = new Text("&nbsp;");
		$space->setStyle($slash->getStyle());

		$panel = new GridPanel(7);
		$panel->add($e->getMonthComponent());
		$panel->add($slash);
		$panel->add($e->getDayComponent());
		$panel->add($slash);
		$panel->add($e->getYearComponent());
		$panel->add($space);
		$panel->add($e->getDOWComponent());
		$panel->setStyle($e->getStyle());

		return $panel->execute($this);
	}

	/**
	 * visits a URLInput
	 */
	public function URLInputCase(URLInput $e){
		$link_text = new Text("Link Text:");
		$link_text->setStyle(clone $e->getStyle());

		$link_url = new Text("Link URL:");
		$link_url->setStyle($link_text->getStyle());

		$panel = new GridPanel(1);
		$panel->add($link_text);
		$panel->add($e->getTextComponent());
		$panel->add($link_url);
		$panel->add($e->getLinkComponent());
		$panel->setStyle($e->getStyle());

		return $panel->execute($this);
	}



	/**
	 * visits a TimeInput
	 */
	public function TimeInputCase(TimeInput $e){
		$slash = new Text(":");
		$slash->setStyle(clone $e->getStyle());
		$slash->getStyle()->setBorderWidth(0);

		$space = new Text("&nbsp;");
		$space->setStyle($slash->getStyle());

		$panel = new GridPanel(7);
		$panel->add($e->getHourComponent());
		$panel->add($slash);
		$panel->add($e->getMinuteComponent());
		$panel->add($space);
		$panel->add($e->getAMPMComponent());
		$panel->setStyle($e->getStyle());

		return $panel->execute($this);
	}



	/**
	 * NORMAL DOCUMENT ELEMENT CASES
	 */

	/**
	 * visits a Menu
	 */
	public function MenuCase(Menu $e){
		$ret = "<div id='" . $e->getId() . "' ";


		$label_style = "";
		$style = $e->getLabelStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$label_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$label_style .= "style='$temp' ";
			}
		}

		$item_style = "";
		$style = $e->getItemStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$item_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$item_style .= "style='$temp' ";
			}
		}

		$menu_style = "";
		$style = $e->getStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$menu_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$menu_style .= "style='$temp' ";
			}
		}

		$ret .= " $menu_style>";
		// closed the beginning div tag

		$ret .= "<div $label_style>";
		$ret .= $e->getComponent()->execute($this);
		$ret .= "</div>";
		$ret .= "<div $menu_style>";
		$ret .= $this->getMenuItemHTML($e);
		$ret .= "</div>";
		$ret .= "</div>";
		// close the menu
		return $ret;
	}

	private function getMenuItemHTML($e){
		$ret = "";
		$items = $e->getComponents();

		$label_style = "";
		$style = $e->getLabelStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$label_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$label_style .= "style='$temp' ";
			}
		}

		$item_style = "";
		$style = $e->getItemStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$item_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$item_style .= "style='$temp' ";
			}
		}

		$menu_style = "";
		$style = $e->getStyle();
		if(is_object($style)){
			if($style->getClassname()){
				$menu_style .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$menu_style .= "style='$temp' ";
			}
		}

		foreach($items as $item){
			if($item instanceof Menu){
				$ret .= "<div $label_style>";
				$ret .= $item->getComponent()->execute($this);
				$ret .= "</div>";
				$ret .= "<div $menu_style>";
				$ret .= $this->getMenuItemHTML($item);
				$ret .= "</div>";
			}else{
				$ret .= "<div $item_style>";
				$ret .= $item->execute($this);
				$ret .= "</div>";
			}
		}
		return $ret;
	}

	public function LinkCase(Link $e){
		$ret = "<a  id='" . $e->getId() . "' ";
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$ret .= "style='$temp' ";
			}
		}

		$ret .= $this->getActions($e);

		$ret .= " href='" . $e->getURL() . "'";
		if($e->getTarget()){
			$ret .= " target='" . $e->getTarget() . "'";
		}
		$ret .= ">" . $e->getText() . "</a>";
		return $ret;
	}


	/**
	 * visits an Anchor
	 */
	public function AnchorCase(Anchor $e){
		$ret = "<a id='" . $e->getId() . "' ";

		if($e->getText()){
			$ret .= "name='" . $e->getText() . "'";
		}
		$ret .= "></a>";

		return $ret;
	}


	/**
	 * visits a Text
	 */
	public function TextCase(Text $e){
		$ret = "<span id='" . $e->getId() . "' ";

		if($e->getAlign()){
			$ret .= "align='" . $e->getAlign() . "'";
		}

		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$temp = $this->getAdditionalStyle($style);
			if(strlen($temp)){
				$ret .= "style='$temp' ";
			}
		}

		$ret .= $this->getActions($e);

		$ret .= ">" . $e->getText() . "</span>";

		return $ret;
	}


	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function ButtonCase(Button $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
		}
		$hand_style = new Style();
		$hand_style->setHandCursor();
		$additional_style .= $this->getAdditionalStyle($hand_style);
		if(strlen($additional_style)){
			$ret .= " style='$additional_style' ";
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";
		$ret .= "<tr><td " . $this->getAlignments($e) . " NOWRAP>";

		/* add all inner components to the panel */

		if($e->getIcon()){
			$ret .= $e->getIcon()->execute($this);
		}

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}
		$ret .= "</td></tr></table>";
		return $ret;
	}

	public function IconWithTextCase(Icon $e){

		/* set the id, but don't close the tag */
		$src = $e->getURL();
		$ret = "<div id='" . $e->getId() . "' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
		}
		$ret .= " style='background: url(" . $src . "); $additional_style' ";

		$as = $e->getActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}

		$ret .= " onClick='$actions' ";

		$ret .= $this->getHeightWidth($e);
		$ret .= $this->getAlignments($e);

		$ret .= ">";
		$ret .= $e->getText()->execute($this);
		$ret .= "</div>";

		return $ret;
	}


	public function IconCase(Icon $e){

		/* set the id, but don't close the tag */
		$src = $e->getURL();
		$ret = "<img src='$src' id='" . $e->getId() . "' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
		}
		if(strlen($additional_style)){
			$ret .= " style='$additional_style' ";
		}

		$as = $e->getActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}

		$ret .= " onClick='$actions' ";

		$ret .= $this->getHeightWidth($e);
		$ret .= $this->getAlignments($e);

		$ret .= ">";

		return $ret;
	}

	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function PanelCase(Panel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";
		$ret .= "<tr><td " . $this->getAlignments($e) . ">";

		/* add all inner components to the panel */

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}
		$ret .= "</td></tr></table>";
		return $ret;
	}

	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function SimplePanelCase(Panel $e){
		/* set the id, but don't close the tag */
		$ret = "<div " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";

		/* add all inner components to the panel */

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}
		$ret .= "</div>";
		return $ret;
	}

	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function TabbedPanelCase(TabbedPanel $e){
		$panel = new GridPanel(1);
		$panel->setStyle($e->getStyle());
		$panel->setWidth($e->getWidth());
		$panel->setHeight($e->getHeight());
		$panel->setAlign($e->getAlign());
		$panel->setValign($e->getValign());
		$panel->setVisible($e->isVisible());

		$as = $e->getActions();
		foreach($as as $a){
			$panel->addAction($a);
		}
		$as = $e->getMouseOverActions();
		foreach($as as $a){
			$panel->addMouseOverAction($a);
		}

		$tabs = $e->getTabs();
		$contents = $e->getComponents();

		$content_panel = new Panel();
		$content_panel->setWidth("100%");
		$tab_panel = new GridPanel(count($tabs) + 1);
		$content_panel->setStyle($e->getContentStyle());

		// initialize panels and style display
		for($i=0; $i<count($tabs);$i++){
			$two_tabs = new Panel();
			$tab = $tabs[$i];
			$content = $contents[$i];
			$two_tabs->add($tab->getFirst());
			$two_tabs->add($tab->getSecond());
			$tab_panel->add($two_tabs);
			$tab->getFirst()->getStyle()->setDisplayNone();
			$tab->getSecond()->getStyle()->setDisplayBlock();
			$content_panel->add($content);
			$content->getStyle()->setDisplayNone();

			$tab->getSecond()->addAction(new CallFunctionAction($e->getCloseFunction()->getName()));
			$tab->getSecond()->addAction(new DisplayNoneAction($tab->getSecond()));
			$tab->getSecond()->addAction(new DisplayBlockAction($tab->getFirst()));
			$tab->getSecond()->addAction(new DisplayBlockAction($content));
		}
		if(count($tabs)){

			$i = $e->tabSelected() - 1;
			$tabs[$i]->getFirst()->getStyle()->setDisplayBlock();
			$tabs[$i]->getSecond()->getStyle()->setDisplayNone();
			$contents[$i]->getStyle()->setDisplayBlock();
		}

		$tabs = new SimpleRowPanel();
		$tabs->setValign("bottom");
		$tabs->setEndStyle($e->getHolderStyle());
		$tabs->add($tab_panel);
		$tabs->setWidth("100%");

		$panel->add($tabs);
		$panel->add($content_panel);

		return $panel->execute($this);
	}

	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function RowPanelCase(RowPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			$ret .= "style='border-collapse: collapse; $additional_style' ";
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";

		$comps = $e->getRowComponents();
		$num_per_row = array();
		/* iterate of the rows of components */
		for($i=0;$i<count($comps);$i++){
			/* iterate over the components for this row */
			foreach($comps[$i] as $comp){
				/* tell the rows that this component is in it */
				for($j=0;$j<$comp->getSecond();$j++){
					if(isset($num_per_row[$i+$j])){
						$num_per_row[$i+$j]++;
					}else{
						$num_per_row[$i+$j] = 1;
					}
				}
			}
		}

		/* iterate over the rows to see the max number of columns we need */
		$max = 0;
		for($i=0;$i<count($num_per_row);$i++){
			$max = (($num_per_row[$i] > $max) ? $num_per_row[$i] : $max);
		}
		/* add an extra cell on the end so we can ensure row height and full width */
		$max++;

		$end_panel_style = new Style();
		$end_panel_style->setHeight($e->getRowHeight());
		$end_panel_style->setWidth("100%");

		/* iterate of the rows of components */
		for($i=0;$i<count($num_per_row);$i++){
			/* iterate over the components for this row */
			$filler = new Panel();
			$filler->setWidth("100%");
			$filler->setHeight($e->getRowHeight());
			$ras = $e->getRowDblClickActions($i);
			foreach($ras as $ra){
				$filler->addDblClickAction($ra);
			}
			for($j=$num_per_row[$i];$j<$max;$j++){
				$comps[$i][]= new Pair($filler, 1);
			}
		}

		/* iterate of the rows of components */
		for($i=0;$i<count($comps);$i++){
			if(count($comps[$i])){
			$ret .= "<tr>";
			/* iterate over the components for this row */
			for($j=0;$j<count($comps[$i]);$j++){
				$ret .= "<td " . $this->getAlignments($e) . " rowspan='" . $comps[$i][$j]->getSecond() . "' ";
				/*
				 * set the cell style class, if any.
				 */
				$style = $e->getCellStyle();

				$last_one = $j == (count($comps[$i]) -1);
				if($last_one){
					$old_height = $style->getHeight();
					$old_width = $style->getWidth();
					$style->setHeight($e->getRowHeight());
					$style->setWidth("100%");
				}
				$additional_style = "";
				if(is_object($style)){
					if($style->getClassname()){
						$ret .= "class='" . $style->getClassname() . "' ";
					}
					$additional_style = $this->getAdditionalStyle($style);
					if(strlen($additional_style)){
						$ret .= "style='$additional_style' ";
					}
				}
				$ret .= ">";
				$ret .= $comps[$i][$j]->getFirst()->execute($this);
				$ret .= "</td>";

				if($last_one){
					$style->setHeight($old_height);
					$style->setWidth($old_width);
				}
			}
			$ret .= "</tr>";
			}
		}
		$ret .= "</table>";
		return $ret;
	}

	/**
	 * visits a Generic Panel, if no specific type of panel could be found
	 */
	public function SimpleRowPanelCase(SimpleRowPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			$ret .= "style='border-collapse: collapse; $additional_style' ";
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";

		$comps = $e->getRowComponents();
		$num_per_row = array();
		/* iterate of the rows of components */
		for($i=0;$i<count($comps);$i++){
			/* iterate over the components for this row */
			foreach($comps[$i] as $comp){
				/* tell the rows that this component is in it */
				for($j=0;$j<$comp->getSecond();$j++){
					if(isset($num_per_row[$i+$j])){
						$num_per_row[$i+$j]++;
					}else{
						$num_per_row[$i+$j] = 1;
					}
				}
			}
		}

		/* iterate over the rows to see the max number of columns we need */
		$max = 0;
		for($i=0;$i<count($num_per_row);$i++){
			$max = (($num_per_row[$i] > $max) ? $num_per_row[$i] : $max);
		}
		/* add an extra cell on the end so we can ensure row height and full width */
		$max++;

		$end_panel_style = new Style();
		$end_panel_style->setWidth("100%");

		/* iterate of the rows of components */
		for($i=0;$i<count($num_per_row);$i++){
			/* iterate over the components for this row */
			$filler = new Panel();
			$filler->setStyle($e->getEndStyle());

			$filler->setWidth("100%");
			$ras = $e->getRowDblClickActions($i);
			foreach($ras as $ra){
				$filler->addDblClickAction($ra);
			}
			for($j=$num_per_row[$i];$j<$max;$j++){
				$comps[$i][]= new Pair($filler, 1);
			}
		}

		/* iterate of the rows of components */
		for($i=0;$i<count($comps);$i++){
			if(count($comps[$i])){
			$ret .= "<tr>";
			/* iterate over the components for this row */
			for($j=0;$j<count($comps[$i]);$j++){
				$ret .= "<td " . $this->getAlignments($e) . " rowspan='" . $comps[$i][$j]->getSecond() . "' ";
				/*
				 * set the cell style class, if any.
				 */
				$style = $e->getCellStyle();

				$last_one = $j == (count($comps[$i]) -1);
				if($last_one){
					$style->setWidth("100%");
				}
				$additional_style = "";
				if(is_object($style)){
					if($style->getClassname()){
						$ret .= "class='" . $style->getClassname() . "' ";
					}
					$additional_style = $this->getAdditionalStyle($style);
					if(strlen($additional_style)){
						$ret .= "style='$additional_style' ";
					}
				}
				$ret .= ">";
				$ret .= $comps[$i][$j]->getFirst()->execute($this);
				$ret .= "</td>";
			}
			$ret .= "</tr>";
			}
		}
		$ret .= "</table>";
		return $ret;
	}

	/**
	 * visits a ToolBar panel
	 */
	public function ToolbarPanelCase(ToolbarPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";
		$ret .= "<tr>";

		/* add all inner components to the panel */

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= "<td " . $this->getAlignments($e) . ">" . $comp->execute($this) . "</td>";
		}
		$ret .= "</tr></table>";
		return $ret;
	}

	/**
	 * visits a ToolBar panel
	 */
	public function SidebarPanelCase(SidebarPanel $e){
		if($e->isClosed()){
			$e->setWidth($e->getOpenWidth());
			$e->getStyle()->setBackgroundImage($e->getClosedBackgroundImage());
		}
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";
		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";

		/* add all inner components to the panel */
		$comp_row = "<tr>";
		$comps = $e->getComponents();
		foreach($comps as $comp){
			if($e->isClosed()){
				$comp->getStyle()->setDisplayNone();
			}
			$comp_row .= "<td " . $this->getAlignments($e) . ">" . $comp->execute($this) . "</td>";
			$e->getCloseButton()->addAction(new DisplayNoneAction($comp));
			$e->getOpenButton()->addAction(new DisplayInlineAction($comp));
		}
		$comp_row .= "</tr>";
		/* end adding all components */

		/* add the button
		 * this needs to come after the components, because when we add
		 * the components, we're also adding actions to the buttons. so
		 * print the buttons after the components
		 */
		$button_row = "<tr>";
		if($e->isClosed()){
			$height = $e->getOpenHeight();
			$e->getOpenButton()->getStyle()->setDisplayBlock();
			$e->getCloseButton()->getStyle()->setDisplayNone();
		}else{
			$height = $e->getCloseHeight();
		}
			$button_row .= "<td " . $this->getAlignments($e) . " height='$height' >";
			$button_row .= $e->getOpenButton()->execute($this);
			$button_row .= $e->getCloseButton()->execute($this);
			$button_row .= "</td>";
		$button_row .= "</tr>";
		/* add the buttons */

		$ret .= $button_row . $comp_row;
		$ret .= "</table>";
		return $ret;
	}

	/**
	 * visits a BorderBar panel
	 */
	public function BorderPanelCase(BorderPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";

		/* find total cell width */
		$cell_count = 0 ;
		if($e->getWest()) $cell_count++;
		if($e->getCenter()) $cell_count++;
		if($e->getEast()) $cell_count++;

		/* north case */
		if($e->getNorth()){
			$height = "";
			if($e->getNorthHeight()){
				$height = "height='" . $e->getNorthHeight() . "'";
			}
			$ret .= "<tr><td $height colspan='" . ($cell_count ? $cell_count : 1) . "' " . $this->getAlignments($e) . " width='100%'>";
			$ret .= $e->getNorth()->execute($this);
			$ret .= "</td></tr>";
		}

		if($cell_count > 0){
		}
		/* west case */
		if($e->getWest()){
			$width = "";
			if($e->getWestWidth()){
				$width = "width='" . $e->getWestWidth() . "'";
			}
			$ret .= "<tr>";
			$ret .= "<td " . $this->getAlignments($e) . " $width height='100%'>";
			$ret .= $e->getWest()->execute($this);
			$ret .= "</td>";
		}

		/* center case */
		if($e->getCenter()){
			if(!is_object($e->getWest())) $ret .= "<tr>";
			$ret .= "<td " . $this->getAlignments($e) . " width='100%' height='100%'>";
			$ret .= $e->getCenter()->execute($this);
			$ret .= "</td>";
		}

		/* east case */
		if($e->getEast()){
			$width = "";
			if($e->getEastWidth()){
				$width = "width='" . $e->getEastWidth() . "'";
			}
			if(!is_object($e->getWest()) && !is_object($e->getCenter())) $ret .= "<tr>";
			$ret .= "<td " . $this->getAlignments($e) . " $width height='100%'>";
			$ret .= $e->getEast()->execute($this);
			$ret .= "</td>";
		}

		/* if any in middle row, close row */
		if(is_object($e->getWest()) ||
		   is_object($e->getCenter()) ||
		   is_object($e->getEast())){
			   $ret .= "</tr>";
		   }


		/* south case */
		if($e->getSouth()){
			$height = "";
			if($e->getSouthHeight()){
				$height = "height='" . $e->getSouthHeight() . "'";
			}
			$ret .= "<tr><td $height colspan='" . ($cell_count ? $cell_count : 1) . "' " . $this->getAlignments($e) . " width='100%'>";
			$ret .= $e->getSouth()->execute($this);
			$ret .= "</td></tr>";
		}


		$ret .= "</table>";
		return $ret;
	}

	/**
	 * visits a QuotePanel
	 */
	public function QuotePanelCase(QuotePanel $e){
		/* set the id, but don't close the tag */
		$ret = "<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			if(strlen($additional_style)){
				$additional_style = $this->getAdditionalStyle($style);
			}
		}
		$ret .= "style='padding-left: 4px; margin-left: " . $e->getIndent() . "px; border-left: 1px solid black; $additional_style' ";

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">";
		$ret .= "<tr><td " . $this->getAlignments($e) . ">";

		/* add all inner components to the panel */

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}
		$ret .= "</td></tr></table>";
		return $ret;
	}

	/**
	 * visits a GridPanel
	 */
	public function GridPanelCase(GridPanel $e){
		/* set the id, but don't close the tag */
		$ret = "\n<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$table_additional_style = "";
		$table_style_class = "";
		if(is_object($style)){
			if($style->getClassname()){
				$table_style_class = "class='" . $style->getClassname() . "' ";
			}
			$table_additional_style = $this->getAdditionalStyle($style);
			$table_additional_style = "style='border-collapse: collapse; $table_additional_style' ";
			$ret .= $table_style_class . $table_additional_style;
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">\n";
		/* the style class is now added, so close the table tag */


		/**
		 * figure out style for each cell
		 */
		$default_cell_style = $e->getCellStyle();
		$default_cell_style_text = "";
		if(is_object($default_cell_style)){
			$cell_style_class = "";
			if($default_cell_style->getClassname()){
				$cell_style_class = "class='" . $default_cell_style->getClassname() . "' ";
			}
			$cell_additional_style = $this->getAdditionalStyle($default_cell_style);
			$cell_additional_style = "style='border-collapse: collapse; $cell_additional_style' ";
			$default_cell_style_text = $cell_style_class . $cell_additional_style;
		}

		/**
		 * add all inner components to the panel
		 */
		$comps = $e->getComponents();
		$count = 0;
		$curr_row = "";
		foreach($comps as $comp){
			$cell_style = $e->getCellStyle($comp);
			$cell_style_text = "";
			if($cell_style != $default_cell_style){
				if(is_object($cell_style)){
					$cell_style_class = "";
					if($cell_style->getClassname()){
						$cell_style_class = "class='" . $cell_style->getClassname() . "' ";
					}
					$cell_additional_style = $this->getAdditionalStyle($cell_style);
					$cell_additional_style = "style='border-collapse: collapse; $cell_additional_style' ";
					$cell_style_text = $cell_style_class . $cell_additional_style;
				}
			}else{
				$cell_style_text = $default_cell_style_text;
			}
			$curr_row .= "<td " . $this->getAlignments($e) . $cell_style_text . ">" . $comp->execute($this) . "</td>";
			$count++;
			if(($count % $e->getColumns()) == 0){
				$ret .= "<tr>$curr_row</tr>\n";
				$curr_row = "";
				$count = 0;
			}
		}

		/**
		 * if they've added fewer elements than will fill exactly
		 */
		if($count != 0){
			$col_width = $e->getColumns() - $count;
			$curr_row .= "<td " . $cell_style_text . " colspan='$col_width'></td>";
			$ret .= "<tr>$curr_row</tr>\n";
		}


		$ret .= "</table>";
		return $ret;
	}


	/**
	 * visits a Scroll Panel, if no specific type of panel could be found
	 */
	public function ScrollPanelCase(ScrollPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<div " . $this->getAlignments($e) . $this->getHeightWidth($e) . " id='" . $e->getId() . "' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */

		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
		}

		$ret .= "style='overflow: auto; $additional_style' ";


		$ret .= $this->getActions($e);
		/* the style and style class are now added, so close the tag */
		$ret .= ">";

		/* add all inner components to the panel */

		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}
		$ret .= "</div>";
		return $ret;
	}


	/**
	 * visits a Month Panel, if no specific type of panel could be found
	 */
	public function MonthPanelCase(MonthPanel $e){
		/* set the id, but don't close the tag */
		$ret = "<div " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */

		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
		}

		if(strlen($additional_style)){
			$ret .= "style='$additional_style' ";
		}


		$ret .= $this->getActions($e);
		/* the style and style class are now added, so close the tag */
		$ret .= ">";
		$ret .= "</div>";

		return $ret;
	}


	/**
	 * visits a FormPanel
	 */
	public function FormPanelCase(FormPanel $e){
		/* set the id, but don't close the tag */
		$ret = "\n<table " . $this->getHeightWidth($e) . " id='" . $e->getId() . "' cellpadding='0' cellspacing='0' border='0' ";

		/* set the style class, if any.
		 * right now, i assume that the div can only
		 * have one style object (later, we'll allow multiple)
		 */
		$style = $e->getStyle();
		$table_additional_style = "";
		$table_style_class = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$table_additional_style = $this->getAdditionalStyle($style);
			if(strlen($table_additional_style)){
				$table_additional_style = "style='$table_additional_style' ";
				$ret .= $table_additional_style;
			}
		}

		$ret .= $this->getActions($e);

		/* the style class is now added, so close the table tag */
		$ret .= ">\n";
		/* the style class is now added, so close the table tag */


		$actions = "";

		$enctype = "";
		if($e->getEncType()){
			$enctype = "enctype='" . $e->getEncType() . "'";
		}
		$ret .= "<tr><form " . (strlen($e->getName()) ? ("name='" . $e->getName() . "'") : "") . " " . (strlen($e->getName()) ? ("id='" . $e->getName() . "'") : "") . "action='" . $e->getLocation() . "' method='" . ($e->isPost() ? "POST" : "GET") . "' $enctype onSubmit='$actions'>";

		$ret .= "<td " . $this->getAlignments($e) . ">";

		$fs = $e->getHiddenFields();
		foreach($fs as $f){
			$ret .= "<input type='hidden' name='" . $f["field"] . "' id='" . $f["field"] . "' value='" . htmlspecialchars($f["value"],ENT_QUOTES) . "'>";
		}

		/**
		 * add all inner components to the panel
		 */
		$comps = $e->getComponents();
		foreach($comps as $comp){
			$ret .= $comp->execute($this);
		}


		$ret .= "</td></tr>";
		$ret .= "</form>";
		$ret .= "</table>";
		return $ret;
	}


	/**
	 * this function takes in a style object and returns code that should
	 * be included in the style property of a tag.
	 * ie: <tag style='getAdditionalStyle(...)'>asf</tag>
	 */
	private function getAdditionalStyle(Style $style){
		$additional_style = "";
		if($style->getFontFamily()){
			$additional_style .= "font-family: " . $style->getFontFamily() . "; ";
		}
		if($style->getFontSize()){
			$additional_style .= "font-size: " . $style->getFontSize() . "pt; ";
		}
		if($style->getFontColor()){
			$additional_style .= "color: " . $style->getFontColor() . "; ";
		}
		if($style->getFontWeight()){
			$additional_style .= "font-weight: " . $style->getFontWeight() . "; ";
		}
		if($style->getPosition()){
			$additional_style .= "position: " . $style->getPosition() . "; ";
		}
		if($style->getTop()){
			$additional_style .= "top: " . $style->getTop() . "px; ";
		}
		if($style->getLeft()){
			$additional_style .= "left: " . $style->getLeft() . "px; ";
		}
		if($style->getBottom()){
			$additional_style .= "bottom: " . $style->getBottom() . "px; ";
		}
		if($style->getRight()){
			$additional_style .= "right: " . $style->getRight() . "px; ";
		}
		if($style->getWidth()){
			$additional_style .= "width: " . $style->getWidth() . "; ";
		}
		if($style->getHeight()){
			$additional_style .= "height: " . $style->getHeight() . "; ";
		}
		if($style->getTextAlign()){
			$additional_style .= "text-align: " . $style->getTextAlign() . "; ";
		}
		if($style->getVerticalAlign()){
			$additional_style .= "vertical-align: " . $style->getVerticalAlign() . "; ";
		}
		if($style->getCursor()){
			$additional_style .= " cursor: " . $style->getCursor() . "; ";
		}
		if($style->getBackground()){
			$additional_style .= " background-color: " . $style->getBackground() . "; ";
		}
		if($style->getBackgroundImage()){
			$additional_style .= " background-image: url(" . $style->getBackgroundImage() . ") " . $style->getBackgroundExtra() . "; ";
			if($style->getRepeatBackground()){
				$additional_style .= " background-repeat: " . $style->getRepeatBackground() . ";";
			}
		}
		if($style->getDisplay()){
			$additional_style .= " display: " . $style->getDisplay() . "; ";
		}
		if(is_integer($style->getBorderWidth())){
			$border = $style->getBorderWidth() . "px";
			if($style->getBorderStyle())
				$border .= " " . $style->getBorderStyle();
			if($style->getBorderColor())
				$border .= " " . $style->getBorderColor();
			$additional_style .= " border: $border; ";
		}
		if($style->getTextIndent()){
			$additional_style .= " text-indent: " . $style->getTextIndent() . "px; ";
		}
		if($style->getPadding()){
			$additional_style .= " padding: " . $style->getPadding() . "px; ";
		}else{
			$padding = "";
			if($style->getPaddingTop()){
				$padding .= "padding-top: " . $style->getPaddingTop() . "px; ";
			}
			if($style->getPaddingBottom()){
				$padding .= "padding-bottom: " . $style->getPaddingBottom() . "px; ";
			}
			if($style->getPaddingRight()){
				$padding .= "padding-right: " . $style->getPaddingRight() . "px; ";
			}
			if($style->getPaddingLeft()){
				$padding .= "padding-left: " . $style->getPaddingLeft() . "px; ";
			}
			$additional_style .= $padding;
		}
		if($style->getMargin()){
			$additional_style .= " margin: " . $style->getMargin() . "px; ";
		}else{
			$margin = "";
			if($style->getMarginTop()){
				$margin .= "argin-top: " . $style->getMarginTop() . "px; ";
			}
			if($style->getMarginBottom()){
				$margin .= "margin-bottom: " . $style->getMarginBottom() . "px; ";
			}
			if($style->getMarginRight()){
				$margin .= "margin-right: " . $style->getMarginRight() . "px; ";
			}
			if($style->getMarginLeft()){
				$margin .= "margin-left: " . $style->getMarginLeft() . "px; ";
			}
			$additional_style .= $margin;
		}
		if($style->getOverflow()){
			$additional_style .= "overflow: " . $style->getOverflow() . "; ";
		}
		return $additional_style;
	}

	private function getAlignments(Alignable $e){
		$align = "";
		if($e->getAlign()){
			$align .= " align='" . $e->getAlign() . "' ";
		}
		if($e->getValign()){
			$align .= " valign='" . $e->getValign() . "' ";
		}

		return $align;
	}

	private function getHeightWidth(Sizeable $e){
		$hw = "";
		if($e->getWidth()){
			$hw .= "width='" . $e->getWidth() . "' ";
		}
		if($e->getHeight()){
			$hw .= "height='" . $e->getHeight() . "' ";
		}
		return $hw;
	}

	private function getActions(Actionable $e){
		$ret = "";
		$as = $e->getActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onClick='$actions'";
		}

		$as = $e->getMouseOverActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onMouseOver='$actions'";
		}

		$as = $e->getMouseOutActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onMouseOut='$actions'";
		}

		$as = $e->getDblClickActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onDblClick='$actions'";
		}

		return $ret;
	}


	private function getInputProperties(Input $e){
		$ret = "";

		$style = $e->getStyle();
		$additional_style = "";
		if(is_object($style)){
			if($style->getClassname()){
				$ret .= "class='" . $style->getClassname() . "' ";
			}
			$additional_style = $this->getAdditionalStyle($style);
			if(strlen($additional_style)){
				$ret .= "style='$additional_style' ";
			}
		}

		if(strlen($e->getName()) > 0){
			$ret .= " name='" . $e->getName() . "' ";
		}

		if($e->isReadOnly()){
			$ret .= " READONLY ";
		}

		if($e->isDisabled()){
			$ret .= " DISABLED ";
		}

		$as = $e->getClickActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onClick='$actions'";
		}


		$as = $e->getChangeActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onChange='$actions'";
		}

		$as = $e->getFocusGainedActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onFocus='$actions'";
		}

		$as = $e->getFocusLostActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onBlur='$actions'";
		}

		$as = $e->getKeyPressActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onKeyPress='$actions'";
		}

		$as = $e->getMouseDownActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onMouseDown='$actions'";
		}

		$as = $e->getMouseUpActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onMouseUp='$actions'";
		}

		$as = $e->getKeyUpActions();
		$actions = "";
		for($i=0;$i<count($as); $i++){
			$actions .= trim($as[$i]->toJS());
		}
		if(strlen($actions) > 0){
			$ret .= " onKeyUp='$actions'";
		}
		return $ret;
	}

}



?>