<?

class module_bootstrap_accounts_create_gui extends module_bootstrap_module{

	private $time_inc;
	private $column_width;
	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("Aurora Day View");
		$this->setInfo("returns the day view of this calendar");
		$this->time_inc = 30;
		$this->column_width = "120px";

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


			$name_error = false;
			$title_error = false;
			$email_error = false;
			$domain_error = false;
			$random_error = false;
			try{
				if(isset($data_list["name"]) && isset($data_list["email"]) && isset($data_list["title"])){
					$accounts = $this->avalanche->getModule("accounts");
					$name = strtolower($data_list["name"]);
					if(!ereg('^[a-z0-9]+$', $name)){
						throw new IllegalArgumentException("\"Account Name\" must be lowercase and/or numeric.");
					}
					$email = $data_list["email"];
					if($this->MailVal($email) !== 0){
						throw new IllegalArgumentException("Poorly formatted email address");
					}

					$tos = isset($data_list["tos"]) && $data_list["tos"];
					if(!$tos){
						throw new IllegalArgumentException("You must accept the Terms of Service");
					}

					$text = new SmallTextInput();
					$text->setName("title");
					$text->loadFormValue($data_list);
					$title = $text->getValue();

					$text = new SmallTextInput();
					$text->setName("domain");
					$text->loadFormValue($data_list);
					$domain = $text->getValue();

					// check lengths
					if(strlen($name) == 0){
						$name_error = true;
					}
					if(strlen($email) == 0){
						$email_error = true;
					}
					if(strlen($title) == 0){
						$title_error = true;
					}
					if(strlen($domain) == 0){
						$domain_error = true;
					}

					$list = $accounts->getAccounts();
					foreach($list as $account){
						if($account->name() == $name){
							$name_error = true;
						}
					}


					$password = false;
					if($name_error || $email_error || $title_error || $domain_error){
						// noop, we'll handle it later
					}else{
						$password = $accounts->addAccount($name, $email, $title, $domain);
					}
					if(is_string($password)){
						// send an email...
						$subject="Your new Account at InversionDesigns.com";
$msg =  "Your website is ready!<br><br>\n\n" .
		"Simply visit http://" . $domain . "/$name/ to view your new calendar!<br><br>" .
		"Log in with the following:<br>\n" .
		"username: $name<br>\n" .
		"password: $password<br><br>\n\n" .
		"You will be greeted with a brief tutorial to set up a new calendar and invite some users! " .
		"After 24 hours, you can also visit http://" . $name . "." . $domain . "/ to get to your new calendar. " .
		"If you have any questions at all, don't hesitate to email me, Adam Wulf, at awulf@calendarfamily.net.<br><br>" .
		"Thanks, and have fun with your new online calendar!<br><br>" .
		" -<br>" .
		" Adam Wulf<br>" .
		" Inversiondesigns.com";


						$mailheaders="From:  Inversion Designs <calendar@" . $this->avalanche->DOMAIN() . ">\n";
						if($this->avalanche->mail($email, $subject, $msg, $mailheaders)){
							$bootstrap = $this->avalanche->getModule("bootstrap");
							$runner = $bootstrap->newDefaultRunner();
							$runner->add(new module_bootstrap_accounts_welcome_gui($this->avalanche, $this->doc, $name));
							$this->avalanche->mail("calendar@inversiondesigns.com", "New Account: $name", "A new account has been created at http://" . $name . "." . $domain . " for " . $email, "From: calendar@inversiondesigns.com");
							return $runner->run(false);
						}else{
							throw new Exception("email could not be sent");
						}
					}else{
						$name_error = true;
					}
				}
			}catch(IllegalArgumentException $e){
				$random_error = $e;
			}

			// styles
			$label_style = new Style("label_style");
			$label_input_box_style = new Style("label_input_box");
			$description_style = new Style("description_style");
			$error_style = new Style("error_style");
			$input_style = new Style("input_style");
			$form_style = new Style("form_style");
			$float_right_style = new Style("float_right");
			$check_style = new Style("check_style");
			// panels

			$signup = new GridPanel(1);
			$content = new GridPanel(1);

