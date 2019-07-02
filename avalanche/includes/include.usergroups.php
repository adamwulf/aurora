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

abstract class avalanche_usergroup{

	/**
	 * created for system purposes. all system permissions will apply only to these types of groups
	 * this has to be "defended" in the sub classes
	 */
	public static $SYSTEM = "SYSTEM";

	/**
	 * created for team only purposes. these teams can be seen by members of the team, but
	 * not by anyone else.
	 * members will be notified when they are added to this group
	 * members cannot request to join this group.
	 * used specifically for the project functionality
	 */
	public static $TEAM = "TEAM";

	/**
	 * groups created for a modules use. these groups will never be seen by users, and are for
	 * backend purposes only
	 */
	public static $MODULE = "MODULE";

	/**
	 * This is for groups that can be created and seen by only one user. they are for personal
	 * organization and permission setting (ie, for calendars)
	 */
	public static $PERSONAL = "PERSONAL";

	/**
	 * This is for groups that can be seen and used (but not edited) by all users
	 * only the system can edit these groups
	 */
	public static $PUBLIC = "PUBLIC";

	/**
	 * This is for groups that represent single users
	 */
	public static $USER = "USER";
	/**
	 * the usergroup id for this group
	 */
	private $_groupId;

	/**
	 * the data (permission, desc, keywords, etc) pulled from the database
	 */
	protected $_group_data;

	/**
	 * the display type for this group (all uppercase type)
	 */
	protected $_display_type;

	//////////////////////////////////////////////////////////
	// (int) returns the id of this usergroup
	//////////////////////////////////////////////////////////
	function getId(){
		return (int) $this->_groupId;
	}

	//////////////////////////////////////////////////////////
	// (int) returns the type of this usergroup
	//////////////////////////////////////////////////////////
	function type(){
		return $this->_group_data["type"];
	}

	//////////////////////////////////////////////////////////
	// (string) returns the type of this usergroup
	//////////////////////////////////////////////////////////
	function display_type(){
		return $this->_display_type[$this->type()];
	}

	//////////////////////////////////////////////////////////
	// (string) returns the name of this usergroup
	//////////////////////////////////////////////////////////
	function name($name = false){
		if(is_string($name) && strlen($name) && $this->rename($name)){
			return $this->name();
		}else{
			return $this->_group_data["name"];
		}
	}


	//////////////////////////////////////////////////////////
	// (string) returns the description of this usergroup
	//////////////////////////////////////////////////////////
	function description($desc = false){
		if(is_string($desc) && $this->updateDescription($desc)){
			return $this->description();
		}else{
			return $this->_group_data["description"];
		}
	}

	//////////////////////////////////////////////////////////
	// (string) returns the description of this usergroup
	//////////////////////////////////////////////////////////
	function keywords($keys = false){
		if(is_string($keys) && $this->updateKeywords($keys)){
			return $this->keywords();
		}else{
			return $this->_group_data["keywords"];
		}
	}


	//////////////////////////////////////////////////////////
	// (int) returns the author of this usergroup
	//////////////////////////////////////////////////////////
	function author(){
		return (int) $this->_group_data["author"];
	}


	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsersIn(){
		$groupId = $this->_groupId;
		$ret = array();
		$table1 = $this->avalanche->PREFIX() . "users";
		$table2 = $this->avalanche->PREFIX() . "user_link";
		$sql = "SELECT DISTINCTROW $table1.id FROM $table1, $table2 WHERE $table1.id = $table2.user_id AND $table2.group_id = $groupId ORDER BY $table1.username";
	        $result = $this->avalanche->mysql_query($sql);
	        while ($myrow = mysqli_fetch_array($result)) {
                        $ret[] = $this->avalanche->getUser((int)$myrow["id"]);
	        }
	        return $ret;
	}

	// this groups instance of the avalanche it lives in
	protected $avalanche = false;

