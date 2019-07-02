<?php
//////////////////////////////////////////////////////////////////////////
//  uninstall.php							//
//----------------------------------------------------------------------//
//  a sample uninstall script for this skin				//
//////////////////////////////////////////////////////////////////////////


//$folder - the folder that this skin is installed in (subfolder of avalanche's skin folder)
$folder = "buffer";

//$version - the current version of this skin (to reference for later updates)
$version = "1.0.0";

//the avalanche skin installer
include "../include.skin.install.php";

	echo "Uninstall Buffer Skin 1.0.0";
	echo "<hr>";


if($submit){
	$result = $avalancheSkinInstaller->uninstall($folder,$version);
	if($result){
		echo "Uninstall successful.<br>";
	}else{
		echo "Uninstall failed.<br>";
	}
}else{
	if(!$avalancheSkinInstaller->isInstalled($folder, $version)){
		echo "Buffer Skin 1.0.0 is not installed";
	}else{
		echo "Uninstall Buffer Skin Now?";
		echo "<form action='uninstall.php' method='get'>";
		echo "<input type='submit' name='submit' value='Uninstall Now'";
		echo "</form>";
	}
}
?>