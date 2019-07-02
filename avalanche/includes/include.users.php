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

class avalanche_user{

	static public $CONTACT_EMAIL = 1;
	static public $CONTACT_SMS = 2;

	//////////////////////////////////////////////////////////
	// returns the id of this usergroup
	//////////////////////////////////////////////////////////
	public function getId(){
		return (int) $this->_id;
	}

	// avalanche
	protected $avalanche = false;
	// my id;
	private $_id;
	// user data
	private $_user_data;
	// avatar loaded
	private $_avatar_loaded = false;
	// if the user is loaded
	private $_loaded = false;
	//////////////////////////////////////////////////////////
	// should only call once at beginning of page load.
	// initializes avalanche variables
	//  - _recent_log_in
	//  - _allModules
	//  - _allSkins
	//  - _defaultSkin
	//  - _currentSkin
	//////////////////////////////////////////////////////////
	public function __construct($userId, $avalanche, $myrow=false){
		$this->avalanche = $avalanche;
		if(!is_int($userId)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an integer");
		}
		$this->_id = $userId;

		if($myrow === false){
			$this->_user_data = false;
		}else if(is_array($myrow)){
			$this->_user_data = $myrow;
			if(isset($myrow["avatar"])){
				$this->_avatar_loaded = true;
			}
		}else{
			throw new IllegalArgumentException("optional argument to " . __METHOD__ . " must be an array");
		}
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	public function reload(){
		if($this->_loaded === false){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "users WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			if($myrow = mysqli_fetch_array($result)){
				$this->_user_data = $myrow;
				$this->_loaded = true;
			}else{
				throw new Exception("cannot load user: " . $this->getId());
			}
		}
	}

	public function reloadAvatar(){
		if(!$this->_avatar_loaded){
			$sql = "SELECT id, avatar FROM " . $this->avalanche->PREFIX() . "users WHERE id='" . $this->getId() . "'";
			$result = $this->avalanche->mysql_query($sql);
			if($myrow = mysqli_fetch_array($result)){
				$this->_user_data["avatar"] = $myrow["avatar"];
				$this->_avatar_loaded = true;
			}else{
				throw new Exception("cannot load user: " . $this->getId());
			}
		}
	}

	// returns true if the user needs to create a new password
	public function needToResetPassword($n=0){
		$this->reload();
		if($n === 0){
			return $this->_user_data["need_new_pass"];
		}else{
			$user_id = $this->getId();
			if(!is_bool($n)){
				throw new IllegalArgumentException("argument \$n to " . __METHOD__ . " must be a boolean");
			}
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET `need_new_pass`=\"".$n."\" WHERE id='$user_id'";
			$this->avalanche->mysql_query($sql);
			$this->_user_data["need_new_pass"] = $n;
			return $n;
		}
	}

