<?

include "../../../include.avalanche.fullApp.php";

$text_input = new SmallTextInput();
$doc = new Document();


$panel = new GridPanel(2);
$panel->getCellStyle()->setPadding(4);
$panel->getCellStyle()->setBorderWidth(1);
$panel->getCellStyle()->setBorderStyle("solid");
$panel->getCellStyle()->setBorderColor("black");
$panel->setWidth("600");
$doc->add($panel);

$panel->add(new Text("This should be a standard text input, with no special actions."));
$panel->add(new SmallTextInput());


$panel->add(new Text("This should be a standard text input, with a 1 px border."));
$text = new SmallTextInput();
$text->getStyle()->setBorderWidth(1);
$text->getStyle()->setBorderStyle("solid");
$text->getStyle()->setBorderColor("black");
$panel->add($text);


$panel->add(new Text("This should be a standard text input, with a 1 px border. the background should turn grey when it has the focus, and back to white when the focus is lost."));
$text = new SmallTextInput();
$text->getStyle()->setBorderWidth(1);
$text->getStyle()->setBorderStyle("solid");
$text->getStyle()->setBorderColor("black");
$panel->add($text);
$text->addFocusGainedAction(new BackgroundAction($text, "silver"));
$text->addFocusLostAction(new BackgroundAction($text, "white"));

$panel->add(new Text("This should be a standard text input, with a size of only 20"));
$text = new SmallTextInput();
$text->setSize(20);
$panel->add($text);

$panel->add(new Text("This should be a standard text input, with a maxlength of 2"));
$text = new SmallTextInput();
$text->setMaxLength(2);
$panel->add($text);

$panel->add(new Text("This should be a standard text input, and is READONLY with a default value of \"my value\""));
$text = new SmallTextInput();
$text->setValue("my value");
$text->setReadOnly(true);
$panel->add($text);

$panel->add(new Text("This should be a standard text input, and has its name set to \"billy\" (check via source)"));
$text = new SmallTextInput();
$text->setName("billy");
$panel->add($text);

$panel->add(new Text("This input should mimic the ampm field in an Aurora time field"));
$text = new SmallTextInput();
$text->setName("time");
$text->setSize(2);
$text->setValue("am");
$text->setReadOnly(true);
$select_all_action = new SelectAction($text);
$text->getStyle()->setBorderWidth(1);
$text->getStyle()->setBorderStyle("solid");
$text->getStyle()->setBorderColor("black");
$text->addClickAction($select_all_action);
$text->addKeyPressAction(new AmPmAction($text));
$text->addKeyPressAction($select_all_action);
$panel->add($text);

				 
$panel->add(new Text("This input field will only allow numbers to be typed"));
$text = new SmallTextInput();
$text->setName("hour");
$text->setSize(2);
$text->setMaxLength(2);
$text->getStyle()->setBorderWidth(1);
$text->getStyle()->setBorderStyle("solid");
$text->getStyle()->setBorderColor("black");
//$text->addKeyPressAction(new AlertKeyCodeAction($text));
$text->addKeyPressAction(new NumberOnlyAction($text));
$panel->add($text);

$panel->add(new Text("This input field will load whatever value is sent in the \$matt form field. Example Link:<br> <a href='html.Input.php?matt=asdf'>html.Input.php?matt=asdf</a>."));
$text = new SmallTextInput();
$text->setName("matt");
$text->loadFormValue(array_merge($_REQUEST, $_FILES));
$text->addKeyPressAction(new NumberOnlyAction());
$panel->add($text);

$panel->add(new Text("This input field does not have a border"));
$text = new SmallTextInput();
$text->getStyle()->setBorderWidth(0);
$panel->add($text);

$panel->add(new Text("This field will alert when changed"));
$text = new SmallTextInput();
$text->addChangeAction(new ManualAction("alert(\"changed!\");"));
$panel->add($text);

$panel->add(new Text("This is the standard date field"));
$date = new DateInput("2004-08-23");
$panel->add($date);

$panel->add(new Text("This is the standard date field that will show an alert box when you click on it"));
$date = new DateInput("2004-08-23");
$date->addClickAction(new ManualAction("alert(\"you clicked the date field!\");"));
$panel->add($date);

$panel->add(new Text("This input field will load whatever value is sent in the \$mydate form field. Example Link:<br> <a href='html.Input.php?mydate_year=1982&mydate_month=11&mydate_day=30'>html.Input.php?mydate_year=1982&mydate_month=11&mydate_day=30</a>."));
$date = new DateInput("2004-08-23");
$date->setName("mydate");
$date->loadFormValue(array_merge($_REQUEST, $_FILES));
$panel->add($date);

$panel->add(new Text("This date field has a different background set"));
$date = new DateInput("2004-08-23");
$date->setName("mydate");
$date->getStyle()->setBackground("silver");
$panel->add($date);

$panel->add(new Text("this is the standard time field"));
$time = new TimeInput("14:34");
$time->setName("mytime");
$time->loadFormValue(array_merge($_REQUEST, $_FILES));
$panel->add($time);

$panel->add(new Text("this time field has a background color set"));
$time = new TimeInput("14:34");
$time->setName("mytime");
$time->getStyle()->setBackground("silver");
$panel->add($time);

$panel->add(new Text("This input field will load whatever value is sent in the \$mytime form field. Example Link:<br> <a href='html.Input.php?mytime_hour=07&mytime_minute=11&mytime_ampm=pm'>html.Input.php?mytime_hour=07&mytime_minute=11&mytime_ampm=pm</a>."));
$time = new TimeInput("14:08");
$time->setName("mytime");
$time->loadFormValue(array_merge($_REQUEST, $_FILES));
$panel->add($time);

$panel->add(new Text("is a standard checkbox"));
$check = new CheckInput();
$check->setName("mycheck");
$panel->add($check);

$panel->add(new Text("is checkbox is checked by default"));
$check = new CheckInput();
$check->setChecked(true);
$check->setName("mycheck");
$panel->add($check);

$panel->add(new Text("is checkbox will show an alert box when the value changes"));
$check = new CheckInput();
$check->setChecked(true);
$check->setName("mycheck");
$check->addChangeAction(new ManualAction("alert(\"you changed the checkbox!\");"));
$panel->add($check);

$panel->add(new Text("this is a sample select box"));
$select = new DropDownInput();
$select->setName("myselect");
$select->addOption(new DropDownOption("Option 1", "value_1"));
$select->addOption(new DropDownOption("Option 2", "value_2"));
$select->addOption(new DropDownOption("Option 3", "value_3"));
$panel->add($select);

$panel->add(new Text("this select box will show an alert message when the value changes"));
$select = new DropDownInput();
$select->setName("myselect");
$select->addOption(new DropDownOption("Option 1", "value_1"));
$select->addOption(new DropDownOption("Option 2", "value_2"));
$select->addOption(new DropDownOption("Option 3", "value_3"));
$select->addChangeAction(new ManualAction("alert(\"you changed the dropdown!\");"));
$panel->add($select);

echo $doc->execute(new HtmlElementVisitor());






?>
