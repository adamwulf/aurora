<?
//SHOW TABLES LIKE 'choosenTable'


$correction_dir = "../../";
include_once "../../include.module.install.php";

$files = array(
	ROOT . APPPATH . MODULES . "strongcal/" . "install.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "module.strongcal.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.calendar.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.constants.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.event.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.export.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.fields.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.recurrance.php",
	ROOT . APPPATH . MODULES . "strongcal/" . "submodule.strongcal.validation.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "checking.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "index.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "installer.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "installerui.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "mysql.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "tos.php",
	ROOT . APPPATH . MODULES . "strongcal/install/" . "readme.txt",
);

$files_name = array(
	"install.php",
	"module.strongcal.php",
	"submodule.strongcal.calendar.php",
	"submodule.strongcal.constants.php",
	"submodule.strongcal.event.php",
	"submodule.strongcal.export.php",
	"submodule.strongcal.fields.php",
	"submodule.strongcal.recurrance.php",
	"submodule.strongcal.validation.php",
	"checking.php",
	"index.php",
	"installer.php",
	"installerui.php",
	"mysql.php",
	"tos.php",
	"readme.txt",
);


	$files_miss = array();


if($show){

$skin = $avalanche->getSkin("installer");
$color = $skin->thcolor();
$okcolor = $skin->tdcolor();

	echo "<html>";
	echo "<head>";
	echo $skin->header();
	echo "</head>";
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