	// init everything
	// $groupId is the id for this group inside of $avalanche
	// $data is the raw data from the mysql
	//   so we'll strip out slashes if we need to (get_magin_quotes_gpc)
	function __construct($groupId, $avalanche, $data=false){
		$this->avalanche = $avalanche;
		/*
		 * InversionDesigns: administrative is for avalanche permissions
		 * public is a group anyone can join at any time of their own free will
		 * invite only are private groups owned by users, adn other users can join only by an invite from that author user
		 * moderated means users can see the group exists, but must request admittance to the group
		 * and application groups define who is allowed to run what modules
		 */
		$this->_display_type = array(
			   self::$SYSTEM => "Administrative",
	                   self::$TEAM => "Team",
			   self::$PUBLIC => "Public",
			   self::$MODULE => "Module",
			   self::$PERSONAL => "Personal");
		$this->_groupId = $groupId;
		if(!is_array($data) && !($data === false)){
			throw new IllegalArgumentException("optional third argument to " . __METHOD__ . " must be an array");
		}
		if($data === false){
			$result = $this->avalanche->mysql_query("SELECT * FROM " . $avalanche->PREFIX() . "usergroups WHERE id='$groupId'");
			if($myrow = mysqli_fetch_array($result)) {
				if(get_magic_quotes_gpc()){
					$myrow['name'] = stripslashes($myrow['name']);
					$myrow['description'] = stripslashes($myrow['description']);
					$myrow['keywords'] = stripslashes($myrow['keywords']);
				}
				$this->_group_data = array(
					// the unique identifier for this usergroup
					"id" => (int)$myrow['id'],
					// the id of the user who created this usergroup
					// 0 for system usergroup
					"author" => $myrow['author'],
					// the id for the type of this usergroup
					// 0 for administrative
					// 1 for public
					// 2 for invite only
					// 3 for moderated
					"type" => $myrow['type'],
					// the name of the usergroup
					"name" => $myrow['name'],
					// the description of the usergroup
					"description" => $myrow['description'],
					// the description of the usergroup
					"keywords" => $myrow['keywords'],
					// 1 if users in this group can install mods
					// 0 otherwise
					"install_mod" => $myrow['install_mod'],
					// 1 if users in this group can uninstall mods
					// 0 otherwise
					"uninstall_mod" => $myrow['uninstall_mod'],
					// 1 if users in this group can install skins
					// 0 otherwise
					"install_skin" => $myrow['install_skin'],
					// 1 if users in this group can uninstall skins
					// 0 otherwise
					"uninstall_skin" => $myrow['uninstall_skin'],
					// 1 if users in this group can add users
					// 0 otherwise
					"add_user" => $myrow['add_user'],
					// 1 if users in this group can delete users
					// 0 otherwise
					"del_user" => $myrow['del_user'],
					// 1 if users in this group can rename users
					// 0 otherwise
					"rename_user" => $myrow['rename_user'],
					// 1 if users in this group can add usergroups
					// 0 otherwise
					"add_usergroup" => $myrow['add_usergroup'],
					// 1 if users in this group can delete usergroups
					// 0 otherwise
					"del_usergroup" => $myrow['del_usergroup'],
					// 1 if users in this group can rename/redescription usergroups
					// 0 otherwise
					"rename_usergroup" => $myrow['rename_usergroup'],
					// 1 if users in this group can change the default skin of avalanche
					// 0 otherwise
					"change_default_skin" => $myrow['change_default_skin'],
					// 1 if users in this group can change permissions
					// 0 otherwise
					"change_permissions" => $myrow['change_permissions'],
					// 1 if users in this group can link users to usergroups
					// 0 otherwise
					"link_user" => $myrow['link_user'],
					// 1 if users in this group can unlink users to usergroups
					// 0 otherwise
					"unlink_user" => $myrow['unlink_user'],
					// 1 if users in this group can change_default_usergroup
					// 0 otherwise
					"change_default_usergroup" => $myrow['change_default_usergroup'],
					// 1 if users in this group can view the control panel (depreciated)
					// 0 otherwise
					"view_cp" => $myrow['view_cp'],
					// 1 if users in this group can change user passwords
					// 0 otherwise
					"change_password" => $myrow['change_password'],
					// 1 if users in this group can view user passwords
					// 0 otherwise
					"view_password" => $myrow['view_password'],
					// 1 if users in this group can change their own username / surname
					// 0 otherwise
					"change_name" => $myrow['change_name'],
					// 1 if users in this group can view their own username / surname
					// 0 otherwise
					"view_name" => $myrow['view_name'],
					// 1 if users in this group can disable users
					// 0 otherwise
					"disable_user" => $myrow['disable_user']
					);
				return true;
			}
		}else if(is_array($data)){
			$data['id'] = (int)$data['id'];
			if(get_magic_quotes_gpc()){
				$data['name'] = stripslashes($data['name']);
				$data['description'] = stripslashes($data['description']);
				$data['keywords'] = stripslashes($data['keywords']);
			}
			$this->_group_data = $data;
			return true;
		}
	}

