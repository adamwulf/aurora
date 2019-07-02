<?

class module_bootstrap_strongcal_field_select_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $calendar;
	private $name;
	private $prompt;

	function __construct($avalanche, Document $doc, $calendar, $name, $prompt){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		if(!is_object($calendar)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a strongcal calendar object");
		}
		if(!is_string($name)){
			throw new IllegalArgumentException("argument 4 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($prompt)){
			throw new IllegalArgumentException("argument 5 to " . __METHOD__ . " must be a string");
		}
		$this->setName("Aurora HTML to add a field");
		$this->setInfo("this module takes raw form input");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->calendar = $calendar;
		$this->name = $name;
		$this->prompt = $prompt;
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

			$main_cal_obj = $this->calendar;
			/** end initializing the input */			

			
			$input_style = new Style();
			$input_style->setBorderWidth(1);
			$input_style->setBorderColor("black");
			$input_style->setBorderStyle("solid");
			
			/************************************************************************
			************************************************************************/
			
			if($main_cal_obj->canWriteFields()){
				$field = $strongcal->fieldManager()->getField("select");
				
				$cal_info = new GridPanel(1);
				$cal_info->setWidth("100%");
				
				if(isset($data_list["done"])){
					$field_from_cal = false;
					if(isset($data_list["field_name"])){
						$field_from_cal = $main_cal_obj->getField($data_list["field_name"]);
					}
					$to_add = $field->to_add();
					$to_add["name"] = $this->name;
					$to_add["prompt"] = $this->prompt;

					$text_input = new TextAreaInput();
					$text_input->setName("default_value");
					$text_input->loadFormValue($data_list);
					$default_value = $text_input->getValue();
					$default_value = trim($default_value);
					
					$text_input = new TextAreaInput();
					$text_input->setName("option_values");
					$text_input->loadFormValue($data_list);
					$option_values = $text_input->getValue();
					
					$option_values = explode("\n", $option_values);
					$value = "";
					foreach($option_values as $option){
						$option = trim($option);
						if(strlen($value)){
							$value .= "\n";
						}
						$value .= $option . "\n";
						$value .= $option . "\n";
						if($option == $default_value){
							$value .= "1";
						}else{
							$value .= "";
						}
					}
					
					
					$to_add["value"] = $value;
					try{
						if(is_object($field_from_cal)){
							$field_from_cal->set_prompt($to_add["prompt"]);
							$field_from_cal->set_value($to_add["value"]);
							header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
							exit;
						}else{
							if($main_cal_obj->addField($to_add)){
								header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
								exit;
							}else{
								$cal_info = new ErrorPanel(new Text("could not create Small Text field"));
								$cal_info->getStyle()->setHeight("180px");
							}
						}
					}catch(DatabaseException $e){
						$cal_info = new ErrorPanel(new Text("could not create Small Text field"));
						$cal_info->getStyle()->setHeight("180px");
					}
				}else{
					$field_from_cal = false;
					if(isset($data_list["field_name"])){
						$field_from_cal = $main_cal_obj->getField($data_list["field_name"]);
					}
					if(!isset($data_list["option_values"]) && is_object($field_from_cal)){
						$vals = "";
							$dd = $field_from_cal->toGui("");
							$opts = $dd->getOptions();
							foreach($opts as $opt){
								if(strlen($vals)){
									$vals .= "\n";
								}
								$vals .= $opt->getDisplay();
								if($opt->isSelected()){
									$data_list["default_value"] = $opt->getDisplay();
								}
							}
							if(get_magic_quotes_gpc()){
								// mimic form input
								$vals = addslashes($vals);
							}
						$data_list["option_values"] = $vals;
						
					}
					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_panel->add(new Text("<br>You're almost done! Now you need to enter the options that will appear in your drop down. Enter each option on a seperate line of the text area below. Click [Preview] and then select the default value. When you're done, click [Finish].<br><br>"));
					$cal_info->add($temp_panel);

					
					$left_side = new GridPanel(1);
					$left_side->setWidth("100%");
					$left_side->setAlign("center");
					
					$check_default = new Panel();
					$check_default->setStyle(new Style("content_font"));
					$check_default->getStyle()->setPadding(3);
					
					$options_field = $strongcal->fieldManager()->getField("largetext");
					$gui = $options_field->toGui("");
					$gui->wordWrapOff();
					$gui->setCols(15);
					$gui->setRows(4);
					$gui->setName("option_values");
					$gui->loadFormValue($data_list);
					
					$text = new Text("Options:<br>");
					$text->getStyle()->setFontFamily("verdana, sans-serif");
					$check_default->add($text);
					$check_default->add($gui);
					
					$field_default = new ErrorPanel($check_default);
					$field_default->getStyle()->setMarginTop(10);
					$field_default->getStyle()->setMarginBottom(10);
					
					$left_side->add($field_default);
					
					$left_side->add(new ErrorPanel(new Text("<input type='submit' value='Preview &gt;' style='border: 1px solid black;'>")));
					
					$form = new FormPanel("index.php");
					$form->setWidth("100%");
					$form->setAlign("center");
					$form->setAsGet();
					$form->addHiddenField("view", $data_list["view"]);
					$form->addHiddenField("subview", $data_list["subview"]);
					$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
					$form->addHiddenField("add_field", "1");
					$form->addHiddenField("field_type", $field->type());
					$form->addHiddenField("field_name", $this->name);
					if(isset($data_list["default_value"])){
						$form->addHiddenField("default_value", $data_list["default_value"]);
					}
					$form->addHiddenField("field_prompt", $this->prompt);
					$form->add($left_side);
					$left_side = $form;
					
					// now the right side
					
					if(isset($data_list["option_values"])){
						$right_side = new GridPanel(1);
						$right_side->setStyle(new Style("content_font"));
						$right_side->getStyle()->setFontFamily("verdana, sans-serif");
						$right_side->setWidth("100%");
						$right_side->setAlign("center");
						
						// create the default value drop down
						$right_side->add(new Text("Pick the Default Value:"));
						
						$options = $gui->getValue();
						$options = explode("\n", $options);
						$dropdown = new DropDownInput();
						$dropdown->setName("default_value");
						$dropdown->getStyle()->setMarginBottom(60);
						foreach($options as $option){
							$dropdown->addOption(new DropDownOption(rtrim($option), rtrim($option)));
						}
						if(isset($data_list["default_value"])){
							$dropdown->setValue(rtrim($data_list["default_value"]));
						}
						
						$right_side->add($dropdown);
						
						// now wrap the form
						$right_side->add(new ErrorPanel(new Text("<input type='submit' value='Finished' style='border: 1px solid black;'>")));
						
						$form = new FormPanel("index.php");
						$form->setWidth("100%");
						$form->setAlign("center");
						$form->setAsGet();
						$form->addHiddenField("view", $data_list["view"]);
						$form->addHiddenField("subview", $data_list["subview"]);
						$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
						$form->addHiddenField("add_field", "1");
						$form->addHiddenField("option_values", $gui->getValue());
						$form->addHiddenField("field_type", $field->type());
						$form->addHiddenField("field_name", $this->name);
						$form->addHiddenField("field_prompt", $this->prompt);
						$form->addHiddenField("done", "1");
						$form->add($right_side);
						$right_side = $form;

						$both_sides = new GridPanel(2);
						$both_sides->setAlign("center");
						$both_sides->setVAlign("bottom");
						$both_sides->setWidth("100%");
						$both_sides->add($left_side);
						$both_sides->add($right_side);
						$cal_info->add($both_sides);
					}else{
						$cal_info->add($left_side);
					}

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
}
?>