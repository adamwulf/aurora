<?
class module_bootstrap_strongcal_default_view extends module_bootstrap_module{

	private $avalanche;
	private $doc;

	function __construct($avalanche, Document $doc){
		if(!is_object($avalanche)){
			throw new IllegalArugmentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->setName("Aurora Default View Loader");
		$this->setInfo("currently returns an html list of calendars. In the future, this module will accept as input
				raw form data which it will delegate to other modules to load a page for aurora. output is an
				html page.");
		$this->avalanche = $avalanche;
		$this->doc = $doc;
	}

	function run($data = false){
		if(!$data instanceof module_bootstrap_data){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$strongcal = $this->avalanche->getModule("strongcal");
			$view = $this->avalanche->loggedInHuh() ? $strongcal->getUserVar("highlight") : "";

			$request = $data->data();
			$bootstrap = $this->avalanche->getModule("bootstrap");
			$runner = $bootstrap->newDefaultRunner();

			if($view == "overview"){
				$runner->add(new module_bootstrap_os_overview_gui($this->avalanche, $this->doc));
			}else
			if($view == "month"){
				$runner->add(new module_bootstrap_strongcal_monthview_gui($this->avalanche, $this->doc));
			}else
			if($view == "week"){
				$runner->add(new module_bootstrap_strongcal_weekview_gui($this->avalanche, $this->doc));
			}else
			if($view == "day"){
				$runner->add(new module_bootstrap_strongcal_dayview_gui($this->avalanche, $this->doc));
			}else{
				$runner->add(new module_bootstrap_os_login_gui($this->avalanche, $this->doc));
			}
			return $runner->run($data);
		}else{
			throw new module_bootstrap_exception("input to " . $this->name() . " must be an associated array.<br>");
		}
	}
}
?>