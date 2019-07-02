<?php
//////////////////////////////////////////////////////////////////////////
//  install.php								//
//----------------------------------------------------------------------//
//  a sample install script for this skin				//
//////////////////////////////////////////////////////////////////////////


//$folder - the folder that this skin should be installed in (subfolder of avalanche's skin folder)
$folder = "default";

//$version - the current version of this skin (to reference for later updates)
$version = "1.0.0";

//the avalanche skin installer
include "../include.skin.install.php";

	echo "Installation for Default Skin 1.0.0";
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
		echo "Default Skin 1.0.0 is installed";
	}else{
		echo "Install Default Skin Now?";
		echo "<form action='install.php' method='get'>";
		echo "<input type='submit' name='submit' value='Install Now!'";
		echo "</form>";
	}
}
?>