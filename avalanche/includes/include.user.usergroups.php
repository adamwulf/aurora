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

class avalanche_user_usergroup extends avalanche_usergroup{

	//////////////////////////////////////////////////////////
	// (string) returns the name of this usergroup
	//////////////////////////////////////////////////////////
	function name($name = false){
		$os = $this->avalanche->getModule("os");
		return $os->getUsername(-$this->getId());
	}


	//////////////////////////////////////////////////////////
	// (string) returns the description of this usergroup
	//////////////////////////////////////////////////////////
	function description($desc = false){
		$user = $avalanche->getUser(-$this->getId());
		return $user->bio();
	}

	// just set everything up in the parent class
	function __construct($user_id, $avalanche){
		$data = array(
			// the unique identifier for this usergroup
			"id" => -(int)$user_id,
			// the id of the user who created this usergroup
			// 0 for system usergroup
			"author" => -1,
			// the id for the type of this usergroup
			// 0 for administrative
			// 1 for public
			// 2 for invite only
			// 3 for moderated
			"type" => avalanche_usergroup::$USER,
			// the name of the usergroup
			"name" => "bogus name",
			// the description of the usergroup
			"description" => "bogus name",
			// the description of the usergroup
			"keywords" => "",
			// 1 if users in this group can install mods
			// 0 otherwise
			"install_mod" => 0,
			// 1 if users in this group can uninstall mods
			// 0 otherwise
			"uninstall_mod" => 0,
			// 1 if users in this group can install skins
			// 0 otherwise
			"install_skin" => 0,
			// 1 if users in this group can uninstall skins
			// 0 otherwise
			"uninstall_skin" => 0,
			// 1 if users in this group can add users
			// 0 otherwise
			"add_user" => 0,
			// 1 if users in this group can delete users
			// 0 otherwise
			"del_user" => 0,
			// 1 if users in this group can rename users
			// 0 otherwise
			"rename_user" => 0,
			// 1 if users in this group can add usergroups
			// 0 otherwise
			"add_usergroup" => 0,
			// 1 if users in this group can delete usergroups
			// 0 otherwise
			"del_usergroup" => 0,
			// 1 if users in this group can rename/redescription usergroups
			// 0 otherwise
			"rename_usergroup" => 0,
			// 1 if users in this group can change the default skin of avalanche
			// 0 otherwise
			"change_default_skin" => 0,
			// 1 if users in this group can change permissions
			// 0 otherwise
			"change_permissions" => 0,
			// 1 if users in this group can link users to usergroups
			// 0 otherwise
			"link_user" => 0,
			// 1 if users in this group can unlink users to usergroups
			// 0 otherwise
			"unlink_user" => 0,
			// 1 if users in this group can change_default_usergroup
			// 0 otherwise
			"change_default_usergroup" => 0,
			// 1 if users in this group can view the control panel (depreciated)
			// 0 otherwise
			"view_cp" => 0,
			// 1 if users in this group can change user passwords
			// 0 otherwise
			"change_password" => 0,
			// 1 if users in this group can view user passwords
			// 0 otherwise
			"view_password" => 0,
			// 1 if users in this group can change their own username / surname
			// 0 otherwise
			"change_name" => 0,
			// 1 if users in this group can view their own username / surname
			// 0 otherwise
			"view_name" => 0,
			// 1 if users in this group can disable users
			// 0 otherwise
			"disable_user" => 0
			);
		return parent::__construct(-(int)$user_id, $avalanche, $data);
	}


	//////////////////////////////////////////////////////////
	// Notes:
	// links the user with the group
	//////////////////////////////////////////////////////////
	function linkUser($userId){
		return false;
	}        
	

	//////////////////////////////////////////////////////////
	// renames the user at the given id iff there is
	// no other user by that username and if the current
	// logged in user has permission
	//////////////////////////////////////////////////////////
	protected function rename($new_name){
		return false;
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateDescription($desc){
		return false;
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateKeywords($key){
		return false;
	}
	
	//////////////////////////////////////////////////////////
	// Notes:
	// unlinks the user with the group
	//////////////////////////////////////////////////////////
	function unlinkUser($userId){
		return false;
	}       
	
	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsersIn(){
	        return array($avalanche->getUser(-$this->getId()));
	}
	
	//////////////////////////////////////////////////////////
	// returns true if the user is in the usergroup
	//
	//////////////////////////////////////////////////////////
	function userInGroupHuh($userId){
		return $userId == -$this->getId();
	}
	
	
}
?>