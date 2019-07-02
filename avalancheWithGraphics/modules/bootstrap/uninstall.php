<?php
//////////////////////////////////////////////////////////////////////////
//  uninstall.php							//
//----------------------------------------------------------------------//
//  a sample uninstall script for this module				//
//////////////////////////////////////////////////////////////////////////


//$folder - the folder that this module should be installed in (subfolder of avalanche's module folder)
$folder = "bootstrap";

//$version - the current version of this module (to reference for later updates)
$version = "1.0.0";

//the avalanche module installer
include "../include.module.install.php";

	echo "Uninstall Bootstrap! 1.0.0";
	echo "<hr>";


if($submit){
	$result = $avalancheModuleInstaller->uninstall($folder,$version);
	if($result){
		echo "Uninstall successful.<br>";
	}else{
		echo "Uninstall failed.<br>";
	}
}else{
	if(!$avalancheModuleInstaller->isInstalled($folder, $version)){
		echo "Bootstrap! 1.0.0 is not installed";
	}else{
		echo "Uninstall Bootstrap! Now?";
		echo "<form action='uninstall.php' method='get'>";
		echo "<input type='submit' name='submit' value='Uninstall Now'";
		echo "</form>";
	}
}
?>