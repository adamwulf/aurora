<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  include.avalanche.php						//
//----------------------------------------------------------------------//
//  initializes the avalanche's class object				//
//									//
//////////////////////////////////////////////////////////////////////////


class avalanche_class{

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	// CONSTANTS							//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	// the name of the account of this avalanche, false if master avalanche
	private $ACCOUNT;
	// the actual account object
	private $account_obj;
	// top level filestructure variables
	private $ROOT;
	private $PUBLICHTML;
	private $HOSTURL;
	private $APPPATH;

	// cookie constants
	//set SECURE to 1 if cookies need to be sent over https connection
	private $DOMAIN;
	private $SECURE;

	// file structure variables
	private $INCLUDEPATH;
	private $JAVASCRIPT;
	private $MODULES;
	private $SKINS;
	private $LIBRARY;
	private $CLASSLOADER;

	// mysql variables
	private $HOST;
	private $ADMIN;
	private $PASS;
	private $DATABASENAME;
	private $PREFIX;
	//////////////////////////////////////////////////////////////////
	// END CONSTANTS						//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	// BEGIN CONSTANT GETTERS					//
	//////////////////////////////////////////////////////////////////

	public function ACCOUNT(){
		return $this->ACCOUNT;
	}

	public function ACCOUNTOBJ(){
		return $this->account_obj;
	}

	public function ROOT(){
		return $this->ROOT;
	}

	public function PUBLICHTML(){
		return $this->PUBLICHTML;
	}

	public function HOSTURL(){
		return $this->HOSTURL;
	}

	public function APPPATH(){
		return $this->APPPATH;
	}

	public function DOMAIN(){
		return $this->DOMAIN;
	}

	public function SECURE(){
		return $this->SECURE;
	}

	public function INCLUDEPATH(){
		return $this->INCLUDEPATH;
	}

	public function JAVASCRIPT(){
		return $this->JAVASCRIPT;
	}

	public function MODULES(){
		return $this->MODULES;
	}

	public function SKINS(){
		return $this->SKINS;
	}

	public function LIBRARY(){
		return $this->LIBRARY;
	}

	public function CLASSLOADER(){
		return $this->CLASSLOADER;
	}

	public function HOST(){
		return $this->HOST;
	}

	public function ADMIN(){
		return $this->ADMIN;
	}

	public function PASS(){
		return $this->PASS;
	}

	public function DATABASENAME(){
		return $this->DATABASENAME;
	}

	public function PREFIX(){
		return $this->PREFIX;
	}
	//////////////////////////////////////////////////////////////////
	// END CONSTANT GETTERS						//
	//////////////////////////////////////////////////////////////////

	// cache's mysql results
	private $_query_cache;

	// counts the number of queries to mysql
	private $_query_count;

	// $_allModules - an array holding the instances of each installed module class
	public $_allModules;

	// $allSkins - an array holding the instances of each installed skin class
	private $_allSkins;

	// $_defaultSkin - an instance of the default loaded skin's class
	private $_defaultSkin;

	// $_defaultSkin - an instance of the currently loaded skin's class
	private $_currentSkin;

	// $_recent_log_in - is the user id if the user logged in this page load, 0 otherwise
	// when a user logs in, we need to be able to see that later in the script. but since we
	// log in with cookies, the cookie won't be available until next page load, so this var
	// will hold a temp var of which user logged in, or false if none, or -1 if no data
	private $_recent_log_in;

	// this flag is true if the user only should be logged in for this page load, and no more.
	// so set the $_recent_log_in, but don't set a cookie
	private $_flag_for_temp_log_in;


	// this object is the visitor manager for avalanche
	private $_visitor_manager;

	// a cache of all usergroup objects
	private $_usergroup_cache;

	// a cache of all user objects
	private $_user_cache;

