<?
if(!$step_num)
	$step_num = $_REQUEST['step_num'];
if(!$sql_adminname)
	$sql_adminname = $_REQUEST['sql_adminname'];
if(!$sql_passname)
	$sql_passname = $_REQUEST['sql_passname'];
if(!$sql_dbname)
	$sql_dbname = $_REQUEST['sql_dbname'];
if(!$sql_hostname)
	$sql_hostname = $_REQUEST['sql_hostname'];
if(!$sql_prefix)
	$sql_prefix = $_REQUEST['sql_prefix'];
if(!$sql_overwrite)
	$sql_overwrite = $_REQUEST['sql_overwrite'];
if(!$username)
	$username = $_REQUEST['username'];
if(!$password)
	$password = $_REQUEST['password'];
if(!$passwordv)
	$passwordv = $_REQUEST['passwordv'];
if(!$root_directory)
	$root_directory = $_REQUEST['root_directory'];
if(!$at_path)
	$at_path = $_REQUEST['at_path'];
if(!$host)
	$host = $_REQUEST['host'];
if(!$domain)
	$domain = $_REQUEST['domain'];
if(!$cookies)
	$cookies = $_REQUEST['cookies'];
if(!$error)
	$error = $_REQUEST['error'];
if(!$readme)
	$readme = $_REQUEST['readme'];
if(!$show)
	$readme = $_REQUEST['show'];

$tables = array(
	$sql_prefix . "loggedinusers",
	$sql_prefix . "modules",
	$sql_prefix . "skins",
	$sql_prefix . "user_link",
	$sql_prefix . "usergroups",
	$sql_prefix . "users",
	$sql_prefix . "varlist"
);

$tables_drop = array(
"DROP TABLE IF EXISTS " . $sql_prefix . "loggedinusers" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "modules" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "skins" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "user_link" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "usergroups" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "users" . ";",
"DROP TABLE IF EXISTS " . $sql_prefix . "varlist" . ";"
);


$tables_insert = array(
	"INSERT INTO " . $sql_prefix . "modules" . " VALUES (1, 'moduleManager', '1.0.0');",
	"INSERT INTO " . $sql_prefix . "skins" . " VALUES (7, 'control', '1.0.0');",
	"INSERT INTO " . $sql_prefix . "skins" . " VALUES (8, 'installer', '1.0.0');",
	"INSERT INTO " . $sql_prefix . "skins" . " VALUES (9, 'default', '1.0.0');",
	"INSERT INTO " . $sql_prefix . "skins" . " VALUES (10, 'buffer', '1.0.0');",
	"INSERT INTO " . $sql_prefix . "user_link" . " VALUES (1, 1, 1);",
	"INSERT INTO " . $sql_prefix . "user_link" . " VALUES (2, 2, 2);",
	"INSERT INTO " . $sql_prefix . "usergroups" . " VALUES (1, 'admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);",
	"INSERT INTO " . $sql_prefix . "usergroups" . " VALUES (2, 'guest', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);",
	"INSERT INTO " . $sql_prefix . "users" . " VALUES (1, '$username', '$password');",
	"INSERT INTO " . $sql_prefix . "users" . " VALUES (2, 'guest', '');",
	"INSERT INTO " . $sql_prefix . "varlist" . " VALUES (1, 'SKIN', 'installer', 'installer');",
	"INSERT INTO " . $sql_prefix . "varlist" . " VALUES (2, 'USERGROUP', '2', '2');",
	"INSERT INTO " . $sql_prefix . "varlist" . " VALUES (3, 'USER', '2', '2');"
);


$tables_syntax = array(
	"CREATE TABLE " . $sql_prefix . "loggedinusers" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  ip text NOT NULL,
	  user_id mediumint(9) NOT NULL default '0',
	  last_active datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",

	"CREATE TABLE " . $sql_prefix . "modules" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  folder text NOT NULL,
	  version text NOT NULL,
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",


	"CREATE TABLE " . $sql_prefix . "skins" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  folder text NOT NULL,
	  version text NOT NULL,
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",


	"CREATE TABLE " . $sql_prefix . "user_link" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  user_id mediumint(9) NOT NULL default '0',
	  group_id mediumint(9) NOT NULL default '0',
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",

	"CREATE TABLE " . $sql_prefix . "usergroups" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  name text NOT NULL,
	  install_mod tinyint(4) NOT NULL default '0',
	  uninstall_mod tinyint(4) NOT NULL default '0',
	  install_skin tinyint(4) NOT NULL default '0',
	  uninstall_skin tinyint(4) NOT NULL default '0',
	  add_user tinyint(4) NOT NULL default '0',
	  del_user tinyint(4) NOT NULL default '0',
	  rename_user tinyint(4) NOT NULL default '0',
	  add_usergroup tinyint(4) NOT NULL default '0',
	  del_usergroup tinyint(4) NOT NULL default '0',
	  rename_usergroup tinyint(4) NOT NULL default '0',
	  change_default_skin tinyint(4) NOT NULL default '0',
	  change_permissions tinyint(4) NOT NULL default '0',
	  link_user tinyint(4) NOT NULL default '0',
	  unlink_user tinyint(4) NOT NULL default '0',
	  change_default_usergroup tinyint(4) NOT NULL default '0',
	  view_cp tinyint(4) NOT NULL default '0',
	  change_group_password tinyint(4) NOT NULL default '0',
	  change_password tinyint(4) NOT NULL default '0',
	  view_group_password tinyint(4) NOT NULL default '0',
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",

	"CREATE TABLE " . $sql_prefix . "users" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  username text NOT NULL,
	  password text NOT NULL,
	  PRIMARY KEY (id)
	) TYPE=MyISAM;",

	"CREATE TABLE " . $sql_prefix . "varlist" . " (
	  id mediumint(9) NOT NULL auto_increment,
	  var text NOT NULL,
	  val text NOT NULL,
	  dflt text NOT NULL,
	  PRIMARY KEY (id)
	) TYPE=MyISAM;"
);

