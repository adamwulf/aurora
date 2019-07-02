<?php
//////////////////////////////////////////////////////////////////////////
//  install.php								//
//----------------------------------------------------------------------//
//  a sample install script for this skin				//
//////////////////////////////////////////////////////////////////////////

if(!$submit)
 $submit = $_REQUEST['submit'];

//$folder - the folder that this skin should be installed in (subfolder of avalanche's skin folder)
$folder = "control";

//$version - the current version of this skin (to reference for later updates)
$version = "1.0.0";

//the avalanche skin control
include "../include.skin.install.php";

	echo "Installation for control Skin 1.0.0";
	echo "<hr>";


if($submit){
	$result = $avalancheSkinInstaller->install($folder,$version);
	
	if($result){
		echo "Installation successful.<br>";
	}else{
		echo "Installation failed.<br>";
	}
}else{
	if($avalancheSkinInstaller->isInstalled($folder, $version)){
		echo "control Skin 1.0.0 is installed";
	}else{
		echo "Install control Skin Now?";
		echo "<form action='install.php' method='get'>";
		echo "<input type='submit' name='submit' value='Install Now!'";
		echo "</form>";
	}
}
?>