			$account_name = new SimplePanel();
			$account_name->setStyle($label_input_box_style);
			$t = new Text("Account Name");
			$t->setStyle($label_style);
			$account_name->add($t);
			$acctname = new SmallTextInput();
			if(isset($data_list["name"])){
				$acctname->setName("name");
				$acctname->loadFormValue($data_list);
			}
			$acctname->setStyle($input_style);
			$acctname->setSize(20);
			$acctname->setName("name");
			$acctname->addKeyPressAction(new AlphaNumericOnlyAction());
			$input = new SimplePanel();
			$input->add($acctname);
			$input->setStyle($float_right_style);
			$account_name->add($acctname);

			$names = new SimplePanel();
			$names->setStyle($description_style);
			$inversion_text = new Text("inversiondesigns.com");
			$calendar_text = new Text("calendarcampus.com");
			$family_text = new Text("calendarfamily.net");
			$calendar_text->getStyle()->setDisplayNone();
			$family_text->getStyle()->setDisplayNone();
			$names->add(new Text("Your new calendar will be located at:<br>http://&lt;account name&gt;."));
			$names->add($inversion_text);
			$names->add($calendar_text);
			$names->add($family_text);
			$name_description = $names;





			$account_title = new SimplePanel;
			$account_title->setStyle($label_input_box_style);
			$t = new Text("Your Calendar Title");
			$t->setStyle($label_style);
			$account_title->add($t);
			$accttitle = new SmallTextInput();
			if(isset($data_list["title"])){
				$accttitle->setName("title");
				$accttitle->loadFormValue($data_list);
			}
			$accttitle->setStyle($input_style);
			$accttitle->setSize(20);
			$accttitle->setName("title");
			$account_title->add($accttitle);

			$title_description = new SimplePanel();
			$title_description->setStyle($description_style);
			$title_description->add(new Text("The title will appear on each page of your calendar."));






			$account_email = new SimplePanel();
			$account_email->setStyle($label_input_box_style);
			$t = new Text("Your Email");
			$t->setStyle($label_style);
			$account_email->add($t);
			$acctemail = new SmallTextInput();
			if(isset($data_list["email"])){
				$acctemail->setName("email");
				$acctemail->loadFormValue($data_list);
			}
			$acctemail->setStyle($input_style);
			$acctemail->setSize(20);
			$acctemail->setName("email");
			$account_email->add($acctemail);

			$tos_description = new SimplePanel();
			$tos_description->setStyle($label_input_box_style);
			$tos = new CheckInput();
			$tos->setStyle($check_style);
			$tos->setValue("1");
			$tos->setChecked(true);
			if(isset($data_list["tos"]) && $data_list["tos"]){
				$tos->setChecked(true);
			}
			$tos->setName("tos");
			$tos_description->add($tos);
			$t = new Text("I agree to the <a href='tos.html' target='_new'>Terms of Service</a>.");
			$tos_description->add($t);

			$email_description = new SimplePanel();
			$email_description->setStyle($description_style);
			$email_description->add(new Text("Your password will be sent to you via email."));



			$account_domain = new SimplePanel();
			$account_domain->setStyle($label_input_box_style);
			$t = new Text("Your Domain");
			$t->setStyle($label_style);
			$account_domain->add($t);
			$acctdomain = new DropDownInput();
			$acctdomain->addOption(new DropDownOption("inversiondesigns.com", "inversiondesigns.com"));
			$acctdomain->addOption(new DropDownOption("calendarcampus.com", "calendarcampus.com"));
			$acctdomain->addOption(new DropDownOption("calendarfamily.net", "calendarfamily.net"));
			$a = new DropDownAction($acctdomain);
			$a->addAction("inversiondesigns.com", new DisplayInlineAction($inversion_text));
			$a->addAction("inversiondesigns.com", new DisplayNoneAction($calendar_text));
			$a->addAction("inversiondesigns.com", new DisplayNoneAction($family_text));
			$a->addAction("calendarcampus.com", new DisplayInlineAction($calendar_text));
			$a->addAction("calendarcampus.com", new DisplayNoneAction($inversion_text));
			$a->addAction("calendarcampus.com", new DisplayNoneAction($family_text));
			$a->addAction("calendarfamily.net", new DisplayInlineAction($family_text));
			$a->addAction("calendarfamily.net", new DisplayNoneAction($calendar_text));
			$a->addAction("calendarfamily.net", new DisplayNoneAction($inversion_text));
			$acctdomain->addChangeAction($a);
			if(isset($data_list["domain"])){
				$acctdomain->setValue($data_list["domain"]);
			}else{
				$acctdomain->setValue("inversiondesigns.com");
			}
			$acctdomain->setStyle($input_style);
			$acctdomain->setName("domain");
			$account_domain->add($acctdomain);