if($show){
	include "include.avalanche.installer.php";
	$skin = $avalanche->getSkin("installer");
	$color = $skin->thcolor();
	$okcolor = $skin->tdcolor();

	echo "<html>";
	echo "<body bgcolor='$color'>";
	echo "<table width='100%' bgcolor='$color'>";
	echo "<tr>";
	echo "<td>";
	echo "<font face='verdana' size='2' color='#000000'>";
	echo "Table Structure Ok?";
	echo "</font>";
	echo "</td>";
	echo "<td align='center'>";
	if(!tablesAreOk()){
		echo "<font face='times' size='3' color='#950000'>X</font>";
	}else{
		echo "<font face='verdana' size='2' color='$okcolor'><b><i>ok</i></b></font>";
	}
	echo "</font>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'>";
	echo "<hr size='1' width='100%' color='#000000' noshade>";
	echo "</td>";
	echo "</tr>";
	for($i=0; $i<count($tables);$i++){
		echo "<tr>";
		echo "<td>";
		echo "<font face='verdana' size='2' color='#000000'>";
		echo $tables[$i];
		echo "</font>";
		echo "</td><td align='center'>";
		if(drop_table($tables_drop[$i]) && create_table($tables_syntax[$i])){
			echo "<font face='verdana' size='2' color='$okcolor'><b><i>ok</i></b></font>";
		}else{
			$tables_miss[] = $tables[$i];
			echo "<font face='times' size='3' color='#950000'>X</font>";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	if(count($tables_miss)){
		echo "<hr size='1' width='100%' color='#000000' noshade>";
		echo "<font face='verdana' size='2' color='#000000'>";
		echo "The following tables could not be created:<br><br>";
		echo "</font>";
		for($i=0; $i<count($tables_miss);$i++){
			echo "<font face='verdana' size='2' color='#000000'>";
			echo ($i+1) . ": " . $tables_miss[$i] . "<br>";
			echo "</font>";
		}
	}else{
	echo "<table width='100%' bgcolor='$color'>";
	echo "<tr>";
	echo "<td colspan='2'>";
	echo "<font face='verdana' size='2' color='#000000'>";
	echo "<br><br>Done creating tables.";
	echo "</font>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'>";
	echo "<hr size='1' width='100%' color='#000000' noshade>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>";
	echo "<font face='verdana' size='2' color='#000000'>";
	echo "Inserting data into tables:";
	echo "</font>";
	echo "</td><td align='center'>";
	if(insert_data()){
		echo "<font face='verdana' size='2' color='$okcolor'><b><i>ok</i></b></font>";
	}else{
		$tables_miss[] = $tables[$i];
		echo "<font face='times' size='3' color='#950000'>X</font>";
	}
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	}

	echo "</body>";
	echo "</html>";

}

function insert_data(){
	global $tables, $sql_adminname, $sql_passname, $sql_dbname,$sql_prefix, $sql_hostname, $tables_insert;


	$db = @mysql_connect($sql_hostname, $sql_adminname, $sql_passname);
	@mysql_select_db($sql_dbname,$db);


	for($i=0; $i<count($tables_insert); $i++){
		$result = @mysql_query($tables_insert[$i],$db);
	}

		return true;
}


function table_exists($table){
	global $tables, $sql_adminname, $sql_passname, $sql_dbname,$sql_prefix, $sql_hostname;
	//Verifies that a MySQL table exists
	if (!$oConn = @mysql_connect($sql_hostname, $sql_adminname, $sql_passname)) {
		$bRetVal = FALSE;
	}else{
		$bRetVal = FALSE;
		$result = @mysql_list_tables($sql_dbname, $oConn);
		while ($row=@mysql_fetch_array($result)) {
			if ($row[0] ==  $table){
				$bRetVal = TRUE;
				break;
			}
		}
		return ($bRetVal);
	}
}


function create_table($table_syntax){
	global $tables, $sql_adminname, $sql_passname, $sql_dbname,$sql_prefix, $sql_hostname;


	$db = @mysql_connect($sql_hostname, $sql_adminname, $sql_passname);
	@mysql_select_db($sql_dbname,$db);

	$result = @mysql_query($table_syntax,$db);

	if($result){
		return true;
	}else{
		return false;
	}
}


function drop_table($table_drop){
	global $tables, $sql_adminname, $sql_passname, $sql_dbname,$sql_prefix, $sql_hostname, $sql_overwrite;
	if($sql_overwrite){
		$db = @mysql_connect($sql_hostname, $sql_adminname, $sql_passname);
		@mysql_select_db($sql_dbname,$db);

		$result = @mysql_query($table_drop,$db);

		return true;

	}else{
		return true;
	}
}

function tablesAreOk(){
	global $tables, $sql_overwrite;
	if(!$sql_overwrite){
		for($i=0;$i<count($tables);$i++){
			if(table_exists($tables[$i])){
				return false;
			}
		}
	}else{
		return true;
	}

	return true;
}
?>