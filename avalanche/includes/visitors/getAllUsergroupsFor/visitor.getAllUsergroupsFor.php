<?

class visitor_getAllUsergroupsFor extends visitor_template{


	private $_user_id;

	function __construct(){
		$this->_init = false;
	}

	function init($user_id){
		$this->_user_id = $user_id;
		$this->_init = true;
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
		if($obj instanceof avalanche_usergroup_class){
			return $this->usergroupCase($obj);
		}else{
			throw new Exception("no case for object of type " . get_class($obj));
		}

	}

	function avalancheCase($avalanche){
		if(!$this->_init){
			trigger_error("visitor getAllUsergroupsFor has not been initialized.", E_USER_WARNING);
			return;
		}
		$userId = $this->_user_id;
		$ret = array();
		$ok = false;
		$table1 = $avalanche->PREFIX() . "usergroups";
		$table2 = $avalanche->PREFIX() . "user_link";
		$sql = "SELECT `$table1`.* FROM `$table1`, `$table2` WHERE `$table1`.id = `$table2`.group_id AND `$table2`.user_id = '$userId'";
	        $result = $avalanche->mysql_query($sql);
	        while ($myrow = mysqli_fetch_array($result)) {
			$ok = true;
			$group = $avalanche->getUsergroup((int)$myrow['id']);
			$ret[] = $group;
        	}

		// get the user usergroup if we're not the system user
		if($userId > 0){
			$group = $avalanche->getUsergroup(-(int)$userId);
			$ret[] = $group;
		}

		if(count($ret)){
			return $ret;
		}else{
			return array();
		}
	}

	function moduleCase($module){
		//noop
	}

	function skinCase($skin){
		//noop
	}

	function usergroupCase($usergroup){
		//noop
	}




}

?>