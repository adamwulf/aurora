<?php
//////////////////////////////////////////////////////////////////////////
//  uninstall.php							//
//----------------------------------------------------------------------//
//  a sample uninstall script for this skin				//
//////////////////////////////////////////////////////////////////////////


//$folder - the folder that this skin is installed in (subfolder of avalanche's skin folder)
$folder = "control";

//$version - the current version of this skin (to reference for later updates)
$version = "1.0.0";

//the avalanche skin control
include "../include.skin.install.php";

	echo "Uninstall control Skin 1.0.0";
	echo "<hr>";


if($submit){
	$result = $avalancheSkincontrol->uninstall($folder,$version);
	if($result){
		echo "Uninstall successful.<br>";
	}else{
		echo "Uninstall failed.<br>";
	}
}else{
	if(!$avalancheSkincontrol->isInstalled($folder, $version)){
		echo "control Skin 1.0.0 is not installed";
	}else{
		echo "Uninstall control Skin Now?";
		echo "<form action='uninstall.php' method='get'>";
		echo "<input type='submit' name='submit' value='Uninstall Now'";
		echo "</form>";
	}
}
?>