	// enable/disable public functions
	// disables a user
	// returns true if user changes from enabled to disabled
	public function disable(){
		if($this->enabled()){
			$user_id = $this->getId();
			$verify = false;
			$default_user = $this->avalanche->getVar("USER");
			if($avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "disable_user") && $user_id != $default_user){
				$sql = "UPDATE" . $this->avalanche->PREFIX() . "users SET disabled='1' WHERE user_id='$user_id'";
				$result = $this->avalanche->mysql_query($sql);
				if($result){
					$this->_user_data["disabled"] = "1";
					$verify = true;
				}else{
					$verify = false;
				}
			}
			return $verify;
		}else{
			return false;
		}
	}

	// enables the user if he is disabled
	// returns true if the user has switched to enabled from disabled
	public function enable(){
		if(!$this->enabled()){
			$verify = false;
			$default_user = $this->getVar("USER");
			$user_id = $this->getId();
			if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "disable_user")){
				$sql = "UPDATE" . $this->avalanche->PREFIX() . "users SET disabled='0' WHERE user_id='$user_id'";
				$result = $this->avalanche->mysql_query($sql);

				if($result){
					$this->_user_data["disabled"] = "0";
					$verify = true;
				}else{
					$verify = false;
				}

			}
			return $verify;
		}else{
			return false;
		}
	}

	// returns true if the user is enabled
	public function enabled(){
		$this->reload();
		return !$this->_user_data["disabled"];
	}

	public function avatar($new_avatar = false){
		$this->reloadAvatar();
		if($new_avatar === false){
			return $this->_user_data["avatar"];
		}
		$user_id = $this->getId();
		if(!is_string($new_avatar)){
			throw new IllegalArgumentException("argument \$new_avatar to " . __METHOD__ . " must be a string");
		}
        	if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name") ||
		   $this->avalanche->loggedInHuh() == $user_id){
			$new_avatar = addslashes($new_avatar);
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET `avatar`=\"".addslashes($new_avatar)."\" WHERE id='$user_id'";
			$this->avalanche->mysql_query($sql);
			$this->_user_data["avatar"] = $new_avatar;
			return $new_avatar;
		}else{
			return $this->_user_data["avatar"];
		}
	}

	// gets/sets username
	public function username($new_name = false){
		$this->reload();
		if($new_name === false){
			return $this->_user_data["username"];
		}
		$user_id = $this->getId();
		if(!is_string($new_name) || strlen($new_name) < 1){
			throw new IllegalArgumentException("argument \$username to " . __METHOD__ . " must be a string");
		}
        	if($this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "rename_user") ||
		   $this->avalanche->loggedInHuh() == $user_id){
			if(!$this->avalanche->findUser($new_name)){
			        $sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET `username`=\"".addslashes($new_name)."\" WHERE id='$user_id'";
				$this->avalanche->mysql_query($sql);
				$this->_user_data["username"] = $new_name;
			        return $new_name;
			}else{
				return $this->_user_data["username"];
			}
		}else{
			return $this->_user_data["username"];
		}
	}


	// gets/sets bio name
	public function bio($bio = false){
		$this->reload();
		if($bio === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["bio"];
		}else if($bio === false){
			return "";
		}
		if(!is_string($bio)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET bio = \"" . addslashes($bio) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["bio"] = $bio;
			return $this->bio();
		}else{
			return $this->bio();
		}
	}


	// gets/sets title name
	public function title($title = false){
		$this->reload();
		if($title === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["title"];
		}else if($title === false){
			return "";
		}
		if(!is_string($title)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET title = \"" . addslashes($title) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["title"] = $title;
			return $this->title();
		}else{
			return $this->title();
		}
	}

	// gets/sets first name
	public function first($first = false){
		$this->reload();
		if($first === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["first"];
		}else if($first === false){
			return "";
		}
		if(!is_string($first)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET first = \"" . addslashes($first) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["first"] = $first;
			return $this->first();
		}else{
			return $this->first();
		}
	}

	// gets/sets middle name
	public function middle($middle = false){
		$this->reload();
		if($middle === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["middle"];
		}else if($middle === false){
			return "";
		}
		if(!is_string($middle)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET middle = \"" . addslashes($middle) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["middle"] = $middle;
			return $this->middle();
		}else{
			return $this->middle();
		}
	}

	// gets/sets last name
	public function last($last = false){
		$this->reload();
		if($last === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["last"];
		}else if($last === false){
			return "";
		}
		if(!is_string($last)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->getActiveUser(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET last = \"" . addslashes($last) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["last"] = $last;
			return $this->last();
		}else{
			return $this->last();
		}
	}

	private function getEmailForContact(){
		$this->reload();
		return $this->_user_data["email"];
	}

	private function getSMSForContact(){
		$this->reload();
		return $this->_user_data["sms"];
	}


	// gets/sets email name
	public function email($email = false){
		$this->reload();
		if($email === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["email"];
		}else if($email === false){
			return "";
		}
		if(!is_string($email)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET email = \"" . addslashes($email) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["email"] = $email;
			return $this->email();
		}else{
			return $this->email();
		}
	}

	public function sms($sms = false){
		$this->reload();
		if($sms === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return $this->_user_data["sms"];
		}else if($sms === false){
			return "";
		}
		if(!is_string($sms)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET sms = \"" . addslashes($sms) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["sms"] =  $sms;
			return $this->sms();
		}else{
			return $this->sms();
		}
	}

	// gets/sets password name
	public function password($password = false){
		$this->reload();
		$this->needToResetPassword(false);
		if($password === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_password")){
			return $this->_user_data["password"];
		}else if($password === false){
			return "";
		}
		if(!is_string($password)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_password")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET password = \"" . addslashes($password) . "\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["password"] = $password;
			return $this->password();
		}else{
			return $this->password();
		}
	}

	public function resetPassword(){
		$os = $this->avalanche->getModule("os");
		$name = $os->getUsername($this->getId());
		$password = substr(md5($this->getId() . rand()), 0, 8);
		$argUserId = $this->getId();
		$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET password = \"$password\" WHERE id = \"$argUserId\"";
		$result = $this->avalanche->mysql_query($sql);
		$this->_user_data["password"] = $password;

		if(is_object($this->avalanche->ACCOUNTOBJ())){
			$acct = $this->avalanche->ACCOUNTOBJ()->name();
		}else{
			$acct = "www";
		}
		$body =  "$name,\n\nYour username and password for http://" . $acct . "." . $this->avalanche->DOMAIN() . " has been reset.\n\n";
		$body .= "Your username is: " . $this->username() . "\n";
		$body .= "Your password is: " . $password . "\n";
		$body .= "\n";
		$body .= "Thank you,\n The Inversion Bot";
		$this->contactEmail($this->avalanche->getUser(-1), "$name, your password has been reset", $body);
	}

	// returns the user's preferred form of contact as a
	// bitwise OR of the CONTACT static variables.
	public function preferredContact($preferred_contact = false){
		reload();
		if($preferred_contact === false && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_name")){
			return (int)$this->_user_data["preferred_contact"];
		}else if($preferred_contact === false){
			return 0;
		}
		if(!is_int($preferred_contact)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$argUserId = $this->getId();
		if($argUserId == $this->avalanche->loggedInHuh() ||
			$this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "change_name")){
			$sql = "UPDATE " . $this->avalanche->PREFIX() . "users SET preferred_contact = \"$preferred_contact\" WHERE id = \"$argUserId\"";
			$result = $this->avalanche->mysql_query($sql);
			$this->_user_data["preferred_contact"] = (int)$preferred_contact;
			return $this->preferredContact();
		}else{
			return $this->preferredContact();
		}

	}

	public function lastActive(){
		$this->reload();
		$user_id = $this->getId();
		if($this->avalanche->loggedInHuh($user_id)){
			$result = $this->avalanche->mysql_query("SELECT * FROM " . $this->avalanche->PREFIX() . "loggedinusers WHERE user_id='" . $user_id . "'");
			while ($myrow = mysqli_fetch_array($result)) {
				return $myrow['last_active'];
			}
			throw new Exception("cannot find user: $user_id");
		}else{
			return $this->lastLoggedOut();
		}
	}

	public function lastLoggedIn(){
		$this->reload();
		$user_id = $this->getId();
		$result = $this->avalanche->mysql_query("SELECT * FROM " . $this->avalanche->PREFIX() . "users WHERE id='" . $user_id . "'");
		while ($myrow = mysqli_fetch_array($result)) {
			return $myrow['last_login'];
		}
		throw new Exception("cannot find user: $user_id");
	}

	public function lastLoggedOut(){
		$this->reload();
		$user_id = $this->getId();
		$result = $this->avalanche->mysql_query("SELECT * FROM " . $this->avalanche->PREFIX() . "users WHERE id='" . $user_id . "'");
		while ($myrow = mysqli_fetch_array($result)) {
			return $myrow['last_logout'];
		}
		throw new Exception("cannot find user: $user_id");
	}

	// contacts the user on behalf of the user $from_user
	//  (0 is guest, -1 is SYSTEM)
	// sends $title and $body in the message
	// uses this user's preferred contact method: email, sms, or both
	public function contact($from_user, $title, $body){
		if(!is_object($from_user) || !($from_user instanceof avalanche_user)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a user object");
		}
		if(!is_string($title)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($body)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string");
		}
		$os = $this->avalanche->getModule("os");
		$email = $this->getEmailForContact();
		$sms = $this->getSMSForContact();

		// if it's set to email or not set at all
		if(strlen($email) && (((int)$this->avalanche->getUserVar("preferred_contact", $this->getId()) & avalanche_user::$CONTACT_EMAIL) || ($this->avalanche->getUserVar("preferred_contact", $this->getId()) === false))){
			$mailheaders="From:  " . $os->getUsername($from_user->getId()) . " <" . $from_user->email() . ">\n";
			$this->avalanche->mail($email, $title, $body, $mailheaders);
		}
		if(strlen($sms) && ((int)$this->avalanche->getUserVar("preferred_contact", $this->getId()) & avalanche_user::$CONTACT_SMS)){
			$mailheaders="From:  " . $os->getUsername($from_user->getId()) . " <" . $from_user->email() . ">\n";
			$this->avalanche->mail($sms, $title, $body, $mailheaders);
		}
	}

	public function contactSMS($from_user, $title, $body){
		if(!is_object($from_user) || !($from_user instanceof avalanche_user)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a user object");
		}
		if(!is_string($title)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($body)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string");
		}
		$os = $this->avalanche->getModule("os");
		$sms = $this->getSMSForContact();

		if(strlen($sms)){
			$mailheaders="From:  " . $os->getUsername($from_user->getId()) . " <" . $from_user->email() . ">\n";
			return $this->avalanche->mail($sms, $title, $body, $mailheaders);
		}else{
			return false;
		}
	}

	public function contactEmail($from_user, $title, $body){
		if(!is_object($from_user) || !($from_user instanceof avalanche_user)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be a user object");
		}
		if(!is_string($title)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}
		if(!is_string($body)){
			throw new IllegalArgumentException("argument 3 to " . __METHOD__ . " must be a string");
		}
		$os = $this->avalanche->getModule("os");
		$email = $this->getEmailForContact();

		if(strlen($email)){
			$mailheaders="From:  " . $os->getUsername($from_user->getId()) . " <" . $from_user->email() . ">\n";
			return $this->avalanche->mail($email, $title, $body, $mailheaders);
		}else{
			return false;
		}
	}


	// runs the visitor on usergroup case
	public function execute($visitor){
		return $visitor->visit($this);
	}
}