	// a cache of all avalanche variables
	private $var_list_cache;
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// returns the contents of a user var				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function getUserVar($var, $user_id=false){
		if($user_id === false){
			$user_id = $this->loggedInHuh();
		}
		if($user_id){
			$table = $this->PREFIX() . "preferences";
			$sql = "SELECT $var FROM $table WHERE user_id='$user_id'";
			$result = $this->mysql_query($sql);
			if(mysql_error()){
				throw new DatabaseException(mysql_error());
			}
			if($myrow = mysql_fetch_array($result)){
				return $myrow[$var];
			}else{
				return false;
			}
		}else{
			if(isset($_COOKIE[$var])){
				return $_COOKIE[$var];
			}else{
				return false;
			}
		}
	}

	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	// sets the contents of a user var				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	function setUserVar($var, $val){
		if($this->loggedInHuh()){
			$table = $this->PREFIX() . "preferences";
			$user_id = $this->loggedInHuh();
			$sql = "SELECT COUNT(*) AS count FROM $table WHERE user_id='$user_id'";
			$result = $this->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				if($myrow["count"]){
					$sql = "UPDATE $table SET $var='$val' WHERE user_id='$user_id'";
					$this->mysql_query($sql);
				}else{
					$sql = "INSERT INTO $table (user_id,$var) VALUES ('$user_id','$val')";
					$this->mysql_query($sql);
				}
			}else{
				return false;
			}
		}else{
			$this->setCookie($var, $val);
		}
	}

	//////////////////////////////////////////////////////////
	// returns true if the user is active
	// returns true if activity monitoring is turned off
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function active($userId = false){
		if(!$userId){
			$userId = $this->loggedInHuh();
		}
		$activity = $this->getVar("ACTIVITY");
		if($activity){
			if($userId){
				$offset = $this->getVar("ACTIVE_OFFSET");
				if($offset){
					$last_allowed = mktime (date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")) - $offset;
					$datetime = date("Y-m-d H:i:s", $last_allowed);
					$argIp = getenv('REMOTE_ADDR');
					$argUser = 0;
					$result = $this->mysql_query("SELECT * FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='" . $userId . "' AND last_active > '" . $datetime . "'");
					while ($myrow = mysql_fetch_array($result)) {
						return $myrow['user_id'];
					}
				}else{
					return true;
				}
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// activates the module
	//////////////////////////////////////////////////////////
	function activateModule($modId){
		trigger_error("activateModule(\$modId) not yet defined", E_USER_ERROR);
		$strongcal = $this->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		if(!$userId){
			$userId = $this->loggedInHuh();
		}
		if($modId){
			$sql = "SELECT * FROM " . $this->PREFIX() . "modules WHERE id='$modId'";
			$result = $this->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$trialTime = $myrow['trialTime'];
				$trialTime = $trialTime * 60 * 60 * 24;
				// $trialTime is now in seconds
				$timestamp = $gmtimestamp + $trialTime;
				if(!$myrow['active']){
					$datetime = date("Y-m-d H:i:s", $timestamp);
					$sql = "UPDATE " . $this->PREFIX() . "modules SET active='1', expiresOn='$datetime' WHERE id='$modId'";
					$result = $this->mysql_query($sql);
					if($result){
						return true;
					}else{
						return false;
					}
				}else{
					return true;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// adds a module to avalanche's module list
	// $mod must be a string of the installation folder
	// of the new mod
	//////////////////////////////////////////////////////////
	function addModule($mod){
		$this->_allModules->put($mod->folder(), $mod);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// adds a skin to avalanche's skin list
	// $skn must be a string of the installation folder
	// of the new skin
	//////////////////////////////////////////////////////////
	function addSkin($skn){
		$this->_allSkins->put($skn->folder(), $skn);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	// this should return the size of the maximum dimension of the users avatar
	// ie, if they have a 90x30, then 90 should be returned
	// or if they have a 45x56, then 56 should be returned
	function getAvatarSize($user_id=false){
		// right now, lets just return a dummy value till i decide to fix it
		return 60;
	}

	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	////////////  BEGIN USERGROUP FUNCTIONS  /////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// adds a usergroup to the usergroups table
	// only one usergroup of a name is allowed, so it
	// only adds a usergroup if the name is not already there.
	// returns the id of the new usergroup if this function
	//	   adds a usergroup to the db
	//         false otherwise
	// $name is the name of the new usergroup
	// $permissions is an array of permissions for the new usergroup
	//	ex array("install_mod" -> 1, "uninstall_mod" -> 1, ... "change_password" -> 1);
	// Notes:
	// notifies all modules of add usergroup attempt and verdict
	//////////////////////////////////////////////////////////
	function addUsergroup($type, $name, $description, $keywords){
		if(!is_string($type)){
			throw new IllegalArgumentException("argument \$type to " . __METHOD__ . " must be a string");
		}
		if($type != "SYSTEM" &&
		   $type != "TEAM" &&
		   $type != "MODULE" &&
		   $type != "PUBLIC" &&
		   $type != "PERSONAL"){
			throw new IllegalArgumentException("argument \$type to " . __METHOD__ . " must be either \"SYSTEM\" \"TEAM\" \"PUBLIC\" \"PERSONAL\"or \"MODULE\"");
		}
		if(!is_string($name)){
			throw new IllegalArgumentException("argument \$name to " . __METHOD__ . " must be a string");
		}
		if(!is_string($description)){
			throw new IllegalArgumentException("argument \$description to " . __METHOD__ . " must be a string");
		}
		if(!is_string($keywords)){
			throw new IllegalArgumentException("argument \$keywords to " . __METHOD__ . " must be a string");
		}

		if($this->hasPermissionHuh($this->loggedInHuh(), "add_usergroup") && ($type == "SYSTEM" || $type == "PUBLIC") ||
		   $type != "SYSTEM"){
			// verify add user
			$verify = 0;
			$name = addslashes($name);
			$description = addslashes($description);
			$keywords = addslashes($keywords);

			$author = $this->loggedInHuh();
			$sql = "INSERT INTO " . $this->PREFIX() . "usergroups (author, type, name, description, keywords) VALUES (\"$author\",\"$type\",\"$name\",\"$description\",\"$keywords\")";
			$verify = $this->mysql_query($sql);

			//get id of new usergroup
			$usergroup_id = mysql_insert_id();

			// notify all modules with new usergroupid and if add went ok
			$count = $this->getModuleCount();
			for($i=0; $i<$count; $i++){
				$module = $this->getModuleAt($i);
				$module->addUsergroup($usergroup_id, $verify);
			}
			return $usergroup_id;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// deletes the usergroup at the given usergroup_id
	// all users in the group are unlinked as well
	// Notes:
	// notifies all modules of delete usergroup attempt and verdict
	//////////////////////////////////////////////////////////
	function deleteUsergroup($usergrp_id){
		if(!is_integer($usergrp_id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an integer");
		}
		// deletes the usergroup from the database (including links from users to this group)
		// returns true if the group is deleted
		// returns false if usergroup not found or not deleted
		// it will return false if this group is the only link to a user

	        $verify = false;
		$default_usergroup = $this->getVar("USERGROUP");
		$all_users_group = $this->getVar("ALLUSERS");

		$group = $this->getUsergroup($usergrp_id);
		if(!is_object($group)){
			return true;
		}
		if($this->getActiveUser() == $group->author() ||
		   $this->hasPermissionHuh($this->loggedInHuh(), "del_usergroup") &&
		   $usergrp_id != $default_usergroup &&
		   $usergrp_id != $all_users_group){
	        	$sql = "DELETE FROM " . $this->PREFIX() . "user_link WHERE group_id='$usergrp_id'";
		        $result = $this->mysql_query($sql);

		        $sql = "DELETE FROM " . $this->PREFIX() . "usergroups WHERE id='$usergrp_id'";
		        $result = $this->mysql_query($sql);
			if($result){
		        	$this->_usergroup_cache->clear($usergrp_id);
				$verify = true;
			}else{
			        $verify = false;
			}

			//////////////////////////////////////////////////////////
			// If did delete usergroup, then notify all modules with//
			// username and verification of delete			//
			//////////////////////////////////////////////////////////
			if($verify){
				$count = $this->getModuleCount();
				for($i = 0; $i < $count; $i++){
					$currMod = $this->getModuleAt($i);
					$currMod->deleteUsergroup($usergrp_id, $verify);
				}
			}
		}
		return $verify;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsergroups(){
		$visitor = $this->_visitor_manager->getVisitor("getAllUsergroups");
		return $this->execute($visitor);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getUsergroup($groupId){
		if(is_object($this->_usergroup_cache->get($groupId))){
			return $this->_usergroup_cache->get($groupId);
		}
		if($groupId < 0){
			$group = new avalanche_user_usergroup(-$groupId, $this);
			$this->_usergroup_cache->put($groupId, $group);
			return $group;
		}else{
			$ok = false;
			$sql = "SELECT * FROM " . $this->PREFIX() . "usergroups WHERE id='$groupId'";
			$result = $this->mysql_query($sql);
			$fields = mysql_num_fields($result);
			if($myrow = mysql_fetch_array($result)) {
				if($myrow["type"] == "SYSTEM"){
					$group = new avalanche_system_usergroup($myrow['id'], $this, $myrow);
				}else if($myrow["type"] == "TEAM"){
					$group = new avalanche_team_usergroup($myrow['id'], $this, $myrow);
				}else if($myrow["type"] == "MODULE"){
					$group = new avalanche_module_usergroup($myrow['id'], $this, $myrow);
				}else if($myrow["type"] == "PERSONAL"){
					$group = new avalanche_personal_usergroup($myrow['id'], $this, $myrow);
				}else if($myrow["type"] == "PUBLIC"){
					$group = new avalanche_public_usergroup($myrow['id'], $this, $myrow);
				}else{
					throw new IllegalArgumentException("undefined usergroup type");
				}
				$this->_usergroup_cache->put($group->getId(), $group);
				return $group;
			}else{
				return false;
			}
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsersFor($groupId){
		$group = $this->getUsergroup($groupId);
		$ret = $group->getAllUsersIn();
		return $ret;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	// a cache to store which groups a user is in
	private $_user_to_groups;
	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsergroupsFor($userId){
		if(!is_array($this->_user_to_groups->get($userId))){
			$visitor = $this->_visitor_manager->getVisitor("getAllUsergroupsFor");
			$visitor->init($userId);
			$groups = $this->execute($visitor);
			$this->_user_to_groups->put($userId, $groups);
		}
		return $this->_user_to_groups->get($userId);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns true if the user with id of $userId has
	// the $perm permission
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function hasPermissionHuh($userId, $perm) {
		if(!$userId){
			$userId = $this->getActiveUser();
		}
		$ret = false;
		if($userId){
			$groups = $this->getAllUsergroupsFor($userId);
			for($i=0;$i<count($groups);$i++){
				if($groups[$i]->hasPermissionHuh($perm)){
					$ret = true;
				}
			}
		}
		return $ret;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	/////////////  END USERGROUP FUNCTIONS  //////////////////
	/////////////   BEGIN USER FUNCTIONS   //////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// adds a user to the users table and links it with the
	// default usergroup
	// only one user of a username is allowed, so it
	// only adds a user if the username is not already there.
	// returns true if this function adds a user to the db
	//         false otherwise
	// $username is the username of the new user
	// $password is the password of the new user
	// Notes:
	// notifies all modules of add user attempt and verdict
	//////////////////////////////////////////////////////////
	function addUser($username, $password, $email=false){
		if(!is_string($username) || strlen($username) == 0){
			throw new IllegalArgumentException("argument \$username to " . __METHOD__ . " must be a string");
		}
		if(!is_string($password)){
			throw new IllegalArgumentException("argument \$password to " . __METHOD__ . " must be a string");
		}
		if(!(is_string($email) || $email === false)){
			throw new IllegalArgumentException("argument \$email to " . __METHOD__ . " must be a string");
		}
		if(is_object($this->account_obj) && (count($this->getAllUsers())-1) >= $this->account_obj->maxUsers()){
			throw new CannotAddUserException("User quota of " . $this->account_obj->maxUsers() . " users already met");
		}else
        	if($this->hasPermissionHuh($this->loggedInHuh(), "add_user")){
			// verify add user
			$verify = 0;
			if($this->findUser($username)){
				throw new CannotAddUserException("user \"$username\" already exists.");
			}else
			if(strlen($email) && $this->findUserByEmail($email)){
				throw new CannotAddUserException("email \"$email\" is in use by another user.");
			}else{
			        $sql = "INSERT INTO " . $this->PREFIX() . "users (username, password, email) VALUES (\"".$username."\",\"".$password."\",\"" . $email . "\")";
			        $result = $this->mysql_query($sql);
				$verify = mysql_insert_id();
				$user_id = mysql_insert_id();
				$default_grp = $this->getVar("USERGROUP");
				$all_grp = $this->getVar("ALLUSERS");
				//get id of new user
			        $sql = "INSERT INTO " . $this->PREFIX() . "user_link (user_id, group_id) VALUES (\"".$user_id."\",\"".$default_grp."\")";
				$verify = mysql_insert_id() && $verify;
			        $this->mysql_query($sql);
			        $sql = "INSERT INTO " . $this->PREFIX() . "user_link (user_id, group_id) VALUES (\"".$user_id."\",\"".$all_grp."\")";
				$verify = mysql_insert_id() && $verify;
			        $this->mysql_query($sql);

				// notify all modules with new username and if add went ok
				$count = $this->getModuleCount();
				for($i=0; $i<$count; $i++){
					$module = $this->getModuleAt($i);
					$module->addUser($user_id, $verify);
				}
			}
			return $user_id;
		}else{
			throw new CannotAddUserException("You do not have permission to add users.");
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getUser($userId){
		if(is_object($this->_user_cache->get($userId))){
			return $this->_user_cache->get($userId);
		}
		if($userId == -1){
			return new avalanche_system_user($this);
		}
		$ret = array();
		$ok = false;
	        $result = $this->mysql_query("SELECT id FROM " . $this->PREFIX() . "users WHERE id='$userId'");
	        $count = mysql_num_rows($result);
		while ($myrow = mysql_fetch_array($result)) {
			$ok = true;
			$user = new avalanche_user((int)$myrow['id'], $this);
			$ret = $user;
	        }

		if($ok){
			$this->_user_cache->put($user->getId(), $user);
		        return $ret;
		}else{
			throw new DatabaseException("cannot find user number $userId");
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// updates a username
	//////////////////////////////////////////////////////////
	function updateUsername($user_id, $new_name){
		$user = $this->getUser($user_id);
		return $user->username($new_name);
	}


	//returns the path of the image up until the root.
	// so $this->ROOT() . $this->getAvatar or HOSTURL . $this->getAvatar are appropriate
	function getAvatar($user_id=false){
		if(!$user_id){
			$user_id = $this->getActiveUser();
		}
		$user = $this->getUser($user_id);
		if(strlen($user->avatar()) > 0){
			return $this->APPPATH() . $this->INCLUDEPATH() . "echo.avatar.in.database.php?account=" . $this->ACCOUNT() . "&user_id=$user_id";
		}
		$realDir = $this->APPPATH() . "images/avatar/";
		$full_name_images = glob($this->ROOT() . "$realDir{*.gif,*.jpg,*.GIF,*.JPG,*.jpeg,*.JPEG}", GLOB_BRACE);
		foreach ($full_name_images as $file) {
			$ext = substr($file, strrpos($file, "."));
			$name = substr($file, 0, strrpos($file, "."));
			$name = substr($name, strrpos($name, "/")+1);
			$should_be = "user_" . $user_id;
			if($name == $should_be){
				return $realDir . "user_" . $user_id . $ext;
			}
		}
		return $this->defaultAvatar();
	}

	function getAvatarContents($user_id){
		$filename = $this->HOSTURL() . $this->getAvatar($user_id);
		return file_get_contents($filename);
	}


	//////////////////////////////////////////////////////////
	// returns a path to the default avatar from the web root
	//////////////////////////////////////////////////////////
	function defaultAvatar(){
		return $this->APPPATH() . "images/no_avatar.gif";;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// deletes the user at the given user_id
	// if the user is logged in, then he is loggedOut
	// the user is deleted from all user_groups as well
	// Notes:
	// notifies all modules of delete user attempt and verdict
	//////////////////////////////////////////////////////////
	function deleteUser($user_id){
		//deletes the user from the database (including users table and user_link table and loggedInHuh)
		//returns teh number of rows affected in teh user table
		// should return true or false  (will return >1 if more that one user has same username (ERROR))
		// returns true is user deleted
		// returns false if user not found or not deleted

	        $verify = false;
		$default_user      = $this->getVar("USER");
		// we can't delete the guest user, and we can't delete ourself
		if($this->hasPermissionHuh($this->loggedInHuh(), "del_user") &&
		   $user_id != $default_user &&
		   $user_id != $this->loggedInHuh()){
	        	$sql = "DELETE FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='$user_id'";
		        $result = $this->mysql_query($sql);

		        $sql = "DELETE FROM " . $this->PREFIX() . "user_link WHERE user_id='$user_id'";
		        $result = $this->mysql_query($sql);

		        $sql = "DELETE FROM " . $this->PREFIX() . "users WHERE id='$user_id'";
		        $result = $this->mysql_query($sql);
			if($result){
		        	$this->_user_cache->clear($user_id);
				$verify = true;
			}else{
			        $verify = false;
			}

			//////////////////////////////////////////////////////////
			// If did delete user, then notify all modules with	//
			// username and verification of delete			//
			//////////////////////////////////////////////////////////
			if($verify){
				$count = $this->getModuleCount();
				for($i = 0; $i < $count; $i++){
					$currMod = $this->getModuleAt($i);
					$currMod->deleteUser($user_id);
				}
			}
		}
		return $verify;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// disables the user at the given user_id
	// cannot disable the default user
	// Notes:
	// notifies all modules of disabled user attempt and verdict
	//////////////////////////////////////////////////////////
	function disableUser($user_id){
		$user = $this->getUser($user_id);
		$verify = $user->disable();

		//////////////////////////////////////////////////////////
		// If did disable user, then notify all modules with	//
		// username and verification of delete			//
		//////////////////////////////////////////////////////////
		if($verify){
			$count = $this->getModuleCount();
			for($i = 0; $i < $count; $i++){
				$currMod = $this->getModuleAt($i);
				$currMod->disableUser($user_id);
			}
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// enables the user at the given user_id
	// cannot disable the default user
	// Notes:
	// notifies all modules of disabled user attempt and verdict
	//////////////////////////////////////////////////////////
	function enableUser($user_id){
		$user = $this->getUser($user_id);
		$verify = $user->enable();

		//////////////////////////////////////////////////////////
		// If did enable user, then notify all modules with	//
		// username and verification of delete			//
		//////////////////////////////////////////////////////////
		if($verify){
			$count = $this->getModuleCount();
			for($i = 0; $i < $count; $i++){
				$currMod = $this->getModuleAt($i);
				$currMod->enableUser($user_id);
			}
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns the user id if the user exists in the db (users tbl)
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function findUser($argname){
	        $result = $this->mysql_query("SELECT id FROM " . $this->PREFIX() . "users WHERE username='$argname'");
	        $fields = mysql_num_fields($result);
	        while ($myrow = mysql_fetch_array($result)) {
                        return (int)$myrow["id"];
	        }
	        return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns the user id if the email exists in the db (users tbl)
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function findUserByEmail($argname){
	        $result = $this->mysql_query("SELECT id FROM " . $this->PREFIX() . "users WHERE email='$argname'");
	        $fields = mysql_num_fields($result);
	        while ($myrow = mysql_fetch_array($result)) {
                        return (int)$myrow["id"];
	        }
	        return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns an array containing all user id's
	//////////////////////////////////////////////////////////
	function getAllUsers($start=false, $max=false){
		if(!$start){
			$start = 0;
		}
		if(!$max){
			$limit = "";
		}else{
			$limit = "LIMIT $start,$max";
		}
		$ret = array();
	        $result = $this->mysql_query("SELECT id FROM " . $this->PREFIX() . "users ORDER BY username $limit");
	        while ($myrow = mysql_fetch_array($result)) {
			$ret[] = new avalanche_user((int)$myrow["id"], $this);
	        }
	        return $ret;
	}


	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	// searches the first, middle, and last name, and the email
	// for $text
	function getAllUsersMatching($text){
		$ret = array();
		$text = addslashes($text);

		$like = "1 ";
		$texts = explode(" ", $text);
		foreach($texts as $text){
			$like .= "AND (first LIKE '%$text%' OR middle LIKE '%$text%' OR last LIKE '%$text%' OR username LIKE '%$text%' OR email LIKE'%$text%') ";
		}
		$sql = "SELECT id FROM " . $this->PREFIX() . "users WHERE $like";
	        $result = $this->mysql_query($sql);
	        while ($myrow = mysql_fetch_array($result)) {
			$ret[] = new avalanche_user((int)$myrow["id"], $this);
	        }
	        return $ret;
	}

	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	// searches the name, description, and keywords
	// for $text
	function getAllTeamsMatching($text){
		$ret = array();
		$text = addslashes($text);

		$like = "type='" . avalanche_usergroup::$TEAM . "' ";
		$texts = explode(" ", $text);
		foreach($texts as $text){
			$like .= "AND (name LIKE '%$text%' OR description LIKE '%$text%' OR keywords LIKE '%$text%') ";
		}
		$sql = "SELECT * FROM " . $this->PREFIX() . "usergroups WHERE $like";
	        $result = $this->mysql_query($sql);
	        while ($myrow = mysql_fetch_array($result)) {
			$ret[] = $this->getUsergroup((int)$myrow["id"]);
	        }
	        return $ret;
	}

	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	/////////////     END USER FUNCTIONS    //////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns the installation folder of the
	// current skin avalanche is using.
	//////////////////////////////////////////////////////////
	function currentSkin(){
		return $this->_currentSkin;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// deactivates the module
	//////////////////////////////////////////////////////////
	function deactivateModule($modId){
		trigger_error("deactivateModule(\$modId) not yet defined", E_USER_ERROR);
		if(!$userId){
			$userId = $this->loggedInHuh();
		}
		if($modId){
			$offset = $this->getVar("ACTIVE_OFFSET");
			if($offset){
//				$datetime = date("Y-m-d H:i:s", $last_allowed);
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns the installation folder of the
	// default skin avalanche is using
	//////////////////////////////////////////////////////////
	function defaultSkin(){
		return $this->_defaultSkin;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////





	//////////////////////////////////////////////////////////
	// returns the number of modules in avalanche's mod list
	//////////////////////////////////////////////////////////
	function getModuleCount(){
		return count($this->_allModules->enum());
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////




	//////////////////////////////////////////////////////////
	// returns the number of skins in avalanche's skin list
	//////////////////////////////////////////////////////////
	function getSkinCount(){
		return count($this->_allSkins->enum());
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////




	//////////////////////////////////////////////////////////
	// returns the indexth module
	// static indexing and order is only guarenteed for
	// this page load
	//////////////////////////////////////////////////////////
	function getModuleAt($index){
		$my_array = $this->_allModules->enum();
		$mod = $my_array[$index];
		$i=0;
		foreach ($my_array as $key => $value) {
			if(is_string($value) && $i == $index){
				$filename = $this->ROOT() . $this->APPPATH() . $this->MODULES() . $value . "/module." . $value . ".php";
				if(file_exists($filename)){
					require_once $filename;
					$classname = "module_" . $value;
					if(class_exists($classname)){
						$obj = new $classname($this);
						$this->addModule($obj);
						return $this->_allModules->get($value);
					}else{
						throw new ClassDefNotFoundException($classname);
					}
				}else{
					// module definition absent
					throw new ModuleNotFoundException($mod);
				}
			}else if($i == $index){
				return $value;
			}
			$i++;
		}
		// module not installed
		throw new ModuleNotInstalledException($mod);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// returns the indexth skin
	// static indexing and order is only guarenteed for
	// this page load
	//////////////////////////////////////////////////////////
	function getSkinAt($index){
		$my_array = $this->_allSkins->enum();
		return $my_array[$index];
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////




	//////////////////////////////////////////////////////////
	// returns the module that is installed in the $mod folder
	//////////////////////////////////////////////////////////
	function getModule($mod){
	 	//input: $mod - the installation folder of the module to find
		//output: the module object in the $mod folder if found
		//output: false if not found
		if(is_string($this->_allModules->get($mod))){
			$folder = $this->_allModules->get($mod);
			$filename = $this->ROOT() . $this->APPPATH() . $this->MODULES() . $folder . "/module." . $folder . ".php";
			if(file_exists($filename) ){
				include_once $filename;
				$classname = "module_" . $folder;
				if(class_exists($classname)){
					$obj = new $classname($this);
					$this->addModule($obj);
				}else{
					throw new ClassDefNotFoundException($classname);
				}
				//this include file will add the module's class object to the avalanche object via the addModule() function.
			}else{
				// module definition absent
				throw new ModuleNotFoundException($mod);
			}
		}

		if(is_object($this->_allModules->get($mod))){
			return $this->_allModules->get($mod);
		}else{
			// module not installed
			throw new ModuleNotInstalledException($mod);
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////




	//////////////////////////////////////////////////////////
	// returns the name of the user with id of $argUserId
	// returns false if the user does not exist
	//////////////////////////////////////////////////////////
	function getName($argUserId){
		if($argUserId == -1){
			return array("title" => "",
					     "first" => "",
					     "middle" => "",
					     "last" => "SYSTEM");
		}
		if(($argUserId == $this->loggedInHuh()) ||
		   $this->hasPermissionHuh($this->loggedInHuh(), "view_name")){
			$result = $this->mysql_query("SELECT id, title, first, middle, last FROM " . $this->PREFIX() . "users WHERE id='$argUserId'");
			$fields = mysql_num_fields($result);
			while ($myrow = mysql_fetch_array($result)) {
				return array("title" => $myrow['title'],
					     "first" => $myrow['first'],
					     "middle" => $myrow['middle'],
					     "last" => $myrow['last']);
			}
			return array("title" => "",
					     "first" => "",
					     "middle" => "",
					     "last" => "");
		}else{
			return array("title" => "",
					     "first" => "",
					     "middle" => "",
					     "last" => "");
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns the password of the user with id of $argUserId
	// returns false if the user does not exist
	//////////////////////////////////////////////////////////
	function getPassword($argUserId){
		if(($argUserId == $this->loggedInHuh()) ||
		   $this->hasPermissionHuh($this->loggedInHuh(), "view_password")){
			$result = $this->mysql_query("SELECT id, password FROM " . $this->PREFIX() . "users WHERE id='$argUserId'");
			$fields = mysql_num_fields($result);
			while ($myrow = mysql_fetch_array($result)) {
				return $myrow['password'];
			}
			return false;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns the username of the user with id of $argUserId
	// returns false if the user does not exist
	//////////////////////////////////////////////////////////
	function getUsername($argUserId){
		if($argUserId == 0){
			return "guest";
		}else
		if($argUserId == -1){
			return "*SYSTEM*";
		}
		$result = $this->mysql_query("SELECT id, username FROM " . $this->PREFIX() . "users WHERE id='$argUserId'");
		$fields = mysql_num_fields($result);
		while ($myrow = mysql_fetch_array($result)) {
			return $myrow['username'];
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// changes the email of the user with id of $argUserId
	// returns false if the email is not changed
	//////////////////////////////////////////////////////////
	function updateEmail($argUserId, $newEmail){
		$user = $this->getUser($argUserId);
		return $user->email($newEmail);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns the username of the user with id of $argUserId
	// returns false if the user does not exist
	//////////////////////////////////////////////////////////
	function getEmail($argUserId){
		$user = $this->getUser($argUserId);
		return $user->email();
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns the skin that is installed in the $skn folder
	//////////////////////////////////////////////////////////
	function getSkin($skn){

		//input: $skn - the installation folder of the skin to find
		//output: the skin object in the $skn folder if found
		//output: false if not found
		if(is_string($this->_allSkins->get($skn))){
			$folder = $this->_allSkins->get($skn);
			include $this->ROOT() . $this->APPPATH() . $this->SKINS() . $folder . "/skin." . $folder . ".php";
			//this include file will add the module's class object to the avalanche object via the addModule() function.
		}
		if(!is_object($this->_allSkins->get($skn))){
			throw new SkinNotFoundException($skn);
		}else{
			return $this->_allSkins->get($skn);
		}

	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// returns the user id that is associated with the
	// inputted username and password.
	// returns false if no such user exists.
	//////////////////////////////////////////////////////////
	function getUserId($argUser, $argPass){
		$sql = "SELECT id FROM " . $this->PREFIX() . "users WHERE username='$argUser' AND password='$argPass'";
		$result = $this->mysql_query($sql);
		while ($myrow = mysql_fetch_array($result)) {
			return $myrow['id'];
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// renames the user at the given id iff there is
	// no other user by that username and if the current
	// logged in user has permission
	//////////////////////////////////////////////////////////
	function renameUser($userId, $new_name){
		$user = $this->getUser($userId);
		return $user->username($new_name);
	}

	//////////////////////////////////////////////////////////
	// updates the name of user with id of $argUserId
	// $new_name is an array with key's "title" "first" "middle" and "last
	//////////////////////////////////////////////////////////
	function updateName($argUserId, $new_name){
		$user = $this->getUser($argUserId);
		$user->title($new_name["title"]);
		$user->first($new_name["first"]);
		$user->middle($new_name["middle"]);
		return $user->last($new_name["last"]);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// updates the password of user with id of $argUserId
	//
	//////////////////////////////////////////////////////////
	function updatePassword($argUserId, $argPass){
		$user = $this->getUser($argUserId);
		return $user->password($argPass);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns "yyyy-mm-dd hh:mm:ss" of when user was last
	// active (if logged in)
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function lastActive($user_id=false){
		if($user_id === false){
			$user_id = $this->loggedInHuh();
		}
		$user = $this->getUser($user_id);
		$user->lastActive();
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns "yyyy-mm-dd hh:mm:ss" of when user was last
	// active (if logged in)
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function lastLoggedIn($user_id=false){
		if($user_id === false){
			$user_id = $this->loggedInHuh();
		}
		$user = $this->getUser($user_id);
		return $user->lastLoggedIn();
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// returns "yyyy-mm-dd hh:mm:ss" of when user was last
	// active (if logged in)
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function lastLoggedOut($user_id=false){
		if($user_id === false){
			$user_id = $this->loggedInHuh();
		}
		if($user_id == 0){
			return date("Y-m-d H:i:s", $this->getModule("strongcal")->gmttimestamp());
		}
		$user = $this->getUser($user_id);
		return $user->lastLoggedOut();
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// gets the contents of a variable stored in varList
	//////////////////////////////////////////////////////////
	function getVar($var){
		if($this->var_list_cache->get($var) === false){
			$sql = "SELECT * FROM " . $this->PREFIX() . "varlist WHERE var='$var'";
			$result = $this->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$this->var_list_cache->put($var, $myrow['val']);
			}else{
				return false;
			}
		}else{
			return $this->var_list_cache->get($var);
		}
		return $this->var_list_cache->get($var);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	private $mailman;
	//////////////////////////////////////////////////////////
	// should only call once at beginning of page load.
	// initializes avalanche variables
	//  - _recent_log_in
	//  - _allModules
	//  - _allSkins
	//  - _defaultSkin
	//  - _currentSkin
	//////////////////////////////////////////////////////////
	function __construct($root, $publichtml, $hosturl, $domain, $apppath, $secure, $includepath, $javascript, $modules, $skins, $library, $classloader, $host, $admin, $pass, $databasename, $prefix, $account=false){
		// the mailman
		$this->mailman = new SMTPMailMan();
		// a cache for the user objects
		$this->_user_cache = new HashTable();
		// a cache for usergroups
		$this->_usergroup_cache = new HashTable();
		// set the cache of mysql_queries
		$this->_query_cache = new SQLCache();
		// count how many mysql queries have been sent...
		$this->_query_count = 0;

		// false if not in account, ie, this is master avalanche
		if(is_object($account) && $account instanceof module_accounts_account){
			$this->ACCOUNT = $account->name();
			$this->account_obj = $account;
		}else{
			$this->ACCOUNT = false;
			$this->account_obj = false;
		}

		// top level filestructure variables
		$this->ROOT = $root;
		$this->PUBLICHTML = $publichtml;
		$this->HOSTURL = $hosturl;
		$this->DOMAIN = $domain;
		$this->APPPATH = $apppath;

		//set SECURE to 1 if cookies need to be sent over https connection
		$this->SECURE = $secure;

		// file structure variables
		$this->INCLUDEPATH = $includepath;
		$this->JAVASCRIPT = $javascript;
		$this->MODULES = $modules;
		$this->SKINS = $skins;
		$this->LIBRARY = $library;
		$this->CLASSLOADER = $classloader;

		// mysql variables
		$this->HOST = $host;
		$this->ADMIN = $admin;
		$this->PASS = $pass;
		$this->DATABASENAME = $databasename;
		$this->PREFIX = $prefix;

		// initialize everything else (modules, skins, visitors, etc)
		$this->_cookieJar = new CookieJar();
		$this->_recent_log_in  = -1;
		$this->_allModules = new HashTable();
		$this->_allSkins = new HashTable();
		$this->_user_to_groups = new HashTable();
		$sql = "SELECT * FROM " . $this->PREFIX() . "modules";
		$result = $this->mysql_query($sql);
		while ($myrow = mysql_fetch_array($result)) {
			$this->_allModules->put($myrow['folder'], $myrow['folder']);
		}

		$sql = "SELECT * FROM " . $this->PREFIX() . "skins";
		$result = $this->mysql_query($sql);
		while ($myrow = mysql_fetch_array($result)) {
			$this->_allSkins->put($myrow['folder'], $myrow['folder']);
		}


		// load variables
		$this->var_list_cache = new HashTable();
		$sql = "SELECT * FROM " . $this->PREFIX() . "varlist";
		$result = $this->mysql_query($sql);
		while($row = mysql_fetch_array($result)){
			if(get_magic_quotes_runtime()){
				$row['val'] = stripslashes($row['val']);
			}
			$this->var_list_cache->put($row['var'], $row['val']);
		}

		// get values for defaults
		$this->_defaultSkin = $this->getVar("SKIN");
		$this->_currentSkin = $this->_defaultSkin;
		$this->_visitor_manager = new avalanche_visitormanager;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	// returns teh visitor manager
	function visitorManager(){
		return $this->_visitor_manager;
	}


	// runs the visitor on avalanche case
	function execute($visitor){
		return $visitor->visit($this);
	}

	//////////////////////////////////////////////////////////
	//
	// Notes:
	// unlinks the user with the group
	//////////////////////////////////////////////////////////
	function linkUser($userId, $groupId){
		$group = $this->getUsergroup($groupId);
		$group->linkUser($userId);
		$this->_user_to_groups->clear($userId);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets a cookie
	//////////////////////////////////////////////////////////
	function setCookie($name, $value, $flag=false){
		// IE will crash when flag is true
		$name = $this->PREFIX() . $this->ACCOUNT() . "_" . $name;
		$ret = $this->_cookieJar->setCookie($name,$value, time() + 3600*24*365, "/", $this->DOMAIN(), $this->SECURE(), $flag);
		return $ret;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// sets a cookie
	//////////////////////////////////////////////////////////
	function deleteCookie($name, $flag=false){
		// IE will crash when flag is true
		$name = $this->PREFIX() . $this->ACCOUNT() . "_" . $name;
		$ret = $this->_cookieJar->deleteCookie($name, $flag);
		return $ret;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// gets a cookie
	//////////////////////////////////////////////////////////
	function getCookie($name){
		$name = $this->PREFIX() . $this->ACCOUNT() . "_" . $name;
		return $this->_cookieJar->getCookie($name);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// logs in the user that is associated with a username of
	// $argUser and a password of $argPass
	// returns true if the user is logged in
	// returns false otherwise
	// Notes:
	// user must support cookies
	// notifies all modules of user log in attempt and verdict
	// must be called prior to sending headers
	//////////////////////////////////////////////////////////
	function logIn($argUser, $argPass) {
		//function must be called before headers are sent
		$strongcal = $this->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		$loginSuccess = "no";

		//checks if i'm already logged in, if so, i don't want to relog in
		if($this->needLogIn()){
			$result = $this->mysql_query("SELECT id FROM " . $this->PREFIX() . "users WHERE username='$argUser' AND password='$argPass'");
			//checks if my username and password are valid
			while ($myrow = mysql_fetch_array($result)) {
				$loginSuccess = "yes";
			}

			if($loginSuccess == "yes"){
				$datetime = date("Y-m-d H:i:s", $gmtimestamp);
				$user_id = $this->getUserId($argUser, $argPass);
				$sql = "INSERT INTO " . $this->PREFIX() . "loggedinusers (ip, user_id, last_active) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "','$user_id','$datetime')";
				$result = $this->mysql_query($sql);

				$sql = "UPDATE " . $this->PREFIX() . "users SET last_ip='" . $_SERVER['REMOTE_ADDR'] . "', last_login='$datetime' WHERE id='$user_id'";
				$result = $this->mysql_query($sql);

				// $this->_recent_log_in update this var with the logged in user's id. more info at top of class
				$this->_recent_log_in = $this->getUserId($argUser, $argPass);
				$this->setCookie("user_id", $this->_recent_log_in);

				if($result){
					$verify=1;
					$this->_recent_log_in = $user_id;
				}else{
					$verify=0;
				}
				//login was successful, so notify modules
				for($i = 0; $i < $this->getModuleCount(); $i++){
					$temp = $this->getModuleAt($i);
					$temp->userLoggedIn($argUser, $verify);
				}
				return true;
			}
			return false;
		}else{
			//login was unsuccessful, so notify modules
			for($i = 0; $i < $this->getModuleCount(); $i++){
				$temp = $this->getModuleAt($i);
				$temp->userLoggedIn($argUser, false); //false b/c it's a bad login
			}
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// logs in user with default user
	// Notes:
	// user must support cookies
	// notifies all modules of user log in attempt and verdict
	// must be called prior to sending headers
	//////////////////////////////////////////////////////////
	function logInDefault ($temporary_login = false) {
		//function must be called before headers are sent
		$argGrpId  = $this->getVar("USERGROUP");
		$argUserId = $this->getVar("USER");

		$this->_flag_for_temp_log_in = $temporary_login;

		if($this->needLogIn()){
		  $result = false;
			if(!$temporary_login){
				$ip = getenv('REMOTE_ADDR');
				$gmtimestamp = $strongcal->gmttimestamp();
				$datetime = date("Y-m-d H:i:s", $gmtimestamp);
				$sql = "INSERT INTO " . $this->PREFIX() . "loggedinusers (user_id, last_active) VALUES ('$argUserId','$datetime')";
				$result = $this->mysql_query($sql);
			}

			$this->_recent_log_in = $argUserId;


			if(!$temporary_login){
				$this->setCookie("user_id",$argUserId);
			}

			if($result && !$temporary_login){
				$verify=1;
			}else{
				$verify=0;
			}
			$this->_recent_log_in = $argUserId;
			return $verify;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sees if the user is logged into the db
	// returns the user id if user id is found in loggedInHuh
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function loggedInHuh($userId = false, $flag = false){
		// flag is true when IE is going to crash
		if(!$userId){
			if($this->_recent_log_in == -1){
				$argUser = "";
				if(isset($_COOKIE[$this->PREFIX() . $this->ACCOUNT() . "_user_id"])){
					$argUser = $_COOKIE[$this->PREFIX() . $this->ACCOUNT() . "_user_id"];
				}
				$result = $this->mysql_query("SELECT * FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='" . $argUser . "'");
				while ($myrow = mysql_fetch_array($result)) {
					$this->_recent_log_in = (int)$myrow['user_id'];
					return (int)$myrow['user_id'];
				}


				//if user is not logged in... delete any cookie if possible
				if(!headers_sent()){
					$this->deleteCookie("user_id",$flag);
				}
				return false;
			}else{
				return (int)$this->_recent_log_in;
			}
		}else{
			$argUser = 0;
			$result = $this->mysql_query("SELECT * FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='" . $userId . "'");
			while ($myrow = mysql_fetch_array($result)) {
				return (int)$myrow['user_id'];
			}
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	// returns the user id of the currently logged in user, or
	// the user id of the guest user if the user is not logged in
	function getActiveUser(){
		if($this->loggedInHuh()){
			return (int) $this->loggedInHuh();
		}else{
			return (int) $this->getVar("USER");
		}
	}


	//////////////////////////////////////////////////////////
	// sees if the user is logged into the db
	// returns the user id if user id is found in loggedInHuh
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function numLoggedIn(){
		$result = $this->mysql_query("SELECT count(*) AS total FROM " . $this->PREFIX() . "loggedinusers");
		while ($myrow = mysql_fetch_array($result)) {
			return $myrow['total'];
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	// sees if the user is logged into the d
	// returns the user id if user id is found in loggedInHuh
	// retutns false otherwise
	// Notes:
	// user must support cookies
	//////////////////////////////////////////////////////////
	function loggedInUsers($start=false, $max=false){
		$argUser = 0;
		if(!$start){
			$start = 0;
		}
		if(!$max){
			$limit = "";
		}else{
			$limit = "LIMIT $start,$max";
		}
		$result = $this->mysql_query("SELECT user_id AS id, ip, last_active FROM " . $this->PREFIX() . "loggedinusers ORDER BY 'last_active' DESC $limit");
		$ret = array();
		while ($myrow = mysql_fetch_array($result)) {
			$ret[] = $myrow;
		}
		return $ret;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// logs out the active user.
	// notifies all modules of user log out attempt and verdict
	// must be called prior to sending headers
	// function must be called before headers are sent
	// logs the user out by deleting his cookie, sucker.
	// user must have cookie set.
	//////////////////////////////////////////////////////////
	function logOut(){
		$strongcal = $this->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		$user_id = $this->loggedInHuh();
		if(!$this->_flag_for_temp_log_in){
			$this->setCookie("user_id","");
		}

		if(!$this->_flag_for_temp_log_in){
	        	$sql = "DELETE FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='$user_id'";
		        $result = $this->mysql_query($sql);

			$datetime = date("Y-m-d H:i:s", $gmtimestamp);
			$sql = "UPDATE " . $this->PREFIX() . "users SET last_logout='$datetime' WHERE id='$user_id'";
			$result = $this->mysql_query($sql);
		}

		$this->_recent_log_in = false;

		//notify modules of logout
		for($i = 0; $i < $this->getModuleCount(); $i++){
			$temp = $this->getModuleAt($i);
			$temp->userLoggedOut($this->loggedInHuh(), true); // will return 1+/0 or true/false. basically, if the logout was successful, than at least one row was deleted
		}

	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// log out a user of a specific id
	// Notes:
	// notifies all modules of user log out attempt and verdict
	//////////////////////////////////////////////////////////
	function logOutSpecId($argId){
		if($argId){
			$sql = "DELETE FROM " . $this->PREFIX() . "loggedinusers WHERE user_id='$argId'";
			$result = $this->mysql_query($sql);
		}

		//notify modules of logout
		for($i = 0; $i < $this->getModuleCount(); $i++){
			$temp = $this->getModuleAt($i);
			$temp->userLoggedOut($argId, $argId);
		}
		if($result){
			return true;
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// $argMod - the install folder of the module in question
	// returns true  if the module $argMod is installed
	// returns false if the module $argMod is not installed
	//////////////////////////////////////////////////////////
	function moduleInstalledHuh($argMod){
		$cnt = $this->getModuleCount();
		for($i=0; $i < $cnt; $i++){
			$tempMod = $this->getModuleAt($i);
			if($argMod==$tempMod->folder()){
				return true;
			}
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns true if the user is logged in
	// returns false if the user is logged in
	//////////////////////////////////////////////////////////
	function needLogIn(){
		return !($this->loggedInHuh()+0);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////
	public function resetPasswordFor($email){
		if(!is_string($email)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$email = addslashes($email);
		$sql = "SELECT * FROM " . $this->PREFIX() . "users WHERE email = '$email'";
		$result = $this->mysql_query($sql);
		$found = false;
		while($myrow = mysql_fetch_array($result)){
			$user_id = (int)$myrow["id"];
			$user = $this->getUser($user_id);
			$user->resetPassword();
			$user->needToResetPassword(true);
			$found = true;
		}
		return $found;
	}

	//////////////////////////////////////////////////////////
	// sets the user active if he is logged in.
	// if he is beyond allowed inactive time, he is
	// logged out.
	// function must be called prior to sending headers.
	// returns true if user is active
	// false on if user is inactive and/or logged out
	//////////////////////////////////////////////////////////
	function setActive(){
		$strongcal = $this->getModule("strongcal");
		$gmtimestamp = $strongcal->gmttimestamp();
		if($this->loggedInHuh()){
			if($this->active()){
				$sql = "UPDATE " . $this->PREFIX() . "loggedinusers SET last_active = '" . date("Y-m-d H:i:s", $gmtimestamp) . "' WHERE user_id='" . $this->loggedInHuh() . "'";
				$result = $this->mysql_query($sql);
				if($result){
					return true;
				}
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets the activity of avalanche
	// returns true if user is activity is reset
	// returns false if activity is not set
	//////////////////////////////////////////////////////////
	function setActivity($val){
		if($this->hasPermissionHuh($this->loggedInHuh(), "active")){
			$sql = "UPDATE " . $this->PREFIX() . "varlist SET val = \"$val\" WHERE var = \"ACTIVITY\"";
			$result = $this->mysql_query($sql);
			$this->var_list_cache = new HashTable();
			return true;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets the organization of avalanche
	// returns true if user is organization is reset
	// returns false if activity is not set
	//////////////////////////////////////////////////////////
	function setOrganization($val){
		if($this->hasPermissionHuh($this->loggedInHuh(), "view_cp")){
			$sql = "UPDATE " . $this->PREFIX() . "varlist SET val = \"$val\" WHERE var = \"ORGANIZATION\"";
			$result = $this->mysql_query($sql);
			$this->var_list_cache = new HashTable();
			return true;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets the activity of avalanche
	// returns true if user is activity is reset
	// returns false if activity is not set
	//////////////////////////////////////////////////////////
	function setActivityOffset($val){
		if($this->hasPermissionHuh($this->loggedInHuh(), "active_offset")){
			$sql = "UPDATE " . $this->PREFIX() . "varlist SET ACTIVE_OFFSET = '$val'";
			$result = $this->mysql_query($sql);
			$this->var_list_cache = new HashTable();
			return true;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets the default Skin for avalanche if $argSkin is
	// installed
	// returns true on success,
	// false on failure
	//////////////////////////////////////////////////////////
	function setDefaultSkin($argSkin){
		if($this->hasPermissionHuh($this->loggedInHuh(), "change_default_skin")){
			if($this->skinInstalledHuh($argSkin)){
				$sql = "UPDATE " . $this->PREFIX() . "varlist SET val = \"$argSkin\" WHERE var = \"SKIN\"";
				$this->var_list_cache = new HashTable();
				$result = $this->mysql_query($sql);
				$this->_defaultSkin = $argSkin;
				if($result){
					return true;
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// sets the current skin of avalanche if $argSkin is
	// installed
	// returns true;
	//////////////////////////////////////////////////////////
	function setSkin($argSkin){
		if(skinInstalledHuh($argSkin)){
			$this->_currentSkin = $argSkin;
			return true;
		}else{
			return false;
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// checks to see if $argSkn is installed or not
	// returns true if $argSkn is installed
	// returns false otherwise
	//////////////////////////////////////////////////////////
	function skinInstalledHuh($argSkin){
		$count = $this->getSkinCount();
		for($i=0; $i < $count; $i++){
			$tempSkin = $this->getSkinAt($i);
			$tempSkin;
			if($argSkin==$tempSkin->folder()){
				return true;
			}
		}
		return false;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// updates the permissions of user with id of $argUserId
	// DEPRECIATED
	//////////////////////////////////////////////////////////
	function updateGroupName($argGroupId, $new_name){
		$group = $this->getUsergroup($argGroupId);
		return $group->rename($new_name);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////
	// updates the permissions of user with id of $argUserId
	//
	//////////////////////////////////////////////////////////
	function updatePermissions($argGroupId, $arrayPerm){
		$group = $this->getUsergroup($argGroupId);
		return $group->updatePermissions($arrayPerm);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	// returns true if the user is in the usergroup
	//
	//////////////////////////////////////////////////////////
	function userInGroupHuh($userId, $groupId){
		$group = $this->getUsergroup($groupId);
		return $group->userInGroupHuh($userId);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////
	//
	// Notes:
	// unlinks the user with the group
	//////////////////////////////////////////////////////////
	function unlinkUser($userId, $groupId){
		$group = $this->getUsergroup($groupId);
		$this->_user_to_groups->clear($userId);
		return $group->unlinkUser($userId);
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////



	private $_mysql_link = false;
	// queries mysql and caches the result if appropriate
	function mysql_query($sql, $verbose=false){
		$verbose = false;
		$sql = trim($sql);
		// if the link's not in the cache, then make one
		$this->_mysql_link = mysql_connect($this->HOST(), $this->ADMIN(), $this->PASS());
		if($this->_mysql_link === false){
			throw new DatabaseException("could not connect to MySQL");
		};
		if(!mysql_select_db($this->DATABASENAME(),$this->_mysql_link)){
			throw new DatabaseException("could not select database: " . $this->DATABASENAME());
		};
		// check the cache
		if($this->_query_cache->get($sql)){
			if($verbose)echo "found in cache<br>";
			$result = $this->_query_cache->get($sql);
			if(mysql_num_rows($result)){
				if($verbose) echo ": seeking to 0";
				mysql_data_seek($result, 0);
			}
			if($verbose) echo "<br>";
		}else{
			if($verbose) echo "not in cache";
			$this->_query_count++;
			$result = mysql_query($sql, $this->_mysql_link);
			if(mysql_error()){
				if($verbose) echo "mysql_error: " . mysql_error() . "<br>";
				throw new DatabaseException(mysql_error());
			}
			if(strpos($sql, "SELECT") === 0){
				if($verbose) echo ": select: $sql<br>";
				//if(true) echo "<span style='color: #0000CC;'>$sql</span><br>";
				$this->_query_cache->put($sql, $result);
			}else{
				if($verbose) echo ": not select: $sql<br>";
				//if(true) echo "<span style='color: #CC0000;'>$sql</span><br>";
				if($verbose) echo "clearing cache<br>";
				$this->_query_cache->clear($sql);
			}
		}
		return $result;
	}

	function reset(){
		$this->_query_cache->reset();
	}

	// returns value of mysql_insert_id
	function mysql_insert_id(){
		$this->_mysql_link = mysql_connect($this->HOST(), $this->ADMIN(), $this->PASS());
		return mysql_insert_id($this->_mysql_link);
	}

	function getQueryCount(){
		return $this->_query_count;
	}


	//gets a value from a table.
	//$table = the table to get the value from
	//$col = the column of the $table to get the value from
	//$Id = the value to look for in the $colForId column
	function get($table, $col, $Id, $colForId){
		$sql = "SELECT $colForId, $col FROM " . $this->PREFIX() . $table . " WHERE $colForId ='$Id'";
		$result = $this->mysql_query($sql);
		while ($myrow = mysql_fetch_array($result)) {
			if($Id == $myrow[$colForId]){  // just in case. should always be true
				return $myrow[$col];
			}
		}
		return false;
	}

	// cron
	public function cron(){
		$ret = "";
		$count = $this->getModuleCount();
		$prefix = "avalanche: ";
		$modules = array();
		for($mod_num=0;$mod_num<$count;$mod_num++){
			$module = $this->getModuleAt($mod_num);
			$modules[] = $module;
		}
		foreach($modules as $module){
			$ret .= "including module \"" .$module->folder() . "\"\n";
			$ret .= $module->cron();
		}
		return $ret;
	}

	public function mail($to, $subj, $body, $headers = false, $params = false){
		return $this->mailman->mail($to, $subj, $body, $headers, $params);
	}

	public function HTMLmail($to, $subj, $body, $headers = false, $params = false){
		// To send HTML mail, the Content-type header must be set
		$true_headers  = 'MIME-Version: 1.0' . "\n";
		$true_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$true_headers .= $headers;
		return $this->mailman->mail($to, $subj, $body, $true_headers, $params);
	}


	public function getMailMan(){
		return $this->mailman;
	}

	public function setMailMan(MailMan $m){
		$this->mailman = $m;
	}
}
?>