<?

class visitor_getAllUsergroups extends visitor_template{




	function __construct(){

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
		$ret = array();
		$ok = false;
	        $sql = "SELECT * FROM " . $avalanche->PREFIX() . "usergroups ORDER BY type ASC";
	        $result = $avalanche->mysql_query($sql);
	        while ($myrow = mysqli_fetch_array($result)) {
			$ok = true;
			$group = $avalanche->getUsergroup((int)$myrow['id']);
			$ret[] = $group;
	        }

		$users = $avalanche->getAllUsers();
		$default = $avalanche->getVar("USER");
		foreach($users as $user){
			if($user->getId() != $default){
				$group = $avalanche->getUsergroup(-$user->getId());
				$ret[] = $group;
			}
		}

		if($ok){
		        return $ret;
		}else{
			return array();
		}
	}

	function moduleCase($module){

	}

	function skinCase($skin){
	}

	function usergroupCase($usergroup){

	}




}

?>