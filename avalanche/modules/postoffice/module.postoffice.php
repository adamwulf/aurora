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

require ROOT . APPPATH . MODULES . "postoffice/submodule.mailbox.php";
require ROOT . APPPATH . MODULES . "postoffice/submodule.account.php";
require ROOT . APPPATH . MODULES . "postoffice/submodule.message.php";

$fileloader = $this->getModule("fileloader");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "postoffice/bootstraps/");
$fileloader->include_recursive(ROOT . APPPATH . MODULES . "postoffice/exceptions/");

//////////////////////////////////////////////////////////////////
// PREMISE
// ---------------------------------------------------------------
// the postoffice manages accounts that have mailboxes
// other modules etc can open accounts with the post office and
//  it will create the appropriate mailboxes etc.
//  when a new account is created, it will return a key to the account
//  this key must be presented with asking for an account object
// 
// a mail account has 3 mailboxes associated with it
//  - inbox
//  - sent items
//  - deleted items
// 
// item's deleted from an account's inbox are moved to the deleted items box
// item's deleted from an account's sent items box are moved to the deleted items box
// the deleted items box is purged of messages over a [variable] time
// 
// more accounts can be created on the fly
//  - associations for these non user accounts must be maintained externally
//
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  MAIN MESSENGER MODULE  //////////////////////////
///////////////                         //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//Syntax - module classes should always start with module_ followed by the module's install folder (name)
class module_postoffice  extends module_template{

	public static $inbox = "Inbox";
	public static $sent = "Sent Items";
	public static $deleted = "Trash";
	public static $drafts = "Drafts";
	
	
	
	//////////////////////////////////////////////////////////////////
	//  __construct($avalanche)					//
	//--------------------------------------------------------------//
	//  input: the avalanche for this module			//
	//  output: none						//
	//	I.E.	module_strongcal::init();			//
	//////////////////////////////////////////////////////////////////
	private $avalanche;
	function __construct($avalanche){
		module_template::__construct($avalanche);
		$this->avalanche = $avalanche;
		$this->_name = "Inversion Message Manager";
		$this->_version = "1.0.0";	
		$this->_desc = "Manages the intra-site messanging service";	
		$this->_folder = "postoffice";
		$this->_copyright = "Copyright 2005 Inversion Designs";
		$this->_author = "Adam Wulf";
		$this->_date = "01-24-05";
		$this->_messages = new HashTable();
	}

	function getAccount($id, $key=false){
		$account_table = $this->avalanche()->PREFIX() . "postoffice_accounts";
		if(!is_int($id)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an int");
		}else if(!(is_string($key) || $key === false)){
			throw new IllegalArgumentException("argument 2 to " . __METHOD__ . " must be a string");
		}else if(is_string($key) && !ereg('^[a-zA-Z0-9]+$', $key)){
			throw new IllegalArgumentException("form argument \"key\" must be alphanumeric");
		}else{
			$sql = "SELECT * FROM $account_table WHERE id='$id'";
			$result = $this->avalanche()->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				$account = new module_postoffice_account($this->avalanche(), $myrow);
				$account->unlock($key);
				return $account;
			}else{
				throw new MailAccountNotFoundException($id);
			}
		}
	}
	
	public function newAccount($name){
		if(!is_string($name)){
			throw new IllegalArgumentException("Argument to " . __METHOD__ . " must be a string");
		}
		// secure it for mysql transmission
		$name = addslashes($name);
		
		$account_table = $this->avalanche()->PREFIX() . "postoffice_accounts";
		$key = "";
		while(strlen($key) == 0 || $this->keyInUseHuh($key)){
			// generate key
			$key = md5(md5($name . rand()) . rand());
		}
		
		$drafts  = $this->newMailbox(module_postoffice::$drafts);
		$sent    = $this->newMailbox(module_postoffice::$sent);
		$deleted = $this->newMailbox(module_postoffice::$deleted);
		$inbox   = $this->newMailbox(module_postoffice::$inbox);
		
		// now i have a key to an account
		$sql = "INSERT INTO $account_table (`the_key`,`name`,`draftbox_id`,`sentbox_id`,`deletedbox_id`,`inbox_id`) VALUES ('$key','$name','" . $drafts->getId() . "','" . $sent->getId() . "','" . $deleted->getId() . "','" . $inbox->getId() . "')";
		$result = $this->avalanche()->mysql_query($sql);
		$account = $this->getAccount($this->avalanche()->mysql_insert_id(), $key);
		return $account;
	}
	
	// creates and returns a new mailbox
	private function newMailbox($name){
		$box_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
		// now i have a key to an account
		$sql = "INSERT INTO $box_table (`name`,`quota`) VALUES ('$name','" . $this->defaultQuota() . "')";
		$result = $this->avalanche()->mysql_query($sql);
		$id = $this->avalanche()->mysql_insert_id();
		if(!is_int($id)){
			throw new Exception("could not create Mailbox. id #$id is not an int");
		}
		return $this->getMailbox($id);
	}
	
	// gets a mailbox of the specified id
	private function getMailbox($box_id){
		$box_table = $this->avalanche()->PREFIX() . "postoffice_mailbox";
		if(!is_int($box_id)){
			throw new IllegalArgumentException("argument 1 to " . __METHOD__ . " must be an int");
		}else{
			$sql = "SELECT * FROM $box_table WHERE id='$box_id'";
			$result = $this->avalanche()->mysql_query($sql);
			if($myrow = mysql_fetch_array($result)){
				return new module_postoffice_mailbox($this->avalanche(), $myrow);
			}else{
				throw new MailboxNotFoundException($box_id);
			}
		}
	}
	

	// utility functions	
	private function keyInUseHuh($key){
		$account_table = $this->avalanche()->PREFIX() . "postoffice_accounts";
		$sql = "SELECT id FROM $account_table WHERE the_key='$key'";
		$result = $this->avalanche()->mysql_query($sql);
		if(mysql_num_rows($result)){
			return true;
		}
		return false;
	}
	
	// returns the default quota for a mailbox
	private function defaultQuota(){
		return 150;
	}
	
	
	// methods from template
	
	public function cron(){
		
	}
}
?>
