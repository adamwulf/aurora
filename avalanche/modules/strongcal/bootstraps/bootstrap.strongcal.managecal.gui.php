<?

class module_bootstrap_strongcal_managecal_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	private $saved;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche User List to HTML");
		$this->setInfo("outputs a html list of users");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
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
			$os = $this->avalanche->getModule("os");

			/** end initializing the input */



			/**
			 * get the list of calendars
			 */
			$inData = false; // send in false as the default value
			$runner = $bootstrap->newDefaultRunner();
			$runner->add(new module_bootstrap_strongcal_calendarlist($this->avalanche));
			$outData = $runner->run($inData);
			$calendar_obj_list = $outData->data();


			if(isset($data_list["subview"])){
				$subview = $data_list["subview"];
			}else{
				$subview = "overview";
			}


			if(count($calendar_obj_list)){
				if(!isset($data_list["cal_id"])){
					$cal_id = $calendar_obj_list[0]->getId();
				}else{
					$cal_id = (int) $data_list["cal_id"];
				}
				$cal = $this->getCalendar($calendar_obj_list, $cal_id);

			}else{
				throw new Exception("You do not have access to any calendars");
			}

			/*********************************************************************
				manage form input
			*********************************************************************/
			$this->saved = false;
			if($subview == "share" && isset($data_list["submit"])){
				$groups = $this->avalanche->getAllUsergroups();
				//print_r($data_list);
				foreach($groups as $g){
					if(isset($data_list["share"][$g->getId()]) && $data_list["share"][$g->getId()]){
						$cal->isPublic(true);
						if(isset($data_list["isAdmin"][$g->getId()]) && $data_list["isAdmin"][$g->getId()]){
							$cal->updatePermission("name", "rw", $g->getId());
							$cal->updatePermission("entry", "rw", $g->getId());
							$cal->updatePermission("comments", "rw", $g->getId());
						}else{
							$cal->updatePermission("name", "r", $g->getId());
							if(isset($data_list["event"][$g->getId()])){
								$val = $data_list["event"][$g->getId()];
								if($val != "" && $val != "r" && $val != "rw"){
									throw new IllegalArgumentException("event permission must be rw, r, or the null string");
								}
								$cal->updatePermission("entry", $val, $g->getId());
							}
							if(isset($data_list["comment"][$g->getId()])){
								$val = $data_list["comment"][$g->getId()];
								if($val != "" && $val != "r" && $val != "rw"){
									throw new IllegalArgumentException("comment permission must be rw, r, or the null string");
								}
								$cal->updatePermission("comments", $val, $g->getId());
							}
						}
					}else{
						$cal->updatePermission("name", "", $g->getId());
					}
				}
				$this->saved = true;
			}











			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/manage_cal.css"));
			$this->doc->addStyleSheet($css);
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/sharing.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/


			$headerStyle = new Style("manageCalHeader");
			$column = new Style("column");
			$activeButtonStyle = new Style("activeButton");
			$inactiveButtonStyle = new Style("inactiveButton");
			$contentStyle = new Style("contentStyle");
			$buttonDescStyle = new Style("buttonDesc");
			$pageStyle = new Style("calPageStyle");

			$infoStyle = new Style("infoBox");
			$infoHolderStyle = new Style("infoBoxHolder");
			/************************************************************************
			    initialize panels
			************************************************************************/

			$overviewContent = new SimplePanel();
			$shareContent = new SimplePanel();
			$fieldContent = new SimplePanel();

			//active buttons
			$aOverviewButton = new SimplePanel();
			$aShareButton = new SimplePanel();
			$aFieldButton = new SimplePanel();
			//inactive buttons
			$iOverviewButton = new SimplePanel();
			$iShareButton = new SimplePanel();
			$iFieldButton = new SimplePanel();

			$header = new SimplePanel();
			$content = new SimplePanel();
			$buttonColumn = new SimplePanel();


			/************************************************************************
			    create buttons
			************************************************************************/
			$aOverviewButton->add(new Text("Overview<br>"));
			$t = new Text("View basic information about this calendar");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$aOverviewButton->add($t2);
			$aShareButton->add(new Text("Share<br>"));
			$t = new Text("Share events and tasks with others");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$aShareButton->add($t2);
			$aFieldButton->add(new Text("Customize<br>"));
			$t = new Text("Customize add and edit event forms");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$aFieldButton->add($t2);


			$iOverviewButton->add(new Text("Overview<br>"));
			$t = new Text("View basic information about this calendar");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$iOverviewButton->add($t2);
			$iShareButton->add(new Text("Share<br>"));
			$t = new Text("Share events and tasks with others");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$iShareButton->add($t2);
			$iFieldButton->add(new Text("Customize<br>"));
			$t = new Text("Customize add and edit event forms");
			$t2 = new SimplePanel();
			$t2->setStyle($buttonDescStyle);
			$t2->add($t);
			$iFieldButton->add($t2);
			/************************************************************************
			    apply styles to created panels
			************************************************************************/

			$header->setStyle($headerStyle);
			$content->setStyle($contentStyle);
			$buttonColumn->setStyle($column);
			$content->setStyle($contentStyle);
			$overviewContent->getStyle()->setDisplayNone();
			$shareContent->getStyle()->setDisplayNone();
			$fieldContent->getStyle()->setDisplayNone();

			// apply style
			$aOverviewButton->setStyle(clone $activeButtonStyle);
			$aShareButton->setStyle(clone $activeButtonStyle);
			$aFieldButton->setStyle(clone $activeButtonStyle);
			$iOverviewButton->setStyle(clone $inactiveButtonStyle);
			$iShareButton->setStyle(clone $inactiveButtonStyle);
			$iFieldButton->setStyle(clone $inactiveButtonStyle);
			// hide buttons
			$aOverviewButton->getStyle()->setDisplayNone();
			$aShareButton->getStyle()->setDisplayNone();
			$aFieldButton->getStyle()->setDisplayNone();
			$iOverviewButton->getStyle()->setDisplayNone();
			$iShareButton->getStyle()->setDisplayNone();
			$iFieldButton->getStyle()->setDisplayNone();

			if($subview == "overview"){
				$aOverviewButton->getStyle()->setDisplayBlock();
				$iShareButton->getStyle()->setDisplayBlock();
				$iFieldButton->getStyle()->setDisplayBlock();
			}else if($subview == "share"){
				$iOverviewButton->getStyle()->setDisplayBlock();
				$aShareButton->getStyle()->setDisplayBlock();
				$iFieldButton->getStyle()->setDisplayBlock();
			}else if($subview == "fields"){
				$iOverviewButton->getStyle()->setDisplayBlock();
				$iShareButton->getStyle()->setDisplayBlock();
				$aFieldButton->getStyle()->setDisplayBlock();
			}else{
				$aOverviewButton->getStyle()->setDisplayBlock();
				$iShareButton->getStyle()->setDisplayBlock();
				$iFieldButton->getStyle()->setDisplayBlock();
			}

			/************************************************************************
			     add button actions
			************************************************************************/
			$iOverviewButton->addAction(new DisplayBlockAction($overviewContent));
			$iOverviewButton->addAction(new DisplayNoneAction($shareContent));
			$iOverviewButton->addAction(new DisplayNoneAction($fieldContent));
			$iOverviewButton->addAction(new DisplayBlockAction($aOverviewButton));
			$iOverviewButton->addAction(new DisplayNoneAction($iOverviewButton));
			$iOverviewButton->addAction(new DisplayNoneAction($aShareButton));
			$iOverviewButton->addAction(new DisplayNoneAction($aFieldButton));
			$iOverviewButton->addAction(new DisplayBlockAction($iShareButton));
			$iOverviewButton->addAction(new DisplayBlockAction($iFieldButton));

			$iShareButton->addAction(new DisplayNoneAction($overviewContent));
			$iShareButton->addAction(new DisplayBlockAction($shareContent));
			$iShareButton->addAction(new DisplayNoneAction($fieldContent));
			$iShareButton->addAction(new DisplayNoneAction($aOverviewButton));
			$iShareButton->addAction(new DisplayBlockAction($aShareButton));
			$iShareButton->addAction(new DisplayNoneAction($iShareButton));
			$iShareButton->addAction(new DisplayNoneAction($aFieldButton));
			$iShareButton->addAction(new DisplayBlockAction($iOverviewButton));
			$iShareButton->addAction(new DisplayBlockAction($iFieldButton));

			$iFieldButton->addAction(new DisplayNoneAction($overviewContent));
			$iFieldButton->addAction(new DisplayNoneAction($shareContent));
			$iFieldButton->addAction(new DisplayBlockAction($fieldContent));
			$iFieldButton->addAction(new DisplayNoneAction($aOverviewButton));
			$iFieldButton->addAction(new DisplayNoneAction($aShareButton));
			$iFieldButton->addAction(new DisplayBlockAction($aFieldButton));
			$iFieldButton->addAction(new DisplayNoneAction($iFieldButton));
			$iFieldButton->addAction(new DisplayBlockAction($iOverviewButton));
			$iFieldButton->addAction(new DisplayBlockAction($iShareButton));

			/************************************************************************
			     create sidebar
			************************************************************************/
			$buttonColumn->add($aOverviewButton);
			$buttonColumn->add($iOverviewButton);
			$buttonColumn->add($aShareButton);
			$buttonColumn->add($iShareButton);
			$buttonColumn->add($aFieldButton);
			$buttonColumn->add($iFieldButton);

			/************************************************************************
			     add the headers
			************************************************************************/
			$header->add(new Text("Manage Calendar: " . $cal->name()));

			/************************************************************************
			     build the panels
			************************************************************************/


			/************************************************************************
				build overview
			************************************************************************/
			$minLineNumber=3;
			$cHeader = $this->calendarHeader($cal);

			$info = new SimplePanel();
			$info->setStyle($infoStyle);
			$infoBox = new SimplePanel();
			$infoBox->setStyle($infoHolderStyle);
			$infoBox->add($info);

			if(strlen($cal->description())){
				$desc = $cal->description();
				$count = substr_count($desc, "\n");
				if($count < $minLineNumber){
					for($i=0;$i<$minLineNumber-$count;$i++){
						$desc .= "<br>\n";
					}
				}
				$info->add(new Text(nl2br($desc)));
			}else{
				$info->add(new Text("<i>There is no description for this calendar</i>"));
			}

			$overviewContent->add(new Text("Overview"));
			$overviewContent->add($cHeader);
			$overviewContent->add($infoBox);

				$title = new Text("<b>Delete Calendar?</b><br>");
				$text = new Text("Delete the calendar <i>" . $cal->name() . "</i>?<br>");
				$warning = new Text("(All related information will be lost. This cannot be reversed.)");
				$warning->getStyle()->setFontSize(8);
				$delete_confirm_window = new SimpleWindow($title);
				$delete_confirm_window->add($text);
				$delete_confirm_window->add($warning);
				$yes_action = new LoadPageAction("index.php?view=manage_cals&subview=delete_cal&cal_id=" . $cal->getId());
				$no_action = new MoveToAction($delete_confirm_window, -1000, -1000);

				$buttons = new SimplePanel();
				$buttons->setStyle(new Style("button_group_style"));

				$no_button = new Button("Never Mind");
				$no_button->setStyle(new Style("confirm_window_no"));
				$no_button->addAction($no_action);
				$buttons->add($no_button);

				$yes_button = new Button("Delete");
				$yes_button->setStyle(new Style("confirm_window_yes"));
				$yes_button->addAction($yes_action);
				$yes_button->addAction($no_action);
				$buttons->add($yes_button);
				$delete_confirm_window->add($buttons);

				$back_button = new ButtonInput("< Back");
				$back_button->setStyle(new Style("calButtonStyle"));
				$back_button->getStyle()->setBackground("#EEEEEE");
				$back_button->addClickAction(new LoadPageAction("index.php?view=manage_cals"));

				$delete_button = new ButtonInput("Delete");
				$delete_button->setStyle(new Style("calButtonStyle"));
				$delete_button->getStyle()->setMarginRight(10);
				$delete_button->addClickAction(new MoveToCenterAction($delete_confirm_window, 500));

				$edit_button = new ButtonInput("Edit");
				$edit_button->setStyle(new Style("calButtonStyle"));
				$edit_button->addClickAction(new LoadPageAction("index.php?view=manage_cals&subview=edit_cal&cal_id=" . $cal->getId()));

				$south_buttons = new SimplePanel();

				$south_buttons->add($back_button);
				if($cal->canWriteName()){
					$south_buttons->add(new Text(" | "));
					$south_buttons->add($delete_button);
					$south_buttons->add($edit_button);
				}

				$this->doc->addHidden($delete_confirm_window);
				$overviewContent->add($south_buttons);

			/************************************************************************
				build sharing
			************************************************************************/

			$shareContent->add(new Text("Share"));
			$shareContent->add($cHeader);
			$shareContent->add($this->getSharing($cal));




			/************************************************************************
				build customize panel
			************************************************************************/
			$cHeader = $this->calendarHeader($cal);

			$fieldContent->add(new Text("Customize"));
			$fieldContent->add($cHeader);

			// make the custom field panel
			$module = new module_bootstrap_strongcal_managefields_gui($this->avalanche, $this->doc, $cal);
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();
			$runner->add($module);
			$customize = $runner->run($data);
			$customize = $customize->data();

			$buddy = new SimplePanel();
			$buddy->getStyle()->setPaddingTop(10);
			$buddy->add($customize);

			$fieldContent->add($buddy);


			/************************************************************************
				put it all together
			************************************************************************/
			if($subview == "overview"){
				$overviewContent->getStyle()->setDisplayInline();
			}else if($subview == "share"){
				$shareContent->getStyle()->setDisplayInline();
			}else if($subview == "fields"){
				$fieldContent->getStyle()->setDisplayInline();
			}else{
				$overviewContent->getStyle()->setDisplayInline();
			}

			$content->add($overviewContent);
			$content->add($shareContent);
			$content->add($fieldContent);


			$page = new SimplePanel();
			$page->setStyle($pageStyle);
			$page->add($header);
			$page->add($buttonColumn);
			$page->add($content);

			return new module_bootstrap_data($page, "a gui component for the manage users view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}


	private function getCalendar($calendar_obj_list, $cal_id){
		/**
		 * get the main calendar
		 */
		$main_cal_obj = false;
		foreach($calendar_obj_list as $cal){
			if($cal->getId() == $cal_id){
				$main_cal_obj = $cal;
				break;
			}
		}
		if(!is_object($main_cal_obj) && is_object($calendar_obj_list[0])){
			// they specified an incorrect calendar id
			// just use the first calendar
			$main_cal_obj = $calendar_obj_list[0];
		}
		return $main_cal_obj;
	}

	private function createUserMenu($trigger, $user_id){
		if(!$trigger instanceof Component){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a Component");
		}
		if(!is_int($user_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$data = false;
		$module = new OSUserMenu($this->avalanche, $this->doc, $trigger, $user_id);
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($module);
		$runner->run($data);
	}


	private function calendarHeader($cal){
			$os = $this->avalanche->getModule("os");

		/** styles **/
			$avatarStyle = new Style("avatarStyle");
			$contentHeaderStyle = new Style("contentHeader");
			$avatarHolderStyle = new Style("avatarHolderStyle");
			$avatarStyle = new Style("avatarIconStyle");
			$headerRightStyle = new Style("headerRight");
			$headerLeftStyle = new Style("headerLeft");
			$authorStyle = new Style("calAuthorHolder");
			$calendarColorBox = new Style("calendarColorBox");

		/** panels **/
			$cHeader = new SimplePanel();
			$cHeader->setStyle($contentHeaderStyle);

			$link = new Link($os->getUsername($cal->author()), "javascript:;");
			$this->createUserMenu($link, $cal->author());
			$author = new SimplePanel();
			$author->setStyle($authorStyle);
			$author->add($link);

			$avatarIcon= new Icon($this->avalanche->HOSTURL() . $this->avalanche->getAvatar($cal->author()));
			$avatarIcon->setStyle($avatarStyle);
			$avatar = new SimplePanel();
			$avatar->setStyle($avatarHolderStyle);
			$avatar->add($avatarIcon);
			$avatar->add($author);

			$color = new SimplePanel();
			$color->setStyle($calendarColorBox);
			$color->getStyle()->setBackground($cal->color());

			$calTitle = new SimplePanel();
			$calTitle->setStyle($headerLeftStyle);
			$calTitle->add($color);
			$calTitle->add(new Text($cal->name()));

			$calAuthor = new SimplePanel();
			$calAuthor->setStyle($headerRightStyle);
			$calAuthor->add($avatar);

			$cHeader->add($calTitle);
			$cHeader->add($calAuthor);


			return $cHeader;
	}

	private function getSharing($cal){

		if(!$cal->canWriteName()){
				$content = new Panel();
				$content->getStyle()->setClassname("error_panel");
				$content->add(new Text("You do not have permission to share this calendar."));
				$error = new ErrorPanel($content);
				$buddy = new SimplePanel();
				$buddy->getStyle()->setPaddingTop(10);
				$buddy->add($error);
				return $buddy;
		}


		$sharingHeaderStyle = new Style("sharingHeaderStyle");
		$sharingHeaderHolderStyle = new Style("sharingHeaderHolder");
		$groups = $this->avalanche->getAllUsergroups();

		// make main panel
		$rows = new SimplePanel();
		$rows->setStyle(new Style("sharingContainer"));

		$header = new SimplePanel();
		$header->setStyle($sharingHeaderStyle);

		$headerHolder = new SimplePanel();
		$headerHolder->setStyle($sharingHeaderHolderStyle);
		$headerHolder->add($header);

		$groupOrUser = new SimplePanel();
		$groupOrUser->setStyle(new Style("nameHeader"));
		$groupOrUser->add(new Text("Name"));
		$header->add($groupOrUser);

		$shareHuh = new SimplePanel();
		$shareHuh->setStyle(new Style("shareHeader"));
		$shareHuh->add(new Text("Share"));
		$header->add($shareHuh);

		$adminPanel = new SimplePanel();
		$adminPanel->setStyle(new Style("permissionHeader1Line"));
		$adminPanel->add(new Text("Admin"));
		$header->add($adminPanel);

		$eventPanel = new SimplePanel();
		$eventPanel->setStyle(new Style("permissionHeader2Line"));
		$eventPanel->add(new Text("Events &<br> Tasks"));
		$header->add($eventPanel);

		$commentPanel = new SimplePanel();
		$commentPanel->setStyle(new Style("permissionHeader1Line"));
		$commentPanel->add(new Text("Comments"));
		$header->add($commentPanel);

		$end = new SimplePanel();
		$end->setStyle(new Style("endBoxStyle"));
		$header->add($end);

		$savedText = new Text("Your settings have been saved!<br>&nbsp;");
		$tutorText = new Text("Toggle the checkboxes to share your calendar, <br>then fine tune their permissions.");;
		$errorText = new Text("The user(s) must be able to view an event to also view comments.<br>This has been automatically adjusted.");
		$errorText->getStyle()->setFontColor("#CC0000");
		$errorText->getStyle()->setDisplayNone();

		if($this->saved){
			$tutorText->getStyle()->setDisplayNone();
			$savedText->getStyle()->setDisplayInline();
		}else{
			$tutorText->getStyle()->setDisplayInline();
			$savedText->getStyle()->setDisplayNone();
		}
		foreach($groups as $g){
			if($g->type() == avalanche_usergroup::$PUBLIC ||
			   $g->type() == avalanche_usergroup::$USER && $g->getId() != -$cal->author() ||
			   $g->type() == avalanche_usergroup::$PERSONAL && $g->author() == $this->avalanche->getActiveUser()){
			   // create panels
				$row = new SimplePanel();

				$row->add($this->itemPanel($g));


				$check = new CheckInput();
				$check->setName("share[" . $g->getId() . "]");
				$checkP = new SimplePanel();
				$checkP->setStyle(new Style("checkSharePanel"));
				$checkP->add($check);
				$row->add($checkP);

				$notShared = new SimplePanel();
				$notShared->setStyle(new Style("notShared"));
				$notShared->add(new Text("not shared"));
				$row->add($notShared);

				// create the Text items used to popup the menus
				$commentRW = new Link("View and Add", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) can view all comments for<br> an event are are allowed to add<br> their own comments"));
					$menu_action = new ToolTipAction($commentRW, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$commentR = new Link("View Only", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) are allowed to view all<br>comments for an event"));
					$menu_action = new ToolTipAction($commentR, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$commentNone = new Link("Hide", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) are not allowed to view any comments for an event."));
					$menu_action = new ToolTipAction($commentNone, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$commentText = new HiddenInput();
				$commentText->getStyle()->setDisplayNone();
				$commentText->setName("comment[" . $g->getId() . "]");
				$eventRW = new Link("View and Add", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) can view all event and task information<br>as well as add their own events and tasks"));
					$menu_action = new ToolTipAction($eventRW, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$eventR = new Link("View Only", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) are allowed to see all<br>event and task information"));
					$menu_action = new ToolTipAction($eventR, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$eventNone = new Link("Show as Busy", "javascript:;");
					$tip = OsGuiHelper::createToolTip(new Text("User(s) see all event title's as 'busy'<br>and cannot see tasks"));
					$menu_action = new ToolTipAction($eventNone, $tip);
					$this->doc->addAction($menu_action);
					$this->doc->addHidden($tip);
				$eventText = new HiddenInput();
				$eventText->getStyle()->setDisplayNone();
				$eventText->setName("event[" . $g->getId() . "]");


				// manage the admin permission
				$adminPanel = new SimplePanel();
				$adminPanel->setStyle(new Style("adminPanel"));
				$adminCheck = new CheckInput();
				$adminCheck->setName("isAdmin[" . $g->getId() . "]");
				$adminCheck->setValue("1");
				$adminPanel->add($adminCheck);
				$row->add($adminPanel);

				$adminTextPanel = new SimplePanel();
				$adminTextPanel->setStyle(new Style("adminTextPanel"));
				$adminTextPanel->add(new Text("Administrator Access"));
				$permPanel = new SimplePanel();


				// manage the event permission
				$eventPanel = new SimplePanel();
				$eventPanel->setStyle(new Style("eventPanel"));
				$eventPanel->add($eventRW);
				$eventPanel->add($eventR);
				$eventPanel->add($eventNone);
				$eventRW->setStyle(new Style("xTrigger"));
				$eventR->setStyle(new Style("xTrigger"));
				$eventNone->setStyle(new Style("xTrigger"));
				$eventPanel->add($eventText);
				$menu = $this->createEventMenu($eventRW, $eventR, $eventNone, $eventText, $commentRW, $commentR, $commentNone, $commentText, $tutorText, $errorText, $savedText);
				$menuA = new MenuInitAction($eventRW,   $menu);
				$this->doc->addHidden($menu);
				$this->doc->addAction($menuA);
				$menuA = new MenuInitAction($eventR,    $menu);
				$this->doc->addAction($menuA);
				$menuA = new MenuInitAction($eventNone, $menu);
				$this->doc->addAction($menuA);
				$permPanel->add($eventPanel);

				// manage the comment permission
				$commentPanel = new SimplePanel();
				$commentPanel->setStyle(new Style("eventPanel"));
				$commentPanel->add($commentRW);
				$commentPanel->add($commentR);
				$commentPanel->add($commentNone);
				$commentRW->setStyle(new Style("xTrigger"));
				$commentR->setStyle(new Style("xTrigger"));
				$commentNone->setStyle(new Style("xTrigger"));
				$commentPanel->add($commentText);
				$menu = $this->createCommentMenu($commentRW, $commentR, $commentNone, $commentText, $eventRW, $eventR, $eventNone, $eventText, $tutorText, $errorText, $savedText);
				$menuA = new MenuInitAction($commentRW,   $menu);
				$this->doc->addHidden($menu);
				$this->doc->addAction($menuA);
				$menuA = new MenuInitAction($commentR,    $menu);
				$this->doc->addAction($menuA);
				$menuA = new MenuInitAction($commentNone, $menu);
				$this->doc->addAction($menuA);
				$permPanel->add($commentPanel);

				$row->add($adminTextPanel);
				$row->add($permPanel);

				// create actions and styles
				// action for share checkbox
				$checkboxAction = new IfCheckedThenAction($check, new ManualAction("xGetElementById(\"" . $row->getId() . "\").className=\"enabledRow\";"));
				$checkboxAction->addAction(new DisplayNoneAction($notShared));
				$checkboxAction->addAction(new IfCheckedThenAction($adminCheck, new DisplayInlineAction($adminTextPanel)));
				$checkboxAction->addAction(new IfNotCheckedThenAction($adminCheck, new DisplayInlineAction($permPanel)));
				$checkboxAction->addAction(new DisplayInlineAction($adminPanel));
				$checkboxAction->addAction(new DisplayNoneAction($savedText));
				$checkboxAction->addAction(new DisplayNoneAction($errorText));
				$checkboxAction->addAction(new DisplayInlineAction($tutorText));
				$check->addClickAction($checkboxAction);
				$checkboxAction = new IfNotCheckedThenAction($check, new ManualAction("xGetElementById(\"" . $row->getId() . "\").className=\"disabledRow\";"));
				$checkboxAction->addAction(new DisplayInlineAction($notShared));
				$checkboxAction->addAction(new DisplayNoneAction($adminTextPanel));
				$checkboxAction->addAction(new DisplayNoneAction($permPanel));
				$checkboxAction->addAction(new DisplayNoneAction($adminPanel));
				$checkboxAction->addAction(new DisplayNoneAction($savedText));
				$checkboxAction->addAction(new DisplayNoneAction($errorText));
				$checkboxAction->addAction(new DisplayInlineAction($tutorText));
				$check->addClickAction($checkboxAction);

				// action for admin checkbox
				$checkboxAction = new IfNotCheckedThenAction($adminCheck, new DisplayNoneAction($adminTextPanel));
				$checkboxAction->addAction(new DisplayInlineAction($permPanel));
				$checkboxAction->addAction(new DisplayNoneAction($savedText));
				$checkboxAction->addAction(new DisplayNoneAction($errorText));
				$checkboxAction->addAction(new DisplayInlineAction($tutorText));
				$adminCheck->addClickAction($checkboxAction);
				$checkboxAction = new IfCheckedThenAction($adminCheck, new DisplayNoneAction($permPanel));
				$checkboxAction->addAction(new DisplayInlineAction($adminTextPanel));
				$checkboxAction->addAction(new DisplayNoneAction($savedText));
				$checkboxAction->addAction(new DisplayNoneAction($errorText));
				$checkboxAction->addAction(new DisplayInlineAction($tutorText));
				$adminCheck->addClickAction($checkboxAction);



				$eventRW->getStyle()->setDisplayNone();
				$eventR->getStyle()->setDisplayNone();
				$eventNone->getStyle()->setDisplayNone();
				$commentRW->getStyle()->setDisplayNone();
				$commentR->getStyle()->setDisplayNone();
				$commentNone->getStyle()->setDisplayNone();
				$adminCheck->setChecked(false);
				$adminTextPanel->getStyle()->setDisplayNone();
				$permPanel->getStyle()->setDisplayInline();
				if($cal->canReadName(array($g))){
					if($cal->canWriteName(array($g))){
						$adminCheck->setChecked(true);
						$adminTextPanel->getStyle()->setDisplayInline();
						$permPanel->getStyle()->setDisplayNone();
					}
					if($cal->canWriteEntries(array($g))){
						$eventText->setValue("rw");
						$eventRW->getStyle()->setDisplayInline();
					}else if($cal->canReadEntries(array($g))){
						$eventText->setValue("r");
						$eventR->getStyle()->setDisplayInline();
					}else{
						$eventText->setValue("");
						$eventNone->getStyle()->setDisplayInline();
					}
					if($cal->canWriteComments(array($g))){
						$commentText->setValue("rw");
						$commentRW->getStyle()->setDisplayInline();
					}else if($cal->canReadComments(array($g))){
						$commentText->setValue("r");
						$commentR->getStyle()->setDisplayInline();
					}else{
						$commentText->setValue("");
						$commentNone->getStyle()->setDisplayInline();
					}
					$row->setStyle(new Style("enabledRow"));
					$check->setChecked(true);
					$notShared->getStyle()->setDisplayNone();
					$eventPanel->getStyle()->setDisplayInline();
					$commentPanel->getStyle()->setDisplayInline();
					$adminPanel->getStyle()->setDisplayInline();
				}else{
					$commentText->setValue("");
					$commentNone->getStyle()->setDisplayInline();
					$eventText->setValue("");
					$eventNone->getStyle()->setDisplayInline();
					$row->setStyle(new Style("disabledRow"));
					$notShared->getStyle()->setDisplayInline();
					$permPanel->getStyle()->setDisplayNone();
					$adminTextPanel->getStyle()->setDisplayNone();
					$adminPanel->getStyle()->setDisplayNone();
				}

				// end the row and add it to the table
				$row->add($end);
				$rows->add($row);
			}
		}

		$submitButton = new SubmitInput("Save");
		$submitButton->setName("submit");
		$submitButton->setStyle(new Style("calButtonStyle"));
		$submit = new SimplePanel();
		$submit->setStyle(new Style("submitStyle"));
		$submit->add($submitButton);


		$p0 = new SimplePanel();
		$p0->setStyle(new Style("messageBox"));
		$p0->add($savedText);
		$p0->add($tutorText);
		$p0->add($errorText);

		$p1 = new SimplePanel();
		$p1->setStyle(new Style("infoBox"));
		$p1->add($rows);
		$p2 = new SimplePanel();
		$p2->setStyle(new Style("infoBoxHolder"));
		$p2->add($p1);
		$page = new SimplePanel();
		$page->add($p0);
		$page->add($headerHolder);
		$page->add($p2);
		$page->add($submit);
		$form = new FormPanel("index.php");
		$form->addHiddenField("view", "manage_cals");
		$form->addHiddenField("subview", "share");
		$form->addHiddenField("cal_id", (string)$cal->getId());
		$form->setAsGet();
		$form->add($page);

		$divToReturn = new SimplePanel();
		$divToReturn->add($form);
		return $divToReturn;
	}


	function itemPanel($i){
		if($i instanceof avalanche_usergroup){
			$iconName = new SimplePanel();
			$iconName->setStyle(new Style("groupNameInnerStyle"));
			$p = new SimplePanel();
			$p->setStyle(new Style("groupNameStyle"));
			if($i->type() == avalanche_usergroup::$USER){
				$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/users.png");
				$groups_icon->setStyle(new Style("userIcon"));
				$groups_icon->getStyle()->setWidth(14);
				$groups_icon->getStyle()->setHeight(22);
			}else{
				$groups_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/groups.png");
				$groups_icon->setStyle(new Style("groupIcon"));
				$groups_icon->getStyle()->setWidth(17);
				$groups_icon->getStyle()->setHeight(22);
			}
			$iconName->add($groups_icon);
			$iconName->add(new Text($i->name()));
			$p->add($iconName);
			return $p;
		}else{
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a user or group");
		}
	}

	function createEventMenu($t1, $t2, $t3, $ti, $c1, $c2, $c3, $ct, $tt, $et, $st){
		$menu = new SimplePanel();
		$menu->setStyle(new Style("xMenu"));
		$menu->getStyle()->setBorderWidth(1);
		$menu->getStyle()->setBorderStyle("solid");
		$menu->getStyle()->setBorderColor("black");
		$menu->getStyle()->setBackground("#EFEFEF");
		$menu->getStyle()->setFontFamily("verdana, sans-serif");
		$menu->getStyle()->setFontSize(10);
		$menu->getStyle()->setPosition("absolute");
		$menu->getStyle()->setTop(-1000);
		$menu->getStyle()->setLeft(-1000);

		$i1 = new Link($t1->getText() . "<br>", "javascript:;");
		$i1->addAction(new SetValueAction($ti, "rw"));
		$i1->addAction(new DisplayInlineAction($t1));
		$i1->addAction(new DisplayNoneAction($st));
		$i1->addAction(new DisplayNoneAction($t2));
		$i1->addAction(new DisplayNoneAction($t3));
		$i1->addAction(new MoveToAction($menu, -1000, -1000));
		$i1->addAction(new DisplayInlineAction($tt));
		$i1->addAction(new DisplayNoneAction($et));
		$i2 = new Link($t2->getText() . "<br>", "javascript:;");
		$i2->addAction(new SetValueAction($ti, "r"));
		$i2->addAction(new MoveToAction($menu, -1000, -1000));
		$i2->addAction(new DisplayNoneAction($st));
		$i2->addAction(new DisplayNoneAction($t1));
		$i2->addAction(new DisplayInlineAction($t2));
		$i2->addAction(new DisplayNoneAction($t3));
		$i2->addAction(new DisplayInlineAction($tt));
		$i2->addAction(new DisplayNoneAction($et));
		$i3 = new Link($t3->getText() . "<br>", "javascript:;");
		$i3->addAction(new SetValueAction($ti, ""));
		$i3->addAction(new MoveToAction($menu, -1000, -1000));
		$i3->addAction(new DisplayNoneAction($st));
		$i3->addAction(new DisplayNoneAction($t1));
		$i3->addAction(new DisplayNoneAction($t2));
		$i3->addAction(new DisplayInlineAction($tt));
		$i3->addAction(new DisplayNoneAction($et));
		$i3->addAction(new DisplayInlineAction($t3));
			$a = new IfThenAction(new StrLenEqualsAction($ct, ">", 0), new DisplayNoneAction($c1));
			$a->addAction(new DisplayNoneAction($tt));
			$a->addAction(new DisplayInlineAction($et));
			$a->addAction(new DisplayNoneAction($c2));
			$a->addAction(new DisplayInlineAction($c3));
			$a->addAction(new SetValueAction($ct,""));
		$i3->addAction($a);

		$p1 = new SimplePanel();
		$p1->add($i1);
		$p1->setStyle(new Style("shareMenuItem"));
		$p2 = new SimplePanel();
		$p2->add($i2);
		$p2->setStyle(new Style("shareMenuItem"));
		$p3 = new SimplePanel();
		$p3->add($i3);
		$p3->setStyle(new Style("shareMenuItem"));

		$menu->add($p1);
		$menu->add($p2);
		$menu->add($p3);

		return $menu;
	}

	function createCommentMenu($t1, $t2, $t3, $ti, $e1, $e2, $e3, $et, $tt, $ert, $st){
		$menu = new SimplePanel();
		$menu->setStyle(new Style("xMenu"));
		$menu->getStyle()->setBorderWidth(1);
		$menu->getStyle()->setBorderStyle("solid");
		$menu->getStyle()->setBorderColor("black");
		$menu->getStyle()->setBackground("#EFEFEF");
		$menu->getStyle()->setFontFamily("verdana, sans-serif");
		$menu->getStyle()->setFontSize(10);
		$menu->getStyle()->setPosition("absolute");
		$menu->getStyle()->setTop(-1000);
		$menu->getStyle()->setLeft(-1000);

		$i1 = new Link($t1->getText() . "<br>", "javascript:;");
		$i1->addAction(new SetValueAction($ti, "rw"));
		$i1->addAction(new DisplayNoneAction($st));
		$i1->addAction(new DisplayInlineAction($t1));
		$i1->addAction(new DisplayNoneAction($t2));
		$i1->addAction(new DisplayNoneAction($t3));
		$i1->addAction(new DisplayNoneAction($ert));
		$i1->addAction(new DisplayInlineAction($tt));
		$i1->addAction(new MoveToAction($menu, -1000, -1000));
			$a = new IfThenAction(new StrLenEqualsAction($et, "==", 0), new DisplayNoneAction($e3));
			$a->addAction(new DisplayInlineAction($ert));
			$a->addAction(new DisplayNoneAction($tt));
			$a->addAction(new DisplayInlineAction($e2));
			$a->addAction(new SetValueAction($et,"r"));
		$i1->addAction($a);

		$i2 = new Link($t2->getText() . "<br>", "javascript:;");
		$i2->addAction(new SetValueAction($ti, "r"));
		$i2->addAction(new MoveToAction($menu, -1000, -1000));
		$i2->addAction(new DisplayNoneAction($st));
		$i2->addAction(new DisplayNoneAction($t1));
		$i2->addAction(new DisplayInlineAction($t2));
		$i2->addAction(new DisplayNoneAction($ert));
		$i2->addAction(new DisplayInlineAction($tt));
		$i2->addAction(new DisplayNoneAction($t3));
			$a = new IfThenAction(new StrLenEqualsAction($et, "==", 0), new DisplayNoneAction($e3));
			$a->addAction(new DisplayInlineAction($ert));
			$a->addAction(new DisplayNoneAction($tt));
			$a->addAction(new DisplayInlineAction($e2));
			$a->addAction(new SetValueAction($et,"r"));
		$i2->addAction($a);

		$i3 = new Link($t3->getText() . "<br>", "javascript:;");
		$i3->addAction(new SetValueAction($ti, ""));
		$i3->addAction(new MoveToAction($menu, -1000, -1000));
		$i3->addAction(new DisplayNoneAction($st));
		$i3->addAction(new DisplayNoneAction($t1));
		$i3->addAction(new DisplayNoneAction($t2));
		$i3->addAction(new DisplayInlineAction($t3));

		$p1 = new SimplePanel();
		$p1->add($i1);
		$p1->setStyle(new Style("shareMenuItem"));
		$p2 = new SimplePanel();
		$p2->add($i2);
		$p2->setStyle(new Style("shareMenuItem"));
		$p3 = new SimplePanel();
		$p3->add($i3);
		$p3->setStyle(new Style("shareMenuItem"));

		$menu->add($p1);
		$menu->add($p2);
		$menu->add($p3);

		return $menu;
	}
}
?>