			$submit_button = new Text("<input type='image' src='images/startnow.gif' value='Create Account' class='goButton'>");

			$button = new Panel();
			$button->setAlign("center");
			$button->add($submit_button);

			// build it

			if(is_object($random_error)){
				$error = new Panel();
				$error->add(new Text($random_error->getMessage()));
				$error->setStyle($error_style);
				$content->add($error);
			}
			if($email_error){
				$error = new Panel();
				$error->add(new Text("Please enter an Email Address"));
				$error->setStyle($error_style);
				$content->add($error);
			}
			$content->add($account_email);
			$content->add($email_description);
			if($title_error){
				$error = new Panel();
				$error->add(new Text("Please enter a Title"));
				$error->setStyle($error_style);
				$content->add($error);
			}
			$content->add($account_title);
			$content->add($title_description);
			if($domain_error){
				$error = new Panel();
				$error->add(new Text("Please enter a Domain"));
				$error->setStyle($error_style);
				$content->add($error);
			}
			$content->add($account_domain);
			if($name_error){
				$error = new Panel();
				$error->add(new Text("Please choose a different Account Name"));
				$error->setStyle($error_style);
				$content->add($error);
			}
			$content->add($account_name);
			$content->add($name_description);
			$content->add($tos_description);
			$content->add($button);

			$signup->add($content);

			$form = new FormPanel("index.php");
			$form->addHiddenField("add", "1");
			if(isset($data_list["testserver"]) && $data_list["testserver"]){
				$form->addHiddenField("testserver", "1");
			}
			$form->add($signup);
			$form->setStyle($form_style);

			return new module_bootstrap_data($form, "a gui component for the day view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an array of calendars.<br>");
		}
	}

