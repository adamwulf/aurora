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
class StrongcalCalendarMenu extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	private $trigger;
	private $cal_id;
	
	private static $menus;
	
	function __construct($avalanche, Document $doc, Component $trigger, $cal_id){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be of type avalanche");
		}
		$this->setName("Strongcal Calendar Menu");
		$this->setInfo("returns the component for the usermenu");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
		$this->trigger = $trigger;
		$this->cal_id = $cal_id;
		if(!is_object(StrongcalCalendarMenu::$menus)){
			StrongcalCalendarMenu::$menus = new HashTable();
		}
	}

	function run($data = false){
		if(is_object($data) && $data instanceof module_bootstrap_data){
			$data_list = $data->data();
			if(isset($data_list["view"])){
				$view = $data_list["view"];
			}
			if(isset($data_list["subview"])){
				$subview = $data_list["subview"];
			}
			$strongcal = $this->avalanche->getModule("strongcal");
			$cal = $strongcal->getCalendarFromDb($this->cal_id);

			/************************************************************************
			    initialize menu
			************************************************************************/
			if(!is_object(StrongcalCalendarMenu::$menus->get($this->cal_id))){
				$title_text = new Panel();
				$title_text->getStyle()->setWidth("120px");
				$title_text->getStyle()->setFontFamily("verdana, sans-serif");
				$title_text->getStyle()->setFontSize(8);
				$title_text->setAlign("left");
				$title_text->add(new Text($cal->name()));
	
				$manage = new BorderPanel();
				$manage_link = new Button("Manage");
				$manage_link->setAlign("left");
				$manage_link->setStyle(new Style("menu_button"));
				$manage_link->getStyle()->setWidth("120px");
				$manage_link->addAction(new LoadPageAction("index.php?view=manage_cals&cal_id=" . $this->cal_id));
				$manage->setCenter($manage_link);
				// $manage_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $strongcal->folder() . "/images/manage.gif");
				// $manage->setWest($manage_icon);
				
				if(isset($view)){
					$view_text = "&view=$view";
				}else{
					$view_text = "";
				}
				if(isset($subview)){
					$subview_text = "&subview=$subview";
				}else{
					$subview_text = "";
				}
				if(isset($data_list["event_id"])){
					$view_text .= "&event_id=" . $data_list["event_id"];
				}
				if(isset($data_list["cal_id"])){
					$view_text .= "&cal_id=" . $data_list["cal_id"];
				}
				if(isset($data_list["task_id"])){
					$view_text .= "&task_id=" . $data_list["task_id"];
				}
				if(isset($data_list["terms"])){
					$terms = $data_list["terms"];
					if(get_magic_quotes_gpc()){
						$terms = stripslashes($data_list["terms"]);
					}
					$view_text .= "&terms=" . urlencode($terms);
				}
				if(!$strongcal->selected($cal)){
					$hide_text = "Hide";
					$hide_val = "hide=1";
				}else{
					$hide_text = "Show";
					$hide_val = "show=1";
				}
				$hide = new BorderPanel();
				$hide_link = new Button($hide_text);
				$hide_link->setAlign("left");
				$hide_link->setStyle(new Style("menu_button"));
				$hide_link->getStyle()->setWidth("120px");
				$hide_link->addAction(new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_hideshowcal_loader&$hide_val&sh_cal_id=" . $cal->getId() . $view_text . $subview_text));
				$hide->setCenter($hide_link);
				// $hide_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/hide.gif");
				// $hide->setWest($hide_icon);
				
				$hide_all = new BorderPanel();
				$hide_all_link = new Button("Hide All Others");
				$hide_all_link->setAlign("left");
				$hide_all_link->setStyle(new Style("menu_button"));
				$hide_all_link->getStyle()->setWidth("120px");
				$hide_all_link->addAction(new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_hideshowcal_loader&hide_all=1&sh_cal_id=" . $cal->getId() . $view_text . $subview_text));
				$hide_all->setCenter($hide_all_link);
				// $hide_all_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/hide_all.gif");
				// $hide_all->setWest($hide_all_icon);
				
				$show_all = new BorderPanel();
				$show_all_link = new Button("Show All");
				$show_all_link->setAlign("left");
				$show_all_link->setStyle(new Style("menu_button"));
				$show_all_link->getStyle()->setWidth("120px");
				$show_all_link->addAction(new LoadPageAction("index.php?primary_loader=module_bootstrap_strongcal_hideshowcal_loader&show_all=1" . $view_text . $subview_text));
				$show_all->setCenter($show_all_link);
				// $show_all_icon = new Icon($this->avalanche->HOSTURL() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . $os->folder() . "/images/show_all.gif");
				// $show_all->setWest($show_all_icon);

				$menu_panel = new GridPanel(1);
				$menu_panel->add($title_text);
				$menu_panel->add($manage);
				$menu_panel->add($hide_link);
				$menu_panel->add($hide_all_link);
				$menu_panel->add($show_all_link);
				$menu_panel->setStyle(new Style("xMenu"));
				$menu_panel->getStyle()->setWidth("120px");
				$menu_panel->getStyle()->setBackground("silver");
				$this->doc->addHidden($menu_panel);
				StrongcalCalendarMenu::$menus->put($this->cal_id, $menu_panel);
			}else{
				$menu_panel = StrongcalCalendarMenu::$menus->get($this->cal_id);
			}
			
			$this->trigger->getStyle()->setClassname("xTrigger");
			$menu_action = new MenuInitAction($this->trigger, $menu_panel);
			$this->doc->addAction($menu_action);
			
			return false;
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be false.<br>");
		}
	}

}
?>