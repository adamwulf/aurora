<?php
//////////////////////////////////////////////////////////////////////////
//  install.php								//
//----------------------------------------------------------------------//
//  a sample install script for this module				//
//////////////////////////////////////////////////////////////////////////


//$folder - the folder that this module should be installed in (subfolder of avalanche's module folder)
$folder = "fileloader";

//$version - the current version of this module (to reference for later updates)
$version = "1.0.0";

//the avalanche module installer
include "../include.module.install.php";

	echo "Installation for FileLoader! 1.0.0";
	echo "<hr>";


if($submit){
	$result = $avalancheModuleInstaller->install($folder,$version);
	if($result){
		echo "Installation successful.<br>";
	}else{
		echo "Installation failed.<br>";
	}
}else{
	if($avalancheModuleInstaller->isInstalled($folder, $version)){
		echo "FileLoader! 1.0.0 is already installed";
	}else{
		echo "Install FileLoader! Now?";
		echo "<form action='install.php' method='get'>";
		echo "<input type='submit' name='submit' value='Install Now!'";
		echo "</form>";
	}
}
?>