/************************************************************************
  * This function checks the format of an email address. There are five levels of
  * checking:
  *
  * 1 - Basic format checking. Ensures that:
  *     There is an @ sign with something on the left and something on the right
  *     To the right of the @ sign, there's at least one dot, with something to the left and right.
  *     To the right of the last dot is either 2 or 3 letters, or the special case "arpa"
  * 2 - The above, plus the letters to the right of the last dot are:
  *     com, net, org, edu, mil, gov, int, arpa or one of the two-letter country codes
  * 3 - The above, plus attempts to check if there is an MX (Mail eXchange) record for the
  *     domain name.
  * 4 - The above, plus attempt to connect to the mail server
  * 5 - The above, plus check to see if there is a response from the mail server. The third
  *     argument to this function is optional, and sets the number of times to loop while
  *     waiting for a response from the mail server. The default is 15000. The actual waiting
  *     time, of course, depends on such things as the speed of your server.
  *
  * Level 1 is bulletproof: if the address fails this level, it's bad. Level 2 is still
  * pretty solid, but less certain: there could be valid TLDs overlooked when writing
  * this function, or new ones could be added. Level 3 is even less certain: there are
  * a number of things that could prevent finding an MX record for a valid address
  * at any given time. 4 and 5 are even less certain still. Ultimately, the only absolutely
  * positive way to test an email address is to send something to it.
  *
  * The function returns 0 for a valid address, or the level at which it failed, for an
  * invalid address.
  *
  ************************************************************************/

  function MailVal($Addr) {
  	if(!$this->checkEmail($Addr)) return 1;
	//  Valid Top-Level Domains
	    $gTLDs = "com:net:org:edu:gov:mil:int:arpa:";
	    $CCs   = "ad:ae:af:ag:ai:al:am:an:ao:aq:ar:as:at:au:aw:az:ba:bb:bd:be:bf:".
		     "bg:bh:bi:bj:bm:bn:bo:br:bs:bt:bv:bw:by:bz:ca:cc:cf:cd:cg:ch:ci:".
		     "ck:cl:cm:cn:co:cr:cs:cu:cv:cx:cy:cz:de:dj:dk:dm:do:dz:ec:ee:eg:".
		     "eh:er:es:et:fi:fj:fk:fm:fo:fr:fx:ga:gb:gd:ge:gf:gh:gi:gl:gm:gn:".
		     "gp:gq:gr:gs:gt:gu:gw:gy:hk:hm:hn:hr:ht:hu:id:ie:il:in:io:iq:ir:".
		     "is:it:jm:jo:jp:ke:kg:kh:ki:km:kn:kp:kr:kw:ky:kz:la:lb:lc:li:lk:".
		     "lr:ls:lt:lu:lv:ly:ma:mc:md:mg:mh:mk:ml:mm:mn:mo:mp:mq:mr:ms:mt:".
		     "mu:mv:mw:mx:my:mz:na:nc:ne:nf:ng:ni:nl:no:np:nr:nt:nu:nz:om:pa:".
		     "pe:pf:pg:ph:pk:pl:pm:pn:pr:pt:pw:py:qa:re:ro:ru:rw:sa:sb:sc:sd:".
		     "se:sg:sh:si:sj:sk:sl:sm:sn:so:sr:st:su:sv:sy:sz:tc:td:tf:tg:th:".
		     "tj:tk:tm:tn:to:tp:tr:tt:tv:tw:tz:ua:ug:uk:um:us:uy:uz:va:vc:ve:".
		     "vg:vi:vn:vu:wf:ws:ye:yt:yu:za:zm:zr:zw:";

	//  The countries can have their own 'TLDs', e.g. mydomain.com.au
	    $cTLDs = "com:net:org:edu:gov:mil:co:ne:or:ed:go:mi:";

	    $fail = 0;

	//  Shift the address to lowercase to simplify checking
	    $Addr = strtolower($Addr);

	//  Split the Address into user and domain parts
	    $UD = explode("@", $Addr);
	    if (count($UD) != 2 || !isset($UD[0])) return 1;

	//  Split the domain part into its Levels
	    $Levels = explode(".", $UD[1]); $sLevels = sizeof($Levels);
	    if ($sLevels < 2) return 1;

	//  Get the TLD, strip off trailing ] } ) > and check the length
	    $tld = $Levels[$sLevels-1];
	    $tld = ereg_replace("[>)}]$|]$", "", $tld);
	    if (strlen($tld) < 2 || strlen($tld) > 3 && $tld != "arpa") return 1;

	//  If the string after the last dot isn't in the generic TLDs or country codes, it's invalid.
	    if (!$fail) {
	    if (!ereg($tld.":", $gTLDs) && !ereg($tld.":", $CCs)) return 2;
	    }

	//  If it's a country code, check for a country TLD; add on the domain name.
	    if (!$fail) {
	    $cd = $sLevels - 2; $domain = $Levels[$cd].".".$tld;
	    if (ereg($Levels[$cd].":", $cTLDs)) { $cd--; $domain = $Levels[$cd].".".$domain; }
	    }

	    return $fail;
	  } //MailVal

	private function checkEmail($email){
		$atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';    // allowed characters for part before "at" character
		$domain = '([a-z]([-a-z0-9]*[a-z0-9]+)?)'; // allowed characters for part after "at" character

		$regex = '^' . $atom . '+' .        // One or more atom characters.
		'(\.' . $atom . '+)*'.              // Followed by zero or more dot separated sets of one or more atom characters.
		'@'.                                // Followed by an "at" character.
		'(' . $domain . '{1,63}\.)+'.        // Followed by one or max 63 domain characters (dot separated).
		$domain . '{2,63}'.                  // Must be followed by one set consisting a period of two
		'$';                                // or max 63 domain characters.
	   if(eregi($regex, $email))
	   {
		  return TRUE;
	   }

	  return FALSE;
	}

}
?>