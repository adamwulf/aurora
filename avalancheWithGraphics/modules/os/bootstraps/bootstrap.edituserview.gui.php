<?

class module_bootstrap_os_edituserview_gui extends module_bootstrap_module{

	/** the avalanche object */
	private $avalanche;
	/** the document we're in */
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		
		$this->setName("Edit user view for Avalanche");
		$this->setInfo("edits an user to avalanche.");
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

			/************************************************************************
			get modules
			************************************************************************/
			$strongcal = $this->avalanche->getModule("strongcal");
			$buffer = $this->avalanche->getSkin("buffer");

			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/gui/os/add_cal_style.css"));
			$this->doc->addStyleSheet($css);
			$css = new CSS(new File($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/css/user_profile.css"));
			$this->doc->addStyleSheet($css);

			if(isset($data_list["search"])){
				$search_default = (string) $data_list["search"];
			}else{
				$search_default = "";
			}
			if(!isset($data_list["user_id"])){
				throw new IllegalArgumentException("user_id must be passed in by form input to edit user page");
			}
			$user_id = (int) $data_list["user_id"];
			
			$main_user = $this->avalanche->getUser($user_id);

			$permission = $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_user") || $user_id == $this->avalanche->loggedInHuh();

			$error = false;
			// if we have an error updating the file, this will be the exception			
			$file_error = isset($data_list["file_error"]) && $data_list["file_error"];
			// check form input to see if we need to edit a user....
			try{
				if(isset($data_list["submit"])){
					if(!isset($data_list["submit"]) ||
					   !isset($data_list["submit"]) ||
					   !isset($data_list["title"])  ||
					   !isset($data_list["first"])  ||
					   !isset($data_list["middle"]) ||
					   !isset($data_list["last"])){
						throw new IllegalArgumentException("arguments \$name and \$color must be sent in via GET or POST to edit a user");
					}else{
						if($permission){
							$name = array();
							$reader = new SmallTextInput();
							$reader->setName("title");
							$reader->loadFormValue($data_list);
							$name["title"] = $reader->getValue();
							$reader->setName("first");
							$reader->loadFormValue($data_list);
							$name["first"] = $reader->getValue();
							$reader->setName("middle");
							$reader->loadFormValue($data_list);
							$name["middle"] = $reader->getValue();
							$reader->setName("last");
							$reader->loadFormValue($data_list);
							$name["last"] = $reader->getValue();
							$reader->setName("email");
							$reader->loadFormValue($data_list);
							$mail = $reader->getValue();
							$reader->setName("sms");
							$reader->loadFormValue($data_list);
							$sms = $reader->getValue();
							$reader->setName("bio");
							$reader->loadFormValue($data_list);
							$bio = $reader->getValue();
							$reader->setName("username");
							if($reader->loadFormValue($data_list)){
								$username = $reader->getValue();
							}else{
								$username = $this->avalanche->getUser($user_id)->username();
							}
							// update the name
							$this->avalanche->updateName($user_id, $name);
							// update email
							$this->avalanche->updateEmail($user_id, $mail);
							// update the sms
							$this->avalanche->getUser($user_id)->sms($sms);
							// update the bio
							$this->avalanche->getUser($user_id)->bio($bio);
	
							if(strlen($username) > 0){
								$this->avalanche->updateUsername($user_id, $username);
							}else{
								throw new CannotEditUserException("Username must not be blank when trying to edit user");
							}
							
							$change_pass_huh = isset($data_list["change_pass"]) && $data_list["change_pass"];
							if($change_pass_huh){
								$pass = $data_list["password"];
								$conf = $data_list["confirm"];
								if(!$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_password") &&
								   $user_id != $this->avalanche->loggedInHuh()){
									throw new CannotEditUserException("You do not have permission to change another user's password");
								}
								if($pass != $conf){
									throw new CannotEditUserException("Password and Confirmation must be the same");
								}
								$this->avalanche->updatePassword($user_id, $pass);
							}
							
	
							$reader->setName("change_avatar");
							$reader->loadFormValue($data_list);
							$change_avatar = $reader->getValue();
							try{
								if($change_avatar){
									if(isset($data_list["avatar"]["tmp_name"]) && strlen($data_list["avatar"]["tmp_name"])){
										// load specs about image
										list($width, $height, $type, $attr) = getimagesize($data_list["avatar"]["tmp_name"]);
										// load image from form
										if($type == IMAGETYPE_GIF){
											$image = imagecreatefromgif($data_list["avatar"]["tmp_name"]);
										}else
										if($type == IMAGETYPE_JPEG){
											$image = imagecreatefromjpeg($data_list["avatar"]["tmp_name"]);
										}else
										if($type == IMAGETYPE_PNG){
											$image = imagecreatefrompng($data_list["avatar"]["tmp_name"]);
										}else
										if($type == IMAGETYPE_WBMP){
											$image = imagecreatefromwbmp($data_list["avatar"]["tmp_name"]);
										}else
										if($type == IMAGETYPE_XBM){
											$image = imagecreatefromxbm($data_list["avatar"]["tmp_name"]);
										}else{
											throw new CannotEditUserException("Image type \"" . image_type_to_mime_type($type) . "\" is not supported");
										}
										
										$max_size = $this->avalanche->getAvatarSize();
										
										// Get new dimensions
										$dim = $width > $height ? $width : $height;
										if($dim > $max_size){
											$old = $image;
											$percent = $max_size / $dim;
											$new_width = $width * $percent;
											$new_height = $height * $percent;
											
											// Resample
											$image = imagecreatetruecolor($new_width, $new_height);
											imagecopyresampled($image, $old, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
											imagedestroy($old);
										}
			
										// create temp file
										$tmpfname = tempnam("/tmp", "FOO");
										// resize image if need be
										
										// save image to tmp file
										imagejpeg ( $image, $tmpfname );
										imagedestroy($image);
										// load image data from new adjusted file
										$image = file_get_contents($tmpfname);
										// save to database
										$this->avalanche->getUser($user_id)->avatar($image);
										// delete my temp file
										unlink($tmpfname);
									}else{
										// delete the avatar
										$this->avalanche->getUser($user_id)->avatar("");
									}
								}
							}catch(Exception $e){
								$file_error = $e;
							}
						}else{
							throw new CannotEditUserException("you do not have permission to edit users");
						}
						
						throw new RedirectException("index.php?view=manage_users&user_id=$user_id&saved_ok=1&file_error=" . is_object($file_error) . "&search=" . $search_default);
					}
				}
			}catch(CannotEditUserException $e){
				$error = $e;
			}
			
			
			

			/************************************************************************
			    initialize panels
			************************************************************************/
			$my_form = new FormPanel("index.php");
			$my_form->addHiddenField("view", "manage_users");
			$my_form->addHiddenField("subview", "edit_user");
			$my_form->addHiddenField("user_id", "$user_id");
			$my_form->addHiddenField("submit", "1");
			$my_form->addHiddenField("search", "$search_default");
			$my_form->setAsPost();
			$my_form->setEncType("multipart/form-data");

			$info_panel = new Panel();
			
			/************************************************************************
			    apply styles to created panels
			************************************************************************/
			$icon_style = new Style("normal_square_icon");
			$profile_header_style = new Style("profile_header_style");
			$profile_icons_style = new Style("profile_icons_style");
			$profile_icons_holder_style = new Style("profile_icons_holder");
			$profile_info_style = new Style("profile_info_style");
			$profile_info_holder_style = new Style("profile_info_holder");
			$info_panel_style = new Style("info_panel");
			$facts_style = new Style("facts_style");
			$bottom_style = new Style("bottom_style");
			$button_style = new Style("button_style");
			
			$label_style = new Style("label_style");
			$value_style = new Style("value_style");

			/************************************************************************
			    add necessary text and html
			************************************************************************/
			
			// show a real user profile
			$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->getAvatar($user_id));
			$icon->setStyle(clone $icon_style);
			$icon->getStyle()->setMarginRight(20);
			$header = new BorderPanel();
			$header->setStyle($profile_header_style);
			$header->setWest($icon);
			$header->setCenter(new Text($os->getUsername($user_id)));
			
			$icons = new Panel();
			$icons->setAlign("center");
			$icons->setWidth("100%");
			$icons->setStyle($profile_icons_style);
			$icons->getStyle()->setPadding(4);
			
			$icons_holder = new Panel();
			$icons_holder->setStyle($profile_icons_holder_style);
			$icons_holder->add($icons);
			
			$info = new Panel();
			$info->setWidth("100%");
			$info->setValign("top");
			$info->setStyle($profile_info_style);
			$info->getStyle()->setPadding(4);
			
			// add inputs
			$padding = 20;
			
			// avatar
			
			$avatar_panel = new Panel();
			$avatar_panel->setWidth("100%");
			$avatar_panel->getStyle()->setClassname("profile_text");
			$avatar_panel->getStyle()->setPaddingBottom($padding);
			
			$change_avatar = new HiddenInput();
			$change_avatar->setName("change_avatar");
			$change_avatar->setValue("0");
			
			$avatar_label = new Text("Avatar:&nbsp;&nbsp;");
			$avatar_label->setStyle(clone $label_style);
			$avatar_input = new FileInput();
			$avatar_input->getStyle()->setDisplayNone();
			$avatar_input->addKeyPressAction(new DisableKeysAction());
			$avatar_input->setSize(40);
			$avatar_input->setName("avatar");
			$avatar_input->getStyle()->setClassname("text_input");
			$avatar_input->getStyle()->setBorderWidth(1);
			$avatar_input->getStyle()->setBorderStyle("solid");
			$avatar_input->getStyle()->setBorderColor("black");
			
			$change_link = new Link("(change)", "javascript:;");
			$dont_change_link = new Link("(don't change)", "javascript:;");
			$dont_change_link->getStyle()->setDisplayNone();
			$change_link->addAction(new DisplayBlockAction($avatar_input));
			$change_link->addAction(new DisplayNoneAction($change_link));
			$change_link->addAction(new DisplayInlineAction($dont_change_link));
			$change_link->addAction(new SetValueAction($change_avatar, "1"));
			$dont_change_link->addAction(new DisplayNoneAction($avatar_input));
			$dont_change_link->addAction(new DisplayInlineAction($change_link));
			$dont_change_link->addAction(new DisplayNoneAction($dont_change_link));
			$dont_change_link->addAction(new SetValueAction($change_avatar, "0"));
			$avatar_panel->add($change_avatar);
			$avatar_panel->add($avatar_label);
			$avatar_panel->add($change_link);
			$avatar_panel->add($dont_change_link);
			$avatar_panel->add($avatar_input);
			$info->add($avatar_panel);

			// name
			$name_panel = new Panel();
			$name_panel->setWidth("100%");
			$name_panel->getStyle()->setPaddingBottom($padding);
			
			$name_inputs = new GridPanel(4);
			$name_inputs->add(new Text("Title"), $label_style);
			$name_inputs->add(new Text("First"), $label_style);
			$name_inputs->add(new Text("Middle"), $label_style);
			$name_inputs->add(new Text("Last"), $label_style);
			$title_input = new DropDownInput();
			$title_input->setName("title");
			$title_input->addOption(new DropDownOption("None", ""));
			$title_input->addOption(new DropDownOption("Mr.", "Mr."));
			$title_input->addOption(new DropDownOption("Ms.", "Ms."));
			$title_input->addOption(new DropDownOption("Mrs.", "Mrs."));
			$title_input->addOption(new DropDownOption("Dr.", "Dr."));
			$title_input->setValue($main_user->title());
			$title_input->getStyle()->setClassname("text_input");
			$first_input = new SmallTextInput();
			$first_input->setSize(10);
			$first_input->setName("first");
			$first_input->setValue($main_user->first());
			$first_input->getStyle()->setClassname("text_input");
			$middle_input = new SmallTextInput();
			$middle_input->setSize(10);
			$middle_input->setName("middle");
			$middle_input->setValue($main_user->middle());
			$middle_input->getStyle()->setClassname("text_input");
			$last_input = new SmallTextInput();
			$last_input->setSize(10);
			$last_input->setName("last");
			$last_input->setValue($main_user->last());
			$last_input->getStyle()->setClassname("text_input");
			$name_inputs->add($title_input);
			$name_inputs->add($first_input);
			$name_inputs->add($middle_input);
			$name_inputs->add($last_input);
			$name_panel->add($name_inputs);
			$info->add($name_panel);

			// email
			$email_panel = new Panel();
			$email_panel->setWidth("100%");
			$email_panel->getStyle()->setClassname("profile_text");
			$email_panel->getStyle()->setPaddingBottom($padding);
			
			$email_label = new Text("Email:");
			$email_label->setStyle($label_style);
			$email_panel->add($email_label);
			$email_input = new SmallTextInput();
			$email_input->setSize(40);
			$email_input->setName("email");
			$email_input->setValue($main_user->email());
			$email_input->getStyle()->setClassname("text_input");
			$email_panel->add($email_input);
			$info->add($email_panel);

			// SMS
			$sms_panel = new Panel();
			$sms_panel->setWidth("100%");
			$sms_panel->getStyle()->setClassname("profile_text");
			$sms_panel->getStyle()->setPaddingBottom($padding);
			
			$sms_label = new Text("SMS:");
			$small_font_style = new Style("example_text");
			$sms_label->setStyle($label_style);
			$sms_panel->add($sms_label);
			$sms_input = new SmallTextInput();
			$sms_input->setSize(40);
			$sms_input->setName("sms");
			$sms_input->setValue($main_user->sms());
			$sms_input->getStyle()->setClassname("text_input");
			$text = new Text("Example: 2815550983@messaging.sprintpcs.com");
			$text->setStyle($small_font_style);
			$sms_panel->add($sms_input);
			$sms_panel->add(new Text("<br>"));
			$sms_panel->add($text);
			$info->add($sms_panel);
			

			// bio
			$bio_panel = new GridPanel(1);
			$bio_panel->setWidth("100%");
			$bio_panel->getStyle()->setClassname("profile_text");
			$bio_panel->getStyle()->setPaddingBottom($padding);
			$bio_panel->setValign("top");
			
			$bio_label = new Text("About me:");
			$small_font_style = new Style("example_text");
			$bio_label->setStyle($label_style);
			$bio_panel->add($bio_label);
			$bio_input = new TextAreaInput();
			$bio_input->setCols(40);
			$bio_input->setRows(4);
			$bio_input->setName("bio");
			$bio_input->setValue($main_user->bio());
			$bio_input->getStyle()->setClassname("text_input");
			$bio_panel->add($bio_input);
			$info->add($bio_panel);
			
			
			// done adding inputs
			$info_holder = new Panel();
			$info_holder->setStyle($profile_info_holder_style);
			$info_holder->add($info);
			

			$facts = new Panel();
			$facts->setValign("top");
			$facts->setWidth("100%");
			$facts->getStyle()->setPadding(4);
			$facts->setStyle($facts_style);
			
			$bottom = new GridPanel(2);
			$bottom->setValign("bottom");
			$bottom->setAlign("center");
			$bottom->setStyle($bottom_style);
			$bottom->getStyle()->setHeight("30px");
			
			$cancel_button = new ButtonInput("Cancel");
			$cancel_button->addClickAction(new LoadPageAction("index.php?view=manage_users&user_id=" . $main_user->getId() . "&search=$search_default"));
			$bottom->add($cancel_button);
			$bottom->add(new SubmitInput("Save"));
			
			$info_panel->add($header);
			$info_panel->add($icons_holder);
			$info_panel->add($info_holder);
			$info_panel->add($bottom);
						
			/************************************************************************
			put it all together
			************************************************************************/
			
			if($file_error){
				$text = new Text("Avatar Not Updated. Supported types are:<br> jpeg, png, gif, xbmp, and xmb");
				$icons->add($text);
			}
			if($permission && isset($data_list["saved_ok"]) && $data_list["saved_ok"]){
				$text = new Text("Profile Saved Successfully");
				$icons->add($text);
			}else if(is_object($error)){
				$icons->add(new Text($error->getMessage()));
			}else{
				$icons->add(new Text("Edit this user"));
			}
			
			$my_form->add($info_panel);
			
			return new module_bootstrap_data($my_form, "a gui component for the add calendar view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}
}
?>