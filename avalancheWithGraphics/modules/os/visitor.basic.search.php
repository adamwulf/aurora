<?

class visitor_basic_search extends visitor_template{

	public static $USERS		= 1;
	public static $TEAMS		= 2;
	public static $CALENDARS	= 3;
	public static $EVENTS		= 4;
	public static $TASKS		= 5;
	public static $COMMENTS		= 6;
	
	
	protected $search_terms;
	
	protected $search_for;

	protected $avalanche;
	function __construct($avalanche, $terms){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->avalanche = $avalanche;
		$this->search_terms = $terms;
		$this->search_for = array(visitor_search::$USERS,
					    visitor_search::$TEAMS,
					    visitor_search::$CALENDARS,
					    visitor_search::$EVENTS,
					    visitor_search::$TASKS,
					    visitor_search::$COMMENTS);
	}
	
	function getSearchTypes(){
		return $this->search_for;
	}
	
	function searchFor($type){
		if(!$this->verifyType($type)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a visitor_search constant");
		}
		if(!in_array($type, $this->search_for)){
			$this->search_for[] = $type;
		}
	}
	
	function doNotSearchFor($type){
		if(!$this->verifyType($type)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a visitor_search constant");
		}
		if(in_array($type, $this->search_for)){
			array_splice($this->search_for, array_search($type, $this->search_for), 1);
		}
	}
	
	private function verifyType($type){
		return  $type == visitor_search::$USERS ||
			$type == visitor_search::$TEAMS ||
			$type == visitor_search::$CALENDARS ||
			$type == visitor_search::$EVENTS ||
			$type == visitor_search::$TASKS ||
			$type == visitor_search::$COMMENTS;
	}

	function visit($obj){
		if($obj instanceof avalanche_class){
			return $this->avalancheCase($obj);
		}else
		if($obj instanceof module_template){
			return $this->moduleCase($obj);
		}else
		if($obj instanceof skin_template){
			return $this->skinCase($obj);
		}else
		if($obj instanceof avalanche_usergroup){
			return $this->usergroupCase($obj);
		}else{
			throw new Exception("no case for object of type " . get_class($obj));
		}
	}



	function avalancheCase($avalanche){
		$mods = array();
		
		$mods[] = $avalanche->getModule("strongcal");
		$mods[] = $avalanche->getModule("taskman");
		$mods[] = $avalanche->getModule("os");
		$results = array();
		foreach($mods as $mod){
			$result = $mod->execute($this);
			foreach($result as $type){
				if(count($type)){
					$index = get_class($type[0]);
					if(!isset($results[$index])){
						$results[$index] = array();
					}
					$results[$index] = array_merge($results[$index], $type);
				}
			}
		}
		return $results;
	}

	function moduleCase($module){
		if($module instanceof module_strongcal){
			// search for calendars and events
			$results = array();
			return $results;
		}else if($module instanceof module_taskman){
			// search for tasks
			$results = array();
			return $results;
		}else if($module instanceof module_os){
			// search for users and groups
			$users = array();
			return array($users, $groups);
		}else{
			return array();
		}
	}

	function skinCase($skin){
		return array();
	}

	function usergroupCase($usergroup){
		return array();
	}




}

?>