<?
/**
 * This module is in charge of loading the entire page.
 *
 * it will end up running 2 bootstraps. the first will load the os header.
 * the second will load the content. the os header bootstrap will be passed in the URL,
 * as will the content loader.
 *
 * this loader will return an html page. it will be a table with two rows, one cell each.
 * the top cell will be the os header. the bottom cell will be the content.
 */
class OSHeaderGui extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("OS Header");
		$this->setInfo("returns the html for the OS Header");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		$timer = new Timer();
		if($data === false){
			$company_name = $this->avalanche->getVar("ORGANIZATION");
			$company_name = new Text($company_name);
			$company_style = new Style("os_header");
			$company_name->setStyle($company_style);

			$os = $this->avalanche->getModule("os");
			$mods = $os->getModules();
			$module_list = "";
			foreach($mods as $mod){
				$module_button = new Button($mod->name());
				$module_button->setStyle(new Style("button"));
				$module_button->addAction(new LoadPageAction("index.php?module=" . $mod->folder()));
//				$module_list .= $module_button->execute($html_visitor);
				// $module_list .= $mod->name() . "&nbsp;&nbsp;";
			}

			$login_link = false;
			if($this->avalanche->loggedInHuh()){
				$tip = OsGuiHelper::createToolTip(new Text("Click to Logout"));
				$login_link = new Button("logout:" . $this->avalanche->getUsername($this->avalanche->loggedInHuh()));
				$login_style = new Style("loginButton");
				$login_link->setStyle($login_style);
				$login_link->addAction(new LoadPageAction("index.php?logout=1"));
			}else{
				$tip = OsGuiHelper::createToolTip(new Text("Click to Login"));
				$login_link = new Button("login");
				$login_style = new Style("loginButton");
				$login_link->setStyle($login_style);
				$login_link->addAction(new LoadPageAction("index.php?view=login&to_page=" . urlencode($_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"])));
			}
			$menu_action = new ToolTipAction($login_link, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			/************************************************************************
			    initialize menu
			************************************************************************/
			$west_width = 30;
			$row_height = "25";
			$icon_style = new Style("sprocketMenuIcon");

			$profiles = new GridPanel(2);
			$profile_link = new Button("My Profile");
			$profile_link->setAlign("left");
			$profile_link->setStyle(new Style("sprocketMenuButton"));
			$profile_link->getStyle()->setWidth("130px");
			$profile_link->getStyle()->setHeight($row_height . "px");
			$profile_link->addAction(new LoadPageAction("index.php?view=manage_users&user_id=" . $this->avalanche->loggedInHuh()));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/profile.gif");
			$profile_icon = new SimplePanel();
			$profile_icon->add($t);
			$profile_icon->setStyle($icon_style);
			$profiles->add($profile_icon);
			$profiles->add($profile_link);
			//$profiles->setWestWidth($west_width);

			$preferences = new GridPanel(2);
			$preferences_link = new Button("Settings");
			$preferences_link->setAlign("left");
			$preferences_link->setStyle(new Style("sprocketMenuButton"));
			$preferences_link->getStyle()->setWidth("130px");
			$preferences_link->getStyle()->setHeight($row_height . "px");
			$preferences_link->addAction(new LoadPageAction("index.php?view=preferences"));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/preferences.gif");
			$preferences_icon = new SimplePanel();
			$preferences_icon->add($t);
			$preferences_icon->setStyle($icon_style);
			$preferences->add($preferences_icon);
			$preferences->add($preferences_link);
			//$preferences->setWestWidth($west_width);

			$teams = new GridPanel(2);
			$team_link = new Button("Group Mgt");
			$team_link->setAlign("left");
			$team_link->setStyle(new Style("sprocketMenuButton"));
			$team_link->getStyle()->setWidth("130px");
			$team_link->getStyle()->setHeight($row_height . "px");
			$team_link->addAction(new LoadPageAction("index.php?view=manage_teams"));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/groups.gif");
			$teams_icon = new SimplePanel();
			$teams_icon->add($t);
			$teams_icon->setStyle($icon_style);
			$teams->add($teams_icon);
			$teams->add($team_link);
			//$teams->setWestWidth($west_width);

			$users = new GridPanel(2);
			$user_link = new Button("User Directory");
			$user_link->setAlign("left");
			$user_link->setStyle(new Style("sprocketMenuButton"));
			$user_link->getStyle()->setWidth("130px");
			$user_link->getStyle()->setHeight($row_height . "px");
			$user_link->addAction(new LoadPageAction("index.php?view=manage_users"));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/users.gif");
			$users_icon = new SimplePanel();
			$users_icon->add($t);
			$users_icon->setStyle($icon_style);
			$users->add($users_icon);
			$users->add($user_link);
			//$users->setWestWidth($west_width);

			$help = new GridPanel(2);
			$help_link = new Button("FAQ");
			$help_link->setAlign("left");
			$help_link->setStyle(new Style("sprocketMenuButton"));
			$help_link->getStyle()->setWidth("130px");
			$help_link->getStyle()->setHeight($row_height . "px");
			$help_link->addAction(new LoadPageAction("http://inversiondesigns.com/helpdesk/faq.php", "_new"));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/faq.gif");
			$help_icon = new SimplePanel();
			$help_icon->add($t);
			$help_icon->setStyle($icon_style);
			$help->add($help_icon);
			$help->add($help_link);
			//$help->setWestWidth($west_width);

			$about = new GridPanel(2);
			$about_link = new Button("About");
			$about_link->setAlign("left");
			$about_link->setStyle(new Style("sprocketMenuButton"));
			$about_link->getStyle()->setWidth("130px");
			$about_link->getStyle()->setHeight($row_height . "px");
			$about_link->addAction(new LoadPageAction("index.php?view=about"));
			$t = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/about.gif");
			$about_icon = new SimplePanel();
			$about_icon->add($t);
			$about_icon->setStyle($icon_style);
			$about->add($about_icon);
			$about->add($about_link);
			//$about->setWestWidth($west_width);

			$purchase = new GridPanel(2);
			$purchase_link = new Button("Purchase");
			$purchase_link->setAlign("left");
			$purchase_link->setStyle(new Style("sprocketMenuButton"));
			$purchase_link->getStyle()->setWidth("130px");
			$purchase_link->getStyle()->setHeight($row_height . "px");
			if(is_object($this->avalanche->ACCOUNTOBJ())){
				$purchase_link->addAction(new LoadPageAction("http://inversiondesigns.com/member.php?subview=purchase&subsubview=upgrade&account=" . $this->avalanche->ACCOUNTOBJ()->name(), "_new"));
			}else{
				$purchase_link->addAction(new LoadPageAction("http://inversiondesigns.com/member.php", "_new"));
			}
			//$purchase_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/about.gif");
			$purchase_icon = new SimplePanel();
			$purchase_icon->setStyle($icon_style);
			$purchase->add($purchase_icon);
			$purchase->add($purchase_link);
			//$purchase->setWestWidth($west_width);

			$menu_panel = new SimplePanel();
			$num = 4;

			if($this->avalanche->loggedInHuh()){
				$num += 2;
				$menu_panel->add($profiles);
				$menu_panel->add($preferences);
			}
			$menu_panel->add($teams);
			$menu_panel->add($users);
			$menu_panel->add($help);
			$menu_panel->add($about);
			if($this->avalanche->hasPermissionHuh($this->avalanche->getActiveUser(), "view_cp")){
				$menu_panel->add($purchase);
			}
			$menu_panel->setStyle(new Style("xMenu sprocketMenu"));
			$menu_panel->getStyle()->setPosition("absolute");
			$menu_panel->getStyle()->setTop(-1000);
			$menu_panel->getStyle()->setLeft(-1000);
			$menu_panel->getStyle()->setWidth("160px");
			$menu_panel->getStyle()->setBackground("#F7F7F7");

			$icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "os/images/configicon.gif");
			$icon->setStyle(new Style("xTrigger sprocketIcon"));

			$menu = new MenuInitAction($icon, $menu_panel);
			$this->doc->addAction($menu);

			$tip = OsGuiHelper::createToolTip(new Text("Click for Options Menu"));
			$menu_action = new ToolTipAction($icon, $tip);
			$this->doc->addAction($menu_action);
			$this->doc->addHidden($tip);

			/************************************************************************
			    initialize panels
			************************************************************************/

			$my_container = new SimplePanel();

			$company_title = new SimplePanel();

			$mid_img = new SimplePanel();

			$filler = new Panel();

			/************************************************************************
			    apply styles to created panels
			************************************************************************/

			$my_container->setStyle(new Style("OSHeaderStyle"));
			$company_title->setStyle(new Style("companytitle"));
			$mid_img->setStyle(new Style("midimg"));

			/************************************************************************
			    add necessary text and html
			************************************************************************/

			$company_title->add($company_name);
			//$mid_img->add(new Text("&nbsp;"));
			$this->doc->add($menu_panel);

			/************************************************************************
			    put it all together
			************************************************************************/

			$my_container->add($company_title);
			$my_container->add($mid_img);
			$my_container->add($icon);
			$my_container->add($login_link);

			return new module_bootstrap_data($my_container, "the component for the os header");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be false.<br>");
		}
	}

}
?>