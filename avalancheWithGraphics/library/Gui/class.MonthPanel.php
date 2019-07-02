<?


/**
 * this class represents an Gui Panel. Panels can have other compoents added to them. the layout of the 
 * items in the panel is determined by the panel type.
 */
class MonthPanel extends Panel{

	private $y;
	private $m;
	private $day_url;
	private $week_url;
	private $month_url;
	private $view;
	/**
	 * basic constructor
	 */
	function __construct($y, $m, $view = "month", $day_url = "", $week_url = "", $month_url = ""){
		parent::__construct();
		
		/* we're just using this as a new id */
		$filler = new Panel();
		if(!is_integer($y)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an integer");
		}
		if(!is_integer($m)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be an integer");
		}
		if(!is_string($view)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be an string");
		}
		if(!is_string($day_url)){
			throw new IllegalArgumentException("argument 4 to " . __METHOD__ . " must be an string");
		}
		if(!is_string($week_url)){
			throw new IllegalArgumentException("argument 5 to " . __METHOD__ . " must be an string");
		}
		if(!is_string($month_url)){
			throw new IllegalArgumentException("argument 6 to " . __METHOD__ . " must be an string");
		}
		if(!($view == "month" || $view == "week" || $view == "day")){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be either \"month\" or \"week\" or \"day\"");
		}
		$this->y = $y;
		$this->m = $m;
		$this->view = $view;
		if($day_url == ""){
			$this->day_url = false;
		}else{
			$this->day_url = $day_url;
		}
		if($week_url == ""){
			$this->week_url = false;
		}else{
			$this->week_url = $week_url;
		}
		if($month_url == ""){
			$this->month_url = false;
		}else{
			$this->month_url = $month_url;
		}
	}
	
	function getDisplayAction(){
		if($this->day_url && $this->week_url && $this->month_url){
			return new ManualAction("buildMonth(\"" . $this->getId() . "\", " . $this->y . ", " . $this->m . ", \"" . $this->view . "\",\"" . $this->day_url . "\",\"" . $this->week_url . "\",\"" . $this->month_url . "\");\n");
		}else{
			return new ManualAction("buildMonth(\"" . $this->getId() . "\", " . $this->y . ", " . $this->m . ");\n");
		}
	}
}



?>
