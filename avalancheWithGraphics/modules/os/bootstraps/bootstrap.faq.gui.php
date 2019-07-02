<?

class module_bootstrap_os_faq_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche Overview to HTML");
		$this->setInfo("outputs an overview of the system.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();

			
			$admin_faqs = array(
				array("question" => new Text("How do I add a calendar?"),
				      "answer" => new Text("Click the [Add New] link in the calendar list in the sidebar. If you do not see an [Add New] link, then you do not have permission to add calendars.")),
				array("question" => new Text("How do I edit a calendar?"),
				      "answer" => new Text("Click the [manage calendars] link in the sidebar, and then click the calendar name that you wish to change. Click the [Edit] button at the bottom of the calendar page.")),
				array("question" => new Text("How do I add an event?"),
				      "answer" => new Text("Click the add event icon in the top right of the page. The click the calendar name you wish to add the event to. If no calendar names appear in the dropdown, then you do not have permission to add events.")),
				array("question" => new Text("How do I edit an event?"),
				      "answer" => new Text("Navigate to the event and click the [Edit] button at the bottom of the event page.")),
				array("question" => new Text("How do I add an task?"),
				      "answer" => new Text("Click the add task icon in the top right of the page. The click the calendar name you wish to add the task to. If no calendar names appear in the dropdown, then you do not have permission to add tasks.")),
				array("question" => new Text("How do I edit a task?"),
				      "answer" => new Text("Navigate to the task and click the [Edit] button at the bottom of the task page.")),
				array("question" => new Text("My calendar doesn't show up in the sidebar anymore. Where did it go?"),
				      "answer" => new Text("You calendar has most likely been hidden. Click the \"more\" link in the sidebar and then click your calendar's name to unhide it.")),
				array("question" => new Text("How do I share my caledar?"),
				      "answer" => new Text("In the calendar list, click the name of the calendar that you want to share and click 'Manage'. Now click the 'share' tab and set permissions by filling in the appropriate check boxes.")),
				array("question" => new Text("What is a \"Group\"?"),
				      "answer" => new Text("These are groups of users that share a common goal and purpose. Groups make it easy to share your calendar to a specific number of users.")),
				array("question" => new Text("How do I create a Group?"),
				      "answer" => new Text("Click the [Group Mgt] link in the sprocket menu dropdown. Next, you will see an [Add New] button. Click this button and follow the instructions on the next screen to add a group.")),
				array("question" => new Text("I am a guest user. How do I register for an account?"),
				      "answer" => new Text("New users cannot currently register for new accounts. New users must be physically added by a site administrator.")),
				array("question" => new Text("What are custom fields?"),
				      "answer" => new Text("Custom fields allow you to specify common fields for events in a calendar. These fields will show up on add and edit event pages.")),
				array("question" => new Text("How do I add custom fields to a calendar?"),
				      "answer" => new Text("In the calendar list, click the name of the calendar that you want add fields to and click 'Manage'. Click the 'fields' tab and then click the [Add Field] button on the next page and follow the on screen instructions."))
			);
			
			/** end initializing the input */		
			$section_title_style = new Style();
			$section_title_style->setFontFamily("verdana, sans-serif");
			$section_title_style->setFontSize(10);
			$section_title_style->setFontWeight("bold");
			
			$section_content_style = new Style();
			$section_content_style->setFontFamily("verdana, sans-serif");
			$section_content_style->setFontSize(9);
			
			$content = new GridPanel(1);
			$content->setValign("top");
			$content->getCellStyle()->setPaddingBottom(20);
			$content->getCellStyle()->setPaddingRight(50);
			
			$toc = new GridPanel(1);
			$toc->setCellStyle($section_content_style);
			
			$content->add($toc);
			$i=0;
			foreach($admin_faqs as $qanda){
				$i++;
				
				$toc->add(new Link($i . ". " . $qanda["question"]->getText(), "#faq_" . $i));
				$question = new GridPanel(1);
				$question->getCellStyle()->setPadding(5);
				$question_panel = new Panel();
				$question_panel->setStyle($section_title_style);
				$question_panel->add(new Anchor("faq_" . $i));
				$question_panel->add($qanda["question"]);
				$question->add($question_panel);
				$answer_panel = new Panel();
				$answer_panel->setValign("middle");
				$answer_panel->setWidth("100%");
				$answer_panel->add($qanda["answer"]);
				$answer_panel->getStyle()->setFontFamily("verdana, sans-serif");
				$answer_panel->getStyle()->setFontSize(9);
				$question->add($answer_panel);
				$content->add($question);
			}
			
			$content->getStyle()->setMarginLeft(20);
			$content->getCellStyle()->setPaddingBottom(10);
			/************************************************************************
			put it all together
			************************************************************************/

			$faq = $content;
			return new module_bootstrap_data($faq, "a gui component for the faq page");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}
}
?>