<?
//SHOW TABLES LIKE 'choosenTable'


define(INCLUDEPATH, "includes/");
define(MODULES, "modules/");
define(SKINS, "skins/");

$files = array(
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
	if(true){
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
	return true;
}
?>
