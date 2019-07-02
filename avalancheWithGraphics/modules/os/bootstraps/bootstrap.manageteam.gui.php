<?

class module_bootstrap_manageteam_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Calendar List to HTML");
		$this->setInfo("this module takes as input an array of calendar objects. the output is a very basic
				html list of the calendars.");
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

			if(isset($data_list["subview"])){
				$subview = (string) $data_list["subview"];
			}else{
				$subview = "show_cal";
			}

			if(isset($data_list["subsubview"])){
				$subsubview = $data_list["subsubview"];
			}else{
				$subsubview = "simple";
			}

			/** end initializing the input */

			/**
			 * get the list of calendars
			 */
			$group_obj_list = $this->avalanche->getAllUsergroups($this->avalanche->loggedInHuh());
			$filtered_list = array();
			foreach($group_obj_list as $group){
				if($group->type() == avalanche_usergroup::$PUBLIC ||
				   $group->type() == avalanche_usergroup::$PERSONAL &&
				   $group->author() == $this->avalanche->getActiveUser()){
					$filtered_list[] = $group;
				}
			}
			$group_obj_list = $filtered_list;

			$sorter = new MDASorter();
			$group_obj_list = $sorter->sortDESC($group_obj_list, new OSUsergroupComparator());

			/**
			 * let's make the panel's !!!
			 */
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/manage_cals.css"));
			$this->doc->addStyleSheet($css);

			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/

			$nobg = new Style("nobg");
			$info_style = new Style("info");

			$title_style = new Style();
			$title_style->setFontFamily("verdana, sans-serif");
			$title_style->setFontSize(10);
			$title_style->setFontWeight("bold");

			/************************************************************************
			    initialize panels
			************************************************************************/

			$my_container = new BorderPanel();
			$cal_info_panel = new BorderPanel();
			$right_panel = new Panel();
			$content_panel = new ErrorPanel($right_panel);

			/************************************************************************
			************************************************************************/

			/************************************************************************
			    apply styles to created panels
			************************************************************************/

			$my_container->getStyle()->setWidth("100%");
			$my_container->getStyle()->setHeight("100%");

			/** done making calendar list for the left side **/

			$width = "650px";

			$content_panel->getStyle()->setWidth($width);
			$content_panel->setValign("top");

			$cal_info_panel->getStyle()->setWidth($width);
			//$cal_info_panel->getStyle()->setHeight($width);
			$cal_info_panel->getStyle()->setBorderWidth(1);
			$cal_info_panel->getStyle()->setBorderStyle("solid");
			$cal_info_panel->getStyle()->setBorderColor("black");

			$right_panel->getStyle()->setWidth($width);
			//$right_panel->getStyle()->setHeight($width);

			if((count($group_obj_list) > 0) && isset($data_list["team_id"])){
				if(!isset($data_list["team_id"])){
					$group_id = $group_obj_list[0]->getId();
				}else{
					$group_id = (int) $data_list["team_id"];
				}

				$main_group_obj = $this->getGroup($group_obj_list, $group_id);

				/**
				cal info should have whatever you want it to have. at the very least the owner (maybe his avatar, too)
				the calendar color, the calendar name, and those three buttons i've got included.
				**/

				// get the header
				$module = new module_bootstrap_team_header_gui($this->avalanche, $this->doc, $main_group_obj);
				$bootstrap = $this->avalanche->getModule("bootstrap");
				$runner = $bootstrap->newDefaultRunner();
				$runner->add($module);
				$data = $runner->run(false);
				$cal_info_header = $data->data();

				$buttons = new TabbedPanel();
				$buttons->getStyle()->setWidth($width);
				$buttons->setHolderStyle(new Style("preferences_buttons_panel"));

				// buttons
				$open_overview_button = new Button("overview");
				$open_overview_button->setStyle(new Style("preferences_tab_open"));
				$open_overview_button->getStyle()->setMarginLeft(10);
				//$open_overview_button->addAction(new LoadPageAction("index.php?view=manage_cals&cal_id=" . $main_group_obj->getId()));
				$panel = new ErrorPanel($open_overview_button);
				$panel->setStyle(new Style("preferences_buttons_panel"));
				$open_overview_button = $panel;

				$closed_overview_button = new Button("overview");
				$closed_overview_button->setStyle(new Style("preferences_tab_closed"));
				$closed_overview_button->getStyle()->setMarginLeft(10);
				//$closed_overview_button->addAction(new LoadPageAction("index.php?view=manage_cals&cal_id=" . $main_group_obj->getId()));
				$panel = new ErrorPanel($closed_overview_button);
				$panel->setStyle(new Style("preferences_buttons_panel"));
				$closed_overview_button = $panel;

				$open_share_button = new Button("members");
				$open_share_button->setStyle(new Style("preferences_tab_open"));
				$open_share_button->getStyle()->setMarginLeft(10);
				//$open_share_button->addAction(new LoadPageAction("index.php?view=manage_cals&subview=share&cal_id=" . $main_group_obj->getId()));
				$panel = new ErrorPanel($open_share_button);
				$panel->setStyle(new Style("preferences_buttons_panel"));
				$open_share_button = $panel;

				$closed_share_button = new Button("members");
				$closed_share_button->setStyle(new Style("preferences_tab_closed"));
				$closed_share_button->getStyle()->setMarginLeft(10);
				//$closed_share_button->addAction(new LoadPageAction("index.php?view=manage_cals&subview=share&cal_id=" . $main_group_obj->getId()));
				$panel = new ErrorPanel($closed_share_button);
				$panel->setStyle(new Style("preferences_buttons_panel"));
				$closed_share_button = $panel;

				// end buttons

				$style = new Style();
				$style->setPaddingTop(10);
				$header = new GridPanel(1);
				$header->getStyle()->setWidth($width);
				$header->add($cal_info_header);
				$header->add($buttons, $style);
				$cal_info_header = $header;


				// the body
				if($subview == "make_public"){
					if(!$main_group_obj->isPublic() && $main_group_obj->canWriteName()){
						$main_group_obj->isPublic(true);
						header("Location: index.php?view=manage_cals&subview=share&subsubview=advanced&cal_id=" . $main_group_obj->getId());
						exit();
					}else{
						$subview = "share";
					}
				}else if($subview == "make_private"){
					if($main_group_obj->isPublic() && $main_group_obj->canWriteName()){
						$main_group_obj->isPublic(false);
						header("Location: index.php?view=manage_cals&subview=share&subsubview=advanced&cal_id=" . $main_group_obj->getId());
						exit();
					}else{
						$subview = "share";
					}
				}


				// start overview
				$description = new GridPanel(1);
				$description->setWidth("100%");
				$description->setStyle(new Style("content_font"));

				$cal_info_body = new ScrollPanel(1);
				$cal_info_body->getStyle()->setPadding(4);
				$cal_info_body->add($description);
				$cal_info_body->setAlign("left");
				$cal_info_body->setStyle(clone $info_style);
				$cal_info_body->setValign("top");


				$desc = $main_group_obj->description();
				if(trim(strip_tags(strlen($desc))) == 0){
					$desc = "<i>no description has been entered</i>";
				}
				$description->add(new Text("Description: " . nl2br($desc)));

				$buttons->add($cal_info_body, $open_overview_button, $closed_overview_button);


				// end overview
				// start members tab
				// get the body
				$cal_info_body = new ScrollPanel(1);
				$cal_info_body->getStyle()->setPadding(4);


				if($subview == "unlink_user"){
					if(!isset($data_list["team_id"])){
						throw new IllegalArgumentException("team_id must be sent as form input when linking user to team");
					}
					$group_id = $data_list["team_id"];
					if(!isset($data_list["user_id"])){
						throw new IllegalArgumentException("user_id must be sent as form input when linking user to team");
					}
					$user_id = $data_list["user_id"];
					if($this->avalanche->userInGroupHuh($user_id, $group_id)){
						$this->avalanche->unlinkUser($user_id, $group_id);
						throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id&user_removed=" . $user_id);
					}
				}else if($subview == "link_user"){
					if(!isset($data_list["team_id"])){
						throw new IllegalArgumentException("team_id must be sent as form input when linking user to team");
					}
					$group_id = $data_list["team_id"];
					if(isset($data_list["user_id"])){
						$user_id = $data_list["user_id"];
						if(!$this->avalanche->userInGroupHuh($user_id, $group_id)){
							$this->avalanche->linkUser($user_id, $group_id);
							$user_added = $user_id;
							throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id&user_added=" . $user_id);
						}else{
							throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id&");
						}
					}
					if(!isset($data_list["query"])){
						throw new IllegalArgumentException("query must be sent when linking user to team");
					}
					$query = $data_list["query"];
					$users = $this->avalanche->getAllUsersMatching($query);
					if(count($users) == 0){
						throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id&");
					}else if(count($users) == 1){
						$user = $users[0];
						if(!$this->avalanche->userInGroupHuh($user->getId(), $group_id)){
							$this->avalanche->linkUser($user->getId(), $group_id);
							throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id&user_added=" . $user->getId());
						}else{
							throw new RedirectException("index.php?view=manage_teams&subview=members&team_id=$group_id");
						}
						exit;
					}else /* count > 1 */{
						$new_body = new FormPanel("index.php");
						$user_text = new GridPanel(1);
						$user_text->setCellStyle(new Style("content_font"));
						$to_select = true;
						foreach($users as $user){
							$text = $user->username();
							$radio = new RadioInput($text);
							$radio->setName("user_id");
							$radio->setValue((string)$user->getId());
							if($to_select){
								$radio->setChecked($to_select);
								$to_select = false;
							}
							$user_text->add($radio);
						}
						$temp_panel = new QuotePanel(20);
						$temp_panel->add($user_text);
						$temp_title = new Text("Choose a user to add");
						$temp_title->setStyle(new Style("content_title"));
						$new_body->setWidth("100%");
						$new_body->add($temp_title);
						$new_body->add($temp_panel);
						$new_body->add(new Text("<input type='submit' value='Add User' class='cal_submit' style='margin-top: 4px;'>"));

						$back_button = new ButtonInput("Cancel");
						$back_button->getStyle()->setBorderWidth(1);
						$back_button->getStyle()->setBorderColor("black");
						$back_button->getStyle()->setBorderStyle("solid");
						$back_button->getStyle()->setMarginLeft(10);
						$back_button->addClickAction(new LoadPageAction("index.php?view=manage_teams&subview=members&team_id=" . $group_id));

						$new_body->add($back_button);
						$new_body->setAsGet();
						$new_body->addHiddenField("view", "manage_teams");
						$new_body->addHiddenField("subview", "link_user");
						$new_body->addHiddenField("team_id", (string)$group_id);
						$cal_info_body->add($new_body);
					}
				}else if(($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "link_user") ||
				   $this->avalanche->loggedInHuh() == $main_group_obj->author()) &&
				   ($main_group_obj->getId() != $this->avalanche->getVar("ALLUSERS")) &&
				   ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
					$name_input = new SmallTextInput();
					$name_input->setStyle(new Style("cal_submit"));
					$name_input->setName("query");
					$name_input->setSize(10);
					$add_user_form = new GridPanel(3);
					$add_user_form->setCellStyle(new Style("content_font"));
					$add_user_form->getCellStyle()->setPaddingRight(4);
					$add_user_form->add(new Text("Search: "));
					$add_user_form->add($name_input);
					$add_user_form->add(new Text("<input type='submit' value='Go' class='cal_submit'>"));

					$actual_form = new FormPanel("index.php");
					$actual_form->setAsGet();
					$actual_form->addHiddenField("view", "manage_teams");
					$actual_form->addHiddenField("subview", "link_user");
					$actual_form->addHiddenField("team_id", (string)$main_group_obj->getId());
					$actual_form->add($add_user_form);

					$temp_panel = new QuotePanel(20);
					$temp_panel->setStyle(new Style("content_font"));
					$temp_panel->add($actual_form);
					$temp_title = new Text("Add User");
					$temp_title->setStyle(new Style("content_title"));

					$add_panel = new Panel();
					$add_panel->setWidth("100%");
					$add_panel->add($temp_title);
					$add_panel->add($temp_panel);
					$cal_info_body->add($add_panel);

				}
					// add list of users
					$users = $this->avalanche->getAllUsersFor($main_group_obj->getId());
					$user_text = new GridPanel(4);
					$user_text->setCellStyle(new Style("content_font"));
					$user_text->getCellStyle()->setPaddingRight(20);
					if(count($users) == 0){
						$user_text->add(new Text("<i>none</i>"));
					}else{
						$user_text->add(new Text("<b>Username</b>"));
						$user_text->add(new Text("<b>Name</b>"));
						$user_text->add(new Text(""));
						$user_text->add(new Text(""));

					}
					foreach($users as $user){
						$name = $this->avalanche->getName($user->getId());
						$username = $user->username();
						$name = $name["title"] . " " . $name["first"] . " " . $name["middle"] . " " . $name["last"];
						$added = "";
						if(isset($data_list["user_added"]) && $user->getId() == $data_list["user_added"]){
							$username = "<b>$username</b>";
							$name = "<b>$name</b>";
							$added = "<b>(just added)</b>";
						}
						$user_text->add(new Text($username));
						$user_text->add(new Text($name));
						if(($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "unlink_user") ||
						   $this->avalanche->getActiveUser() == $main_group_obj->author()) &&
						   ($main_group_obj->getId() != $this->avalanche->getVar("ALLUSERS")) &&
						   ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
							$user_text->add(new Link("remove", "index.php?view=manage_teams&subview=unlink_user&user_id=" . $user->getId() . "&team_id=" . $main_group_obj->getId()));
						}else{
							$user_text->add(new Text("&nbsp;"));
						}
						$user_text->add(new Text($added));
					}
					$temp_panel = new QuotePanel(20);
					$temp_panel->add($user_text);
					$temp_title = new Text("Members");
					$temp_title->setStyle(new Style("content_title"));

					$reg_panel = new Panel();
					$reg_panel->setWidth("100%");
					$reg_panel->add($temp_title);
					$reg_panel->add($temp_panel);

					$cal_info_body->add($reg_panel);

				$cal_info_body->setAlign("left");
				$cal_info_body->setStyle(clone $info_style);
				$cal_info_body->setValign("top");

				// don't show members tab if its the all users group or the guest group
				if(($main_group_obj->getId() != $this->avalanche->getVar("ALLUSERS")) &&
				   ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
					$buttons->add($cal_info_body, $open_share_button, $closed_share_button);
				}


				if($subview == "members" || $subview == "link_user"){
					$buttons->selectTab(2);
				}else /* overview */{
					$buttons->selectTab(1);
				}



				$cal_info_panel->setNorth($cal_info_header);
				/**
				button panels have nothin' but buttons. go fig.
				*/
				$title = new Text("<b>Delete Group?</b><br>");
				$text = new Text("Delete the group <i>" . $main_group_obj->name() . "</i>?<br>");
				$warning = new Text("(All related information will be lost. This cannot be reversed.)");
				$warning->getStyle()->setFontSize(8);
				$delete_confirm_window = new SimpleWindow($title);
				$delete_confirm_window->add($text);
				$delete_confirm_window->add($warning);
				$yes_action = new LoadPageAction("index.php?view=manage_teams&subview=delete_team&team_id=" . $main_group_obj->getId());
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

				$back_button = new ButtonInput("< Back");
				$back_button->getStyle()->setBorderWidth(1);
				$back_button->getStyle()->setBorderColor("black");
				$back_button->getStyle()->setBorderStyle("solid");
				$back_button->addClickAction(new LoadPageAction("index.php?view=manage_teams"));

				$delete_button = new ButtonInput("Delete");
				$delete_button->getStyle()->setBorderWidth(1);
				$delete_button->getStyle()->setBorderColor("black");
				$delete_button->getStyle()->setBorderStyle("solid");
				$delete_button->addClickAction(new MoveToCenterAction($delete_confirm_window));

				$edit_button = new ButtonInput("Edit");
				$edit_button->getStyle()->setBorderWidth(1);
				$edit_button->getStyle()->setBorderColor("black");
				$edit_button->getStyle()->setBorderStyle("solid");
				$edit_button->addClickAction(new LoadPageAction("index.php?view=manage_teams&subview=edit_team&team_id=" . $main_group_obj->getId()));

				$south_buttons = new GridPanel(6);
				$south_buttons->getCellStyle()->setPaddingRight(4);
				$south_buttons->getStyle()->setMarginLeft(12);


				$south_buttons->add($back_button);
				if(($this->avalanche->getActiveUser() == $main_group_obj->author()) ||
				   ($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "del_usergroup") ||
				   $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_usergroup")) &&
				   ($main_group_obj->getId() != $this->avalanche->getVar("ALLUSERS")) &&
				   ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
					   $south_buttons->add(new Text(" | "));
				}
				if(($this->avalanche->getActiveUser() == $main_group_obj->author()) || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "del_usergroup") && ($this->avalanche->getVar("ALLUSERS") != $main_group_obj->getId()) && ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
					$south_buttons->add($delete_button);
				}
				if(($this->avalanche->getActiveUser() == $main_group_obj->author()) || $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_usergroup") && ($main_group_obj->getId() != $this->avalanche->getVar("ALLUSERS")) && ($main_group_obj->getId() != $this->avalanche->getVar("GUESTGROUP"))){
					$south_buttons->add($edit_button);
				}
				$cal_info_panel->setSouth($south_buttons);
				$cal_info_panel->setSouthHeight("30px");

				/************************************************************************
				************************************************************************/
				$right_panel->add($cal_info_panel);
				$right_panel->add($delete_confirm_window);
				$this->doc->addFunction($buttons->getCloseFunction());

			}
			/************************************************************************
			put it all together
			************************************************************************/


			$my_container->setCenter($content_panel);
			$grid = new GridPanel(1);
			$grid->add($my_container);

			$manage_view = new Panel();
			$manage_view->setWidth("100%");
			$manage_view->setAlign("center");
			$manage_view->getStyle()->setPaddingTop(60);
			$manage_view->getStyle()->setPaddingBottom(30);
			$manage_view->add($grid);
			return new module_bootstrap_data($manage_view, "a gui component for the month view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

	private function getGroup($group_obj_list, $group_id){
		/**
		 * get the main calendar
		 */
		$main_group_obj = false;
		foreach($group_obj_list as $group){
			if($group->getId() == $group_id){
				$main_group_obj = $group;
				break;
			}
		}
		if(!is_object($main_group_obj) && is_object($group_obj_list[0])){
			// they specified an incorrect calendar id
			// just use the first calendar
			$main_group_obj = $group_obj_list[0];
		}
		return $main_group_obj;
	}
}
?>