// represents the SYSTEM user
final class avalanche_system_user extends avalanche_user{

	//////////////////////////////////////////////////////////
	// returns the id of this usergroup
	//////////////////////////////////////////////////////////
	public function getId(){
		return (int) $this->_id;
	}

	//////////////////////////////////////////////////////////
	// returns the name of this usergroup
	//////////////////////////////////////////////////////////
	public function name(){
		return "SYSTEM";
	}

	// my id;
	private $_id;

	//////////////////////////////////////////////////////////
	// should only call once at beginning of page load.
	// initializes avalanche variables
	//  - _recent_log_in
	//  - _allModules
	//  - _allSkins
	//  - _defaultSkin
	//  - _currentSkin
	//////////////////////////////////////////////////////////
	public function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_id = -1;
	}
	//////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////


	public function reloadAvatar(){
		// noop
	}

	public function reload(){
		// noop
	}

	public function avatar($newAvatar=false){
		return "";
	}


	// enable/disable public functions
	// disables a user
	// returns true if user changes from enabled to disabled
	public function disable(){
		throw new Exception("cannot disable SYSTEM user");
	}

	// enables the user if he is disabled
	// returns true if the user has switched to enabled from disabled
	public function enable(){
		return true;
	}

	// returns true if the user is enabled
	public function enabled(){
		return true;
	}


	// gets/sets username
	public function username($new_name = false){
		return $this->name();
	}


	// gets/sets title name
	public function title($title=false){
		return "";
	}

	// gets/sets first name
	public function first($first = false){
		return "";
	}

	// gets/sets middle name
	public function middle($middle = false){
		return "";
	}

	// gets/sets last name
	public function last($last = false){
		return "SYSTEM";
	}

	// gets/sets email name
	public function email($email=false){
		return "noreply@" . $this->avalanche->DOMAIN();
	}

	public function sms($sms=false){
		return "";
	}

	// gets/sets password name
	public function password($password = false){
		return "";
	}

	public function lastActive(){
		$strongcal = $this->avalanche->getModule("strongcal");
		return date("Y-m-d H:i:s", $strongcal->gmttimestamp());
	}

	public function lastLoggedIn(){
		$strongcal = $this->avalanche->getModule("strongcal");
		return date("Y-m-d H:i:s", $strongcal->gmttimestamp());
	}

	public function lastLoggedOut(){
		$strongcal = $this->avalanche->getModule("strongcal");
		return date("Y-m-d H:i:s", $strongcal->gmttimestamp());
	}

	// contacts the user on behalf of the user $from_user
	//  (0 is guest, -1 is SYSTEM)
	// sends $title and $body in the message
	// uses this user's preferred contact method: email, sms, or both

	public function contact($from_user, $title, $body){
		// noop
	}



	// runs the visitor on usergroup case
	public function execute($visitor){
		return $visitor->visit($this);
	}
}
?>