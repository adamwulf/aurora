<?
//SHOW TABLES LIKE 'choosenTable'
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


define(INCLUDEPATH, "includes/");
define(MODULES, "modules/");
define(SKINS, "skins/");

$files = array(
	"control/images/control_01.gif",
	"control/images/control_02.gif",
	"control/images/control_03.gif",
	"control/images/control_04.gif",
	"control/images/control_05.gif",
	"control/images/control_06.gif",
	"control/images/control_07.gif",
	"control/images/control_08.gif",
	"control/images/control_09.gif",
	"control/images/control_10.gif",
	"control/images/control_11.gif",
	"control/images/control_12.gif",
	"control/images/control_13.gif",
	"control/images/control_14.gif",
	"control/images/control_15.gif",
	"control/images/control_16.gif",
	"control/images/control_17.gif",
	"control/images/control_18.gif",
	"control/images/control_19.gif",
	"control/images/control_20.gif",
	"control/images/control_21.gif",
	"control/images/control_22.gif",
	"control/images/control_23.gif",
	"control/images/spacer.gif",
	"control/index.php",
	"control/modules.php",
	"control/showOffSkin.php",
	"control/skins.php",
	"control/user.php",
	"control/usergroups.php",
	INCLUDEPATH . "include.avalanche.php",
	INCLUDEPATH . "include.connect.php",
	INCLUDEPATH . "include.date.php",
	INCLUDEPATH . "include.php",
	INCLUDEPATH . "include.string.php",
	MODULES . "include.module.install.php",
	MODULES . "moduleManager/" . "green.gif",
	MODULES . "moduleManager/" . "red.gif",
	MODULES . "moduleManager/" . "install.php",
	MODULES . "moduleManager/" . "module.moduleManager.php",
	MODULES . "moduleManager/" . "uninstall.php",
	SKINS . "include.skin.install.php",
	SKINS . "skins.readme.txt",
	SKINS . "template/" . "skin.template.php",
	SKINS . "default/" . "skin.default.style.css",
	SKINS . "default/" . "skin.default.java.js",
	SKINS . "default/" . "install.php",
	SKINS . "default/" . "skin.default.php",
	SKINS . "default/" . "uninstall.php",
	SKINS . "control/" . "skin.control.style.css",
	SKINS . "control/" . "skin.control.java.js",
	SKINS . "control/" . "install.php",
	SKINS . "control/" . "skin.control.php",
	SKINS . "control/" . "uninstall.php",
	SKINS . "installer/" . "skin.installer.style.css",
	SKINS . "installer/" . "skin.installer.java.js",
	SKINS . "installer/" . "config.php",
	SKINS . "installer/" . "install.php",
	SKINS . "installer/" . "skin.installer.php",
	SKINS . "installer/" . "uninstall.php",
	SKINS . "buffer/" . "skin.buffer.style.css",
	SKINS . "buffer/" . "skin.buffer.java.js",
	SKINS . "buffer/" . "install.php",
	SKINS . "buffer/" . "skin.buffer.php",
	SKINS . "buffer/" . "uninstall.php",
	"config.php",
	"mysql.php",
	"readme.txt"
);

$files_name = array(
	"control_01.gif",
	"control_02.gif",
	"control_03.gif",
	"control_04.gif",
	"control_05.gif",
	"control_06.gif",
	"control_07.gif",
	"control_08.gif",
	"control_09.gif",
	"control_10.gif",
	"control_11.gif",
	"control_12.gif",
	"control_13.gif",
	"control_14.gif",
	"control_15.gif",
	"control_16.gif",
	"control_17.gif",
	"control_18.gif",
	"control_19.gif",
	"control_20.gif",
	"control_21.gif",
	"control_22.gif",
	"control_23.gif",
	"spacer.gif",
	"index.php",
	"modules.php",
	"showOffSkin.php",
	"skins.php",
	"user.php",
	"usergroups.php",
	"include.avalanche.php",
	"include.connect.php",
	"include.date.php",
	"include.php",
	"include.string.php",
	"include.module.install.php",
	"green.gif",
	"red.gif",
	"install.php",
	"module.moduleManager.php",
	"uninstall.php",
	"include.skin.install.php",
	"skins.readme.txt",
	"skin.template.php",
	"skin.default.style.css",
	"skin.default.java.js",
	"install.php",
	"skin.default.php",
	"uninstall.php",
	"skin.control.style.css",
	"skin.control.java.js",
	"install.php",
	"skin.control.php",
	"uninstall.php",
	"skin.installer.style.css",
	"skin.installer.java.js",
	"config.php",
	"install.php",
	"skin.installer.php",
	"uninstall.php",
	"skin.buffer.style.css",
	"skin.buffer.java.js",
	"install.php",
	"skin.buffer.php",
	"uninstall.php",
	"config.php",
	"mysql.php",
	"readme.txt"
);


	$files_miss = array();


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
	echo "File System Ok?";
	echo "</font>";
	echo "</td>";
	echo "<td align='center'>";
	if(!filesAreOk()){
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
for($i=0; $i<count($files);$i++){
	echo "<tr>";
	echo "<td>";
	echo "<font face='verdana' size='2' color='#000000'>";
	echo $files_name[$i];
	echo "</font>";
	echo "</td><td align='center'>";
	if(file_exists($files[$i])){
		echo "<font face='verdana' size='2' color='$okcolor'><b><i>ok</i></b></font>";
	}else{
		$files_miss[] = $files[$i];
		echo "<font face='times' size='3' color='#950000'>X</font>";
	}
	echo "</td>";
	echo "</tr>";
}
	echo "</table>";


if(count($files_miss)){
	echo "<hr size='1' width='100%' color='#000000' noshade>";
	echo "<font face='verdana' size='2' color='#000000'>";
	echo "You are missing the following files:<br><br>";
	echo "</font>";
	for($i=0; $i<count($files_miss);$i++){
		echo "<font face='verdana' size='2' color='#000000'>";
		echo ($i+1) . ": " . $files_miss[$i] . "<br>";
		echo "</font>";
	}
}


	echo "</body>";
	echo "</html>";

}else{

}


function filesAreOk(){
	global $files;
	for($i=0; $i<count($files);$i++){
		if(!file_exists($files[$i])){
			return false;
		}
	}
	return true;
}
?>
