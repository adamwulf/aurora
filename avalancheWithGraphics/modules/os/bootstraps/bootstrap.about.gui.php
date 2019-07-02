<?

class module_bootstrap_os_about_gui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Avalanche About page to HTML");
		$this->setInfo("outputs a html page describing Avalanche");
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
			$taskman   = $this->avalanche->getModule("taskman");
			
			if(isset($data_list["subview"]) && (
				 $data_list["subview"] == "about" ||
				 $data_list["subview"] == "tos" ||
				 $data_list["subview"] == "privacy" ||
				 $data_list["subview"] == "news")){
				$subview = $data_list["subview"];
			}else{
				$subview = "about";
			}
			
			/**
			 * let's make the panel's !!!
			 */
			/************************************************************************
			create style objects to apply to the panels
			************************************************************************/
			
			$this->doc->addHidden(new Text("<style>
<!--
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{margin:0in;
	margin-bottom:.0001pt;
	}
 /* List Definitions */
 ol
	{padding-left:.33in;margin-left:0in;margin-top:0in;margin-bottom:0in;}
ul
	{padding-left:.33in;margin-left:0in;margin-top:0in;margin-bottom:0in;}
-->
</style>"));
			
			/************************************************************************
			    initialize panels
			************************************************************************/
			
			$my_container = new GridPanel(1);
			
			$about_content = new Panel();
			$tos_content = new Panel();
			$privacy_content = new Panel();
			$news_content = new Panel();
			
			/************************************************************************
			************************************************************************/
			
			$header = new Panel();
			$header->setStyle(new Style("about_header"));
			$header->setValign("middle");
			$header->add(new Text("<b>About Aurora Calendar</b>"));
			
			$buttons = new GridPanel(4);
			$buttons->setStyle(new Style("border_left"));
			
			$open_about_button = new Button();
			$open_about_button->setStyle(new Style("tab_open"));
			$open_about_button->getStyle()->setWidth("100px");
			$open_about_button->getStyle()->setHeight("25px");
			$open_about_button->setAlign("left");
			$open_about_button->setValign("top");
			$open_about_button->add(new Text("About"));
			
			$closed_about_button = new Button();
			$closed_about_button->setStyle(new Style("tab_closed"));
			$closed_about_button->getStyle()->setWidth("100px");
			$closed_about_button->getStyle()->setHeight("25px");
			$closed_about_button->setAlign("center");
			$closed_about_button->add(new Text("About"));
			
			$open_tos_button = new Button();
			$open_tos_button->setStyle(new Style("tab_open"));
			$open_tos_button->getStyle()->setWidth("100px");
			$open_tos_button->getStyle()->setHeight("25px");
			$open_tos_button->setAlign("center");
			$open_tos_button->add(new Text("TOS"));
			
			$closed_tos_button = new Button();
			$closed_tos_button->setStyle(new Style("tab_closed"));
			$closed_tos_button->getStyle()->setWidth("100px");
			$closed_tos_button->getStyle()->setHeight("25px");
			$closed_tos_button->setAlign("center");
			$closed_tos_button->add(new Text("TOS"));
			
			$open_privacy_button = new Button();
			$open_privacy_button->setStyle(new Style("tab_open"));
			$open_privacy_button->getStyle()->setWidth("100px");
			$open_privacy_button->getStyle()->setHeight("25px");
			$open_privacy_button->setAlign("center");
			$open_privacy_button->add(new Text("Privacy"));
			
			$closed_privacy_button = new Button();
			$closed_privacy_button->setStyle(new Style("tab_closed"));
			$closed_privacy_button->getStyle()->setWidth("100px");
			$closed_privacy_button->getStyle()->setHeight("25px");
			$closed_privacy_button->setAlign("center");
			$closed_privacy_button->add(new Text("Privacy"));
			
			$open_news_button = new Button();
			$open_news_button->setStyle(new Style("tab_open"));
			$open_news_button->getStyle()->setWidth("100px");
			$open_news_button->getStyle()->setHeight("25px");
			$open_news_button->setAlign("center");
			$open_news_button->add(new Text("News"));
			
			$closed_news_button = new Button();
			$closed_news_button->setStyle(new Style("tab_closed"));
			$closed_news_button->getStyle()->setWidth("100px");
			$closed_news_button->getStyle()->setHeight("25px");
			$closed_news_button->setAlign("center");
			$closed_news_button->add(new Text("News"));
			
			$open_about_button->getStyle()->setDisplayNone();
			$closed_about_button->getStyle()->setDisplayBlock();
			$open_tos_button->getStyle()->setDisplayNone();
			$closed_tos_button->getStyle()->setDisplayBlock();
			$open_privacy_button->getStyle()->setDisplayNone();
			$closed_privacy_button->getStyle()->setDisplayBlock();
			$open_news_button->getStyle()->setDisplayNone();
			$closed_news_button->getStyle()->setDisplayBlock();
			
			$about_content->getStyle()->setDisplayNone();
			$tos_content->getStyle()->setDisplayNone();
			$privacy_content->getStyle()->setDisplayNone();
			$news_content->getStyle()->setDisplayNone();

			// set button visibility
			if($subview == "about"){
				$open_about_button->getStyle()->setDisplayBlock();
				$closed_about_button->getStyle()->setDisplayNone();
				$about_content->getStyle()->setDisplayBlock();
			}else if($subview == "tos"){
				$open_tos_button->getStyle()->setDisplayBlock();
				$closed_tos_button->getStyle()->setDisplayNone();
				$tos_content->getStyle()->setDisplayBlock();
			}else if($subview == "privacy"){
				$open_privacy_button->getStyle()->setDisplayBlock();
				$closed_privacy_button->getStyle()->setDisplayNone();
				$privacy_content->getStyle()->setDisplayBlock();
			}else if($subview == "news"){
				$open_news_button->getStyle()->setDisplayBlock();
				$closed_news_button->getStyle()->setDisplayNone();
				$news_content->getStyle()->setDisplayBlock();
			}
			
			$closed_button_decoy = new Button();
			$closed_button_decoy->setStyle(new Style("tab_closed"));
			$closed_button_decoy->getStyle()->setWidth("100px");
			$closed_button_decoy->getStyle()->setHeight("25px");
			$closed_button_decoy->setAlign("center");
			$closed_button_decoy->add(new Text(""));
			
			
			// add buttons
			$about = new Panel();
			$about->add($open_about_button);
			$about->add($closed_about_button);
			$news = new Panel();
			$news->add($open_news_button);
			$news->add($closed_news_button);
			$buttons->add($about);
			$buttons->add($news);
			//$buttons->add($closed_button_decoy);
			//$buttons->add($closed_button_decoy);
			$tos = new Panel();
			$tos->add($open_tos_button);
			$tos->add($closed_tos_button);
			$buttons->add($tos);
			$privacy = new Panel();
			$privacy->add($open_privacy_button);
			$privacy->add($closed_privacy_button);
			$buttons->add($privacy);
			
			// create visibility functions and set up actions
			$closefunction = new NewFunctionAction("close_about_tabs");
			$closefunction->addAction(new DisplayNoneAction($open_about_button));
			$closefunction->addAction(new DisplayNoneAction($about_content));
			$closefunction->addAction(new DisplayBlockAction($closed_about_button));
			$closefunction->addAction(new DisplayNoneAction($open_tos_button));
			$closefunction->addAction(new DisplayNoneAction($tos_content));
			$closefunction->addAction(new DisplayBlockAction($closed_tos_button));
			$closefunction->addAction(new DisplayNoneAction($open_privacy_button));
			$closefunction->addAction(new DisplayNoneAction($privacy_content));
			$closefunction->addAction(new DisplayBlockAction($closed_privacy_button));
			$closefunction->addAction(new DisplayNoneAction($open_news_button));
			$closefunction->addAction(new DisplayNoneAction($news_content));
			$closefunction->addAction(new DisplayBlockAction($closed_news_button));
			$this->doc->addFunction($closefunction);
			
			$closed_about_button->addAction(new CallFunctionAction("close_about_tabs"));
			$closed_about_button->addAction(new DisplayNoneAction($closed_about_button));
			$closed_about_button->addAction(new DisplayBlockAction($open_about_button));
			$closed_about_button->addAction(new DisplayBlockAction($about_content));
			$closed_tos_button->addAction(new CallFunctionAction("close_about_tabs"));
			$closed_tos_button->addAction(new DisplayNoneAction($closed_tos_button));
			$closed_tos_button->addAction(new DisplayBlockAction($open_tos_button));
			$closed_tos_button->addAction(new DisplayBlockAction($tos_content));
			$closed_privacy_button->addAction(new CallFunctionAction("close_about_tabs"));
			$closed_privacy_button->addAction(new DisplayNoneAction($closed_privacy_button));
			$closed_privacy_button->addAction(new DisplayBlockAction($open_privacy_button));
			$closed_privacy_button->addAction(new DisplayBlockAction($privacy_content));
			$closed_news_button->addAction(new CallFunctionAction("close_about_tabs"));
			$closed_news_button->addAction(new DisplayNoneAction($closed_news_button));
			$closed_news_button->addAction(new DisplayBlockAction($open_news_button));
			$closed_news_button->addAction(new DisplayBlockAction($news_content));
			
			
			// content pages
			$content_header_style = new Style();
			$content_header_style->setPadding(3);
			
			$content = new ScrollPanel();
			$content->setStyle(new Style("about_content"));
			$content->getStyle()->setWidth("357px");
			$content->getStyle()->setHeight("200px");
			
			$news = new GridPanel(1);
			$news->getCellStyle()->setFontFamily("verdana, sans-serif");
			$news->getCellStyle()->setFontSize(12);
			$news->getCellStyle()->setFontColor("black");
			$news->add(new Text("News"), $content_header_style);
			$news->add($this->getNews($data_list));
			
			$privacy = new GridPanel(1);
			$privacy->getCellStyle()->setFontFamily("verdana, sans-serif");
			$privacy->getCellStyle()->setFontSize(12);
			$privacy->getCellStyle()->setFontColor("black");
			$privacy->add(new Text("Privacy"), $content_header_style);
			$privacy->add($this->getPrivacy($data_list));
			
			$tos = new GridPanel(1);
			$tos->getCellStyle()->setFontFamily("verdana, sans-serif");
			$tos->getCellStyle()->setFontSize(12);
			$tos->getCellStyle()->setFontColor("black");
			$tos->add(new Text("Terms of Service"), $content_header_style);
			$tos->add($this->getTOS($data_list));
			
			$about = new Panel();
			$about->getStyle()->setFontFamily("verdana, sans-serif");
			$about->getStyle()->setFontSize(10);
			$about->getStyle()->setFontColor("black");
			$about->add($this->getAbout($data_list));
			
			$about_content->add($about);
			$tos_content->add($tos);
			$privacy_content->add($privacy);
			$news_content->add($news);
			
			$content->add($about_content);
			$content->add($tos_content);
			$content->add($privacy_content);
			$content->add($news_content);
			
			$info = new ErrorPanel($content);
			$info->setStyle(new Style("about_footer"));
			$info->getStyle()->setPaddingLeft(20);
			$info->getStyle()->setPaddingRight(20);
			
			/************************************************************************
			put it all together
			************************************************************************/
			$my_container->add($header);
			$my_container->add($buttons);
			$my_container->add($info);
			
			
			$about_view =  new ErrorPanel($my_container);
			$about_view->getStyle()->setHeight("500px");
			return new module_bootstrap_data($about_view, "a gui component for the manage teams view");
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be form input.<br>");
		}
	}
	
	private function getNews($data_list){
		$bootstrap = $this->avalanche->getModule("bootstrap");
		$data = new module_bootstrap_data($data_list, "the form input");
		$loader = new OSNewsGui($this->avalanche, $this->doc);
		$runner = $bootstrap->newDefaultRunner();
		$runner->add($loader);
		$data = $runner->run($data);
		if(!($data instanceof module_bootstrap_data) || !($data->data() instanceof Component)){
			throw new Exception("poorly formatted output: <br><br>" . str_replace("\n", "<br>", print_r($data, true)));
		}
		return $data->data();
	}
	
	private function getPrivacy($data_list){
		$t = new Text("<div>

<p class=MsoNormal style='margin-left:5px'>The following describes how Inversion Designs gathers and distributes the information gathered for and by this Website. Inversion Designs reserves the right to amend this Privacy Policy at any time with or without notice. Only the current Privacy Policy is deemed effective, so please review this Privacy Policy periodically.

Inversion Designs has created this Privacy Policy to demonstrate our commitment to our members. The following rules summarize this commitment:
<ol>
   <li> We do not reveal any personally identifiable information that we collect about you, your use of the Services or any information that you post on your Site to anyone, with one exception: 2checkout.com. 2checkout.com is our affiliate who manages all online purchases. Only the information required for purchase will be shared with 2checkout.com.</li>

   <li> Unless you choose otherwise, InversionDesigns.com shares information about you only on a basis that does not personally identify you.</li>

   <li> Your personal information is password protected and visible only to a few amount of Inversion Designs employees.</li>
</ol>
</div>");
		$t->getStyle()->setFontFamily("verdana, sans-serif");
		$t->getStyle()->setFontSize(10);
		$t->getStyle()->setFontColor("black");
		return $t;
	}
	
	private function getTOS($data_list){
		$t = new Text("<div>

<ol style='margin-top:0in' start=1 type=1>
 <li class=MsoNormal>Service Level</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs will provide 99.9% monthly uptime for
      the calendar software. This does not include routine upgrades and
      maintenance to software. Upgrades will occur nightly (CST time) and last
      on average no more than 5 minutes.</li>
  <li class=MsoNormal>Service outages caused by customer misuse of the system
      are not covered. Offending customers will be charged a “System Downtime”
      fee if they are found to be the cause of any serious system downtime. </li>
  <li class=MsoNormal>Customers will be notified by their primary account email
      of any significant scheduled system downtimes.</li>
 </ol>
 <li class=MsoNormal>Security</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Aurora Calendar software has been written with security
      in mind. All access to primary software systems requires password access
      and appropriate clearance for the user.</li>
  <li class=MsoNormal>Inversion Designs constantly upgrades Aurora Calendar and
      closes any security holes as they arrive.</li>
  <li class=MsoNormal>Any accounts that have had a security violation will be
      notified of the violation as well as what data was compromised, if known.</li>
  <li class=MsoNormal>Inversion Designs will attempt to restore any data lost
      due to security problems at no cost to the Account Owner.</li>
  <li class=MsoNormal>The Account Owner is responsible for ensuring that all
      users on the account have been given appropriate permission levels for
      that account. Any power that has been mistakenly given to a user by the
      Account Owner that the user exercises is not considered a security
      violation and is not the responsibility of Inversion Designs. Inversion
      Designs will attempt to restore any data that has been lost or corrupted
      by the user, at a cost of at least \$50 to the Account Owner, per incident.</li>
  <li class=MsoNormal>Security violations of appropriate size will be reported
      to the presiding law enforcement agencies.</li>
  <li class=MsoNormal>Training and Consultation is provided by Inversion
      Designs for the Account Owner to educate on best practices to help reduce
      account misuse and security. Training and Consultation is provided at a cost
      of at least \$50 per hour unless otherwise agreed to by Inversion Designs.</li>
 </ol>
 <li class=MsoNormal>Tracking and Reporting</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Serious runtime errors are emailed immediately to
      Inversion Designs staff for review. These errors will be fixed in order
      of importance and receipt.</li>
  <li class=MsoNormal>Inversion Designs provides a bot that periodically checks
      for downtime in the system.</li>
 </ol>
 <li class=MsoNormal>System Performance</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Estimated page load time is reported at the bottom of every page in
      Aurora Calendar.</li>
  <li class=MsoNormal>Aurora Calendar is designed to load each page in under 1
      second. Inversion Designs does not guarantee these load times, they are
      only a metric. If load times become too slow for use, the Account Owner
      is responsible for requesting a review of the account to help decrease
      downtime. If Inversion Designs agrees that the load times are too slow,
      then it will attempt to lower load times in any way that it reasonably
      can.</li>
 </ol>
 <li class=MsoNormal>Remedies</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs may offer rebates or special deals to
      the Account Owner if the performance of Aurora Calendar does not fit the
      criteria in this document.</li>
 </ol>
 <li class=MsoNormal>Upgrades</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs will update the software roughly once a
      week.</li>
  <li class=MsoNormal>Updates will be carried out at night (CST time).</li>
  <li class=MsoNormal>Security patches and critical fixes will be released as
      soon as they are available.</li>
  <li class=MsoNormal>Backups of Aurora Calendar are made before each major
      upgrade, and Inversion Designs will rollback to the previous version if
      the upgrade fails.</li>
 </ol>
 <li class=MsoNormal>Contingency, Backup, and Disaster Recovery</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Backups of Aurora Calendar are made before any upgrade or
      new release.</li>
  <li class=MsoNormal>Backups of data are made at least weekly, but might be
      made more frequently. The Account Owner is responsible for keeping an
      offline backup of all online data.</li>
 </ol>
 <li class=MsoNormal>Support and Help Desk Services</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Support requests can be made via email at no cost to the
      Account Owner.</li>
  <li class=MsoNormal>Email support is available from at least 10am to 5pm.
      Inversion Designs will respond to requests at most two hours after the
      request is made. Most requests should be handled within 20 minutes.</li>
  <li class=MsoNormal>Support requests that can be handled via email are not
      limited to account upgrades, quick how-to’s, error reporting and fixing,
      and finding a lost password.</li>
  <li class=MsoNormal>Reported Errors will be addressed and resolved by
      Inversion Designs as soon as it reasonably can. Most problems can be
      resolved within one business day.</li>
  <li class=MsoNormal>The Frequently Asked Questions page will be periodically
      updated with answers to common support requests.</li>
 </ol>
 <li class=MsoNormal>Termination</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs reserves the right to terminate any
      account found in violation of these terms and conditions.</li>
  <li class=MsoNormal>Inversion Designs reserved the right to suspend any
      account for suspected violation of these terms and conditions</li>
  <li class=MsoNormal>Inversion Designs reserves the right to not renew any
      contract after the contract expires</li>
 </ol>
 <li class=MsoNormal>Ownership</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs is the sole owner and provider of the
      Aurora Calendar software. Inversion Designs makes no claims, either
      expressed or implied, to relinquish any ownership or rights therein to
      the Account Owner or any user of the Account.</li>
  <li class=MsoNormal>Inversion Designs retains all copyrights to Aurora
      Calendar.</li>
  <li class=MsoNormal>The Account Owner is the owner of all data entered into
      they system by the Account Owner or other users of the Account. The
      Account Owner gives Inversion Designs permission to view and edit this
      data at the Owner’s request.</li>
  <li class=MsoNormal>Inversion Designs is the owner of all data entered into
      the system during and for Account setup or maintenance.</li>
 </ol>
 <li class=MsoNormal>Intellectual Property Indemnification</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>Inversion Designs will not indemnify Account Owner for
      claims that Inversion Designs services utilized by the customer infringe
      upon the intellectual property rights of others</li>
 </ol>
 <li class=MsoNormal>Indemnification by Customer</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>The Account Owner will indemnify Inversion Designs for
      third-party claims against Inversion Designs as a result of Account
      Owner’s activities</li>
 </ol>
 <li class=MsoNormal>General</li>
 <ol style='margin-top:0in' start=1 type=a>
  <li class=MsoNormal>These terms and conditions are also subject to the terms
      and conditions of MonsterHosting.ca.</li>
  <li class=MsoNormal>The Account Owner will be notified via email of any
      acquisition or merger of Inversion Designs</li>
  <li class=MsoNormal>Inversion Designs may change these Terms and Conditions at
      any time without notifying the Account Owner. It is the responsibility of
      the Account Owner to adhere to these guidelines.</li>
 </ol>
</ol>

</div>");
		$t->getStyle()->setFontFamily("verdana, sans-serif");
		$t->getStyle()->setFontSize(10);
		$t->getStyle()->setFontColor("black");
		return $t;
	}
	
	private function getAbout($data_list){
		$strongcal = $this->avalanche->getModule("strongcal");
		$taskman = $this->avalanche->getModule("taskman");
		
		$logo_panel = new Panel();
		$logo_panel->setAlign("center");
		$logo_panel->setWidth("100%");
		$logo_panel->getStyle()->setHeight("116px");
		
		$logo = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/logo.jpg");
		$logo->getStyle()->setBorderWidth(2);
		$logo->getStyle()->setBorderColor("#91A1C6");
		$logo->getStyle()->setBorderStyle("solid");
		$logo_panel->add($logo);
		
		$versions = new GridPanel(2);
		$versions->getCellStyle()->setFontFamily("verdana, sans-serif");
		$versions->getCellStyle()->setFontSize(10);
		$versions->getCellStyle()->setFontColor("black");
		$versions->getStyle()->setWidth("180px");
		$versions->getCellStyle()->setPaddingBottom(5);
		
		$versions->add(new Text("<b>Module</b>"));
		$versions->add(new Text("<b>Version</b>"));
		$versions->add(new Text("Calendar"));
		$versions->add(new Text($strongcal->version()));
		$versions->add(new Text("Task Manager"));
		$versions->add(new Text($taskman->version()));
		
		$info = new Panel();
		$info->setAlign("center");
		$info->setWidth("100%");
		$info->getStyle()->setFontFamily("verdana, sans-serif");
		$info->getStyle()->setFontSize(8);
		
		$info->add(new Text("Powered by "));
		$info->add(new Link("Inversion Designs", "http://inversiondesigns.com"));
		
		$about = new GridPanel(1);
		$about->setAlign("center");
		$about->getStyle()->setWidth("340");
		$about->add($logo_panel);
		$about->add($versions);
		$about->add($info);
		return $about;
	}
}
?>