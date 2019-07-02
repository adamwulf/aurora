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

class avalanche_personal_usergroup extends avalanche_usergroup{

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
		if($this->avalanche->getActiveUser() == $this->author()){
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
		if($this->avalanche->getActiveUser() == $this->author()){
			return parent::rename($new_name);
		}else{
			return false;
		}
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateDescription($desc){
		if($this->avalanche->getActiveUser() == $this->author()){
			return parent::updateDescription($desc);
		}else{
			return false;
		}
	}
	
	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateKeywords($key){
		if($this->avalanche->getActiveUser() == $this->author()){
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
		if($this->avalanche->getActiveUser() == $this->author()){
			return parent::unlinkUser($userId);
		}else{
			return false;
		}
	}        
}
?>