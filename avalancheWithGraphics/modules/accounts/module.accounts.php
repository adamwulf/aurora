<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.php						//
//----------------------------------------------------------------------//
//  initializes the module's class object and adds it to avalanches	//
//  module list								//
//									//
//									//
//  NOTE: filename must be of format module.<install folder>.php	//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.php						//
//----------------------------------------------------------------------//
//									//
//  This is an abstract module. All modules for avalanche must extend	//
//	this class.							//
//									//
//  NOTE: ALL MODULES WILL BE INCLUDE *INSIDE* OF THE avalanche'S MAIN	//
//	CLASS. SO REFER ANY FUNCTION CALLS THAT ARE *OUTSIDE* OF YOUR	//
//	CLASS TO avalanche BY USING *THIS->functionhere*		//
//									//
//////////////////////////////////////////////////////////////////////////

// DEPENDANT ON BOOTSTRAP
try{
	$bootstrap = $this->getModule("bootstrap");
	if(!is_object($bootstrap)){
		throw new ClassDefNotFoundException("module_bootstrap");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Account Manager cannot include dependancy \"BOOTSTRAP\" exiting.", E_USER_ERROR);
	echo "Account Manager cannot include dependancy \"BOOTSTRAP\" exiting.";
	exit;
}

// DEPENDANT ON FILELOADER
try{
	$fileloader = $this->getModule("fileloader");
	if(!is_object($fileloader)){
		throw new ClassDefNotFoundException("module_fileloader");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Account Manager cannot include dependancy \"FILELOADER\" exiting.", E_USER_ERROR);
	echo "Account Manager cannot include dependancy \"FILELOADER\" exiting.";
	exit;
}

// DEPENDANT ON STRONGCAL
try{
	$strongcal = $this->getModule("strongcal");
	if(!is_object($strongcal)){
		throw new ClassDefNotFoundException("module_strongcal");
	}
}catch(ClassDefNotFoundException $e){
	trigger_error("Account Manager cannot include dependancy \"STRONGCAL\" exiting.", E_USER_ERROR);
	echo "Account Manager cannot include dependancy \"STRONGCAL\" exiting.";
	exit;
}

require ROOT . APPPATH . MODULES . "accounts/subclass.accounts_exception.php";
require ROOT . APPPATH . MODULES . "accounts/submodule.account.php";
require ROOT . APPPATH . MODULES . "accounts/submodule.accounts.accountComparator.php";
require ROOT . APPPATH . MODULES . "accounts/submodule.transaction.php";
require ROOT . APPPATH . MODULES . "accounts/submodule.product.php";
require ROOT . APPPATH . MODULES . "accounts/submodule.calculator.php";



