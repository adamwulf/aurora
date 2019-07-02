<?php // -*- mode: html; mmm-classes: html-php -*-

include("../avalanche/include.avalanche.fullApp.php");
echo "<html>";
echo "<head>";
$menuMaker = $avalanche->getModule("menuMaker");
$skin = $avalanche->getSkin("glass");
echo $menuMaker->getHeadHTML($skin);
echo "<title>PHP-Unit Results</title>";
echo "<STYLE TYPE=\"text/css\">";
include("stylesheet.css");
echo "</STYLE>";
echo "</head>";
echo "<body>";

	$skin = $avalanche->getSkin("jetsetblue");
	$skin->setLayer("no_graphic_border");
	$menuMaker = $avalanche->getModule("menuMaker");
	$menuMaker->createMenu("my_menu");
	$menuMaker->addItem("my_menu", new module_menuMaker_MenuLinkItem("my_text", "href='#'"));
	$menuMaker->addItem("my_menu", new module_menuMaker_MenuLinkItem("my_text2", "href='#'"));
	$menuMaker->addItem("my_menu", new module_menuMaker_MenuLinkItem("my_text3", "href='#'"));

	$menu = $menuMaker->getMenu("my_menu");

	echo $menuMaker->getHTML($skin);

	echo "<a href='#' onMouseOver='showmenu(event," . $menu->htmlId() . ")' onMouseout='delayhidemenu()'>asdfasfasdfsadf</a>";

echo "</body>";
?>