	// runs the visitor on usergroup case
	function execute($visitor){
		return $visitor->visit($this);
	}


	//////////////////////////////////////////////////////////
	// Notes:
	// links the user to the group
	// no permission checking by default!
	//////////////////////////////////////////////////////////
	function linkUser($userId){
		$groupId = $this->_groupId;
		// if the logged in user has permission to link users
		// and if the user is not already linked...
		$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "user_link (user_id, group_id) VALUES (\"".$userId."\",\"".$groupId."\")";
		$result = $this->avalanche->mysql_query($sql);
		return true;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// renames the user at the given id iff there is
	// no other user by that username and if the current
	// logged in user has permission
	//////////////////////////////////////////////////////////
	protected function rename($new_name){
		$usergroupId = $this->getId();
		$result = false;
		$data_name = addslashes($new_name);
		$query = "UPDATE " . $this->avalanche->PREFIX() . "usergroups SET name='" . $data_name . "' WHERE id='" . $usergroupId . "'";
		$result = $this->avalanche->mysql_query($query);
		$this->_group_data["name"] = $new_name;
		return true;
	}

	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateDescription($desc){
		$usergroupId = $this->getId();
		$result = false;
		$data = addslashes($desc);
		$query = "UPDATE " . $this->avalanche->PREFIX() . "usergroups SET description='" . $data . "' WHERE id='" . $usergroupId . "'";
		$result = $this->avalanche->mysql_query($query);
		$this->_group_data["description"] = $data;
		return true;
	}

	//////////////////////////////////////////////////////////
	// updates the description of the group
	//////////////////////////////////////////////////////////
	protected function updateKeywords($key){
		$usergroupId = $this->getId();
		$result = false;
		$data = addslashes($key);
		$query = "UPDATE " . $this->avalanche->PREFIX() . "usergroups SET keywords='" . $data . "' WHERE id='" . $usergroupId . "'";
		$result = $this->avalanche->mysql_query($query);
		$this->_group_data["keywords"] = $data;
		return true;
	}

	//////////////////////////////////////////////////////////
	// returns true if the user is in the usergroup
	//
	//////////////////////////////////////////////////////////
	function userInGroupHuh($userId){
		$groupId = $this->getId();
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "user_link WHERE group_id = \"$groupId\" AND user_id=\"$userId\"";
		$result = $this->avalanche->mysql_query($sql);
		if($myrow = mysqli_fetch_array($result)){
			return true;
		}
		return false;
	}

	//////////////////////////////////////////////////////////
	// Notes:
	// unlinks the user with the group
	//////////////////////////////////////////////////////////
	function unlinkUser($userId){
		$groupId = $this->getId();
		$sql = "DELETE FROM " . $this->avalanche->PREFIX() . "user_link WHERE user_id='$userId' && group_id='$groupId'";
		$result = $this->avalanche->mysql_query($sql);
		return true;
	}



	//////////////////////////////////////////////////////////
	// returns true if this usergroup has
	// the $perm permission
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function hasPermissionHuh($perm) {
		return false;
	}

	//////////////////////////////////////////////////////////
	// updates the permissions of user with id of $argUserId
	//////////////////////////////////////////////////////////
	function updatePermissions($arrayPerm){
		return false;
	}

	//////////////////////////////////////////////////////////
	// updates the $permission and sets the $value
	// does not check for errors!
	//////////////////////////////////////////////////////////
	function updatePermission($permission, $value){
		return false;
	}
}
?>