$fileloader = $this->getModule("fileloader");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "accounts/bootstraps/");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "accounts/visitors/");


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                        ///////////////////////////
///////////////  MAIN ACCOUNTS MODULE  ///////////////////////////
///////////////                        ///////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_accounts  extends module_template{
	// cpanel
	private $host='localhost';
	private $port='2082';

	private $sqladmin='invers';
	private $sqlpass='samplepassword';

	private $ftpadmin='invers';
	private $ftppass='samplepassword';

	private $cpaneladmin='invers';
	private $cpanelpass='samplepassword';

	//////////////////////////////////////////////////////////////////
	//  __construct()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//
	//  precondition:						//
	//	should only be called once				//
	//	(command.php of avalanche will include this		//
	//	   file after installation)				//
	//								//
	//  postcondition:						//
	//  	all variables in this object are initialized		//
	//								//
	//--------------------------------------------------------------//
	//  IF THIS FUNTION IS REDEFINED TO HOLD MORE CODE, BE SURE	//
	//	TO INCLUDE THE PARENT CLASS FUNCTION CALL FOR THE	//
	//	FIRST LINE.						//
	//								//
	//	I.E.	module_strongcal::init();			//
	//////////////////////////////////////////////////////////////////
	private $avalanche;
	function __construct($avalanche){
		$this->avalanche = $avalanche;
		$this->_name = "Inversion Account Manager";
		$this->_version = "1.0.0";
		$this->_desc = "Manages and Adds accounts for the Avalanche/Aurora/Taskman trio";
		$this->_folder = "accounts";
		$this->_copyright = "Copyright 2004 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "10-26-04";
		$this->_accounts = new HashTable();
		$this->_products = new HashTable();
	}

	function getAccounts(){
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts";
		$result = $this->avalanche->mysql_query($sql);
		$ret = array();
		while($myrow = mysql_fetch_array($result)){
			$ret[] = $this->getAccount($myrow["name"]);
		}
		return $ret;
	}

	function getAccount($name){
		if(!is_string($name)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a string");
		}
		$acct = $this->_accounts->get($name);
		if(is_object($acct)){
			return $acct;
		}else{
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts WHERE name=\"$name\"";
			$result = $this->avalanche->mysql_query($sql);
			$ret = false;
			if($myrow = mysql_fetch_array($result)){
				$ret = new module_accounts_account($this->avalanche, $myrow);
				$this->_accounts->put($myrow["name"], $ret);
				return $ret;
			}
		}
		throw new AccountNotFoundException($name);
	}

	// adds an account.
	// returns false on failure, or the new account's password on success
	public function addAccount($name, $email, $title, $domain){
		if(file_exists($this->avalanche->PUBLICHTML() . $name)){
			return false;
		}
		// generate the password for new accounts.
		$pass = substr(md5($name . rand()), 0, 8);

		$strongcal = $this->avalanche->getModule("strongcal");
		$timestamp = $strongcal->gmttimestamp();
		$added_date = date("Y-m-d H:i:s", $timestamp);
		$expires_date = strtotime("+1 month", $timestamp);
		$expires_date = date("Y-m-d H:i:s", $expires_date);
		$addok = true;
		// $addok = $addok && $this->addEmail($name);
		if(!$this->addSubdomain($name, $domain)) throw new Exception("cannot add subdomain for $name");
		//$addok = $addok && $this->addFTPUser($name);
		//$addok = $addok && $this->addSQLUser($name);
		if(!$this->addDatabase($name)) throw new Exception("cannot add database for $name");
		//$addok = $addok && $this->addSQLUserToDatabase($name);
		if(!$this->initDatabase($name, $pass, $email, $title)) throw new Exception("cannot init database for $name");
		if(!$this->initFileSystem($name, $domain)) throw new Exception("cannot init file system for $name");

		if($addok){
			$sql = "INSERT INTO " . $this->avalanche->PREFIX() . "accounts (name, email, added_on, domain, months, expires_on, discount) VALUES (\"$name\",\"$email\",\"$added_date\",\"$domain\",\"1\",\"$expires_date\",\"10\")";
			$result = $this->avalanche->mysql_query($sql);
			return $pass;
		}
		return false;
	}

	public function deleteAccount($name){
		if($this->avalanche->loggedInHuh() && $this->avalanche->hasPermissionHuh($this->avalanche->loggedInHuh(), "view_cp")){
			$acct = $this->getAccount($name);
			$delok = //$this->removeEmail($name) &&
			$this->removeSubdomain($name, $acct->domain()) &&
			//$this->removeSQLUser($name) &&
			$this->removeDatabase($name) &&
			$this->cleanFileSystem($name);
//			$this->removeFTPUser($name);

			if($delok){
				$sql = "DELETE FROM " . $this->avalanche->PREFIX() . "accounts WHERE name='$name'";
				$result = $this->avalanche->mysql_query($sql);
				$sql = "DELETE FROM " . $this->avalanche->PREFIX() . "accounts_transactions WHERE account_name='$name'";
				$result = $this->avalanche->mysql_query($sql);
				return true;
			}
		}
		return false;
	}






	private function cleanFileSystem($name){
		$ftp_server = $this->avalanche->DOMAIN();
		// set up basic connection
		$conn_id = ftp_connect($ftp_server);
		// login with username and password
		$login_result = ftp_login($conn_id, $this->ftpadmin, $this->ftppass);

		// check connection
		if ((!$conn_id) || (!$login_result)) {
		    return false;
		   }

		$changeok = ftp_chdir($conn_id, "public_html");
		if(!$changeok){
			return false;
		}

		$changeok = ftp_chdir($conn_id, "$name");
		if(!$changeok){
			return false;
		}

		// upload the file
		$delete = ftp_delete ($conn_id, "include.avalanche.fullApp.php");
		$delete = $delete && ftp_delete ($conn_id, "index.php");
		$delete = $delete && ftp_delete ($conn_id, "xicon.gif");
		$delete = $delete && ftp_delete ($conn_id, "csshover.htc");
		$delete = $delete && ftp_rmdir($conn_id, "cgi-bin");

		$changeok = ftp_chdir($conn_id, "..");
		if(!$changeok){
			return false;
		}


		$delete = $delete && ftp_rmdir($conn_id, $name);

		// check upload status
		if (!$delete){
		       return false;
		}
		// close the FTP stream
		ftp_close($conn_id);
		return true;
	}

	private function initFileSystem($name, $domain){
		// make include.avalanche.fullApp.php
		$filename = tempnam("/tmp", "foo");
		$stub_include_filename = $this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/include.avalanche.fullApp.php";
		$include_filename = $this->avalanche->PUBLICHTML() . $name . "/include.avalanche.fullApp.php";
		$contents = file_get_contents($stub_include_filename);
		$contents = str_replace("%user%", $name, $contents);
		$contents = str_replace("%domain%", $domain, $contents);
		$contents = str_replace("%mysqluser%", $this->sqladmin, $contents);
		$contents = str_replace("%mysqlpass%", $this->sqlpass, $contents);
		file_put_contents($filename, $contents);
		$this->upload($name, $filename, "include.avalanche.fullApp.php");

		// make index.php
		$filename = tempnam("/tmp", "foo");
		$stub_include_filename = $this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/index.php";
		$index_filename = $this->avalanche->PUBLICHTML() . $name . "/index.php";
		$contents = file_get_contents($stub_include_filename);
		file_put_contents($filename, $contents);
		$this->upload($name, $filename, "index.php");

		// make xicon.gif
		$filename = tempnam("/tmp", "foo");
		$icon_filename = $this->avalanche->PUBLICHTML() . $name . "/xicon.gif";
		$stub_include_filename = $this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/xicon.gif";
		$contents = file_get_contents($stub_include_filename);
		file_put_contents($filename, $contents);
		$this->upload($name, $filename, "xicon.gif");

		// make csshover.htc
		$filename = tempnam("/tmp", "foo");
		$icon_filename = $this->avalanche->PUBLICHTML() . $name . "/csshover.htc";
		$stub_include_filename = $this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/csshover.htc";
		$contents = file_get_contents($stub_include_filename);
		file_put_contents($filename, $contents);
		$this->upload($name, $filename, "csshover.htc");

		return file_exists($include_filename) && file_exists($index_filename) && file_exists($icon_filename);
	}

	private function upload($name, $source, $dest){
		$ftp_server = $this->avalanche->DOMAIN();
		// set up basic connection
		$conn_id = ftp_connect($ftp_server);
		// login with username and password
		$login_result = ftp_login($conn_id, $this->ftpadmin, $this->ftppass);
		// check connection
		if ((!$conn_id) || (!$login_result)) {
			echo "can't login as " . $this->ftpadmin . "<br>";
			return false;
		}
		// upload the file
		$changeok = ftp_chdir($conn_id, "public_html");
		if(!$changeok){
			echo "can't ftp_put(\$conn_id, $dest, $source, FTP_ASCII)<br>";
			return false;
		}

		// upload the file
		$changeok = ftp_chdir($conn_id, "$name");
		if(!$changeok){
			echo "can't ftp_put(\$conn_id, $dest, $source, FTP_ASCII)<br>";
			return false;
		}


		$upload = ftp_put($conn_id, $dest, $source, FTP_ASCII);
		// check upload status
		if (!$upload){
			echo "can't ftp_put(\$conn_id, $dest, $source, FTP_ASCII)<br>";
			return false;
		}
		// close the FTP stream
		ftp_close($conn_id);
		return true;
	}

	private function initDatabase($name, $pass, $email, $title){
		$sql = file_get_contents ($this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "accounts/startup.sql");
		$sql = str_replace("%user%", $name, $sql);
		$sql = str_replace("%pass%", $pass, $sql);
		$sql = str_replace("%email%", $email, $sql);
		$sql = str_replace("%title%", addslashes($title), $sql);
		$sqls = explode(";", $sql);
		$link = mysql_connect($this->avalanche->HOST(), $this->sqladmin, $this->sqlpass);
		mysql_select_db($this->cpaneladmin . "_acct" . $name, $link);
		foreach($sqls as $sql){
			$sql = trim($sql);
			if(strlen($sql)){
				mysql_query($sql, $link);
				if(mysql_error()){
					throw new DatabaseException(mysql_error());
				}
			}
		}
		return true;
	}

	private function addEmail($name){
		if($this->checkName($name)){
			$request = "/frontend/bluelagoon/mail/doaddpop.html?email=$name&domain=" . $this->avalanche->DOMAIN() . "&password=$name";
			$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
			// cPanel confirm Added to fail duplicate emails
			$show = strip_tags($result);
			if (strpos($show, "already") !== false && strpos($show, "exists") !== false) {
				return false;
			}else{
				return true;
			}
		}else{
			throw new Exception("\"" . $name . "\" is not a valid username");
		}
	}

	private function addSubdomain($name, $domain){
		if($this->checkName($name)){
			$request = "/frontend/bluelagoon/subdomain/doadddomain.html?rootdomain=" . $domain . "&domain=$name";
			$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
			// cPanel confirm Added to fail duplicate emails
			$show = strip_tags($result);
			if (strpos($show, "has been added") !== false) {
				return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("\"" . $name . "\" is not a valid username");
		}
	}

	private function addDatabase($name){
		if($this->checkName($name)){
			$request = "/frontend/bluelagoon/sql/adddb.html?db=acct$name";
			$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
			// cPanel confirm Added to fail duplicate emails
			$show = strip_tags($result);
			if (strpos($show, "Database Created") !== false) {
				return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("\"" . $name . "\" is not a valid username");
		}
	}

	private function addSQLUser($name){
		if($this->checkName($name)){
			$request = "/frontend/bluelagoon/sql/adduser.html?user=$name&pass=$name";
			$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
			// cPanel confirm Added to fail duplicate emails
			$show = strip_tags($result);
			if (strpos($show, "Account Created") !== false) {
				return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("\"" . $name . "\" is not a valid username");
		}
	}

	private function addSQLUserToDatabase($name){
		$request = "/frontend/bluelagoon/sql/addusertodb.html?user=" . $this->cpaneladmin . "_" . $name . "&db=" . $this->cpaneladmin . "_acct" . $name . "&ALL=ALL";
		$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
		// cPanel confirm Added to fail duplicate emails
		$show = strip_tags($result);
		if (strpos($show, "Added the user") !== false) {
			return true;
		}else{
			return false;
		}
	}

	private function addFTPUser($name){
		if($this->checkName($name)){
			$request = "/frontend/bluelagoon/ftp/doaddftp.html?login=$name&password=$name&homedir=/$name";
			$result = $this->cprq($this->host,$this->port,$this->cpaneladmin,$this->cpanelpass,$request);
			// cPanel confirm Added to fail duplicate emails
			$show = strip_tags($result);
			if (strpos($show, "was added") !== false) {
				return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("\"" . $name . "\" is not a valid username");
		}
	}


	// doesn't work for some reason? it comes back saying true, but it doesn't delete
	private function removeEmail($name){
		$request = "/frontend/bluelagoon/mail/realdelpop.html?email=$name&domain=" . $this->avalanche->DOMAIN();
		$result = $this->cprq($this->host, $this->port, $this->cpaneladmin, $this->cpanelpass, $request);
		//cPanel confirm Deleted to fail if it can't process
		$show = strip_tags($result);
		if (strpos($show, "successfully") !== false && strpos($show, "deleted") !== false) {
			return true;
		}else{
			return false;
		}
	}

	// does work!
	private function removeSQLUser($name){
		$request = "/frontend/bluelagoon/sql/deluser.html?user=" . $this->cpaneladmin . "_" . $name;
		$result = $this->cprq($this->host, $this->port, $this->cpaneladmin, $this->cpanelpass, $request);
		//cPanel confirm Deleted to fail if it can't process
		$show = strip_tags($result);
		if (strpos($show, "Deleted the user") !== false) {
			return true;
		}else{
			return false;
		}
	}

	// does work!
	private function removeFTPUser($name){
		$request = "/frontend/bluelagoon/ftp/dodelftp.html?login=$name";
		$result = $this->cprq($this->host, $this->port, $this->cpaneladmin, $this->cpanelpass, $request);
		//cPanel confirm Deleted to fail if it can't process
		$show = strip_tags($result);
		if (strpos($show, "was deleted") !== false) {
			return true;
		}else{
			return false;
		}
	}

	// does work!
	private function removeSubdomain($name, $domain){
		$request = "/frontend/bluelagoon/subdomain/dodeldomain.html?domain=" . $name . "_" . $domain;
		$result = $this->cprq($this->host, $this->port, $this->cpaneladmin, $this->cpanelpass, $request);
		//cPanel confirm Deleted to fail if it can't process
		$show = strip_tags($result);
		if (strpos($show, "has been removed") !== false) {
			return true;
		}else{
			return false;
		}
	}

	// does work!
	private function removeDatabase($name){
		$request = "/frontend/bluelagoon/sql/deldb.html?db=" . $this->cpaneladmin . "_acct" . $name;
		$result = $this->cprq($this->host, $this->port, $this->cpaneladmin, $this->cpanelpass, $request);
		//cPanel confirm Deleted to fail if it can't process
		$show = strip_tags($result);
		if (strpos($show, "database") !== false && strpos($show, "dropped") !== false){
			return true;
		}else{
			return false;
		}
	}

	public function checkName($name){
		return preg_match("/^([0-9]|[a-z]|[A-Z])*\$/", $name);
	}


	// function to talk to cpanel
	private function cprq($host,$port,$ownername,$reseller_pass,$request)  {
		$errno = 0;
		$errstr = "";
		$trial = 0;
		$sock = false;
		// try to connect
		for($trial=0;$trial<3 && !$sock;$trial++){
			$sock = fsockopen($host,$port, $errno, $errstr, 30);
		}
		if(!$sock){
			throw new Exception("Socket Error");
			exit();
		}
		$authstr = "$ownername:$reseller_pass";
		$pass = base64_encode($authstr);
		$in  = "GET $request\r\n";
		$in .= "HTTP/1.0\r\n";
		$in .= "Host:" . $this->avalanche->DOMAIN() . "\r\n";
		$in .= "Authorization: Basic $pass\r\n";
		$in .= "Connection: close\r\n\r\n";
		$in .= "\r\n";
		fputs($sock, $in);
		$result = "";
		while (!feof($sock)){
			// fgets ($sock,128);
			$result .= fgets ($sock,128);
		}
		fclose( $sock );
		return $result;
	}



	private $_products;
	public function getProducts(){
		$ret = array();
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts_products";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			$ret[] = $this->getProduct((int)$myrow["id"]);
		}
		return $ret;
	}

	public function getProduct($id){
		if(!is_int($id)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$ret = false;
		$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "accounts_products WHERE id='$id'";
		$result = $this->avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			if(is_object($this->_products->get((int)$myrow["id"]))){
				$ret = $this->_products->get((int)$myrow["id"]);
			}else{
				$product = new AccountProduct($this->avalanche, $myrow);
				$this->_products->put((int)$myrow["id"], $product);
				$ret = $product;
			}
		}
		return $ret;
	}


	////////////////////////////////////////////////////////////////////////////////////
	// cron
	////////////////////////////////////////////////////////////////////////////////////
	public function cron(){
		$strongcal = $this->avalanche->getModule("strongcal");
		$now = date("Y-m-d H:i:s", $strongcal->gmttimestamp());

		$ret = "";
		$accounts = $this->getAccounts();
		$ret .= "checking accounts\n";
		$ret .= "now: $now\n";
		foreach($accounts as $account){
			$ret .= " account: " . $account->name() . "\n";
			$ret .= " checking age of account...\n";
			// start age check
			$datetime = $account->expiresOn();
			$ret .= "expires: $datetime\n";
			if(!$account->disabled() && $datetime < $now){
				$ret .= "disabling " . $account->name() . "\n";
				$sql = "UPDATE " . $this->avalanche->PREFIX() . "accounts SET `disabled` = '1' WHERE `id` = '" . $account->getId() . "'";
				$result = $this->avalanche->mysql_query($sql);
				$account->reload();
				//	$account->disable(true);
			}

			if(!$account->disabled()){
				// cron the account if age is ok
				$ret .= "running cron for " . $account->name() . "\n";
				$ret .= $account->getAvalanche()->cron();
			}
		}
		return $ret;
	}
}

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>