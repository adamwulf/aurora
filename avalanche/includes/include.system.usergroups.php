<?

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  include.usergroups.php						//
//----------------------------------------------------------------------//
//  includes the definition for the usergroup class objects		//
//									//
//////////////////////////////////////////////////////////////////////////

class avalanche_system_usergroup extends avalanche_usergroup{

	// just set everything up in the parent class
	function __construct($groupId, $avalanche, $data=false){
		return parent::__construct($groupId, $avalanche, $data);
	}


	//////////////////////////////////////////////////////////
	// Notes:
	// links the user with the group
	//////////////////////////////////////////////////////////
	function linkUser($userId){
		// if the logged in user has permission to link users
		// and if the user is not already linked...
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "link_user") && !$this->userInGroupHuh($userId)){
			return parent::linkUser($userId);
		}else{
			return false;
		}
	}        
	

	//////////////////////////////////////////////////////////
	// renames the user at the given id iff there is
	// no other user by that username and if the current
	// logged in user has permission
	//////////////////////////////////////////////////////////
	protected function rename($new_name){
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_usergroup")){
			return parent::rename($new_name);
		}else{
			return false;
		}
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateDescription($desc){
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_usergroup")){
			return parent::updateDescription($desc);
		}else{
			return false;
		}
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateKeywords($key){
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_usergroup")){
			return parent::updateKeywords($key);
		}else{
			return false;
		}
	}
	
	//////////////////////////////////////////////////////////
	// Notes:
	// unlinks the user with the group
	//////////////////////////////////////////////////////////
	function unlinkUser($userId){
		$groupId = $this->getId();
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "unlink_user")){
			return parent::unlinkUser($userId);
		}else{
			return false;
		}
	}        
	
	//////////////////////////////////////////////////////////
	// returns true if this usergroup has
	// the $perm permission
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function hasPermissionHuh($perm) {
		return $this->_group_data[$perm];
	}
		
	//////////////////////////////////////////////////////////
	// updates the permissions of user with id of $argUserId
	//////////////////////////////////////////////////////////
	function updatePermissions($arrayPerm){
		if(!is_array($arrayPerm)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an associated array");
		}
		$argGroupId = $this->getId();
		// if the loggedinuser is allowed to change permissions
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_permissions")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "usergroups SET %set% WHERE id = \"$argGroupId\"";
			$set = false;
			$ok = true;
			foreach($arrayPerm as $key => $val){
				$ok = $ok && $this->updatePermission($key, $val);
				if($ok){
					$this->_group_data[$key] = $val;
				}
			}
			if($ok){
				return true;
			}
			return false;
		}else{
			return false;
		}
	}

	//////////////////////////////////////////////////////////
	// updates the $permission and sets the $value
	// does not check for errors!
	//////////////////////////////////////////////////////////
	function updatePermission($permission, $value){
		if($this->type() != self::$SYSTEM){
			throw new Exception("cannot change a permission of a non SYSTEM group");
		}
		$argGroupId = $this->getId();
		// if the loggedinuser is allowed to change permissions
		if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_permissions")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "usergroups SET $permission = \"$value\" WHERE id = \"$argGroupId\"";
			$result = $this->avalanche->mysql_query($sql);
			if(!mysql_error()){
				return true;
			}
			return false;
		}else{
			return false;
		}
	}
}
?>