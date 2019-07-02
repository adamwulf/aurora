<?
/**
 * abstractly visits a Element
 */
abstract class AElementVisitor implements ElementVisitor{

	private $hash = false;
	public function __construct(){
		$this->hash = new HashTable();
	}


	public function accept(Element $e){
		if($e instanceof Document){
			$ret = $this->DocumentCase($e);
		}else
		if($e instanceof CSS){
			$ret = $this->CSSCase($e);
		}else
		if($e instanceof Paragraph){
			$ret = $this->ParagraphCase($e);
		}else
		if($e instanceof Style){
			$ret = $this->StyleCase($e);
		}else
		if($e instanceof Link){
			$ret = $this->LinkCase($e);
		}else
		if($e instanceof Anchor){
			$ret = $this->AnchorCase($e);
		}else
		if($e instanceof Text){
			$ret = $this->TextCase($e);
		}else
		if($e instanceof Menu){
			$ret = $this->MenuCase($e);
		}else
		if($e instanceof FileInput){
			$ret = $this->FileInputCase($e);
		}else
		if($e instanceof SubmitInput){
			$ret = $this->SubmitInputCase($e);
		}else
		if($e instanceof ButtonInput){
			$ret = $this->ButtonInputCase($e);
		}else
		if($e instanceof SmallTextInput){
			$ret = $this->SmallTextInputCase($e);
		}else
		if($e instanceof HiddenInput){
			$ret = $this->HiddenInputCase($e);
		}else
		if($e instanceof TextAreaInput){
			$ret = $this->TextAreaInputCase($e);
		}else
		if($e instanceof URLInput){
			$ret = $this->URLInputCase($e);
		}else
		if($e instanceof DateInput){
			$ret = $this->DateInputCase($e);
		}else
		if($e instanceof TimeInput){
			$ret = $this->TimeInputCase($e);
		}else
		if($e instanceof CheckInput){
			$ret = $this->CheckInputCase($e);
		}else
		if($e instanceof RadioInput){
			$ret = $this->RadioInputCase($e);
		}else
		if($e instanceof DropDownInput){
			$ret = $this->DropDownInputCase($e);
		}else
		if($e instanceof IconWithText){
			$ret = $this->IconWithTextCase($e);
		}else
		if($e instanceof Icon){
			$ret = $this->IconCase($e);
		}else
		if($e instanceof TabbedPanel){
			$ret = $this->TabbedPanelCase($e);
		}else
		if($e instanceof SimplePanel){
			$ret = $this->SimplePanelCase($e);
		}else
		if($e instanceof RowPanel){
			$ret = $this->RowPanelCase($e);
		}else
		if($e instanceof SimpleRowPanel){
			$ret = $this->SimpleRowPanelCase($e);
		}else
		if($e instanceof GridPanel){
			$ret = $this->GridPanelCase($e);
		}else
		if($e instanceof MonthPanel){
			$ret = $this->MonthPanelCase($e);
		}else
		if($e instanceof ScrollPanel){
			$ret = $this->ScrollPanelCase($e);
		}else
		if($e instanceof SidebarPanel){
			$ret = $this->SidebarPanelCase($e);
		}else
		if($e instanceof BorderPanel){
			$ret = $this->BorderPanelCase($e);
		}else
		if($e instanceof QuotePanel){
			$ret = $this->QuotePanelCase($e);
		}else
		if($e instanceof ToolbarPanel){
			$ret = $this->ToolbarPanelCase($e);
		}else
		if($e instanceof FormPanel){
			$ret = $this->FormPanelCase($e);
		}else
		if($e instanceof Button){
			$ret = $this->ButtonCase($e);
		}else
		if($e instanceof Panel){
			$ret = $this->PanelCase($e);
		}else
		if($e instanceof ConfirmWindow){
			$ret = $this->ConfirmWindowCase($e);
		}else{
			$ret = $this->ElementCase($e);
		}
		return $ret;
	}

	/**
	 * this is the catch all function. if no other function can handle the input object,
	 * and no other catch all is defined, then this function will be called.
	 *
	 */
	public function ElementCase(Element $e){
		throw new Exception("no behavior defined for an Element in " . __METHOD__);
	}

	/**
	 * visits a Document
	 */
	abstract public function DocumentCase(Document $e);

	/**
	 * visits a CSS
	 */
	abstract public function CSSCase(CSS $e);

	/**
	 * visits a Paragraph
	 */
	abstract public function ParagraphCase(Paragraph $e);

	/**
	 * visits a Style
	 */
	abstract public function StyleCase(Style $e);

	/**
	 * visits a Text
	 */
	abstract public function TextCase(Text $e);

	/**
	 * visits a Anchor
	 */
	abstract public function AnchorCase(Anchor $e);

	/**
	 * visits a Menu
	 */
	abstract public function MenuCase(Menu $e);

	/**
	 * visits an FileInput
	 */
	abstract public function FileInputCase(FileInput $e);

	/**
	 * visits an SubmitInput
	 */
	abstract public function SubmitInputCase(SubmitInput $e);

	/**
	 * visits an ButtonInput
	 */
	abstract public function ButtonInputCase(ButtonInput $e);

	/**
	 * visits an SmallTextInput
	 */
	abstract public function SmallTextInputCase(SmallTextInput $e);

	/**
	 * visits an HiddenInput
	 */
	abstract public function HiddenInputCase(HiddenInput $e);

	/**
	 * visits an TextAreaInput
	 */
	abstract public function TextAreaInputCase(TextAreaInput $e);

	/**
	 * visits an CheckInput
	 */
	abstract public function CheckInputCase(CheckInput $e);

	/**
	 * visits an RadioInput
	 */
	abstract public function RadioInputCase(RadioInput $e);

	/**
	 * visits an DropDownInput
	 */
	abstract public function DropDownInputCase(DropDownInput $e);

	/**
	 * visits an DateInput
	 */
	abstract public function DateInputCase(DateInput $e);

	/**
	 * visits an TimeInput
	 */
	abstract public function TimeInputCase(TimeInput $e);

	/**
	 * visits a Link
	 */
	abstract public function LinkCase(Link $e);

	/**
	 * visits a TabbedPanel
	 */
	abstract public function TabbedPanelCase(TabbedPanel $e);

	/**
	 * visits a RowPanel
	 */
	abstract public function RowPanelCase(RowPanel $e);

	/**
	 * visits a SimpleRowPanel
	 */
	abstract public function SimpleRowPanelCase(SimpleRowPanel $e);

	/**
	 * visits a GridPanel
	 */
	abstract public function GridPanelCase(GridPanel $e);

	/**
	 * visits a SidebarPanel
	 */
	abstract public function SidebarPanelCase(SidebarPanel $e);

	/**
	 * visits a MonthPanel
	 */
	abstract public function MonthPanelCase(MonthPanel $e);

	/**
	 * visits a ScrollPanel
	 */
	abstract public function ScrollPanelCase(ScrollPanel $e);

	/**
	 * visits a BorderPanel
	 */
	abstract public function BorderPanelCase(BorderPanel $e);

	/**
	 * visits a QuotePanel
	 */
	abstract public function QuotePanelCase(QuotePanel $e);

	/**
	 * visits a ToolbarPanel
	 */
	abstract public function ToolbarPanelCase(ToolbarPanel $e);

	/**
	 * visits a FormPanel
	 */
	abstract public function FormPanelCase(FormPanel $e);

	/**
	 * visits a Button
	 */
	abstract public function ButtonCase(Button $e);

	/**
	 * visits a Panel
	 */
	abstract public function PanelCase(Panel $e);

}



?>