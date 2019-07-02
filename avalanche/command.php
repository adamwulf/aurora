<?php

include "include.avalanche.fullApp.php";


$global = $avalanche;

if($avalanche_command){
	switch($avalanche_command){
		case "login": 
			break;
		case "logout":
			break;
		case "setSkin":
			$avalanche->setDefaultSkin($skinToSwap);
			break;
	}
}







$skin = $avalanche->defaultSkin();
$skin = $avalanche->getSkin($skin);

echo "<html>";
echo "<head>";

echo $skin->header();

echo "</head>";
echo "<body>";
echo $skin->javascript();


if($avalanche_command){
	switch($avalanche_command){
		case "showOffSkin":
			include "showOffSkin.php";
			break;
		case "module":
			$module = $avalanche->getModule($avalanche_module);
			if($module){
				echo "testing " . $module->name();
				echo "<br>";
				echo "<br>";

				echo "Adding user \"Adam Wulf\"";
				$module->addUser("Adam Wulf");
				echo "<br>";
				echo "done.";
				echo "<br>";
				echo "<br>";

				echo "Logging in user \"Adam Wulf\"";
				$module->userLoggedIn("Adam Wulf");
				echo "<br>";
				echo "done.";
				echo "<br>";
				echo "<br>";

				echo "Logging out user \"Adam Wulf\"";
				$module->userLoggedOut("Adam Wulf");
				echo "<br>";
				echo "done.";
				echo "<br>";
				echo "<br>";

				echo "Deleting user \"Adam Wulf\"";
				$module->deleteUser("Adam Wulf");
				echo "<br>";
				echo "done.";
				echo "<br>";
				echo "<br>";

				echo "testing finished.<br><br>";
				
			}else{
				echo "Error! No module with name: $avalanche_module. <br><br>";
			}
			break;
	}
}


		echo $skin->font("<br><br>");
		$default = $avalanche->defaultSkin();
		$default = $avalanche->getSkin($default);
		$current = $avalanche->currentSkin();
		$current = $avalanche->getSkin($current);
	if($avalanche->getSkinCount()>0){
		echo $skin->font("Default Skin = \"" . $default->name() . "\"");
		echo "<br>";
		echo $skin->font("Current Skin = \"" . $skin->name() . "\"");
	}else{
		echo $skin->font("Error loading default skin. No skins loaded.");
	}




	echo "<br><br>";

	$header = "";
	$footer = "";
	$body = "";

	$mngr = $avalanche->getModule("moduleManager");
	$modList = $mngr->getModuleList();
	$header = "Begin Module List<br>";
	$header = $skin->p_title($header);
	$header = $skin->th($header);
	$header = $skin->tr($header);

	for($i = 0; $i < count($modList); $i++){
		$extra = "href=command.php?avalanche_command=module&avalanche_module=". $modList[$i]["folder"];
		$temp = $skin->font($modList[$i]["folder"]);
		if($modList[$i]["installedHuh"]){
		$temp = $skin->a($temp, $extra);
		}

		if($modList[$i]["installedHuh"]){
			$icon = HOSTURL . APPPATH . MODULES . $mngr->folder() . "/green.gif";
		}else{
			$icon = HOSTURL . APPPATH . MODULES . $mngr->folder() . "/red.gif";
		}

		$temp =  "<img width='7' height='7' border='0' src=\"" . $icon . "\"> : " . $temp . " - ";
		if($modList[$i]["installedHuh"]){
			$temp .= "<a href=\"" . HOSTURL . APPPATH . MODULES . $modList[$i]["folder"] . "/uninstall.php\">Uninstall</a>";
		}else{
			$temp .= "<a href=\"" . HOSTURL . APPPATH . MODULES . $modList[$i]["folder"] . "/install.php\">Install</a>";
		}
		$temp .= "<br>";
		$temp = $skin->font($temp);
		$temp = $skin->td($temp);
		$extra = "onmouseover=\"setPointer(this, '". $skin->thcolor() ."')\" onmouseout=\"setPointer(this, '". $skin->tdcolor() ."')\"";
		$temp = $skin->tr($temp, $extra);
		$body .= $temp;
	}

	$footer = "End Module List";
	$footer = $skin->p_title($footer);
	$footer = $skin->th($footer);
	$footer = $skin->tr($footer);
	$body = $skin->table($header . $body . $footer, "50%");

	echo $body;

	echo "<br><br>";
	$sknList = $mngr->getSkinList();


	$header = "";
	$footer = "";
	$body = "";

	$mngr = $avalanche->getModule("moduleManager");
	$modList = $mngr->getModuleList();
	$header = "Begin Skin List<br>";
	$header = $skin->p_title($header);
	$header = $skin->th($header);
	$header = $skin->tr($header);


	for($i = 0; $i < count($sknList); $i++){
		$extra = "href=command.php?avalanche_command=showOffSkin&theSkin=" . $sknList[$i]["folder"];
		$temp = $skin->font($sknList[$i]["folder"]);
		if($sknList[$i]["installedHuh"]){
			$temp = $skin->a($temp, $extra);
		}

		if($sknList[$i]["installedHuh"]){
			$icon = HOSTURL . APPPATH . MODULES . $mngr->folder() . "/green.gif";
		}else{
			$icon = HOSTURL . APPPATH . MODULES . $mngr->folder() . "/red.gif";
		}

		$temp =  "<img width='7' height='7' border='0' src=\"" . $icon . "\"> : " . $temp . " - ";
		if($sknList[$i]["installedHuh"]){
			$temp .=  "<a href=\"" . HOSTURL . APPPATH . SKINS . $sknList[$i]["folder"] . "/uninstall.php\">Uninstall</a>";
			$temp .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$temp .=  "<a href=\"command.php?avalanche_command=setSkin&skinToSwap=".$sknList[$i]["folder"] . "\">Swap to Skin</a>";
		}else{
			$temp .=  "<a href=\"" . HOSTURL . APPPATH . SKINS . $sknList[$i]["folder"] . "/install.php\">Install</a>";
		}
		$temp .=  "<br>";
		$temp = $skin->font($temp);
		$temp = $skin->td($temp);
		$extra = "onmouseover=\"setPointer(this, '". $skin->thcolor() ."')\" onmouseout=\"setPointer(this, '". $skin->tdcolor() ."')\"";
		$temp = $skin->tr($temp, $extra);
		$body .= $temp;
	}

	$footer = "End Skin List";
	$footer = $skin->p_title($footer);
	$footer = $skin->th($footer);
	$footer = $skin->tr($footer);
	$body = $skin->table($header . $body . $footer, "50%");



	$user_id = $avalanche->addUser("adam", "asdf");
	echo "<br><br>";
	echo $body;
	echo "<br><br>";
	echo $user_id . "<br>";
	echo $avalanche->deleteUser($user_id) . "<br>";

	$permissions = array(
				"install_mod"              => 1,
				"uninstall_mod"            => 1,
				"install_skin"             => 1,
				"uninstall_skin"           => 1,
				"add_user"                 => 1,
				"del_user"                 => 1,
				"rename_user"              => 1,
				"add_usergroup"            => 1,
				"del_usergroup"            => 1,
				"rename_usergroup"         => 1,
				"change_default_skin"      => 1,
				"link_user"                => 1,
				"unlink_user"              => 1,
				"change_default_usergroup" => 1,
				"view_cp"                  => 1,
				"change_group_password"    => 1,
				"change_password"          => 1);


function ListHeader(){
	global $skin;
	$cell1 = $skin->p_title("Name");
	$cell1 = $skin->th($cell1);
	$cell2 = $skin->p_title("Version");
	$cell2 = $skin->th($cell2);
	$cell3 = $skin->p_title("Description");
	$cell3 = $skin->th($cell3);
	$cell4 = $skin->p_title("Uninstall");
	$cell4 = $skin->th($cell4);
	return $skin->tr($cell1 . $cell2 . $cell3 . $cell4);
}


function ListHeader2(){
	global $skin;
	$cell1 = $skin->p_title("Name");
	$cell1 = $skin->th($cell1);
	$cell2 = $skin->p_title("Version");
	$cell2 = $skin->th($cell2);
	$cell3 = $skin->p_title("Description");
	$cell3 = $skin->th($cell3);
	$cell4 = $skin->p_title("Uninstall");
	$cell4 = $skin->th($cell4);
	$cell5 = $skin->p_title("Set as Default");
	$cell5 = $skin->th($cell5);
	return $skin->tr($cell1 . $cell2 . $cell3 . $cell4 . $cell5);
}
echo "</body>";
echo "</html>";
?>