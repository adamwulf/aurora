<?

class module_bootstrap_strongcal_managefields_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $calendar;

	function __construct($avalanche, Document $doc, $calendar){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_object($calendar)){
			throw new IllegalArgumentException("");
		}
		$this->setName("Aurora HTML to manage fields");
		$this->setInfo("this module takes raw form input");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->calendar = $calendar;
	}

	function run($data = false){
		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$data_list = $data->data();
			/** initialize the input */
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$strongcal = $this->avalanche->getModule("strongcal");

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_cal";
			}

			$main_cal_obj = $this->calendar;
			/** end initializing the input */

			$input_style = new Style();
			$input_style->setBorderWidth(1);
			$input_style->setBorderColor("black");
			$input_style->setBorderStyle("solid");

			/************************************************************************
			************************************************************************/

			if($main_cal_obj->canWriteFields()){
				$cal_info = new GridPanel(1);
				$cal_info->setWidth("100%");

				$cancel = new Button("cancel");
				$cancel->getStyle()->setClassname("manage_cals_button");
				$cancel->addAction(new LoadPageAction("index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId()));

				if(isset($data_list["cal_id"]) && isset($data_list["delete_field"])){
					if($main_cal_obj->dropField($data_list["delete_field"])){
						header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
						exit();
					}else{
						$error = new ErrorPanel(new Text("Could not delete field: \"" . $data_list["delete_field"] . "\""));
						$error->getStyle()->setHeight("200px");
						$cal_info->add($error);
					}
				}else if(isset($data_list["add_field"]) && isset($data_list["field_type"]) &&
				   isset($data_list["field_name"]) && strlen($data_list["field_name"]) &&
				   isset($data_list["field_prompt"])){
					// the field that they want to add
					// let's get the field to verify that it's a valid type
					$field = $strongcal->fieldManager()->getField($data_list["field_type"]);
					$field_from_cal = false;
					if(isset($data_list["field_name"])){
						$field_from_cal = $main_cal_obj->getField($data_list["field_name"]);
					}

					if(is_object($field_from_cal)){
						$add_edit = "Edit";
					}else{
						$add_edit = "Add";
					}
					$temp_title = new GridPanel(2);
					$temp_title->getCellStyle()->setPaddingRight(4);
					$temp_title->add(new Text("$add_edit Custom " . $field->displayType() . " Field (Final Step) -"));
					$temp_title->add($cancel);
					$temp_title->setStyle(new Style("content_title"));
					$cal_info->add($temp_title);

					// get page for customizing this type of field
					$bootstrap_name = "module_bootstrap_strongcal_field_" . $data_list["field_type"] . "_gui";

					$field_name = $data_list["field_name"];
					$field_prompt = $data_list["field_prompt"];

					$text_input = new TextAreaInput();
					$text_input->setName("field_name");
					$text_input->loadFormValue($data_list);
					$field_name = $text_input->getValue();

					$text_input->setName("field_prompt");
					$text_input->loadFormValue($data_list);
					$field_prompt = $text_input->getValue();

					$module = new $bootstrap_name($this->avalanche, $this->doc, $main_cal_obj, $field_name, $field_prompt);
					$data = new module_bootstrap_data($data_list, "modified (by module_bootstrap_strongcal_main_loader) form data");
					$runner = $bootstrap->newDefaultRunner();
					$runner->add($module);
					$field_panel = $runner->run($data);
					$field_panel = $field_panel->data();

					$cal_info->add($field_panel);

				}else if(isset($data_list["add_field"]) && isset($data_list["field_type"])){
					// the field that they want to add
					// let's get the field to verify that it's a valid type
					$field = $strongcal->fieldManager()->getField($data_list["field_type"]);
					$field_from_cal = false;
					if(isset($data_list["field_name"])){
						$field_from_cal = $main_cal_obj->getField($data_list["field_name"]);
					}

					// the name input
					$field_name = new SmallTextInput();
					$field_name->setName("field_name");
					$field_name->setStyle($input_style);
					$field_name->setSize(14);
					$field_name->addKeyPressAction(new AlphaNumericOnlyAction());

					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_title = new GridPanel(2);
					$temp_title->getCellStyle()->setPaddingRight(4);
					if(is_object($field_from_cal)){
						$add_edit = "Edit";
					}else{
						$add_edit = "Add";
					}
					$temp_title->add(new Text("$add_edit Custom " . $field->displayType() . " Field -"));
					$temp_title->add($cancel);
					$temp_title->setStyle(new Style("content_title"));
					$temp_panel->add(new Text("<b>Name</b><br>"));
					if(is_object($field_from_cal)){
						$text = new Text("You cannot edit the name of a field. ");
					}else{
						$text = new Text("Please select a unique name for your field. ");
					}
					if(isset($data_list["field_name"]) && !strlen($data_list["field_name"])){
						$text->getStyle()->setFontColor("#DD0000");
					}else if(isset($data_list["field_name"]) && strlen($data_list["field_name"])){
						if(is_object($field_from_cal)){
							$field_name->setValue($data_list["field_name"]);
							$field_name->setReadOnly(true);
						}
					}
					$temp_panel->add($text);
					$temp_panel->add(new Text("This will help you identify your custom field later.<br>"));
					$temp_panel->add(new Text("Name: "));
					$temp_panel->add($field_name);

					// the prompt input
					$field_prompt = new SmallTextInput();
					$field_prompt->setName("field_prompt");
					$field_prompt->setStyle($input_style);
					if(isset($data_list["field_prompt"])){
						$field_prompt->setValue($data_list["field_prompt"]);
					}else if(is_object($field_from_cal)){
						$field_prompt->setValue($field_from_cal->prompt());
					}
					$field_prompt->setSize(30);

					$temp_panel->add(new Text("<br><br><b>Prompt</b><br>"));
					$temp_panel->add(new Text("Specify a custom prompt for your field. This prompt will be shown above your field in the add and edit event forms.<br>"));
					$temp_panel->add(new Text("Prompt: "));
					$temp_panel->add($field_prompt);


					$cal_info->add($temp_title);
					$cal_info->add($temp_panel);

					$submit = new ErrorPanel(new Text("<input type='submit' value='Next Step' style='border: 1px solid black;'>"));
					$submit->getStyle()->setPaddingTop(10);
					$cal_info->add($submit);

					$form = new FormPanel("index.php");
					$form->setAsGet();
					$form->addHiddenField("view", $data_list["view"]);
					$form->addHiddenField("subview", $data_list["subview"]);
					$form->addHiddenField("add_field", "1");
					$form->addHiddenField("field_type", $data_list["field_type"]);
					$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
					$form->add($cal_info);
					$cal_info = $form;

				}else if(isset($data_list["add_field"])){
					// drop down for fields
					$add_drop = new DropDownInput();
					$add_drop->setName("field_type");
					$fields = $strongcal->fieldManager()->getFields();
					$fields = $this->sortFields($fields);

					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_title = new GridPanel(2);
					$temp_title->getCellStyle()->setPaddingRight(4);
					$temp_title->add(new Text("Add Custom Field - "));
					$temp_title->add($cancel);
					$temp_title->setStyle(new Style("content_title"));
					$temp_panel->add(new Text("Select the type of field you would like to add from the drop down below.<br><br>"));
					$temp_panel->add(new Text("Type: "));
					$temp_panel->add($add_drop);
					$cal_info->add($temp_title);
					$cal_info->add($temp_panel);

					/////////////////////////////////////////
					// create drop down to show sample inputs
					/////////////////////////////////////////
					$temp_title = new Text("Sample");
					$temp_title->setStyle(new Style("content_title"));
					$cal_info->add($temp_title);

					$ddclearfunction = new NewFunctionAction("clear_dd");
					$this->doc->addFunction($ddclearfunction);
					$ddaction = new DropDownAction($add_drop);
					$add_drop->addChangeAction(new CallFunctionAction("clear_dd"));
					$add_drop->getStyle()->setClassname("manage_cals_button");
					$first_huh = true;
					$panels = new GridPanel(1);
					foreach($fields as $field){
						$field_panel = new Panel();
						if($field["field"]->type() == "select"){
							$field["field"]->set_value("Option 1\n1\n1\nOption 2\n2\n0\nOption 3\n3\n0");
						}else if($field["field"]->type() == "check"){
							$field["field"]->set_value("1");
						}
						$gui = $field["field"]->toGui("temp");
						$field_panel->add($gui);
						$add_drop->addOption(new DropDownOption($field["field"]->displayType(), $field["field"]->type()));
						$ddclearfunction->addAction(new DisplayNoneAction($field_panel));
						$ddaction->addAction($field["field"]->type(), new DisplayBlockAction($field_panel));
						$add_drop->addChangeAction($ddaction);

						if($first_huh){
							$first_huh = false;
							$field_panel->getStyle()->setDisplayBlock();
						}else{
							$field_panel->getStyle()->setDisplayNone();
						}

						$panels->add($field_panel);
					}
					$field_demos = new ErrorPanel($panels);
					$field_demos->getStyle()->setMarginTop(10);
					$field_demos->getStyle()->setMarginBottom(10);
					//$field_demos->getStyle()->setHeight("110px");

					$cal_info->add($field_demos);

					$cal_info->add(new ErrorPanel(new Text("<input type='submit' value='Next Step' style='border: 1px solid black;'>")));

					$form = new FormPanel("index.php");
					$form->setAsGet();
					$form->addHiddenField("view", $data_list["view"]);
					$form->addHiddenField("subview", $data_list["subview"]);
					$form->addHiddenField("add_field", "1");
					$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
					$form->add($cal_info);

					$cal_info = $form;


					/////////////////////////////////////////
					// end creating drop down to show sample inputs
					/////////////////////////////////////////
				}else if(isset($data_list["action"]) &&
					 $data_list["action"] == "form_order_down" &&
					 isset($data_list["field"]) && isset($data_list["cal_id"])){
					$fields = $main_cal_obj->fields();
					// find the field and the field to switch with
					$field = false;
					$switcher = false;
					for($i=0;$i<count($fields);$i++){
						if($fields[$i]->field() == $data_list["field"] && $i < count($fields) - 2){
							$field = $fields[$i];
							$switcher = $fields[$i+1];
						}
					}
					if(is_object($field) && is_object($switcher)){
						$temp_loc = $field->form_order();
						$field->set_form_order($switcher->form_order());
						$switcher->set_form_order($temp_loc);
						header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
						exit();
					}
				}else if(isset($data_list["action"]) &&
					 $data_list["action"] == "form_order_up" &&
					 isset($data_list["field"]) && isset($data_list["cal_id"])){
					$fields = $main_cal_obj->fields();
					// find the field and the field to switch with
					$field = false;
					$switcher = false;
					for($i=1;$i<count($fields);$i++){
						if($fields[$i]->field() == $data_list["field"]){
							$field = $fields[$i];
							$switcher = $fields[$i-1];
						}
					}
					if(is_object($field) && is_object($switcher)){
						$temp_loc = $field->form_order();
						$field->set_form_order($switcher->form_order());
						$switcher->set_form_order($temp_loc);
						header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
						exit();
					}
				}else{
					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_title = new Text("Custom Fields");
					$temp_title->setStyle(new Style("content_title"));
					$temp_panel->add(new Text("Customize your calendar by adding fields specific to your organizational needs.<br>"));
					$temp_panel->add(new Text("To change the order that these fields appear on the add and edit event forms, click the move up and move down links."));
					$cal_info->add($temp_title);
					$cal_info->add($temp_panel);

					$add_button = new Button("Add Field");
					$add_button->getStyle()->setClassname("manage_cals_button");
					$add_button->addAction(new LoadPageAction("index.php?view=manage_cals&subview=fields&add_field=1&cal_id=" . $main_cal_obj->getId()));

					$temp_panel = new QuotePanel(20);
					$temp_panel->setWidth("100%");
					$temp_panel->setStyle(new Style("content_font"));
					$temp_title = new Text("Current Fields");
					$temp_title->setStyle(new Style("content_title"));
					$fields = $main_cal_obj->fields();

					$field_panel = new GridPanel(5);
					$field_panel->setWidth("100%");
					$field_panel->setCellStyle(new Style("content_font"));
					$field_panel->getCellStyle()->setPadding(2);
					$field_panel->add(new Text("<b>Name</b>"));
					$field_panel->add(new Text("<b>Type</b>"));
					$field_panel->add(new Text(""));
					$field_panel->add(new Text(""));
					$field_panel->add(new Text(""));
					$removeable_fields = array();
					foreach($fields as $field){
						if($field->removeable()){
							$removeable_fields[] = $field;
						}
					}
					$fields = $removeable_fields;
					for($i=0;$i<count($fields);$i++){
						$field = $fields[$i];
						$field_panel->add(new Text($field->field()));
						$field_panel->add(new Text($field->displayType()));
						$edit_button = new Button("edit");
						$edit_button->getStyle()->setClassname("manage_cals_button");
						$edit_button->addAction(new LoadPageAction("index.php?view=manage_cals&subview=fields&add_field=1&field_name=" . $field->field() . "&field_type=" . $field->type() . "&cal_id=" . $main_cal_obj->getId()));
						$field_panel->add($edit_button);

						$title = new Text("<b>Delete Field?</b><br>");
						$text = new Text("Delete the field <i>" . $field->field() . "</i>?<br>");
						$warning = new Text("(All related information will be lost. This cannot be reversed.)");
						$warning->getStyle()->setFontSize(8);
						$delete_confirm_window = new SimpleWindow($title);
						$delete_confirm_window->add($text);
						$delete_confirm_window->add($warning);
						$yes_action = new LoadPageAction("index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId() . "&delete_field=" . $field->field());
						$no_action = new MoveToAction($delete_confirm_window, -1000, -1000);

						$no_action = new MoveToAction($delete_confirm_window, -1000, -1000);
						$no_button = new Button("Never Mind");
						$no_button->setStyle(new Style("confirm_window_no"));
						$no_button->addAction($no_action);
						$delete_confirm_window->add($no_button);

						$yes_button = new Button("Delete");
						$yes_button->setStyle(new Style("confirm_window_yes"));
						$yes_button->addAction($yes_action);
						$yes_button->addAction($no_action);
						$delete_confirm_window->add($yes_button);

						$delete_button = new Button("delete");
						$delete_button->getStyle()->setClassname("manage_cals_button");
						$delete_button->addAction(new MoveToCenterAction($delete_confirm_window, 500));
						$field_panel->add($delete_button);
						$this->doc->addHidden($delete_confirm_window);


						$form_order_panel = new Panel();
						$form_order_panel->setStyle(new Style("content_font"));

						$down_url = "index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId() . "&action=form_order_down&field=" . $field->field();
						$up_url = "index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId() . "&action=form_order_up&field=" . $field->field();



						if(count($fields) == 1){
							// don't show move up/down links
						}else if($i == 0){
							$form_order_panel->add(new Text("("));
							$form_order_panel->add(new Link("move down", $down_url));
							$form_order_panel->add(new Text(")"));
						}else if($i == count($fields) - 1){
							$form_order_panel->add(new Text("("));
							$form_order_panel->add(new Link("move up", $up_url));
							$form_order_panel->add(new Text(")"));
						}else{
							$form_order_panel->add(new Text("("));
							$form_order_panel->add(new Link("move down", $down_url));
							$form_order_panel->add(new Text(" | "));
							$form_order_panel->add(new Link("up", $up_url));
							$form_order_panel->add(new Text(")"));
						}
						$field_panel->add($form_order_panel);
					}
					if(count($fields) == 0){
						$temp_panel->add(new Text("<i>There are no custom fields for this calendar.<br></i>"));
					}else{
						$temp_panel->add($field_panel);
					}
					$temp_panel->add($add_button);
					$cal_info->add($temp_title);
					$cal_info->add($temp_panel);
				}
				$output_panel = $cal_info;
			}else{
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to view or change custom fields for this calendar."));
				$error = new ErrorPanel($content);
				$output_panel = $error;
			}

			/************************************************************************
			 put it all together
			************************************************************************/

			return new module_bootstrap_data($output_panel, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	private function sortFields($fields){
		$fs = array();
		// put fields in array without names
		foreach($fields as $field){
			$fs[] = $field["field"];
		}

		// sort fields
		for($i=0;$i<count($fs);$i++){
			for($j=$i+1;$j<count($fs);$j++){
				if($fs[$i]->displayType() > $fs[$j]->displayType()){
					$temp = $fs[$i];
					$fs[$i] = $fs[$j];
					$fs[$j] = $temp;
				}
			}
		}

		// put fields back in array
		$fields = array();
		foreach($fs as $f){
			$fields[] = array("field" => $f, "name" => $f->type());
		}
		return $fields;
	}
}
?>