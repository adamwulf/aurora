<?

class module_bootstrap_strongcal_field_largetext_gui extends module_bootstrap_module{

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
				$field = $strongcal->fieldManager()->getField("largetext");
				
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
					$to_add["value"] = $default_value;
					try{
						if(is_object($field_from_cal)){
							$field_from_cal->set_prompt($to_add["prompt"]);
							$field_from_cal->set_value($default_value);
							header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
							exit;
						}else{
							if($main_cal_obj->addField($to_add)){
								header("Location: index.php?view=manage_cals&subview=fields&cal_id=" . $main_cal_obj->getId());
								exit;
							}else{
								$cal_info = new ErrorPanel(new Text("could not create Large Text field"));
								$cal_info->getStyle()->setHeight("180px");
							}
						}
					}catch(DatabaseException $e){
						$cal_info = new ErrorPanel(new Text("could not create Large Text field"));
						$cal_info->getStyle()->setHeight("180px");
					}
				}else{
					$field_from_cal = false;
					if(isset($data_list["field_name"])){
						$field_from_cal = $main_cal_obj->getField($data_list["field_name"]);
					}

					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_panel->add(new Text("<br>You're almost done! Just enter the text that you would like to show as the default value for this field.<br><br>"));
					$cal_info->add($temp_panel);

					$check_default = new Panel();
					$check_default->setStyle(new Style("content_font"));
					$check_default->getStyle()->setPadding(3);
					
					if(is_object($field_from_cal)){
						$gui = $field_from_cal->toGui("");
					}else{
						$gui = $field->toGui("");
					}
					$gui->setName("default_value");
					
					$text = new Text("Default Value:<br>");
					$text->getStyle()->setFontFamily("verdana, sans-serif");
					$check_default->add($text);
					$check_default->add($gui);
					
					$field_default = new ErrorPanel($check_default);
					$field_default->getStyle()->setMarginTop(10);
					$field_default->getStyle()->setMarginBottom(10);
					
					$cal_info->add($field_default);
					
					$cal_info->add(new ErrorPanel(new Text("<input type='submit' value='Finish' style='border: 1px solid black;'>")));
					
					$form = new FormPanel("index.php");
					$form->setAsGet();
					$form->addHiddenField("view", $data_list["view"]);
					$form->addHiddenField("subview", $data_list["subview"]);
					$form->addHiddenField("cal_id", (string)$main_cal_obj->getId());
					$form->addHiddenField("add_field", "1");
					$form->addHiddenField("field_type", $field->type());
					$form->addHiddenField("field_name", $this->name);
					$form->addHiddenField("field_prompt", $this->prompt);
					$form->addHiddenField("done", "1");
					$form->add($cal_info);
					
					$cal_info = $form;

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